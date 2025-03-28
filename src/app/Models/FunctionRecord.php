<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunctionRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'pair_id',
        'user_id',
        'function_name',
        'details',
    ];
    
    public function pair()
    {
        return $this->belongsTo(Pair::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

