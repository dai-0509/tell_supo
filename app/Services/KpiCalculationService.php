<?php

namespace App\Services;

use App\Models\CallLog;
use App\Models\User;
use App\Models\UserKpiTarget;
use Carbon\Carbon;

/**
 * KPI計算サービス
 * 
 * アポ獲得目標から逆算して適切な架電目標を提案し、
 * 過去の実績データを基に成功率を学習する
 */
class KpiCalculationService
{
    /**
     * デフォルト成功率（初回設定時）
     */
    const DEFAULT_SUCCESS_RATE = 35.0; // 業界平均通話成功率
    const DEFAULT_APPOINTMENT_RATE = 2.5; // 業界平均アポ獲得率

    /**
     * デフォルト曜日別配分比率（平日のみ）
     */
    const DEFAULT_WEEKDAY_RATIO = [
        'monday' => 0.20,    // 20%
        'tuesday' => 0.25,   // 25%
        'wednesday' => 0.25, // 25%
        'thursday' => 0.20,  // 20%
        'friday' => 0.10,    // 10%
        'saturday' => 0.0,   // 0%
        'sunday' => 0.0,     // 0%
    ];

    /**
     * アポ獲得目標から必要な架電数を計算
     *
     * @param int $monthlyAppointmentTarget 月次アポ獲得目標
     * @param float|null $appointmentRate アポ獲得率（過去実績またはデフォルト）
     * @param float|null $successRate 通話成功率（過去実績またはデフォルト）
     * @return array 計算結果
     */
    public function calculateRequiredCalls(
        int $monthly_appointment_target,
        ?float $appointment_rate = null,
        ?float $success_rate = null
    ): array {
        if ($appointment_rate === null) {
            $appointment_rate = self::DEFAULT_APPOINTMENT_RATE;
        }
        if ($success_rate === null) {
            $success_rate = self::DEFAULT_SUCCESS_RATE;
        }

        // 必要な成功通話数を計算
        $required_successful_calls = $monthly_appointment_target / ($appointment_rate / 100);
        
        // 必要な総架電数を計算
        $required_total_calls = $required_successful_calls / ($success_rate / 100);
        
        // 週次目標を計算（4週間想定）
        $weekly_target = $required_total_calls / 4;

        return [
            'monthly_call_target' => round($required_total_calls),
            'weekly_call_target' => round($weekly_target),
            'required_successful_calls' => round($required_successful_calls),
            'used_appointment_rate' => $appointment_rate,
            'used_success_rate' => $success_rate,
            'is_based_on_history' => $appointment_rate !== self::DEFAULT_APPOINTMENT_RATE || $success_rate !== self::DEFAULT_SUCCESS_RATE,
        ];
    }

    /**
     * 曜日別に架電目標を配分
     *
     * @param int $weeklyTarget 週次目標
     * @param array|null $customRatio カスタム配分比率
     * @return array 曜日別の目標
     */
    public function distributeWeeklyTarget(int $weekly_target, ?array $custom_ratio = null): array
    {
        if ($custom_ratio === null) {
            $ratio = self::DEFAULT_WEEKDAY_RATIO;
        } else {
            $ratio = $custom_ratio;
        }
        
        $distribution = [];
        foreach ($ratio as $day => $rate) {
            $distribution[$day . '_call_target'] = round($weekly_target * $rate);
        }

        // 端数調整（最も大きい曜日に加減）
        $total_distributed = array_sum($distribution);
        $difference = $weekly_target - $total_distributed;
        
        if ($difference !== 0) {
            $max_day = array_keys($distribution, max($distribution))[0];
            $distribution[$max_day] += $difference;
        }

        return $distribution;
    }

    /**
     * ユーザーの過去実績から成功率を計算
     *
     * @param int $userId ユーザーID
     * @param int $months 過去何ヶ月分のデータを使用するか
     * @return array 実績データ
     */
    public function calculateHistoricalRates(int $user_id, int $months = 3): array
    {
        $start_date = Carbon::now()->subMonths($months);
        
        $call_logs = CallLog::where('user_id', $user_id)
            ->where('started_at', '>=', $start_date)
            ->get();

        $total_calls = $call_logs->count();
        
        if ($total_calls === 0) {
            return [
                'success_rate' => null,
                'appointment_rate' => null,
                'total_calls' => 0,
                'successful_calls' => 0,
                'appointments' => 0,
                'data_period_months' => $months,
            ];
        }

        $successful_calls = $call_logs->where('result', '!=', '不通')->count();
        $appointments = $call_logs->where('result', 'アポ獲得')->count();

        $success_rate = ($successful_calls / $total_calls) * 100;
        $appointment_rate = $total_calls > 0 ? ($appointments / $total_calls) * 100 : 0;

        return [
            'success_rate' => round($success_rate, 2),
            'appointment_rate' => round($appointment_rate, 2),
            'total_calls' => $total_calls,
            'successful_calls' => $successful_calls,
            'appointments' => $appointments,
            'data_period_months' => $months,
        ];
    }

