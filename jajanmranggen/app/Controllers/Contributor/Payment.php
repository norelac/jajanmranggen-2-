<?php
namespace App\Controllers\Contributor;
use App\Controllers\BaseController;
use App\Models\KulinerModel;
use App\Models\PaymentModel;
use App\Libraries\WhatsappNotification;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class Payment extends BaseController
{
    protected $kulinerModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->kulinerModel = new KulinerModel();
        $this->paymentModel = new PaymentModel();

        MidtransConfig::$serverKey    = env('midtrans.serverKey');
        MidtransConfig::$isProduction = env('midtrans.isProduction', false);
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;
    }

    public function sponsor($kuliner_id)
    {
        $kuliner = $this->kulinerModel->find($kuliner_id);
        if (!$kuliner || $kuliner['contributor_id'] != session()->get('user_id')) {
            return redirect()->to('/contributor/kuliner')->with('error', 'Data tidak ditemukan.');
        }
        return view('contributor/payment/sponsor', ['kuliner' => $kuliner]);
    }

    public function checkout()
    {
        $kuliner_id = $this->request->getPost('kuliner_id');
        $amount     = 50000; // Rp 50.000 untuk 7 hari promosi

        $invoice = 'INV-' . date('YmdHis') . '-' . session()->get('user_id');

        $payment_id = $this->paymentModel->insert([
            'user_id'        => session()->get('user_id'),
            'kuliner_id'     => $kuliner_id,
            'invoice_number' => $invoice,
            'amount'         => $amount,
            'status'         => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id'     => $invoice,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => session()->get('username'),
                'email'      => session()->get('email'),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $this->paymentModel->update($payment_id, ['snap_token' => $snapToken]);

        return view('contributor/payment/checkout', [
            'snap_token' => $snapToken,
            'client_key' => env('midtrans.clientKey'),
            'invoice'    => $invoice,
        ]);
    }
}