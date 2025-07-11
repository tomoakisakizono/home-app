<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'pair_id'];

    public function shoppingLists()
    {
        return $this->hasMany(ShoppingList::class);
    }

    public function pair()
    {
        return $this->belongsTo(Pair::class);
    }
}
