<?php

namespace App\Controllers\Contributor;

use App\Controllers\BaseController;
use App\Models\KulinerModel;

/**
 * Contributor Dashboard - Ringkasan data kontributor
 */
class Dashboard extends BaseController
{
    /**
     * Tampilkan dashboard kontributor dengan statistik kuliner
     */
    public function index()
    {
        $kulinerModel = new KulinerModel();
        $user_id      = session()->get('user_id');

        $data['total_kuliner']  = $kulinerModel->where('contributor_id', $user_id)->countAllResults();
        $data['approved']       = $kulinerModel->where('contributor_id', $user_id)->where('status', 'approved')->countAllResults();
        $data['pending']        = $kulinerModel->where('contributor_id', $user_id)->where('status', 'pending')->countAllResults();
        $data['rejected']       = $kulinerModel->where('contributor_id', $user_id)->where('status', 'rejected')->countAllResults();
        $data['recent_kuliner'] = $kulinerModel->getByContributor($user_id);

        return view('contributor/dashboard/index', $data);
    }
}
