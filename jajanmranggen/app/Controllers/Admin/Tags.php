<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TagModel;

class Tags extends BaseController
{
    protected $tagModel;

    public function __construct()
    {
        $this->tagModel = new TagModel();
    }

    public function index()
    {
        $data = [
            'title'      => 'Kelola Tag (Label)',
            'page_title' => 'Kelola Tag',
            'tags'       => $this->tagModel->orderBy('name', 'ASC')->findAll(),
        ];
        return view('admin/tags/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Tambah Tag',
            'page_title' => 'Tambah Tag Baru',
        ];
        return view('admin/tags/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $slug = url_title($name, '-', true);

        // Make slug unique
        $existing = $this->tagModel->where('slug', $slug)->countAllResults();
        if ($existing > 0) {
            $slug .= '-' . time();
        }

        $this->tagModel->insert([
            'name' => $name,
            'slug' => $slug,
        ]);

        return redirect()->to('/admin/tags')->with('success', 'Tag berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tag = $this->tagModel->find($id);
        if (!$tag) {
            return redirect()->to('/admin/tags')->with('error', 'Tag tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Tag',
            'page_title' => 'Edit Tag',
            'tag'        => $tag,
        ];
        return view('admin/tags/edit', $data);
    }

    public function update($id)
    {
        $tag = $this->tagModel->find($id);
        if (!$tag) {
            return redirect()->to('/admin/tags')->with('error', 'Tag tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tagModel->update($id, [
            'name' => $this->request->getPost('name'),
        ]);

        return redirect()->to('/admin/tags')->with('success', 'Tag berhasil diperbarui.');
    }

    public function delete($id)
    {
        $tag = $this->tagModel->find($id);
        if (!$tag) {
            return redirect()->to('/admin/tags')->with('error', 'Tag tidak ditemukan.');
        }

        $this->tagModel->delete($id);
        return redirect()->to('/admin/tags')->with('success', 'Tag berhasil dihapus.');
    }
}
