<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * CategoryModel - Kategori kuliner
 */
class CategoryModel extends Model
{
    protected $table      = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug', 'description'];
    protected $useTimestamps = true;
}
