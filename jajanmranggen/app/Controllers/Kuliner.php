<?php

namespace App\Controllers;

use App\Models\KulinerModel;
use App\Models\CategoryModel;
use App\Models\ReviewModel;

class Kuliner extends BaseController
{
    protected $kulinerModel;
    protected $categoryModel;
    protected $reviewModel;

    public function __construct()
    {
        $this->kulinerModel = new KulinerModel();
        $this->categoryModel = new CategoryModel();
        $this->reviewModel = new ReviewModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $category = $this->request->getGet('kategori');

        $this->kulinerModel->select('kuliner.*, categories.name as category_name')
                           ->join('categories', 'categories.id = kuliner.category_id', 'left')
                           ->where('kuliner.status', 'approved');

        // kalau ngesearch bisa pake semua kata/ga spesifik (memakai 'OR LIKE')
        if ($search) {
            $words = array_filter(explode(' ', trim($search)));
            if (!empty($words)) {
                $this->kulinerModel->groupStart();
                foreach ($words as $word) {
                    $this->kulinerModel->groupStart()
                                       ->like('kuliner.name', $word)
                                       ->orLike('kuliner.address', $word)
                                       ->orLike('categories.name', $word)
                                       ->groupEnd();
                }
                $this->kulinerModel->groupEnd();
            }
        }

        if ($category) {
            $this->kulinerModel->where('categories.slug', $category);
        }

        // Add a secondary sort order after rating
        $this->kulinerModel->orderBy('kuliner.average_rating', 'DESC');
        $this->kulinerModel->orderBy('kuliner.created_at', 'DESC');

        $data = [
            'title' => 'Eksplor Kuliner',
            'kuliners' => $this->kulinerModel->paginate(12, 'kuliner'),
            'pager' => $this->kulinerModel->pager,
            'categories' => $this->categoryModel->findAll(),
            'search' => $search,
            'current_category' => $category
        ];

        return view('kuliner/index', $data);
    }

    public function show($slug)
    {
        $kuliner = $this->kulinerModel->select('kuliner.*, categories.name as category_name, users.username as contributor_name')
            ->join('categories', 'categories.id = kuliner.category_id', 'left')
            ->join('users', 'users.id = kuliner.contributor_id', 'left')
            ->where('kuliner.slug', $slug)
            ->where('kuliner.status', 'approved')
            ->first();

        if (!$kuliner) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kuliner tidak ditemukan');
        }

        $reviews = $this->reviewModel->select('reviews.*, users.username, users.role')
            ->join('users', 'users.id = reviews.user_id', 'left')
            ->where('reviews.kuliner_id', $kuliner['id'])
            ->orderBy('reviews.created_at', 'DESC')
            ->findAll();

        $is_favorited = false;
        if (session()->get('logged_in')) {
            $favoriteModel = new \App\Models\FavoriteModel();
            $is_favorited = $favoriteModel->isFavorited(session()->get('user_id'), $kuliner['id']);
        }

        $data = [
            'title' => $kuliner['name'],
            'kuliner' => $kuliner,
            'reviews' => $reviews,
            'is_favorited' => $is_favorited
        ];

        return view('kuliner/show', $data);
    }

    public function storeReview()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu untuk memberikan ulasan.');
        }

        $kuliner_id = $this->request->getPost('kuliner_id');
        $rating = $this->request->getPost('rating');
        $comment = $this->request->getPost('comment');

        // Validation
        if (!$this->validate([
            'rating' => 'required|numeric|greater_than_equal_to[1]|less_than_equal_to[5]',
            'comment' => 'required|min_length[5]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->reviewModel->insert([
            'kuliner_id' => $kuliner_id,
            'user_id' => session()->get('user_id'),
            'rating' => $rating,
            'comment' => $comment
        ]);

        // Update Average Rating
        $this->kulinerModel->updateAverageRating($kuliner_id);

        return redirect()->back()->with('success', 'Review berhasil ditambahkan! Terima kasih.');
    }
}
