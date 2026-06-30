<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KulinerModel;
use App\Models\CategoryModel;

class KulinerAdmin extends BaseController
{
    protected $kulinerModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->kulinerModel  = new KulinerModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status') ?: '';
        $search = $this->request->getGet('q') ?: '';

        $builder = $this->kulinerModel
            ->select('kuliner.*, categories.name as category_name, users.username as contributor_name')
            ->join('categories', 'categories.id = kuliner.category_id', 'left')
            ->join('users', 'users.id = kuliner.contributor_id', 'left')
            ->orderBy('kuliner.created_at', 'DESC');

        if ($status) {
            $builder->where('kuliner.status', $status);
        }
        // kalau ngesearch bisa pake semua kata/ga spesifik (memakai 'OR LIKE')
        if ($search) {
            $words = array_filter(explode(' ', trim($search)));
            if (!empty($words)) {
                $builder->groupStart();
                foreach ($words as $word) {
                    $builder->groupStart()
                            ->like('kuliner.name', $word)
                            ->orLike('users.username', $word)
                            ->groupEnd();
                }
                $builder->groupEnd();
            }
        }

        $data = [
            'title'      => 'Kelola Kuliner',
            'page_title' => 'Kelola Kuliner',
            'kuliners'   => $builder->paginate(15, 'kuliner'),
            'pager'      => $this->kulinerModel->pager,
            'status'     => $status,
            'search'     => $search,
        ];

        return view('admin/kuliner/index', $data);
    }

    public function show($id)
    {
        $kuliner = $this->kulinerModel
            ->select('kuliner.*, categories.name as category_name, users.username as contributor_name')
            ->join('categories', 'categories.id = kuliner.category_id', 'left')
            ->join('users', 'users.id = kuliner.contributor_id', 'left')
            ->find($id);

        if (!$kuliner) {
            return redirect()->to('/admin/kuliner')->with('error', 'Kuliner tidak ditemukan.');
        }

        $data = [
            'title'      => $kuliner['name'],
            'page_title' => 'Detail Kuliner',
            'kuliner'    => $kuliner,
        ];

        return view('admin/kuliner/show', $data);
    }

    public function approve($id)
    {
        if ($this->kulinerModel->update($id, ['status' => 'approved'])) {
            return redirect()->back()->with('success', 'Kuliner berhasil disetujui.');
        }
        return redirect()->back()->with('error', 'Gagal menyetujui kuliner.');
    }

    public function reject($id)
    {
        if ($this->kulinerModel->update($id, ['status' => 'rejected'])) {
            return redirect()->back()->with('success', 'Kuliner berhasil ditolak.');
        }
        return redirect()->back()->with('error', 'Gagal menolak kuliner.');
    }

    public function delete($id)
    {
        $kuliner = $this->kulinerModel->find($id);
        if (!$kuliner) {
            return redirect()->to('/admin/kuliner')->with('error', 'Kuliner tidak ditemukan.');
        }

        $this->kulinerModel->delete($id);
        return redirect()->to('/admin/kuliner')->with('success', 'Kuliner berhasil dihapus.');
    }
}
