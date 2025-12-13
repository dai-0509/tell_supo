<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKpiTargetRequest;
use App\Http\Requests\UpdateKpiTargetRequest;
use App\Models\UserKpiTarget;
use App\Services\KpiCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * KPI目標管理コントローラー
 */
class KpiTargetController extends Controller
{
    // 認証ミドルウェアはルートで適用済み

    protected KpiCalculationService $kpiCalculationService;

    public function __construct(KpiCalculationService $kpiCalculationService)
    {
        $this->kpiCalculationService = $kpiCalculationService;
    }

    /**
     * KPI管理画面を表示する
     */
    public function index(): View
    {
        $user = Auth::user();

        // 現在有効なKPI目標を取得
        $activeTarget = $user->activeKpiTarget();

        // KPI目標履歴を取得（最新5件）
        $recentTargets = $user->kpiTargets()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('kpi-targets.index', compact('activeTarget', 'recentTargets'));
    }

    /**
     * KPI目標作成フォームを表示する（ウィザード形式）
     */
    public function create(): View
    {
        $user_id = Auth::id();

        // 過去実績を取得
        $historicalRates = $this->kpiCalculationService->calculateHistoricalRates($user_id, 3);
        $weekdayPerformance = $this->kpiCalculationService->analyzeWeekdayPerformance($user_id, 3);

        return view('kpi-targets.create', compact('historicalRates', 'weekdayPerformance'));
    }

    /**
     * アポ獲得目標から推奨架電数を計算（AJAX）
     */
    public function calculateRecommendedCalls(Request $request): JsonResponse
    {
        $monthly_appointment_target = $request->input('monthly_appointment_target');
        $user_id = Auth::id();

        // 過去実績を取得
        $historicalRates = $this->kpiCalculationService->calculateHistoricalRates($user_id, 3);

        // 推奨架電数を計算
        $calculation = $this->kpiCalculationService->calculateRequiredCalls(
            $monthly_appointment_target,
            $historicalRates['appointment_rate'],
            $historicalRates['success_rate']
        );

        return response()->json([
            'success' => true,
            'calculation' => $calculation,
            'historical_data' => $historicalRates,
        ]);
    }

    /**
     * 週次目標を曜日別に配分（AJAX）
     */
    public function distributeWeeklyTarget(Request $request): JsonResponse
    {
        $weekly_target = $request->input('weekly_target');
        $method = $request->input('method', 'auto'); // auto, manual, ai_suggested
        $user_id = Auth::id();

        switch ($method) {
            case 'ai_suggested':
                $distribution = $this->kpiCalculationService->suggestOptimalDistribution($user_id, $weekly_target);
                break;
            case 'auto':
            default:
                $distribution = $this->kpiCalculationService->distributeWeeklyTarget($weekly_target);
                break;
        }

        return response()->json([
            'success' => true,
            'distribution' => $distribution,
            'method' => $method,
        ]);
    }

    /**
     * 新しいKPI目標を保存する
     */
    public function store(StoreKpiTargetRequest $request): RedirectResponse
    {
        try {
            // 既存のアクティブな目標を無効化
            UserKpiTarget::forUser(Auth::id())
                ->active()
                ->update(['is_active' => false]);

            // 新しい目標を作成
            $kpiTarget = UserKpiTarget::create($request->validated());

            return redirect()
                ->route('kpi-targets.index')
                ->with('success', 'KPI目標を設定しました。');

        } catch (\Exception $e) {
            Log::error('KPI Target Store Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'KPI目標の設定に失敗しました。再度お試しください。');
        }
    }

    /**
     * KPI目標詳細を表示する
     */
    public function show(UserKpiTarget $kpiTarget): View
    {
        // 認可チェック：自分のKPI目標のみ閲覧可能
        if ($kpiTarget->user_id !== Auth::id()) {
            abort(403);
        }

        return view('kpi-targets.show', compact('kpiTarget'));
    }

    /**
     * KPI目標編集フォームを表示する
     */
    public function edit(UserKpiTarget $kpiTarget): View
    {
        // 認可チェック：自分のKPI目標のみ編集可能
        if ($kpiTarget->user_id !== Auth::id()) {
            abort(403);
        }

        return view('kpi-targets.edit', compact('kpiTarget'));
    }

    /**
     * KPI目標を更新する
     */
    public function update(UpdateKpiTargetRequest $request, UserKpiTarget $kpiTarget): RedirectResponse
    {
        try {
            $kpiTarget->update($request->validated());

            return redirect()
                ->route('kpi-targets.index')
                ->with('success', 'KPI目標を更新しました。');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'KPI目標の更新に失敗しました。再度お試しください。');
        }
    }

    /**
     * KPI目標を無効化する（論理削除）
     */
    public function destroy(UserKpiTarget $kpiTarget): RedirectResponse
    {
        // 認可チェック：自分のKPI目標のみ削除可能
        if ($kpiTarget->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            // 論理削除（is_activeをfalseに）
            $kpiTarget->update(['is_active' => false]);

            return redirect()
                ->route('kpi-targets.index')
                ->with('success', 'KPI目標を無効化しました。');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'KPI目標の無効化に失敗しました。再度お試しください。');
        }
    }

    /**
     * KPI目標をリセットする（すべて無効化）
     */
    public function reset(): RedirectResponse
    {
        try {
            UserKpiTarget::forUser(Auth::id())
                ->update(['is_active' => false]);

            return redirect()
                ->route('kpi-targets.index')
                ->with('success', 'すべてのKPI目標をリセットしました。新しい目標を設定してください。');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'KPI目標のリセットに失敗しました。再度お試しください。');
        }
    }
}
