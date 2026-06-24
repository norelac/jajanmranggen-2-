<?php
namespace App\Controllers\Contributor;
use App\Controllers\BaseController;
use App\Models\KulinerModel;
use App\Models\PaymentModel;
use App\Libraries\WhatsappNotification;

class Payment extends BaseController
{
    protected $kulinerModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->kulinerModel = new KulinerModel();
        $this->paymentModel = new PaymentModel();
    }

    public function sponsor($kuliner_id)
    {
        $kuliner = $this->kulinerModel->find($kuliner_id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Data tidak ditemukan.');
        }
        return view('contributor/payment/sponsor', ['kuliner' => $kuliner]);
    }

    public function checkout()
    {
        $kuliner_id = $this->request->getPost('kuliner_id');
        $amount     = 50000; // Rp 50.000 untuk 7 hari promosi

        $invoice = 'INV-' . date('YmdHis') . '-' . session()->get('user_id');

        $payment_id = $this->paymentModel->insert([
            'user_id'        => session()->get('user_id'),
            'kuliner_id'     => $kuliner_id,
            'invoice_number' => $invoice,
            'amount'         => $amount,
            'status'         => 'pending',
        ]);

        $clientId = env('doku.clientId');
        $sharedKey = env('doku.sharedKey');
        $isProduction = env('doku.isProduction', false);
        
        $url = $isProduction ? 'https://api.doku.com/checkout/v1/payment' : 'https://api-sandbox.doku.com/checkout/v1/payment';
        $targetPath = '/checkout/v1/payment';
        
        $requestId = bin2hex(random_bytes(16));
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $payload = [
            'order' => [
                'amount' => $amount,
                'invoice_number' => $invoice,
                'callback_url' => base_url('contributor/kuliner'),
            ],
            'payment' => [
                'payment_due_date' => 60
            ],
            'customer' => [
                'name' => session()->get('username'),
                'email' => session()->get('email')
            ]
        ];

        $jsonPayload = json_encode($payload);
        $digest = base64_encode(hash('sha256', $jsonPayload, true));

        $rawSignature = "Client-Id:" . $clientId . "\n" .
                        "Request-Id:" . $requestId . "\n" .
                        "Request-Timestamp:" . $timestamp . "\n" .
                        "Request-Target:" . $targetPath . "\n" .
                        "Digest:" . $digest;

        $signature = base64_encode(hash_hmac('sha256', $rawSignature, $sharedKey, true));
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
}