<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'industry',
        'status',
        'priority',
        'memo',
        'temperature_rating',
        'area',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * ユーザーとのリレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 架電履歴とのリレーション
     */
    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }

    /**
     * 最近の架電履歴（最新5件）
     */
    public function recentCallLogs(): HasMany
    {
        return $this->callLogs()
                   ->orderBy('called_at', 'desc')
                   ->limit(5);
    }

    /**
     * ステータス別スコープ
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 優先度別スコープ
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    /**
     * 最新の架電ログを取得
     */
    public function getLastCallAttribute()
    {
        return $this->callLogs()->latest('called_at')->first();
    }
}
