<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = ['pair_id', 'user_id', 'image_path', 'comment', 'photo_date', 'category','family_id'];

    public function pair()
    {
        return $this->belongsTo(Pair::class, 'pair_id');
    }

    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }

    public function images()
    {
        return $this->hasMany(PhotoImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
