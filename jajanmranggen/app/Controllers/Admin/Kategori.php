<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Kategori extends BaseController
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Kelola Kategori',
            'page_title' => 'Kelola Kategori',
            'kategori'   => $this->categoryModel->orderBy('name', 'ASC')->findAll(),
        ];
        return view('admin/kategori/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah Kategori',
            'page_title' => 'Tambah Kategori',
        ];
        return view('admin/kategori/create', $data);
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $slug = url_title($name, '-', true);

        // Make slug unique
        $existing = $this->categoryModel->where('slug', $slug)->countAllResults();
        if ($existing > 0) {
            $slug .= '-' . time();
        }

        $this->categoryModel->insert([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = $this->categoryModel->find($id);
        if (!$kategori) {
            return redirect()->to('/admin/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Kategori',
            'page_title' => 'Edit Kategori',
            'kategori'   => $kategori,
        ];
        return view('admin/kategori/edit', $data);
    }

    public function update($id)
    {
        $kategori = $this->categoryModel->find($id);
        if (!$kategori) {
            return redirect()->to('/admin/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete($id)
    {
        $kategori = $this->categoryModel->find($id);
        if (!$kategori) {
            return redirect()->to('/admin/kategori')->with('error', 'Kategori tidak ditemukan.');
        }

        $this->categoryModel->delete($id);
        return redirect()->to('/admin/kategori')->with('success', 'Kategori berhasil dihapus.');
    }
}
