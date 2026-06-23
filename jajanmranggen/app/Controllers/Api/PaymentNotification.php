<?php
namespace App\Controllers\Api;
use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\KulinerModel;
use App\Models\UserModel;
use App\Libraries\WhatsappNotification;
use Midtrans\Config as MidtransConfig;
use Midtrans\Notification;

class PaymentNotification extends BaseController
{
    public function handle()
    {
        MidtransConfig::$serverKey    = env('midtrans.serverKey');
        MidtransConfig::$isProduction = env('midtrans.isProduction', false);

        try {
            $notification   = new Notification();
            $transStatus    = $notification->transaction_status;
            $fraudStatus    = $notification->fraud_status;
            $orderId        = $notification->order_id;
            $paymentMethod  = $notification->payment_type;

            $paymentModel = new PaymentModel();
            $kulinerModel = new KulinerModel();
            $userModel    = new UserModel();
            $wa           = new WhatsappNotification();

            $payment = $paymentModel->where('invoice_number', $orderId)->first();
            if (!$payment) return $this->response->setStatusCode(404);

            if ($transStatus === 'capture' && $fraudStatus === 'accept' || $transStatus === 'settlement') {
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
                if ($user && $user['phone']) {
                    $msg = "✅ Pembayaran sponsor *{$kuliner['name']}* berhasil!\n"
                         . "Invoice: {$orderId}\n"
                         . "Kuliner Anda akan dipromosikan selama 7 hari.\nTerima kasih! 🍜";
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
                    <p>Terima kasih telah menggunakan JajanMranggen! 🍜</p>"
                );
                $emailService->send();

            } elseif (in_array($transStatus, ['cancel', 'deny', 'expire'])) {
                $paymentModel->update($payment['id'], ['status' => 'failed']);
            }

            return $this->response->setJSON(['status' => 'ok']);

        } catch (\Exception $e) {
            log_message('error', 'Midtrans Webhook Error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
}