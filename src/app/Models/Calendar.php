<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    // 🔹 テーブル名（デフォルトの `calendars` のため省略可能）
    protected $table = 'calendars';

    // 🔹 一括代入を許可するカラム
    protected $fillable = [
        'pair_id',
        'user_id',
        'title',
        'event_date',
        'event_time',
        'description',
    ];

    // 🔹 event_date を `date` 型としてキャスト
    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i:s',
    ];

    // 🔹 ペアとのリレーション（多対一）
    public function pair()
    {
        return $this->belongsTo(Pair::class);
    }

    // 🔹 ユーザーとのリレーション（多対一）
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🔹 メッセージとのリレーション（予定とメッセージを関連付ける）
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
