<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentModel;

class Payments extends BaseController
{
    protected $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
    }

    public function index()
    {
        // Join with users and kuliners
        $payments = $this->paymentModel
            ->select('payments.*, users.username, users.email, kuliner.name as kuliner_name')
            ->join('users', 'users.id = payments.user_id', 'left')
            ->join('kuliner', 'kuliner.id = payments.kuliner_id', 'left')
            ->orderBy('payments.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Riwayat Pembayaran',
            'page_title' => 'Riwayat Pembayaran',
            'payments' => $payments
        ];

        return view('admin/payments/index', $data);
    }
}
