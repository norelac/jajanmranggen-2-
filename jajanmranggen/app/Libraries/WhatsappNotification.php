<?php

namespace App\Libraries;

/**
 * WhatsappNotification - Kirim notifikasi via Fonnte API
 */
class WhatsappNotification
{
    /**
     * Kirim pesan WhatsApp ke nomor tujuan
     */
    public function send(string $phone, string $message): bool
    {
        $token = env('fonnte.token');
        $phone = $this->formatPhone($phone);

        $client = \Config\Services::curlrequest();
        try {
            $response = $client->post('https://api.fonnte.com/send', [
                'headers'     => ['Authorization' => $token],
                'form_params' => [
                    'target'  => $phone,
                    'message' => $message,
                ],
            ]);
            $result = json_decode($response->getBody(), true);
            return $result['status'] ?? false;
        } catch (\Exception $e) {
            log_message('error', 'WA Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format nomor HP ke format internasional (62xxx)
     */
    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
