<?php
namespace App\Libraries;

class WhatsappNotification
{
    public function send(string $phone, string $message): bool
    {
        $token = env('fonnte.token');

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
}