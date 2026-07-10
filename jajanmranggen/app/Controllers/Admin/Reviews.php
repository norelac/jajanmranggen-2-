<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReviewModel;
use App\Models\KulinerModel;

/**
 * Reviews Controller - Moderasi ulasan oleh admin
 */
class Reviews extends BaseController
{
    protected $reviewModel;
    protected $kulinerModel;

    public function __construct()
    {
        $this->reviewModel  = new ReviewModel();
        $this->kulinerModel = new KulinerModel();
    }

    /**
     * Daftar semua ulasan dengan pencarian
     */
    public function index()
    {
        $search = $this->request->getGet('q') ?: '';

        $builder = $this->reviewModel
            ->select('reviews.*, users.username, kuliner.name as kuliner_name, kuliner.slug as kuliner_slug')
            ->join('users', 'users.id = reviews.user_id', 'left')
            ->join('kuliner', 'kuliner.id = reviews.kuliner_id', 'left')
            ->orderBy('reviews.created_at', 'DESC');

        if ($search) {
            $builder->groupStart()
                    ->like('users.username', $search)
                    ->orLike('reviews.comment', $search)
                    ->orLike('kuliner.name', $search)
                    ->groupEnd();
        }

        $data = [
            'title'      => 'Moderasi Ulasan',
            'page_title' => 'Moderasi Ulasan',
            'reviews'    => $builder->paginate(20, 'reviews'),
            'pager'      => $this->reviewModel->pager,
            'search'     => $search,
        ];

        return view('admin/reviews/index', $data);
    }

    /**
     * Hapus ulasan & recalculate average rating
     */
    public function delete($id)
    {
        $review = $this->reviewModel->find($id);
        if (!$review) {
            return redirect()->to('/admin/reviews')->with('error', 'Ulasan tidak ditemukan.');
        }

        $kuliner_id = $review['kuliner_id'];
        $this->reviewModel->delete($id);

        $this->kulinerModel->updateAverageRating($kuliner_id);

        return redirect()->to('/admin/reviews')->with('success', 'Ulasan berhasil dihapus.');
    }
}
