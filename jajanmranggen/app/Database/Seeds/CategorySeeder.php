<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Warung Makan',    'slug' => 'warung-makan',    'description' => 'Warung nasi dan lauk-pauk'],
            ['name' => 'Jajanan Pasar',   'slug' => 'jajanan-pasar',   'description' => 'Makanan tradisional pasar'],
            ['name' => 'Minuman & Es',    'slug' => 'minuman-es',      'description' => 'Aneka minuman dan es segar'],
            ['name' => 'Bakso & Mie',     'slug' => 'bakso-mie',       'description' => 'Bakso, mie ayam, dan sejenisnya'],
            ['name' => 'Gorengan & Snack','slug' => 'gorengan-snack',  'description' => 'Gorengan dan camilan'],
            ['name' => 'Seafood',         'slug' => 'seafood',         'description' => 'Masakan ikan dan seafood'],
        ];

        foreach ($categories as &$cat) {
            $cat['created_at'] = date('Y-m-d H:i:s');
            $cat['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('categories')->insertBatch($categories);
    }
}
