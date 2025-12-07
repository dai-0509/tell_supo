<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('KPI目標詳細') }}
            </h2>
            <div class="flex space-x-3">
                @if($kpiTarget->is_active)
                    <a href="{{ route('kpi-targets.edit', $kpiTarget) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        編集
                    </a>
                    <form method="POST" action="{{ route('kpi-targets.destroy', $kpiTarget) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                onclick="return confirm('この目標を無効化しますか？')">
                            無効化
                        </button>
                    </form>
                @endif
                <a href="{{ route('kpi-targets.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    戻る
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- ステータス情報 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">目標ステータス</h3>
                        @if($kpiTarget->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                有効
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                無効
                            </span>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-800 mb-1">設定日</h4>
                            <p class="text-lg font-semibold text-blue-600">
                                {{ $kpiTarget->created_at->format('Y年m月d日') }}
                            </p>
                            <p class="text-sm text-blue-600">{{ $kpiTarget->created_at->format('H:i') }}</p>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-green-800 mb-1">有効開始日</h4>
                            <p class="text-lg font-semibold text-green-600">
                                {{ \Carbon\Carbon::parse($kpiTarget->effective_from)->format('Y年m月d日') }}
                            </p>
                        </div>
                        
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-purple-800 mb-1">有効終了日</h4>
                            <p class="text-lg font-semibold text-purple-600">
                                @if($kpiTarget->effective_until)
                                    {{ \Carbon\Carbon::parse($kpiTarget->effective_until)->format('Y年m月d日') }}
                                @else
                                    無期限
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 架電目標 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-phone text-blue-600 mr-2"></i>
                        架電目標
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="bg-blue-50 p-6 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">日次目標</h4>
                                <p class="text-3xl font-bold text-blue-600">{{ $kpiTarget->daily_call_target }}</p>
                                <p class="text-sm text-blue-600 mt-1">件 / 日</p>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-green-50 p-6 rounded-lg">
                                <h4 class="text-sm font-medium text-green-800 mb-2">週次目標</h4>
                                <p class="text-3xl font-bold text-green-600">{{ $kpiTarget->weekly_call_target }}</p>
                                <p class="text-sm text-green-600 mt-1">件 / 週</p>
                                <div class="mt-2 text-xs text-green-700">
                                    日次目標の{{ round($kpiTarget->weekly_call_target / $kpiTarget->daily_call_target, 1) }}倍
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="bg-purple-50 p-6 rounded-lg">
                                <h4 class="text-sm font-medium text-purple-800 mb-2">月次目標</h4>
                                <p class="text-3xl font-bold text-purple-600">{{ $kpiTarget->monthly_call_target }}</p>
                                <p class="text-sm text-purple-600 mt-1">件 / 月</p>
                                <div class="mt-2 text-xs text-purple-700">
                                    週次目標の{{ round($kpiTarget->monthly_call_target / $kpiTarget->weekly_call_target, 1) }}倍
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- アポ獲得目標 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-handshake text-green-600 mr-2"></i>
                        アポ獲得目標
                    </h3>
                    
                    <div class="text-center max-w-md mx-auto">
                        <div class="bg-green-50 p-8 rounded-lg">
                            <h4 class="text-sm font-medium text-green-800 mb-2">月次アポ獲得目標</h4>
                            <p class="text-4xl font-bold text-green-600">{{ $kpiTarget->monthly_appointment_target }}</p>
                            <p class="text-sm text-green-600 mt-2">件 / 月</p>
                            
                            @if($kpiTarget->monthly_call_target > 0)
                                <div class="mt-4 text-sm text-green-700 bg-green-100 p-2 rounded">
                                    架電数に対する期待アポ率: 
                                    <strong>{{ round(($kpiTarget->monthly_appointment_target / $kpiTarget->monthly_call_target) * 100, 2) }}%</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- 成功率目標 --}}
            @if($kpiTarget->target_success_rate || $kpiTarget->target_appointment_rate)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-chart-line text-yellow-600 mr-2"></i>
                            成功率目標
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($kpiTarget->target_success_rate)
                                <div class="text-center">
                                    <div class="bg-yellow-50 p-6 rounded-lg">
                                        <h4 class="text-sm font-medium text-yellow-800 mb-2">目標通話成功率</h4>
                                        <p class="text-3xl font-bold text-yellow-600">{{ $kpiTarget->target_success_rate }}%</p>
                                        <p class="text-sm text-yellow-600 mt-1">成功通話 / 総架電数</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if($kpiTarget->target_appointment_rate)
                                <div class="text-center">
                                    <div class="bg-orange-50 p-6 rounded-lg">
                                        <h4 class="text-sm font-medium text-orange-800 mb-2">目標アポ獲得率</h4>
                                        <p class="text-3xl font-bold text-orange-600">{{ $kpiTarget->target_appointment_rate }}%</p>
                                        <p class="text-sm text-orange-600 mt-1">アポ獲得 / 成功通話数</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- 目標の整合性チェック --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clipboard-check text-indigo-600 mr-2"></i>
                        目標整合性チェック
                    </h3>
                    
                    @if($kpiTarget->isConsistent())
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-green-800 font-medium">目標設定は整合性が取れています</span>
                            </div>
                            <div class="mt-2 text-sm text-green-700">
                                週次目標が日次目標の5倍以上、月次目標が週次目標の4倍以上に設定されています。
                            </div>
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                <span class="text-red-800 font-medium">目標設定に整合性の問題があります</span>
                            </div>
                            <div class="mt-2 text-sm text-red-700">
                                週次目標または月次目標の設定を見直すことをお勧めします。
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 統計情報 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-calculator text-gray-600 mr-2"></i>
                        目標統計
                    </h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div class="bg-gray-50 p-4 rounded">
                            <h4 class="text-xs font-medium text-gray-600 mb-1">1時間あたり目標</h4>
                            <p class="text-lg font-bold text-gray-800">
                                {{ round($kpiTarget->daily_call_target / 8, 1) }}件
                            </p>
                            <p class="text-xs text-gray-600">(8時間労働想定)</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <h4 class="text-xs font-medium text-gray-600 mb-1">平日1日あたり</h4>
                            <p class="text-lg font-bold text-gray-800">
                                {{ round($kpiTarget->weekly_call_target / 5, 1) }}件
                            </p>
                            <p class="text-xs text-gray-600">(週次目標ベース)</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <h4 class="text-xs font-medium text-gray-600 mb-1">営業日あたり</h4>
                            <p class="text-lg font-bold text-gray-800">
                                {{ round($kpiTarget->monthly_call_target / 20, 1) }}件
                            </p>
                            <p class="text-xs text-gray-600">(20営業日想定)</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <h4 class="text-xs font-medium text-gray-600 mb-1">日次アポ期待値</h4>
                            <p class="text-lg font-bold text-gray-800">
                                {{ round($kpiTarget->monthly_appointment_target / 20, 1) }}件
                            </p>
                            <p class="text-xs text-gray-600">(20営業日想定)</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>