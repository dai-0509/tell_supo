<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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

    /**
     * ユーザーのKPI目標設定とのリレーション
     */
    public function kpiTargets(): HasMany
    {
        return $this->hasMany(UserKpiTarget::class, 'user_id');
    }

    /**
     * 現在有効なKPI目標を取得
     */
    public function activeKpiTarget()
    {
        return $this->kpiTargets()->active()->first();
    }

    // TODO: F007実装時に追加予定
    // public function dailyKpiResults(): HasMany { ... }
    // public function weeklyKpiResults(): HasMany { ... }
    // public function monthlyKpiResults(): HasMany { ... }
}
