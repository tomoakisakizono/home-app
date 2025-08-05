<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gratitude extends Model
{
    use HasFactory;

    protected $fillable = [
        'pair_id',
        'user_id',
        'message',
        'family_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pair()
    {
        return $this->belongsTo(Pair::class);
    }
}
