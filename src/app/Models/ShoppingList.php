<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'pair_id', 'user_id', 'item_name', 'quantity', 'status', 'category', 'due_date', 'note', 'category_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pair()
    {
        return $this->belongsTo(Pair::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
