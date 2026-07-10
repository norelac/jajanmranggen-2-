<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * UserModel - Data pengguna (admin & kontributor)
 */
class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'email', 'password', 'role', 'phone'];
    protected $useTimestamps = true;
    protected $hidden = ['password'];
}
