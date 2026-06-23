<?php

namespace App\Models;

use CodeIgniter\Model;

class FavoriteModel extends Model
{
    protected $table      = 'favorites';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'kuliner_id'];
    protected $useTimestamps = true;
    protected $updatedField  = ''; // no updated_at in favorites

    public function isFavorited($user_id, $kuliner_id): bool
    {
        return $this->where('user_id', $user_id)
                    ->where('kuliner_id', $kuliner_id)
                    ->countAllResults() > 0;
    }

    public function toggle($user_id, $kuliner_id): string
    {
        $existing = $this->where('user_id', $user_id)
                         ->where('kuliner_id', $kuliner_id)
                         ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return 'removed';
        }

        $this->insert(['user_id' => $user_id, 'kuliner_id' => $kuliner_id]);
        return 'added';
    }

    public function getUserFavorites($user_id)
    {
        return $this->select('favorites.*, kuliner.name, kuliner.slug, kuliner.address, kuliner.average_rating, categories.name as category_name')
                    ->join('kuliner', 'kuliner.id = favorites.kuliner_id')
                    ->join('categories', 'categories.id = kuliner.category_id', 'left')
                    ->where('favorites.user_id', $user_id)
                    ->where('kuliner.status', 'approved')
                    ->orderBy('favorites.created_at', 'DESC')
                    ->findAll();
    }
}
