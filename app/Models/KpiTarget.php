<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiTarget extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'target_type',
        'target_date',
        'call_target',
        'appointment_target',
    ];

    protected $casts = [
        'target_date' => 'date',
        'call_target' => 'integer',
        'appointment_target' => 'integer',
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
     * 週次目標のスコープ
     */
    public function scopeWeekly($query)
    {
        return $query->where('target_type', 'weekly');
    }

    /**
     * 月次目標のスコープ
     */
    public function scopeMonthly($query)
    {
        return $query->where('target_type', 'monthly');
    }

    /**
     * 指定期間の目標を取得
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('target_date', [$startDate, $endDate]);
    }

    /**
     * 現在の週の目標を取得
     */
    public function scopeCurrentWeek($query)
    {
        $startOfWeek = now()->startOfWeek();
        return $query->where('target_type', 'weekly')
                     ->where('target_date', $startOfWeek->format('Y-m-d'));
    }

    /**
     * 現在の月の目標を取得
     */
    public function scopeCurrentMonth($query)
    {
        $startOfMonth = now()->startOfMonth();
        return $query->where('target_type', 'monthly')
                     ->where('target_date', $startOfMonth->format('Y-m-d'));
    }

    /**
     * 架電目標達成率を計算
     */
    public function getCallAchievementRateAttribute()
    {
        if ($this->call_target <= 0) {
            return 0;
        }

        $actualCalls = $this->getActualCallsCount();
        return min(round(($actualCalls / $this->call_target) * 100, 1), 100);
    }

    /**
     * アポ目標達成率を計算
     */
    public function getAppointmentAchievementRateAttribute()
    {
        if ($this->appointment_target <= 0) {
            return 0;
        }

        $actualAppointments = $this->getActualAppointmentsCount();
        return min(round(($actualAppointments / $this->appointment_target) * 100, 1), 100);
    }

    /**
     * 実際の架電数を取得
     */
    public function getActualCallsCount()
    {
        if ($this->target_type === 'weekly') {
            $startDate = $this->target_date;
            $endDate = $this->target_date->addDays(6);
        } else {
            $startDate = $this->target_date;
            $endDate = $this->target_date->endOfMonth();
        }

        return \App\Models\CallLog::where('user_id', $this->user_id)
                                  ->whereBetween('called_at', [$startDate, $endDate])
                                  ->count();
    }

    /**
     * 実際のアポ数を取得
     */
    public function getActualAppointmentsCount()
    {
        if ($this->target_type === 'weekly') {
            $startDate = $this->target_date;
            $endDate = $this->target_date->addDays(6);
        } else {
            $startDate = $this->target_date;
            $endDate = $this->target_date->endOfMonth();
        }

        return \App\Models\CallLog::where('user_id', $this->user_id)
                                  ->where('result', 'appointment')
                                  ->whereBetween('called_at', [$startDate, $endDate])
                                  ->count();
    }
}