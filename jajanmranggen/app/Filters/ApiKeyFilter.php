<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ApiKeyFilter - Proteksi endpoint API dengan header X-API-KEY
 */
class ApiKeyFilter implements FilterInterface
{
    /**
     * Validasi API Key dari header sebelum request diproses
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $apiKey   = $request->getHeaderLine('X-API-KEY');
        $validKey = env('api.secretKey', 'JAJANMRANGGEN_SECRET_KEY_2024');

        if ($apiKey !== $validKey) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['status' => 'error', 'message' => 'API Key tidak valid.']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
