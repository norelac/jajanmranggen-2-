<?php

namespace App\Controllers;

use App\Models\FavoriteModel;
use App\Models\KulinerModel;

/**
 * Favorites Controller - Toggle & list favorit user
 */
class Favorites extends BaseController
{
    protected $favoriteModel;
    protected $kulinerModel;

    public function __construct()
    {
        $this->favoriteModel = new FavoriteModel();
        $this->kulinerModel  = new KulinerModel();
    }

    /**
     * Toggle favorit via AJAX POST /favorites/toggle
     */
    public function toggle()
    {
        $user_id    = session()->get('user_id');
        $kuliner_id = $this->request->getPost('kuliner_id');

        if (!$kuliner_id || !is_numeric($kuliner_id)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'ID kuliner tidak valid.',
            ]);
        }

        $kuliner = $this->kulinerModel->find($kuliner_id);
        if (!$kuliner) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Kuliner tidak ditemukan.',
            ]);
        }

        $action = $this->favoriteModel->toggle($user_id, $kuliner_id);

        return $this->response->setJSON([
            'status'  => 'success',
            'action'  => $action,
            'message' => $action === 'added' ? 'Ditambahkan ke favorit.' : 'Dihapus dari favorit.',
        ]);
    }

    /**
     * Halaman daftar favorit saya GET /favorites
     */
    public function index()
    {
        $user_id = session()->get('user_id');
        $data = [
            'title'     => 'Favorit Saya',
            'favorites' => $this->favoriteModel->getUserFavorites($user_id),
        ];

        return view('favorites/index', $data);
    }
}
