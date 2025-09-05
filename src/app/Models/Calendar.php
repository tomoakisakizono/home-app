<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Calendar extends Model
{
    protected $table = 'calendars';

    protected $fillable = [
        'family_id',
        'pair_id',      // 互換で残すならOK（未使用なら削除可）
        'user_id',
        'title',
        'event_date',
        'event_time',   // "HH:MM" or null
        'description',
    ];

    protected $casts = [
        'event_date' => 'date', // Carbon にキャスト
    ];

    /**
     * 表示用アクセサ：常に "HH:MM" を返す（秒は切る）
     */
    public function getEventTimeHiAttribute(): ?string
    {
        $t = $this->attributes['event_time'] ?? null;
        if (!$t) {
            return null;
        }

        $s = (string)$t;
        if (preg_match('/^\d{2}:\d{2}/', $s)) {
            return substr($s, 0, 5); // "HH:MM(:SS)" → "HH:MM"
        }
        try {
            return Carbon::parse($s)->format('H:i');
        } catch (\Throwable $e) {
            return substr($s, 0, 5);
        }
    }
}
