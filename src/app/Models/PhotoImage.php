<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PhotoImage extends Model
{
    use HasFactory;

    protected $fillable = ['photo_id', 'image_path'];

    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photo_id');
    }

    /**
     * ストレージ上の公開URLを返す
     */
    public function getPublicUrlAttribute(): string
    {
        $p = (string) $this->image_path;

        // 絶対URLならそのまま
        if (preg_match('#^https?://#i', $p)) {
            return $p;
        }

        // storage/app/public 配下想定
        $rel = preg_replace('#^public/#', '', $p);
        return Storage::disk('public')->exists($rel)
            ? Storage::url($rel)
            : asset('images/placeholder.png');
    }
}
