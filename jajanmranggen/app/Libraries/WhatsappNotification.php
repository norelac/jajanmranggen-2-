<?php
namespace App\Libraries;

class WhatsappNotification
{
    public function send(string $phone, string $message): bool
    {
        $token = env('fonnte.token');

        // Konversi nomor HP ke format internasional (62)
        $phone = $this->formatPhone($phone);

        $client = \Config\Services::curlrequest();
        try {
            $response = $client->post('https://api.fonnte.com/send', [
                'headers' => ['Authorization' => $token],
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

    private function formatPhone(string $phone): string
    {
        // Hapus karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Kalau diawali 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        // Kalau belum diawali 62, tambahkan 62
        elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}