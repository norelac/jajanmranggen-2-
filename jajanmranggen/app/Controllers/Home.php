<?php

namespace App\Controllers;

use App\Models\KulinerModel;

/**
 * Home Controller - Halaman utama
 */
class Home extends BaseController
{
    /**
     * Tampilkan halaman utama dengan 6 kuliner unggulan
     */
    public function index(): string
    {
        $kulinerModel = new KulinerModel();

        $featured = $kulinerModel->select('kuliner.*, categories.name as category_name')
            ->join('categories', 'categories.id = kuliner.category_id', 'left')
            ->where('kuliner.status', 'approved')
            ->orderBy('kuliner.is_promoted', 'DESC')
            ->orderBy('kuliner.average_rating', 'DESC')
            ->limit(6)
            ->find();

        return view('home', [
            'title' => 'JajanMranggen - Direktori Kuliner Terbaik',
            'featured_kuliners' => $featured
        ]);
    }
}
