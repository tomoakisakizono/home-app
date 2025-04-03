<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'pair_id',
        'user_id',
        'youtube_url',
        'comment',
        'category',
        'registered_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
