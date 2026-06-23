<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\KulinerModel;

/**
 * @internal
 * Milestone 9 - Review & Rating Unit Test
 * Menguji bahwa average rating kuliner dihitung dengan benar
 * ketika review ditambahkan.
 */
class ReviewRatingTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    // Gunakan database group 'tests' (jajanmranggen_test — DB terpisah agar data asli aman)
    protected $DBGroup = 'tests';

    // Rollback semua tabel dan migrate ulang sebelum SETIAP test
    // Sehingga setiap test selalu dimulai dari kondisi database yang bersih
    protected $migrate = true;
    protected $refresh = true;

    /**
     * Helper: Buat data prerequisite (kategori + users) di database test.
     * Dipanggil di awal setiap test, aman karena $refresh=true sudah membersihkan tabel.
     */
    private function seedPrerequisites(): void
    {
        $db = db_connect('tests');

        $db->table('categories')->insert([
            'name'       => 'Warung Makan',
            'slug'       => 'warung-makan',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $db->table('users')->insertBatch([
            [
                'username'   => 'admin_test',
                'email'      => 'admin@test.com',
                'password'   => password_hash('admin123', PASSWORD_BCRYPT),
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'kontributor_test',
                'email'      => 'kontributor@test.com',
                'password'   => password_hash('pass123', PASSWORD_BCRYPT),
                'role'       => 'contributor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Test 1: Rata-rata rating dihitung ulang dengan benar setelah 2 review ditambahkan.
     * Skenario: review rating 4 dan 5 → average harus 4.50
     */
    public function testRataRataRatingDihitungKetikaTambahReview(): void
    {
        $this->seedPrerequisites();

        $db           = db_connect('tests');
        $kulinerModel = new KulinerModel($db);

        // Insert kuliner
        $kuliner_id = $kulinerModel->insert([
            'name'           => 'Warung Test Kuliner',
            'slug'           => 'warung-test-kuliner',
            'address'        => 'Jl. Test No.1, Mranggen, Demak',
            'category_id'    => 1,
            'contributor_id' => 2,
            'status'         => 'approved',
            'average_rating' => 0.00,
        ]);

        // Insert 2 review: rating 4 dan 5 → average = 4.50
        $db->table('reviews')->insertBatch([
            [
                'kuliner_id' => $kuliner_id,
                'user_id'    => 1,
                'rating'     => 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'kuliner_id' => $kuliner_id,
                'user_id'    => 2,
                'rating'     => 5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);

        // Hitung ulang average rating
        $kulinerModel->updateAverageRating($kuliner_id);

        // Verifikasi: (4 + 5) / 2 = 4.50
        $updated = $kulinerModel->find($kuliner_id);
        $this->assertEquals(4.50, (float) $updated['average_rating']);
    }

    /**
     * Test 2: Kuliner dengan 1 review saja → average sama dengan rating review tersebut.
     */
    public function testSatuReviewAverageRatingSamaDenganRating(): void
    {
        $this->seedPrerequisites();

        $db           = db_connect('tests');
        $kulinerModel = new KulinerModel($db);

        $kuliner_id = $kulinerModel->insert([
            'name'           => 'Warung Sate Test',
            'slug'           => 'warung-sate-test',
            'address'        => 'Jl. Sate No.2, Mranggen',
            'category_id'    => 1,
            'contributor_id' => 1,
            'status'         => 'approved',
            'average_rating' => 0.00,
        ]);

        $db->table('reviews')->insert([
            'kuliner_id' => $kuliner_id,
            'user_id'    => 1,
            'rating'     => 5,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $kulinerModel->updateAverageRating($kuliner_id);

        // Verifikasi: 1 review rating 5 → average = 5.00
        $updated = $kulinerModel->find($kuliner_id);
        $this->assertEquals(5.00, (float) $updated['average_rating']);
    }
}
