<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ユーザーKPI目標設定モデル
 * 
 * @property int $kpi_target_id KPI目標ID
 * @property int $user_id ユーザーID
 * @property int $daily_call_target 日次目標架電数
 * @property int $weekly_call_target 週次目標架電数
 * @property int $monthly_call_target 月次目標架電数
 * @property int $monthly_appointment_target 月次目標アポ獲得数
 * @property float|null $target_success_rate 目標通話成功率（%）
 * @property float|null $target_appointment_rate 目標アポ獲得率（%）
 * @property string $effective_from 有効開始日
 * @property string|null $effective_until 有効終了日
 * @property bool $is_active アクティブフラグ
 */
class UserKpiTarget extends Model
{
    use HasFactory;

    /**
     * テーブル名
     */
    protected $table = 'user_kpi_targets';

    /**
     * カスタム主キー設定
     */
    protected $primaryKey = 'kpi_target_id';

    /**
     * 主キーの型
     */
    protected $keyType = 'int';

    /**
     * 主キーの自動インクリメント
     */
    public $incrementing = true;

    /**
     * 一括代入可能な属性
     */
    protected $fillable = [
        'user_id',
        'daily_call_target',
        'weekly_call_target',
        'monthly_call_target',
        'monthly_appointment_target',
        'target_success_rate',
        'target_appointment_rate',
        'effective_from',
        'effective_until',
        'is_active',
    ];

    /**
     * 属性のキャスト
     */
    protected $casts = [
        'daily_call_target' => 'integer',
        'weekly_call_target' => 'integer',
        'monthly_call_target' => 'integer',
        'monthly_appointment_target' => 'integer',
        'target_success_rate' => 'decimal:2',
        'target_appointment_rate' => 'decimal:2',
        'effective_from' => 'date',
        'effective_until' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * ユーザーとのリレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 現在有効な目標を取得するスコープ
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('effective_from', '<=', now()->toDateString())
                    ->where(function ($q) {
                        $q->whereNull('effective_until')
                          ->orWhere('effective_until', '>=', now()->toDateString());
                    });
    }

    /**
     * 特定ユーザーの目標を取得するスコープ
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 期間で絞り込むスコープ
     */
    public function scopeBetweenDates($query, string $startDate, string $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->where(function ($subQuery) use ($startDate, $endDate) {
                // 開始日が期間内
                $subQuery->whereBetween('effective_from', [$startDate, $endDate]);
            })->orWhere(function ($subQuery) use ($startDate, $endDate) {
                // 終了日が期間内
                $subQuery->whereBetween('effective_until', [$startDate, $endDate]);
            })->orWhere(function ($subQuery) use ($startDate, $endDate) {
                // 期間を跨いでいる
                $subQuery->where('effective_from', '<=', $startDate)
                        ->where(function ($q) use ($endDate) {
                            $q->whereNull('effective_until')
                              ->orWhere('effective_until', '>=', $endDate);
                        });
            });
        });
    }

    /**
     * 目標の整合性をチェック
     */
    public function isConsistent(): bool
    {
        // 週次目標 >= 日次目標 × 5（平日想定）
        if ($this->weekly_call_target < ($this->daily_call_target * 5)) {
            return false;
        }

        // 月次目標 >= 週次目標 × 4
        if ($this->monthly_call_target < ($this->weekly_call_target * 4)) {
            return false;
        }

        return true;
    }
}
