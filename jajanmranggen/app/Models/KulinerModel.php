<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * KulinerModel - CRUD & query kuliner
 * Termasuk: geocoding Haversine, auto-rating, slug generator
 */
class KulinerModel extends Model
{
    protected $table      = 'kuliner';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'slug', 'description', 'address',
        'latitude', 'longitude', 'category_id', 'contributor_id',
        'status', 'average_rating', 'is_promoted', 'promoted_until',
    ];
    protected $useTimestamps = true;

    /**
     * Ambil kuliner dengan nama kategori
     */
    public function getWithCategory($status = 'approved', $limit = 20, $offset = 0)
    {
        return $this->select('kuliner.*, categories.name as category_name')
                    ->join('categories', 'categories.id = kuliner.category_id')
                    ->where('kuliner.status', $status)
                    ->orderBy('kuliner.is_promoted', 'DESC')
                    ->orderBy('kuliner.average_rating', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Cari kuliner terdekat menggunakan rumus Haversine
     */
    public function getNearby($lat, $lng, $radius = 5, $categorySlug = null)
    {
        $haversine = "(6371 * acos(cos(radians($lat)) * cos(radians(latitude))
                      * cos(radians(longitude) - radians($lng))
                      + sin(radians($lat)) * sin(radians(latitude))))";

        $builder = $this->select("kuliner.*, categories.name as category_name, $haversine AS distance")
                        ->join('categories', 'categories.id = kuliner.category_id')
                        ->where('kuliner.status', 'approved')
                        ->having("distance <=", $radius)
                        ->orderBy('distance', 'ASC');

        if ($categorySlug) {
            $builder->where('categories.slug', $categorySlug);
        }

        return $builder->findAll();
    }

    /**
     * Hitung & update rata-rata rating dari semua review
     */
    public function updateAverageRating($kuliner_id)
    {
        $avg = $this->db->table('reviews')
                        ->selectAvg('rating', 'avg_rating')
                        ->where('kuliner_id', $kuliner_id)
                        ->get()->getRow()->avg_rating;

        $this->update($kuliner_id, ['average_rating' => round($avg ?? 0, 2)]);
    }

    /**
     * Buat slug unik dari nama kuliner
     */
    public function makeSlug($name)
    {
        $slug  = url_title($name, '-', true);
        $count = $this->where('slug', $slug)->countAllResults();
        return $count > 0 ? $slug . '-' . time() : $slug;
    }

    /**
     * Ambil semua kuliner milik satu kontributor
     */
    public function getByContributor($contributor_id)
    {
        return $this->select('kuliner.*, categories.name as category_name')
                    ->join('categories', 'categories.id = kuliner.category_id', 'left')
                    ->where('kuliner.contributor_id', $contributor_id)
                    ->orderBy('kuliner.created_at', 'DESC')
                    ->findAll();
    }
}
