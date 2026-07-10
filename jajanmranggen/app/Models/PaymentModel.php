<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PaymentModel - Data pembayaran sponsor kuliner
 */
class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'kuliner_id',
        'invoice_number',
        'amount',
        'status',
        'snap_token',
        'payment_method'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
