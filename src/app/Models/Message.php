<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'scheduled_at', 'family_id', 'receiver_id', 'is_read', 'sender_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
