<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pair extends Model
{
    use HasFactory;

    // 🔹 `invite_code` を `$fillable` に追加
    protected $fillable = ['user1_id', 'user2_id', 'invite_code', 'status', 'pair_name', 'pair_image'];

    /**
     * ユーザー1のリレーション
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * ユーザー2のリレーション
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * **ペアの相手を取得**
     * - `user1_id` が現在のユーザーなら `user2_id` を返す
     * - `user2_id` が現在のユーザーなら `user1_id` を返す
     */
    public function partner($currentUserId)
    {
        if ($this->user1_id === $currentUserId) {
            return $this->user2;
        } elseif ($this->user2_id === $currentUserId) {
            return $this->user1;
        }
        return null;
    }

    /**
     * **ペアが確定しているか確認**
     */
    public function hasAccepted()
    {
        return $this->status === 'accepted' && $this->user2_id !== null;
    }

    /**
     * **ペア画像の取得（デフォルト画像設定）**
     */
    public function getImageUrl()
    {
        return $this->pair_image ? Storage::url($this->pair_image) : asset('images/default_pair.png');
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