    /**
     * 曜日別の成功率を分析
     *
     * @param int $userId ユーザーID
     * @param int $months 過去何ヶ月分のデータを使用するか
     * @return array 曜日別分析結果
     */
    public function analyzeWeekdayPerformance(int $user_id, int $months = 3): array
    {
        $start_date = Carbon::now()->subMonths($months);
        
        $call_logs = CallLog::where('user_id', $user_id)
            ->where('started_at', '>=', $start_date)
            ->get();

        $weekday_stats = [];
        $day_names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        foreach ($day_names as $index => $day_name) {
            $day_logs = $call_logs->filter(function ($log) use ($index) {
                return Carbon::parse($log->started_at)->dayOfWeek === $index;
            });

            $total = $day_logs->count();
            $successful = $day_logs->where('result', '!=', '不通')->count();
            $appointments = $day_logs->where('result', 'アポ獲得')->count();

            $weekday_stats[strtolower($day_name)] = [
                'total_calls' => $total,
                'successful_calls' => $successful,
                'appointments' => $appointments,
                'success_rate' => $total > 0 ? round(($successful / $total) * 100, 2) : 0,
                'appointment_rate' => $total > 0 ? round(($appointments / $total) * 100, 2) : 0,
            ];
        }

        return $weekday_stats;
    }

    /**
     * AIによる推奨配分を計算
     *
     * @param int $userId ユーザーID
     * @param int $weeklyTarget 週次目標
     * @return array 推奨配分
     */
    public function suggestOptimalDistribution(int $user_id, int $weekly_target): array
    {
        $weekday_stats = $this->analyzeWeekdayPerformance($user_id);
        
        // 成功率の高い曜日により多く配分
        $total_success_rate = array_sum(array_column($weekday_stats, 'success_rate'));
        
        if ($total_success_rate === 0) {
            // データがない場合はデフォルト配分
            return $this->distributeWeeklyTarget($weekly_target);
        }

        $suggested_ratio = [];
        foreach ($weekday_stats as $day => $stats) {
            if (in_array($day, ['saturday', 'sunday'])) {
                $suggested_ratio[$day] = 0; // 土日は基本的に0
            } else {
                $suggested_ratio[$day] = $stats['success_rate'] / $total_success_rate;
            }
        }

        // 平日の比率を正規化
        $weekday_total = array_sum([
            $suggested_ratio['monday'],
            $suggested_ratio['tuesday'],
            $suggested_ratio['wednesday'],
            $suggested_ratio['thursday'],
            $suggested_ratio['friday'],
        ]);

        if ($weekday_total > 0) {
            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
                $suggested_ratio[$day] = $suggested_ratio[$day] / $weekday_total;
            }
        } else {
            // フォールバック
            return $this->distributeWeeklyTarget($weekly_target);
        }

        return $this->distributeWeeklyTarget($weekly_target, $suggested_ratio);
    }

    /**
     * 目標設定の妥当性をチェック
     *
     * @param array $targets 設定された目標
     * @return array 検証結果
     */
    public function validateTargets(array $targets): array
    {
        $issues = [];
        $warnings = [];

        // 週次目標と曜日合計の整合性チェック
        $weekday_sum_monday = isset($targets['monday_call_target']) ? $targets['monday_call_target'] : 0;
        $weekday_sum_tuesday = isset($targets['tuesday_call_target']) ? $targets['tuesday_call_target'] : 0;
        $weekday_sum_wednesday = isset($targets['wednesday_call_target']) ? $targets['wednesday_call_target'] : 0;
        $weekday_sum_thursday = isset($targets['thursday_call_target']) ? $targets['thursday_call_target'] : 0;
        $weekday_sum_friday = isset($targets['friday_call_target']) ? $targets['friday_call_target'] : 0;
        $weekday_sum_saturday = isset($targets['saturday_call_target']) ? $targets['saturday_call_target'] : 0;
        $weekday_sum_sunday = isset($targets['sunday_call_target']) ? $targets['sunday_call_target'] : 0;
        
        $weekday_sum = $weekday_sum_monday + $weekday_sum_tuesday + $weekday_sum_wednesday + 
                       $weekday_sum_thursday + $weekday_sum_friday + $weekday_sum_saturday + $weekday_sum_sunday;

        if ($weekday_sum !== $targets['weekly_call_target']) {
            $issues[] = '曜日別目標の合計が週次目標と一致しません。';
        }

        // 月次目標と週次目標の整合性チェック
        if (($targets['weekly_call_target'] * 4) !== $targets['monthly_call_target']) {
            $warnings[] = '月次目標が週次目標の4倍と異なります。第5週がある月を考慮していますか？';
        }

        // 極端な値のチェック
        foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day) {
            $day_target = isset($targets[$day . '_call_target']) ? $targets[$day . '_call_target'] : 0;
            if ($day_target > 200) {
                $warnings[] = "{$day}の目標が非常に高く設定されています（{$day_target}件）。";
            }
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'warnings' => $warnings,
        ];
    }
}