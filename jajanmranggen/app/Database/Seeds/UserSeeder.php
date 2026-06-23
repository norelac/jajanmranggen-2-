<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'username'   => 'admin',
                'email'      => 'admin@jajanmranggen.com',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'phone'      => '081234567890',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'kontributor1',
                'email'      => 'kontributor1@gmail.com',
                'password'   => password_hash('pass123', PASSWORD_BCRYPT),
                'role'       => 'contributor',
                'phone'      => '082345678901',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'kontributor2',
                'email'      => 'kontributor2@gmail.com',
                'password'   => password_hash('pass123', PASSWORD_BCRYPT),
                'role'       => 'contributor',
                'phone'      => '083456789012',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
