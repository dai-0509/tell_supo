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
 * @property int $monday_call_target 月曜日の架電目標
 * @property int $tuesday_call_target 火曜日の架電目標
 * @property int $wednesday_call_target 水曜日の架電目標
 * @property int $thursday_call_target 木曜日の架電目標
 * @property int $friday_call_target 金曜日の架電目標
 * @property int $saturday_call_target 土曜日の架電目標
 * @property int $sunday_call_target 日曜日の架電目標
 * @property int $weekly_call_target 週次目標架電数
 * @property int $monthly_call_target 月次目標架電数
 * @property int $monthly_appointment_target 月次目標アポ獲得数
 * @property float|null $target_success_rate 目標通話成功率（%）
 * @property float|null $target_appointment_rate 目標アポ獲得率（%）
 * @property float|null $historical_success_rate 過去の平均通話成功率（%）
 * @property float|null $historical_appointment_rate 過去の平均アポ獲得率（%）
 * @property int|null $recommended_monthly_calls 推奨月次架電数
 * @property int|null $recommended_weekly_calls 推奨週次架電数
 * @property string $setting_method 設定方法
 * @property array|null $weekday_distribution_ratio 曜日別配分比率
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
        'monday_call_target',
        'tuesday_call_target',
        'wednesday_call_target',
        'thursday_call_target',
        'friday_call_target',
        'saturday_call_target',
        'sunday_call_target',
        'weekly_call_target',
        'monthly_call_target',
        'monthly_appointment_target',
        'target_success_rate',
        'target_appointment_rate',
        'historical_success_rate',
        'historical_appointment_rate',
        'recommended_monthly_calls',
        'recommended_weekly_calls',
        'setting_method',
        'weekday_distribution_ratio',
        'effective_from',
        'effective_until',
        'is_active',
    ];

    /**
     * 属性のキャスト
     */
    protected $casts = [
        'monday_call_target' => 'integer',
        'tuesday_call_target' => 'integer',
        'wednesday_call_target' => 'integer',
        'thursday_call_target' => 'integer',
        'friday_call_target' => 'integer',
        'saturday_call_target' => 'integer',
        'sunday_call_target' => 'integer',
        'weekly_call_target' => 'integer',
        'monthly_call_target' => 'integer',
        'monthly_appointment_target' => 'integer',
        'target_success_rate' => 'decimal:2',
        'target_appointment_rate' => 'decimal:2',
        'historical_success_rate' => 'decimal:2',
        'historical_appointment_rate' => 'decimal:2',
        'recommended_monthly_calls' => 'integer',
        'recommended_weekly_calls' => 'integer',
        'weekday_distribution_ratio' => 'array',
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
        // 曜日別目標の合計が週次目標と一致するかチェック
        $weekdaySum = $this->getWeekdaySum();
        if ($weekdaySum !== $this->weekly_call_target) {
            return false;
        }

        // 月次目標 >= 週次目標 × 4
        if ($this->monthly_call_target < ($this->weekly_call_target * 4)) {
            return false;
        }

        return true;
    }

    /**
     * 曜日別目標の合計を取得（全曜日）
     */
    public function getWeekdaySum(): int
    {
        return $this->monday_call_target +
               $this->tuesday_call_target +
               $this->wednesday_call_target +
               $this->thursday_call_target +
               $this->friday_call_target +
               $this->saturday_call_target +
               $this->sunday_call_target;
    }

    /**
     * 平日のみの目標合計を取得
     */
    public function getWeekdaysOnlySum(): int
    {
        return $this->monday_call_target +
               $this->tuesday_call_target +
               $this->wednesday_call_target +
               $this->thursday_call_target +
               $this->friday_call_target;
    }

    /**
     * 特定の曜日の目標を取得
     */
    public function getTargetForDay(int $dayOfWeek): int
    {
        $targets = [
            0 => $this->sunday_call_target,     // 日曜日
            1 => $this->monday_call_target,     // 月曜日
            2 => $this->tuesday_call_target,    // 火曜日
            3 => $this->wednesday_call_target,  // 水曜日
            4 => $this->thursday_call_target,   // 木曜日
            5 => $this->friday_call_target,     // 金曜日
            6 => $this->saturday_call_target,   // 土曜日
        ];

        return $targets[$dayOfWeek] ?? 0;
    }

    /**
     * 推奨値が使用されているかチェック
     */
    public function isBasedOnHistoricalData(): bool
    {
        return !is_null($this->historical_success_rate) && 
               !is_null($this->historical_appointment_rate);
    }

    /**
     * 各曜日の目標配分比率を計算
     */
    public function getDistributionRatio(): array
    {
        $total = $this->getWeekdaySum();
        
        if ($total === 0) {
            return [
                'monday' => 0,
                'tuesday' => 0,
                'wednesday' => 0,
                'thursday' => 0,
                'friday' => 0,
                'saturday' => 0,
                'sunday' => 0,
            ];
        }

        return [
            'monday' => round($this->monday_call_target / $total, 3),
            'tuesday' => round($this->tuesday_call_target / $total, 3),
            'wednesday' => round($this->wednesday_call_target / $total, 3),
            'thursday' => round($this->thursday_call_target / $total, 3),
            'friday' => round($this->friday_call_target / $total, 3),
            'saturday' => round($this->saturday_call_target / $total, 3),
            'sunday' => round($this->sunday_call_target / $total, 3),
        ];
    }
}
