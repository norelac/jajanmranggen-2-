<?php

namespace App\Controllers;

use App\Models\KulinerModel;

class Home extends BaseController
{
    public function index(): string
    {
        $kulinerModel = new KulinerModel();
        
        // Fetch 6 featured kuliners (approved, maybe sort by rating or random)
        $featured = $kulinerModel->select('kuliner.*, categories.name as category_name')
            ->join('categories', 'categories.id = kuliner.category_id', 'left')
            ->where('kuliner.status', 'approved')
            ->orderBy('kuliner.average_rating', 'DESC')
            ->limit(6)
            ->find();

        return view('home', [
            'title' => 'JajanMranggen - Direktori Kuliner Terbaik',
            'featured_kuliners' => $featured
        ]);
    }
}
