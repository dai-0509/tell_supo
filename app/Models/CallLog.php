<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'started_at',
        'ended_at',
        'result',
        'notes',
        'duration_seconds',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCustomer(Builder $query, int $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByResult(Builder $query, string $result): Builder
    {
        return $query->where('result', $result);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('started_at', Carbon::today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('started_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ]);
    }

    public function getFormattedDurationAttribute(): string
    {
        if (! $this->duration_seconds) {
            return '00:00';
        }

        $minutes = intval($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getResultLabelAttribute(): string
    {
        return match ($this->result) {
            'connected' => '接続済',
            'no_answer' => '応答なし',
            'busy' => '話中',
            'failed' => '失敗',
            'voicemail' => '留守電',
            default => '不明',
        };
    }

    public function calculateDuration(): void
    {
        if ($this->started_at && $this->ended_at) {
            $this->duration_seconds = $this->ended_at->diffInSeconds($this->started_at);
        }
    }

    public static function getResultOptions(): array
    {
        return [
            'connected' => '接続済',
            'no_answer' => '応答なし',
            'busy' => '話中',
            'failed' => '失敗',
            'voicemail' => '留守電',
        ];
    }
}
