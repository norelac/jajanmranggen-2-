<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\KulinerModel;

class KulinerApi extends BaseController
{
    /**
     * GET /api/kuliner
     *
     * Query params:
     *   - lat      (required) : latitude titik pusat pencarian
     *   - lng      (required) : longitude titik pusat pencarian
     *   - radius   (optional) : radius dalam km, default 5
     *   - category (optional) : slug kategori, misal 'bakso-mie'
     *
     * Header:
     *   X-API-KEY : JAJANMRANGGEN_SECRET_KEY_2024
     */
    public function index()
    {
        $lat      = $this->request->getGet('lat');
        $lng      = $this->request->getGet('lng');
        $radius   = $this->request->getGet('radius') ?? 5;
        $category = $this->request->getGet('category');

        // Validasi parameter wajib
        if (!$lat || !$lng) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Parameter lat dan lng wajib diisi.',
                ]);
        }

        // Validasi tipe data
        if (!is_numeric($lat) || !is_numeric($lng)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Parameter lat dan lng harus berupa angka.',
                ]);
        }

        // Validasi radius
        $radius = (float) $radius;
        if ($radius <= 0 || $radius > 50) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Radius harus di antara 0 dan 50 km.',
                ]);
        }

        $model   = new KulinerModel();
        $kuliner = $model->getNearby((float) $lat, (float) $lng, $radius, $category ?: null);
//
        // Format data respons (hilangkan field sensitif)
        $data = array_map(function ($item) {
            return [
                'id'             => (int) $item['id'],
                'name'           => $item['name'],
                'slug'           => $item['slug'],
                'description'    => $item['description'],
                'address'        => $item['address'],
                'latitude'       => (float) $item['latitude'],
                'longitude'      => (float) $item['longitude'],
                'category_name'  => $item['category_name'],
                'average_rating' => (float) $item['average_rating'],
                'is_promoted'    => (bool) $item['is_promoted'],
                'distance_km'    => round((float) $item['distance'], 2),
            ];
        }, $kuliner);

        return $this->response->setJSON([
            'status'  => 'success',
            'total'   => count($data),
            'radius'  => $radius,
            'center'  => ['lat' => (float) $lat, 'lng' => (float) $lng],
            'data'    => $data,
            //
        ]);
    }
}
