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
        'role',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
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
            'last_login_at' => 'datetime',
            'failed_login_attempts' => 'integer',
            'locked_until' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * 顧客とのリレーション
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * 架電履歴とのリレーション
     */
    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }

    /**
     * KPI目標とのリレーション
     */
    public function kpiTargets(): HasMany
    {
        return $this->hasMany(KpiTarget::class);
    }

    /**
     * スクリプトとのリレーション
     */
    public function scripts(): HasMany
    {
        return $this->hasMany(Script::class);
    }

    /**
     * 管理者かどうかを判定
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * マネージャーかどうかを判定
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * オペレーターかどうかを判定
     */
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * 今日の架電数を取得
     */
    public function getTodayCallsCountAttribute()
    {
        return $this->callLogs()
                    ->whereDate('called_at', today())
                    ->count();
    }

    /**
     * 今週の架電数を取得
     */
    public function getWeeklyCallsCountAttribute()
    {
        return $this->callLogs()
                    ->whereBetween('called_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])
                    ->count();
    }

    /**
     * 今月の架電数を取得
     */
    public function getMonthlyCallsCountAttribute()
    {
        return $this->callLogs()
                    ->whereMonth('called_at', now()->month)
                    ->whereYear('called_at', now()->year)
                    ->count();
    }

    /**
     * 今週のアポ数を取得
     */
    public function getWeeklyAppointmentsCountAttribute()
    {
        return $this->callLogs()
                    ->where('result', 'appointment')
                    ->whereBetween('called_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])
                    ->count();
    }

    /**
     * 現在の週次目標を取得
     */
    public function getCurrentWeeklyTarget()
    {
        return $this->kpiTargets()
                    ->where('target_type', 'weekly')
                    ->where('target_date', now()->startOfWeek()->format('Y-m-d'))
                    ->first();
    }

    /**
     * 現在の月次目標を取得
     */
    public function getCurrentMonthlyTarget()
    {
        return $this->kpiTargets()
                    ->where('target_type', 'monthly')
                    ->where('target_date', now()->startOfMonth()->format('Y-m-d'))
                    ->first();
    }
}
