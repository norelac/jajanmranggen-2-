<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\KulinerModel;
use App\Models\UserModel;

/**
 * PaymentInvoice Controller - Tampilkan bukti pembayaran publik
 */
class PaymentInvoice extends BaseController
{
    /**
     * Tampilkan invoice berdasarkan nomor invoice
     */
    public function show($invoice_number)
    {
        $paymentModel = new PaymentModel();
        $kulinerModel = new KulinerModel();
        $userModel    = new UserModel();

        $payment = $paymentModel->where('invoice_number', $invoice_number)->first();

        if (!$payment) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice tidak ditemukan.');
        }

        $kuliner = $kulinerModel->find($payment['kuliner_id']);
        $user    = $userModel->find($payment['user_id']);

        $data = [
            'title'   => 'Invoice ' . $payment['invoice_number'],
            'payment' => $payment,
            'kuliner' => $kuliner,
            'user'    => $user,
        ];

        return view('payment/invoice', $data);
    }
}
