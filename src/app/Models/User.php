<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pairAsUser1()
    {
        return $this->hasOne(Pair::class, 'user1_id');
    }

    public function pairAsUser2()
    {
        return $this->hasOne(Pair::class, 'user2_id');
    }

    public function pair()
    {
        return $this->hasOne(Pair::class, function ($query) {
            $query->where('status', 'accepted');
        })->where(function ($query) {
            $query->where('user1_id', $this->id)
                ->orWhere('user2_id', $this->id);
        });
    }
    // public function pair()
    // {
    //     return $this->hasOne(Pair::class, 'user1_id')
    //             ->orWhere('user2_id', $this->id)
    //             ->where('status', 'accepted');
    // }

    public function pairUser()
    {
        return $this->belongsTo(User::class, 'id', 'user1_id')
                ->orWhere('id', '<>', $this->id);
    }

}
