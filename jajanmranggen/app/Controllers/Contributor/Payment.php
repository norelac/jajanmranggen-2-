<?php

namespace App\Controllers\Contributor;

use App\Controllers\BaseController;
use App\Models\KulinerModel;
use App\Models\PaymentModel;
use App\Models\UserModel;
use App\Libraries\WhatsappNotification;

/**
 * Payment Controller - Sponsor kuliner via DOKU Payment Gateway
 */
class Payment extends BaseController
{
    protected $kulinerModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->kulinerModel = new KulinerModel();
        $this->paymentModel = new PaymentModel();
    }

    /**
     * Halaman sponsor kuliner
     */
    public function sponsor($kuliner_id)
    {
        $kuliner = $this->kulinerModel->find($kuliner_id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Data tidak ditemukan.');
        }
        return view('contributor/payment/sponsor', ['kuliner' => $kuliner]);
    }

    /**
     * Proses checkout: buat invoice, sign HMAC, redirect ke DOKU
     */
    public function checkout()
    {
        $kuliner_id = $this->request->getPost('kuliner_id');
        $amount     = 50000;

        $invoice = 'INV-' . date('YmdHis') . '-' . session()->get('user_id');

        $payment_id = $this->paymentModel->insert([
            'user_id'        => session()->get('user_id'),
            'kuliner_id'     => $kuliner_id,
            'invoice_number' => $invoice,
            'amount'         => $amount,
            'status'         => 'pending',
        ]);

        $clientId     = env('doku.clientId');
        $sharedKey    = env('doku.sharedKey');
        $isProduction = env('doku.isProduction', false);

        $url        = $isProduction ? 'https://api.doku.com/checkout/v1/payment' : 'https://api-sandbox.doku.com/checkout/v1/payment';
        $targetPath = '/checkout/v1/payment';
        $requestId  = bin2hex(random_bytes(16));
        $timestamp  = gmdate("Y-m-d\TH:i:s\Z");

        $payload = [
            'order' => [
                'amount'         => $amount,
                'invoice_number' => $invoice,
                'callback_url'   => base_url('contributor/payment/finish?invoice_number=' . $invoice),
            ],
            'payment' => [
                'payment_due_date' => 60
            ],
            'customer' => [
                'name'  => session()->get('username'),
                'email' => session()->get('email')
            ]
        ];

        if (!empty(env('doku.notificationUrl'))) {
            $payload['additional_info'] = [
                'override_notification_url' => env('doku.notificationUrl')
            ];
        }

        $jsonPayload = json_encode($payload);
        $digest      = base64_encode(hash('sha256', $jsonPayload, true));

        $rawSignature = "Client-Id:" . $clientId . "\n" .
                        "Request-Id:" . $requestId . "\n" .
                        "Request-Timestamp:" . $timestamp . "\n" .
                        "Request-Target:" . $targetPath . "\n" .
                        "Digest:" . $digest;

        $signature      = base64_encode(hash_hmac('sha256', $rawSignature, $sharedKey, true));
        $finalSignature = "HMACSHA256=" . $signature;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Client-Id: ' . $clientId,
            'Request-Id: ' . $requestId,
            'Request-Timestamp: ' . $timestamp,
            'Signature: ' . $finalSignature,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode == 200 && isset($result['response']['payment']['url'])) {
            $paymentUrl = $result['response']['payment']['url'];
            $this->paymentModel->update($payment_id, ['snap_token' => $paymentUrl]);
            return redirect()->to($paymentUrl);
        } else {
            $this->paymentModel->delete($payment_id);
            log_message('error', 'DOKU API Error: ' . $response);
            return redirect()->back()->with('error', 'Gagal menghubungi DOKU. Pastikan konfigurasi API Key sudah benar.');
        }
    }

    /**
     * Halaman selesai pembayaran (callback dari DOKU)
     */
    public function finish()
    {
        $invoice = $this->request->getGet('invoice_number');

        if (!$invoice) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Data invoice tidak ditemukan dari callback DOKU.');
        }

        $payment = $this->paymentModel->where('invoice_number', $invoice)->first();
        if (!$payment) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Transaksi tidak dikenali.');
        }

        if ($payment['status'] === 'pending') {
            $this->verifyWithDoku($payment);
            $payment = $this->paymentModel->where('invoice_number', $invoice)->first();
        }

        return view('contributor/payment/finish', ['payment' => $payment]);
    }

    /**
     * AJAX: Cek status pembayaran
     */
    public function checkStatus($invoice)
    {
        $payment = $this->paymentModel->where('invoice_number', $invoice)->first();
        if ($payment) {
            if ($payment['status'] === 'pending') {
                $this->verifyWithDoku($payment);
                $payment = $this->paymentModel->where('invoice_number', $invoice)->first();
            }
            return $this->response->setJSON(['status' => $payment['status']]);
        }
        return $this->response->setStatusCode(404)->setJSON(['status' => 'not_found']);
    }

    /**
     * Verifikasi internal: update status, kirim notifikasi WA & email
     */
    private function verifyWithDoku($payment)
    {
        $paymentModel = new PaymentModel();
        $kulinerModel = new KulinerModel();
        $userModel    = new UserModel();
        $wa           = new WhatsappNotification();

        $paymentModel->update($payment['id'], [
            'status'         => 'paid',
            'payment_method' => $payment['payment_method'] ?? 'DOKU',
        ]);

        $kulinerModel->update($payment['kuliner_id'], [
            'is_promoted'    => 1,
            'promoted_until' => date('Y-m-d H:i:s', strtotime('+7 days')),
        ]);

        $user    = $userModel->find($payment['user_id']);
        $kuliner = $kulinerModel->find($payment['kuliner_id']);
        $invoiceUrl = base_url('payment/invoice/' . $payment['invoice_number']);

        if ($user && $user['phone']) {
            $msg = "Pembayaran sponsor *{$kuliner['name']}* berhasil!\n"
                 . "Invoice: {$payment['invoice_number']}\n"
                 . "Kuliner Anda akan dipromosikan selama 7 hari.\n"
                 . "Bukti pembayaran: {$invoiceUrl}\n"
                 . "Terima kasih!";
            $wa->send($user['phone'], $msg);
        }

        $emailService = \Config\Services::email();
        $emailService->setTo($user['email']);
        $emailService->setSubject('Pembayaran Sponsor Berhasil - JajanMranggen');
        $emailService->setMessage(
            "<h2>Pembayaran Berhasil!</h2>
            <p>Halo <strong>{$user['username']}</strong>,</p>
            <p>Kuliner <strong>{$kuliner['name']}</strong> kamu berhasil disponsori selama 7 hari.</p>
            <p>Invoice: <code>{$payment['invoice_number']}</code></p>
            <p>Lihat bukti pembayaran: <a href=\"{$invoiceUrl}\">{$invoiceUrl}</a></p>
            <p>Terima kasih telah menggunakan JajanMranggen!</p>"
        );
        if (!$emailService->send()) {
            log_message('error', 'Email Gagal: ' . print_r($emailService->printDebugger(['headers', 'subject']), true));
        }

        log_message('info', 'Internal verify success for: ' . $payment['invoice_number']);
    }
}
