<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KulinerSeeder extends Seeder
{
    public function run()
    {
        $kuliner = [
            ['name' => 'Warung Bu Mira Mranggen',      'slug' => 'warung-bu-mira-mranggen',      'description' => 'Warung nasi rumahan dengan masakan Jawa otentik.',               'address' => 'Jl. Raya Mranggen No.12, Mranggen, Demak',    'latitude' => -6.9917, 'longitude' => 110.4897, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.50],
            ['name' => 'Es Dawet Pak Surip',            'slug' => 'es-dawet-pak-surip',            'description' => 'Es dawet ayu legendaris di Mranggen sejak 1990.',               'address' => 'Pasar Mranggen, Demak',                        'latitude' => -6.9920, 'longitude' => 110.4870, 'category_id' => 3, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.80],
            ['name' => 'Bakso Pak Harto Mranggen',      'slug' => 'bakso-pak-harto-mranggen',      'description' => 'Bakso sapi asli dengan kuah bening segar.',                     'address' => 'Jl. Kauman No.5, Mranggen, Demak',             'latitude' => -6.9930, 'longitude' => 110.4905, 'category_id' => 4, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.30],
            ['name' => 'Gorengan Mbak Yem',             'slug' => 'gorengan-mbak-yem',             'description' => 'Gorengan renyah: tempe, bakwan, tahu isi.',                     'address' => 'Depan SD N 1 Mranggen, Demak',                 'latitude' => -6.9940, 'longitude' => 110.4880, 'category_id' => 5, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.20],
            ['name' => 'Warung Soto Semarang Bu Rini',  'slug' => 'warung-soto-semarang-bu-rini',  'description' => 'Soto ayam Semarang dengan bumbu kuning khas.',                  'address' => 'Jl. Depok No.8, Mranggen, Demak',              'latitude' => -6.9908, 'longitude' => 110.4912, 'category_id' => 1, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.60],
            ['name' => 'Jajanan Pasar Mranggen',        'slug' => 'jajanan-pasar-mranggen',        'description' => 'Aneka jajanan pasar tradisional: klepon, onde-onde, cenil.',    'address' => 'Pasar Pagi Mranggen, Demak',                   'latitude' => -6.9918, 'longitude' => 110.4868, 'category_id' => 2, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.10],
            ['name' => 'Seafood Pak Darto Trimulyo',    'slug' => 'seafood-pak-darto-trimulyo',    'description' => 'Ikan bakar dan goreng segar dari tambak lokal.',                'address' => 'Jl. Trimulyo Km.2, Genuk, Semarang',           'latitude' => -6.9850, 'longitude' => 110.4790, 'category_id' => 6, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.40],
            ['name' => 'Mie Ayam Pak Bambang',          'slug' => 'mie-ayam-pak-bambang',          'description' => 'Mie ayam dengan topping ayam kecap gurih.',                    'address' => 'Jl. Soekarno Hatta, Mranggen, Demak',          'latitude' => -6.9925, 'longitude' => 110.4920, 'category_id' => 4, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.35],
            ['name' => 'Warung Pecel Mranggen',         'slug' => 'warung-pecel-mranggen',         'description' => 'Pecel sayuran dengan bumbu kacang otentik.',                   'address' => 'Jl. Ahmad Yani No.20, Mranggen, Demak',        'latitude' => -6.9912, 'longitude' => 110.4895, 'category_id' => 1, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.55],
            ['name' => 'Es Teh Pak Min Karangsono',     'slug' => 'es-teh-pak-min-karangsono',     'description' => 'Es teh manis jumbo paling seger di Karangsono.',                'address' => 'Karangsono, Mranggen, Demak',                  'latitude' => -6.9945, 'longitude' => 110.4855, 'category_id' => 3, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.00],
            ['name' => 'Nasi Goreng Bang Jon Mranggen', 'slug' => 'nasi-goreng-bang-jon',          'description' => 'Nasi goreng spesial dengan telur dan kerupuk.',                 'address' => 'Jl. Branjang No.3, Mranggen, Demak',           'latitude' => -6.9935, 'longitude' => 110.4910, 'category_id' => 1, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.25],
            ['name' => 'Warung Gado-Gado Bu Lastri',    'slug' => 'warung-gado-gado-bu-lastri',    'description' => 'Gado-gado segar dengan bumbu kacang pilihan.',                  'address' => 'Jl. Kali Banger, Mranggen, Demak',             'latitude' => -6.9905, 'longitude' => 110.4875, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.15],
            ['name' => 'Angkringan Mas Joko Tegalarum', 'slug' => 'angkringan-mas-joko-tegalarum', 'description' => 'Angkringan hits dengan nasi bakar dan wedang ronde.',           'address' => 'Tegalarum, Mranggen, Demak',                   'latitude' => -6.9955, 'longitude' => 110.4845, 'category_id' => 1, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.70],
            ['name' => 'Warung Sambel Bu Darni',        'slug' => 'warung-sambel-bu-darni',        'description' => 'Lalapan dan sambel terasi pedas merica istimewa.',              'address' => 'Jl. Mondokan, Mranggen, Demak',                'latitude' => -6.9922, 'longitude' => 110.4930, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.45],
            ['name' => 'Tahu Gimbal Pak Tomo',          'slug' => 'tahu-gimbal-pak-tomo',          'description' => 'Tahu gimbal khas Semarang dengan bumbu kacang pedas.',          'address' => 'Jl. Mranggen Raya, Demak',                     'latitude' => -6.9915, 'longitude' => 110.4888, 'category_id' => 2, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.60],
            ['name' => 'Nasi Kucing Mbak Sri',          'slug' => 'nasi-kucing-mbak-sri',          'description' => 'Nasi kucing porsi kecil dengan lauk lengkap, cocok camilan malam.', 'address' => 'Depan Masjid Al-Ikhlas Mranggen',          'latitude' => -6.9928, 'longitude' => 110.4900, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.30],
            ['name' => 'Bakwan Jagung Mbak Tini',       'slug' => 'bakwan-jagung-mbak-tini',       'description' => 'Bakwan jagung renyah dan panas, cocok untuk camilan sore.',     'address' => 'Gang Mawar No.2, Mranggen, Demak',              'latitude' => -6.9942, 'longitude' => 110.4892, 'category_id' => 5, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.05],
            ['name' => 'Warung Tongseng Pak Wahyu',     'slug' => 'warung-tongseng-pak-wahyu',     'description' => 'Tongseng kambing kaya rempah yang menggugah selera.',           'address' => 'Jl. Brumbungan, Mranggen, Demak',              'latitude' => -6.9908, 'longitude' => 110.4918, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.75],
            ['name' => 'Es Kelapa Muda Pak Slamet',     'slug' => 'es-kelapa-muda-pak-slamet',     'description' => 'Es kelapa muda langsung dari kelapa segar.',                   'address' => 'Pinggir Jalan Raya Mranggen-Purwodadi',        'latitude' => -6.9950, 'longitude' => 110.4935, 'category_id' => 3, 'contributor_id' => 3, 'status' => 'approved', 'average_rating' => 4.90],
            ['name' => 'Warung Sate Bu Endah',          'slug' => 'warung-sate-bu-endah',          'description' => 'Sate ayam dan kambing dengan bumbu kecap khas Mranggen.',      'address' => 'Jl. Pangkalan, Mranggen, Demak',               'latitude' => -6.9910, 'longitude' => 110.4870, 'category_id' => 1, 'contributor_id' => 2, 'status' => 'approved', 'average_rating' => 4.65],
        ];

        foreach ($kuliner as &$k) {
            $k['created_at'] = date('Y-m-d H:i:s');
            $k['updated_at'] = date('Y-m-d H:i:s');
            $k['is_promoted'] = 0;
        }

        $this->db->table('kuliner')->insertBatch($kuliner);
    }
}
