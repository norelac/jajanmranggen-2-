<?php
namespace App\Controllers\Api;
use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\KulinerModel;
use App\Models\UserModel;
use App\Libraries\WhatsappNotification;


class PaymentNotification extends BaseController
{
    public function handle()
    {
        $sharedKey = env('doku.sharedKey');
        
        $clientIdHeader = $this->request->getHeaderLine('Client-Id');
        $requestIdHeader = $this->request->getHeaderLine('Request-Id');
        $requestTimestampHeader = $this->request->getHeaderLine('Request-Timestamp');
        $signatureHeader = $this->request->getHeaderLine('Signature');
        
        $jsonBody = $this->request->getBody();
        $targetPath = '/api/payment/notification';
        
        // Internal verification dari server sendiri (localhost) — lewati signature check
        $isInternalVerify = $this->request->getHeaderLine('X-Internal-Verify') === 'true';
        
        if (!$isInternalVerify) {
            // Verifikasi Signature DOKU
            $digest = base64_encode(hash('sha256', $jsonBody, true));
            $rawSignature = "Client-Id:" . $clientIdHeader . "\n" .
                            "Request-Id:" . $requestIdHeader . "\n" .
                            "Request-Timestamp:" . $requestTimestampHeader . "\n" .
                            "Request-Target:" . $targetPath . "\n" .
                            "Digest:" . $digest;
            
            $calculatedSignature = "HMACSHA256=" . base64_encode(hash_hmac('sha256', $rawSignature, $sharedKey, true));
            
            // Validasi signature
            if (!empty($signatureHeader) && !hash_equals($calculatedSignature, $signatureHeader)) {
                log_message('error', 'DOKU Webhook Invalid Signature');
                return $this->response->setStatusCode(401)->setJSON(['error' => 'Invalid Signature']);
            }
        }
        
        try {
            $data = json_decode($jsonBody, true);
            
            $orderId = $data['order']['invoice_number'] ?? '';
            $transStatus = $data['transaction']['status'] ?? '';
            $paymentMethod = $data['channel']['id'] ?? 'DOKU';

            $paymentModel = new PaymentModel();
            $kulinerModel = new KulinerModel();
            $userModel    = new UserModel();
            $wa           = new WhatsappNotification();

            $payment = $paymentModel->where('invoice_number', $orderId)->first();
            if (!$payment) return $this->response->setStatusCode(404);

            if (strtoupper($transStatus) === 'SUCCESS') {
                $paymentModel->update($payment['id'], [
                    'status'         => 'paid',
                    'payment_method' => $paymentMethod,
                ]);

                $kulinerModel->update($payment['kuliner_id'], [
                    'is_promoted'    => 1,
                    'promoted_until' => date('Y-m-d H:i:s', strtotime('+7 days')),
                ]);

                // Kirim notifikasi WA
                $user   = $userModel->find($payment['user_id']);
                $kuliner = $kulinerModel->find($payment['kuliner_id']);
                $invoiceUrl = base_url('payment/invoice/' . $orderId);
                if ($user && $user['phone']) {
                    $msg = "✅ Pembayaran sponsor *{$kuliner['name']}* berhasil!\n"
                         . "Invoice: {$orderId}\n"
                         . "Kuliner Anda akan dipromosikan selama 7 hari.\n"
                         . "Bukti pembayaran: {$invoiceUrl}\n"
                         . "Terima kasih! 🍜";
                    $wa->send($user['phone'], $msg);
                }

                // Kirim Email
                $emailService = \Config\Services::email();
                $emailService->setTo($user['email']);
                $emailService->setSubject('Pembayaran Sponsor Berhasil - JajanMranggen');
                $emailService->setMessage(
                    "<h2>Pembayaran Berhasil!</h2>
                    <p>Halo <strong>{$user['username']}</strong>,</p>
                    <p>Kuliner <strong>{$kuliner['name']}</strong> kamu berhasil disponsori selama 7 hari.</p>
                    <p>Invoice: <code>{$orderId}</code></p>
                    <p>Lihat bukti pembayaran: <a href=\"{$invoiceUrl}\">{$invoiceUrl}</a></p>
                    <p>Terima kasih telah menggunakan JajanMranggen! 🍜</p>"
                );
                if (!$emailService->send()) {
                    log_message('error', 'Email Gagal: ' . print_r($emailService->printDebugger(['headers', 'subject']), true));
                }

            } elseif (in_array(strtoupper($transStatus), ['FAILED', 'EXPIRED'])) {
                $paymentModel->update($payment['id'], ['status' => 'failed']);
            }

            return $this->response->setJSON(['status' => 'ok']);

        } catch (\Exception $e) {
            log_message('error', 'DOKU Webhook Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
}