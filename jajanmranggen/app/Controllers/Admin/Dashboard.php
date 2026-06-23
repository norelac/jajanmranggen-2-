<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KulinerModel;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\ReviewModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $kulinerModel  = new KulinerModel();
        $userModel     = new UserModel();
        $categoryModel = new CategoryModel();
        $reviewModel   = new ReviewModel();

        $data = [
            'title'             => 'Admin Dashboard',
            'page_title'        => 'Beranda Admin',
            'total_kuliner'     => $kulinerModel->countAllResults(),
            'total_approved'    => $kulinerModel->where('status', 'approved')->countAllResults(),
            'total_pending'     => $kulinerModel->where('status', 'pending')->countAllResults(),
            'total_users'       => $userModel->countAllResults(),
            'total_reviews'     => $reviewModel->countAllResults(),
            'total_categories'  => $categoryModel->countAllResults(),
            'recent_kuliner'    => $kulinerModel
                                    ->select('kuliner.*, categories.name as category_name, users.username as contributor_name')
                                    ->join('categories', 'categories.id = kuliner.category_id', 'left')
                                    ->join('users', 'users.id = kuliner.contributor_id', 'left')
                                    ->orderBy('kuliner.created_at', 'DESC')
                                    ->limit(5)
                                    ->findAll(),
            'pending_kuliner'   => $kulinerModel
                                    ->select('kuliner.*, categories.name as category_name, users.username as contributor_name')
                                    ->join('categories', 'categories.id = kuliner.category_id', 'left')
                                    ->join('users', 'users.id = kuliner.contributor_id', 'left')
                                    ->where('kuliner.status', 'pending')
                                    ->orderBy('kuliner.created_at', 'DESC')
                                    ->limit(5)
                                    ->findAll(),
        ];

        return view('admin/dashboard/index', $data);
    }
}
