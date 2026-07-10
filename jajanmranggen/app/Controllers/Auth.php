<?php

namespace App\Controllers;

use App\Models\UserModel;

/**
 * Auth Controller - Login, Register, Logout
 */
class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman login
     */
    public function login()
    {
        if (session()->get('user_id')) {
            return $this->redirectByRole();
        }
        return view('auth/login');
    }

    /**
     * Proses login
     */
    public function loginPost()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->where('email', $this->request->getPost('email'))->first();

        if (!$user || !password_verify($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        session()->set([
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'email'     => $user['email'],
            'role'      => $user['role'],
            'logged_in' => true,
        ]);

        return $this->redirectByRole();
    }

    /**
     * Halaman registrasi
     */
    public function register()
    {
        return view('auth/register');
    }

    /**
     * Proses registrasi
     */
    public function registerPost()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'phone'    => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->save([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'phone'    => $this->request->getPost('phone'),
            'role'     => 'contributor',
        ]);

        return redirect()->to('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    /**
     * Redirect berdasarkan role user
     */
    private function redirectByRole()
    {
        $role = session()->get('role');
        return $role === 'admin'
            ? redirect()->to('/admin/dashboard')
            : redirect()->to('/contributor/dashboard');
    }
}
