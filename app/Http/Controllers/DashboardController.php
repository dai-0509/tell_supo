<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CallLog;
use App\Models\KpiTarget;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * ダッシュボード表示
     */
    public function index()
    {
        $user = Auth::user();
        
        // KPIデータを取得
        $kpiData = $this->getKpiData($user->id);
        
        return view('dashboard', compact('kpiData'));
    }

    /**
     * KPIデータを取得
     */
    private function getKpiData($userId)
    {
        return [
            'todayCallsCount' => $this->getTodayCallsCount($userId),
            'weeklyTarget' => $this->getCurrentWeeklyTarget($userId),
            'weeklyProgress' => $this->getWeeklyProgress($userId),
            'callSuccessRate' => $this->getCallSuccessRate($userId),
            'appointmentsCount' => $this->getAppointmentsCount($userId),
            'averageCallTime' => $this->getAverageCallTime($userId),
            'weeklyCallsData' => $this->getWeeklyCallsData($userId),
            'todayRecentCalls' => $this->getTodayRecentCalls($userId),
        ];
    }

    /**
     * 今日の架電数を取得
     */
    public function getTodayCallsCount($userId)
    {
        return CallLog::where('user_id', $userId)
                     ->today()
                     ->count();
    }

    /**
     * 現在の週次目標を取得
     */
    public function getCurrentWeeklyTarget($userId)
    {
        return KpiTarget::where('user_id', $userId)
                       ->currentWeek()
                       ->first();
    }

    /**
     * 週次進捗率を計算
     */
    private function getWeeklyProgress($userId)
    {
        $target = $this->getCurrentWeeklyTarget($userId);
        if (!$target) {
            return 0;
        }

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        
        $actualCalls = CallLog::where('user_id', $userId)
                             ->whereBetween('called_at', [$weekStart, $weekEnd])
                             ->count();

        return $target->call_target > 0 
            ? min(round(($actualCalls / $target->call_target) * 100, 1), 100)
            : 0;
    }

    /**
     * 架電成功率を計算
     */
    private function getCallSuccessRate($userId)
    {
        $totalCalls = CallLog::where('user_id', $userId)
                            ->thisWeek()
                            ->count();

        if ($totalCalls === 0) {
            return 0;
        }

        $successfulCalls = CallLog::where('user_id', $userId)
                                 ->thisWeek()
                                 ->whereIn('result', ['success', 'appointment', 'interested'])
                                 ->count();

        return round(($successfulCalls / $totalCalls) * 100, 1);
    }

    /**
     * 今日のアポ獲得数を取得
     */
    private function getAppointmentsCount($userId)
    {
        return CallLog::where('user_id', $userId)
                     ->today()
                     ->appointment()
                     ->count();
    }

    /**
     * 平均架電時間を計算（モック）
     */
    private function getAverageCallTime($userId)
    {
        // 実際の実装では通話時間データが必要
        // 今回はモックデータを返す
        return 3.2;
    }

    /**
     * 週次架電データを取得
     */
    private function getWeeklyCallsData($userId)
    {
        $weekStart = now()->startOfWeek();
        $data = [];
        
        for ($i = 0; $i < 5; $i++) { // 平日のみ
            $date = $weekStart->copy()->addDays($i);
            $count = CallLog::where('user_id', $userId)
                           ->whereDate('called_at', $date)
                           ->count();
            $data[] = $count;
        }
        
        return $data;
    }

    /**
     * 今日の最新架電履歴を取得
     */
    private function getTodayRecentCalls($userId)
    {
        return CallLog::with('customer')
                     ->where('user_id', $userId)
                     ->today()
                     ->orderBy('called_at', 'desc')
                     ->limit(5)
                     ->get();
    }

    /**
     * 架電数をインクリメント
     */
    public function incrementCall(Request $request)
    {
        $user = Auth::user();
        
        // 新しい架電記録を作成（簡易版）
        $callLog = CallLog::create([
            'user_id' => $user->id,
            'customer_id' => $request->input('customer_id'), // 架電メーターからはnull
            'called_at' => now(),
            'result' => 'success', // とりあえず成功として記録
            'notes' => '架電メーターから追加',
        ]);

        $todayCount = $this->getTodayCallsCount($user->id);
        $target = $this->getCurrentWeeklyTarget($user->id);
        $dailyTarget = $target ? round($target->call_target / 5) : 50; // 週目標を5日で割る

        return response()->json([
            'success' => true,
            'todayCount' => $todayCount,
            'dailyTarget' => $dailyTarget,
            'achievementRate' => $dailyTarget > 0 ? round(($todayCount / $dailyTarget) * 100, 1) : 0,
            'message' => '架電数を更新しました',
        ]);
    }

    /**
     * 架電数をデクリメント
     */
    public function decrementCall(Request $request)
    {
        $user = Auth::user();
        
        // 最新の架電記録を削除
        $latestCall = CallLog::where('user_id', $user->id)
                            ->today()
                            ->orderBy('created_at', 'desc')
                            ->first();

        if ($latestCall) {
            $latestCall->delete();
        }

        $todayCount = $this->getTodayCallsCount($user->id);
        $target = $this->getCurrentWeeklyTarget($user->id);
        $dailyTarget = $target ? round($target->call_target / 5) : 50;

        return response()->json([
            'success' => true,
            'todayCount' => $todayCount,
            'dailyTarget' => $dailyTarget,
            'achievementRate' => $dailyTarget > 0 ? round(($todayCount / $dailyTarget) * 100, 1) : 0,
            'message' => '架電数を修正しました',
        ]);
    }
}