<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected $table = 'calendars';

    // ✅ family_id を追加（これが無いと保存されない）
    protected $fillable = [
        'family_id',
        'pair_id',     // ※将来廃止予定なら残してOK
        'user_id',
        'title',
        'event_date',
        'event_time',
        'description',
    ];

    // ✅ event_time は time型なので string で扱うのが安全
    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'string',
    ];

    public function pair()
    {
        return $this->belongsTo(Pair::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 便利アクセサ（“HH:mm”）
    public function getEventTimeHmAttribute(): ?string
    {
        if (!$this->event_time) {
            return null;
        }
        try {
            return \Carbon\Carbon::createFromFormat('H:i:s', $this->event_time)->format('H:i');
        } catch (\Throwable $e) {
            return (string)$this->event_time; // 既存データが H:i の場合もそのまま
        }
    }
}
