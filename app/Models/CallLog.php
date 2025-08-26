<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'called_at',
        'result',
        'next_call_date',
        'notes',
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'next_call_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * ユーザーとのリレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 顧客とのリレーション
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * 今日の架電スコープ
     */
    public function scopeToday($query)
    {
        return $query->whereDate('called_at', today());
    }

    /**
     * 今週の架電スコープ
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('called_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * 成功した架電のスコープ
     */
    public function scopeSuccessful($query)
    {
        return $query->where('result', 'success');
    }

    /**
     * アポ獲得のスコープ
     */
    public function scopeAppointment($query)
    {
        return $query->where('result', 'appointment');
    }
}