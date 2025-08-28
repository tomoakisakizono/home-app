<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'pair_id',
        'user_id',
        'comment',
        'photo_date',
        'category',
        'family_id'
    ];

    public function pair()
    {
        return $this->belongsTo(Pair::class, 'pair_id');
    }

    public function images()
    {
        return $this->hasMany(PhotoImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 最初の画像のURLを返す（存在しない場合はプレースホルダ）
     */
    public function getFirstImageUrlAttribute(): string
    {
        $first = $this->images->first();
        return $first ? $first->public_url : asset('images/placeholder.png');
    }
}
