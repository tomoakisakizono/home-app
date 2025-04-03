<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'pair_id',
        'title',
        'due_date',
        'is_done',
    ];

    public function pair()
    {
        return $this->belongsTo(Pair::class);
    }

    public function getIsDueSoonAttribute()
{
    $dueDate = Carbon::parse($this->due_date);
    $today = Carbon::today();

    // 今日 or 明日が期限
    return !$this->is_done && $dueDate->between($today, $today->copy()->addDay(3));
}
}
