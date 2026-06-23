<?php

namespace Tests\App;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use App\Database\Seeds\UserSeeder;

/**
 * @internal
 * Milestone 8 - Auth Feature Test
 * Menguji fungsi login, redirect berdasarkan role, dan keamanan akses route.
 */
class AuthTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // Gunakan database group 'tests' (jajanmranggen_test)
    protected $DBGroup = 'tests';

    // Jalankan migration sebelum suite test ini
    protected $migrate = true;

    // Rollback dan migrate ulang sebelum SETIAP test agar kondisi selalu bersih
    protected $refresh = true;

    // Jalankan seeder ini setelah migration
    protected $seed = UserSeeder::class;

    /**
     * Test 1: Login berhasil sebagai Admin harus redirect ke /admin/dashboard
     */
    public function testLoginSuksesAsAdmin(): void
    {
        $result = $this->post('/login', [
            'email'    => 'admin@jajanmranggen.com',
            'password' => 'admin123',
        ]);

        $result->assertRedirectTo(site_url('/admin/dashboard'));
    }

    /**
     * Test 2: Login dengan password salah harus mengembalikan flash error
     */
    public function testLoginGagalPasswordSalah(): void
    {
        $result = $this->post('/login', [
            'email'    => 'admin@jajanmranggen.com',
            'password' => 'passwordsalah123',
        ]);

        $result->assertSessionHas('error', 'Email atau password salah.');
    }

    /**
     * Test 3: Contributor yang login tidak boleh mengakses route Admin
     */
    public function testContributorTidakBisaAksesAdminRoute(): void
    {
        $result = $this->withSession([
            'user_id'   => 2,
            'username'  => 'kontributor1',
            'role'      => 'contributor',
            'logged_in' => true,
        ])->get('/admin/dashboard');

        $result->assertSessionHas('error', 'Akses ditolak. Hanya Admin.');
    }

    /**
     * Test 4: Tamu (belum login) tidak boleh mengakses dashboard Admin
     */
    public function testGuestTidakBisaAksesDashboard(): void
    {
        $result = $this->get('/admin/dashboard');

        $result->assertRedirectTo(site_url('/login'));
    }
}
