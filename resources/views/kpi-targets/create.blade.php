<x-app-layout>
<div class="max-w-4xl mx-auto px-4 py-6">
    <!-- ヘッダー -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">🎯 KPI目標設定ウィザード</h1>
        <p class="text-gray-600">アポ獲得目標から最適な架電数を計算し、曜日別に配分します</p>
    </div>

    <!-- プログレス表示 -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div data-step="1" class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white text-sm font-medium">1</div>
                <div class="hidden sm:block text-sm text-gray-500">アポ獲得目標</div>
            </div>
            <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
            <div class="flex items-center space-x-2">
                <div data-step="2" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 text-sm font-medium">2</div>
                <div class="hidden sm:block text-sm text-gray-500">架電数計算</div>
            </div>
            <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
            <div class="flex items-center space-x-2">
                <div data-step="3" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 text-sm font-medium">3</div>
                <div class="hidden sm:block text-sm text-gray-500">曜日別配分</div>
            </div>
            <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
            <div class="flex items-center space-x-2">
                <div data-step="4" class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-200 text-gray-500 text-sm font-medium">4</div>
                <div class="hidden sm:block text-sm text-gray-500">確認・保存</div>
            </div>
        </div>
    </div>

    <!-- フォーム開始 -->
    <form method="POST" action="{{ route('kpi-targets.store') }}" class="space-y-8">
        @csrf

        <!-- Step 1: 月次アポ獲得目標設定 -->
        <div id="step-1" class="bg-white rounded-lg shadow-sm border p-8">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">📅 月次アポ獲得目標を設定</h2>

                <!-- 過去実績表示 (あれば) -->
                @if(!empty($historicalRates))
                <div class="mb-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h3 class="font-semibold text-blue-900 mb-3">📊 過去3ヶ月の実績</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600">{{ number_format($historicalRates['total_calls'] ?? 0) }}</div>
                            <div class="text-gray-600">総架電数</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-green-600">{{ number_format($historicalRates['success_rate'] ?? 0, 1) }}%</div>
                            <div class="text-gray-600">通話成功率</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-purple-600">{{ number_format($historicalRates['appointment_rate'] ?? 0, 1) }}%</div>
                            <div class="text-gray-600">アポ獲得率</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- 目標入力 -->
                <div class="mb-8">
                    <label for="monthly_appointment_target" class="block text-sm font-medium text-gray-700 mb-3">今月の目標アポ獲得数</label>
                    <div class="flex items-center justify-center">
                        <input type="number"
                               id="monthly_appointment_target"
                               name="monthly_appointment_target"
                               class="text-3xl font-bold text-center w-40 px-4 py-3 border-2 border-blue-300 rounded-lg focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-500"
                               placeholder="20"
                               min="1"
                               max="1000"
                               required>
                        <span class="text-2xl font-bold text-gray-700 ml-3">件</span>
                    </div>
                    <p class="mt-3 text-sm text-gray-500">※ 1〜1000件の範囲で入力してください</p>
                </div>

                <!-- 次へボタン -->
                <div class="flex justify-center">
                    <button type="button"
                            id="calculate-calls"
                            class="px-8 py-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        📊 必要架電数を計算する
                    </button>
                </div>

                <!-- ローディング表示 -->
                <div id="calculation-loading" class="hidden mt-4 text-center">
                    <div class="inline-flex items-center px-4 py-2 font-medium text-blue-600">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        計算中...
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: 計算結果表示・調整 -->
        <div id="step-2" class="hidden bg-white rounded-lg shadow-sm border p-8">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">📊 推奨架電数計算結果</h2>

                <!-- 計算結果表示エリア -->
                <div id="calculation-results" class="hidden">
                    <!-- JavaScript で動的に生成 -->
                </div>

                <!-- ナビゲーション -->
                <div class="flex justify-between mt-8">
                    <button type="button"
                            data-prev-step="1"
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200">
                        ← 戻る
                    </button>
                    <button type="button"
                            data-next-step="3"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        曜日別配分へ →
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: 曜日別配分設定 -->
        <div id="step-3" class="hidden bg-white rounded-lg shadow-sm border p-8">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">📅 曜日別目標配分</h2>

                <!-- 週次目標表示 -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-center">
                    <span class="text-lg text-blue-800">週次目標: </span>
                    <span id="weekly-target-display" class="text-2xl font-bold text-blue-600">0</span>
                    <span class="text-lg text-blue-800">件</span>
                </div>

                <!-- 自動配分ボタン -->
                <div class="flex justify-center space-x-4 mb-8">
                    <button type="button"
                            id="auto-distribute"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                        ⚖️ 自動配分（均等）
                    </button>
                    @if(!empty($weekdayPerformance))
                    <button type="button"
                            id="ai-distribute"
                            class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200">
                        🤖 AI推奨配分
                    </button>
                    @endif
                </div>

                <!-- ローディング表示 -->
                <div id="distribution-loading" class="hidden text-center mb-4">
                    <div class="inline-flex items-center px-4 py-2 font-medium text-blue-600">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        配分計算中...
                    </div>
                </div>

                <!-- 曜日別入力 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- 平日 -->
                    <div class="space-y-4">
                        <h3 class="font-semibold text-gray-800 border-b pb-2">平日</h3>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label for="monday_call_target" class="w-16 text-sm font-medium text-gray-700">月曜日</label>
                                <div class="flex items-center">
                                    <input type="number" id="monday_call_target" name="monday_call_target"
                                           class="w-20 px-3 py-2 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           min="0" max="300" value="0">
                                    <span class="ml-2 text-sm text-gray-500">件</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="tuesday_call_target" class="w-16 text-sm font-medium text-gray-700">火曜日</label>
                                <div class="flex items-center">
                                    <input type="number" id="tuesday_call_target" name="tuesday_call_target"
                                           class="w-20 px-3 py-2 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           min="0" max="300" value="0">
                                    <span class="ml-2 text-sm text-gray-500">件</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="wednesday_call_target" class="w-16 text-sm font-medium text-gray-700">水曜日</label>
                                <div class="flex items-center">
                                    <input type="number" id="wednesday_call_target" name="wednesday_call_target"
                                           class="w-20 px-3 py-2 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           min="0" max="300" value="0">
                                    <span class="ml-2 text-sm text-gray-500">件</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="thursday_call_target" class="w-16 text-sm font-medium text-gray-700">木曜日</label>
                                <div class="flex items-center">
                                    <input type="number" id="thursday_call_target" name="thursday_call_target"
                                           class="w-20 px-3 py-2 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           min="0" max="300" value="0">
                                    <span class="ml-2 text-sm text-gray-500">件</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="friday_call_target" class="w-16 text-sm font-medium text-gray-700">金曜日</label>
                                <div class="flex items-center">
                                    <input type="number" id="friday_call_target" name="friday_call_target"
                                           class="w-20 px-3 py-2 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           min="0" max="300" value="0">
                                    <span class="ml-2 text-sm text-gray-500">件</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 週末 -->
                    <div class="space-y-4">
                        <h3 class="font-semibold text-gray-800 border-b pb-2">週末（任意）</h3>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label for="saturday_call_target" class="w-16 text-sm font-medium text-gray-700">土曜日</label>
                                <div class="flex items-center">
                                    <input type="number" id="saturday_call_target" name="saturday_call_target"
                                           class="w-20 px-3 py-2 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           min="0" max="200" value="0">
                                    <span class="ml-2 text-sm text-gray-500">件</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="sunday_call_target" class="w-16 text-sm font-medium text-gray-700">日曜日</label>
                                <div class="flex items-center">
                                    <input type="number" id="sunday_call_target" name="sunday_call_target"
                                           class="w-20 px-3 py-2 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           min="0" max="200" value="0">
                                    <span class="ml-2 text-sm text-gray-500">件</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 合計表示 -->
                <div class="bg-gray-50 border rounded-lg p-4 mb-6 text-center">
                    <span class="text-lg text-gray-800">曜日別合計: </span>
                    <span id="weekday-total" class="text-2xl font-bold text-red-600">0</span>
                    <span class="text-lg text-gray-800">件</span>
                    <div class="text-sm text-gray-500 mt-1">※ 週次目標と一致させてください</div>
                </div>

                <!-- ナビゲーション -->
                <div class="flex justify-between">
                    <button type="button"
                            data-prev-step="2"
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200">
                        ← 戻る
                    </button>
                    <button type="button"
                            data-next-step="4"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        確認画面へ →
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 4: 確認・保存 -->
        <div id="step-4" class="hidden bg-white rounded-lg shadow-sm border p-8">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">✅ 設定内容の確認</h2>

                <!-- サマリー表示エリア -->
                <div id="summary-content">
                    <!-- JavaScript で動的に生成 -->
                </div>

                <!-- 隠しフィールド（計算結果） -->
                <input type="hidden" id="weekly_call_target" name="weekly_call_target" value="">
                <input type="hidden" id="monthly_call_target" name="monthly_call_target" value="">
                <input type="hidden" name="setting_method" value="ai_suggested">
                <input type="hidden" name="effective_from" value="{{ now()->format('Y-m-d') }}">

                <!-- ナビゲーション・保存 -->
                <div class="flex justify-between mt-8">
                    <button type="button"
                            data-prev-step="3"
                            class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-200">
                        ← 戻る
                    </button>
                    <button type="submit"
                            class="px-8 py-4 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-200">
                        💾 KPI目標を保存
                    </button>
                </div>
            </div>
        </div>

    </form>
</div>

<!-- API URL用メタタグ -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="calculate-calls-url" content="{{ route('kpi-targets.calculate-calls') }}">
<meta name="distribute-weekly-url" content="{{ route('kpi-targets.distribute-weekly') }}">

<!-- KPI設定ウィザード専用JavaScript -->
@vite(['resources/js/kpi-wizard.js'])
</x-app-layout>
