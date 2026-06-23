<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q') ?: '';
        $role   = $this->request->getGet('role') ?: '';

        $builder = $this->userModel->orderBy('created_at', 'DESC');

        if ($search) {
            $builder->groupStart()
                    ->like('username', $search)
                    ->orLike('email', $search)
                    ->groupEnd();
        }
        if ($role) {
            $builder->where('role', $role);
        }

        $data = [
            'title'      => 'Kelola Pengguna',
            'page_title' => 'Kelola Pengguna',
            'users'      => $builder->paginate(20, 'users'),
            'pager'      => $this->userModel->pager,
            'search'     => $search,
            'role'       => $role,
        ];

        return view('admin/users/index', $data);
    }

    public function delete($id)
    {
        // Prevent deleting own account
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan.');
        }

        $this->userModel->delete($id);
        return redirect()->to('/admin/users')->with('success', "User '{$user['username']}' berhasil dihapus.");
    }
}
