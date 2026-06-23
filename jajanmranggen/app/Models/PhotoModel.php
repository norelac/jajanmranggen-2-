<?php
namespace App\Models;
use CodeIgniter\Model;

class PhotoModel extends Model
{
    protected $table      = 'photos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kuliner_id', 'filename', 'is_primary'];
    protected $useTimestamps = true;

    public function getPrimaryPhoto($kuliner_id)
    {
        return $this->where('kuliner_id', $kuliner_id)
                    ->where('is_primary', 1)
                    ->first();
    }

    public function getByKuliner($kuliner_id)
    {
        return $this->where('kuliner_id', $kuliner_id)
                    ->orderBy('is_primary', 'DESC')
                    ->findAll();
    }
}
