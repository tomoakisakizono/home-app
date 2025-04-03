<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoImage extends Model
{
    use HasFactory;

    protected $fillable = ['photo_id', 'image_path'];

    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photo_id');
    }
}
