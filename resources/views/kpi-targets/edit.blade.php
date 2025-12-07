<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('KPI目標編集') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('kpi-targets.show', $kpiTarget) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    詳細表示
                </a>
                <a href="{{ route('kpi-targets.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    一覧に戻る
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    {{-- 現在の目標表示 --}}
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">現在の設定</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">日次:</span> 
                                <span class="font-medium">{{ $kpiTarget->daily_call_target }}件</span>
                            </div>
                            <div>
                                <span class="text-gray-600">週次:</span> 
                                <span class="font-medium">{{ $kpiTarget->weekly_call_target }}件</span>
                            </div>
                            <div>
                                <span class="text-gray-600">月次:</span> 
                                <span class="font-medium">{{ $kpiTarget->monthly_call_target }}件</span>
                            </div>
                            <div>
                                <span class="text-gray-600">アポ:</span> 
                                <span class="font-medium">{{ $kpiTarget->monthly_appointment_target }}件</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('kpi-targets.update', $kpiTarget) }}" class="space-y-8">
                        @csrf
                        @method('PUT')
                        
                        {{-- 架電目標セクション --}}
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                                <i class="fas fa-phone mr-2"></i>
                                架電目標
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="daily_call_target" class="block text-sm font-medium text-gray-700 mb-2">
                                        日次目標架電数 <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="daily_call_target"
                                           name="daily_call_target" 
                                           value="{{ old('daily_call_target', $kpiTarget->daily_call_target) }}" 
                                           min="1" 
                                           max="200"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('daily_call_target') border-red-500 @enderror">
                                    @error('daily_call_target')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">1～200件</p>
                                </div>
                                
                                <div>
                                    <label for="weekly_call_target" class="block text-sm font-medium text-gray-700 mb-2">
                                        週次目標架電数 <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="weekly_call_target"
                                           name="weekly_call_target" 
                                           value="{{ old('weekly_call_target', $kpiTarget->weekly_call_target) }}" 
                                           min="5" 
                                           max="1400"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('weekly_call_target') border-red-500 @enderror">
                                    @error('weekly_call_target')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">5～1400件（日次×5以上）</p>
                                </div>
                                
                                <div>
                                    <label for="monthly_call_target" class="block text-sm font-medium text-gray-700 mb-2">
                                        月次目標架電数 <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="monthly_call_target"
                                           name="monthly_call_target" 
                                           value="{{ old('monthly_call_target', $kpiTarget->monthly_call_target) }}" 
                                           min="20" 
                                           max="6000"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('monthly_call_target') border-red-500 @enderror">
                                    @error('monthly_call_target')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">20～6000件（週次×4以上）</p>
                                </div>
                            </div>
                        </div>

                        {{-- アポ獲得目標セクション --}}
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                                <i class="fas fa-handshake mr-2"></i>
                                アポ獲得目標
                            </h3>
                            <div class="max-w-md">
                                <label for="monthly_appointment_target" class="block text-sm font-medium text-gray-700 mb-2">
                                    月次目標アポ獲得数 <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       id="monthly_appointment_target"
                                       name="monthly_appointment_target" 
                                       value="{{ old('monthly_appointment_target', $kpiTarget->monthly_appointment_target) }}" 
                                       min="0" 
                                       max="500"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 @error('monthly_appointment_target') border-red-500 @enderror">
                                @error('monthly_appointment_target')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">0～500件</p>
                            </div>
                        </div>

                        {{-- 成功率目標セクション --}}
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                                <i class="fas fa-chart-line mr-2"></i>
                                成功率目標（オプション）
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="target_success_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                        目標通話成功率 (%)
                                    </label>
                                    <input type="number" 
                                           id="target_success_rate"
                                           name="target_success_rate" 
                                           value="{{ old('target_success_rate', $kpiTarget->target_success_rate) }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 @error('target_success_rate') border-red-500 @enderror">
                                    @error('target_success_rate')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">0～100%（空欄可）</p>
                                </div>
                                
                                <div>
                                    <label for="target_appointment_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                        目標アポ獲得率 (%)
                                    </label>
                                    <input type="number" 
                                           id="target_appointment_rate"
                                           name="target_appointment_rate" 
                                           value="{{ old('target_appointment_rate', $kpiTarget->target_appointment_rate) }}" 
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 @error('target_appointment_rate') border-red-500 @enderror">
                                    @error('target_appointment_rate')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">0～100%（空欄可）</p>
                                </div>
                            </div>
                        </div>

                        {{-- 有効期間セクション --}}
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-purple-800 mb-4 flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                有効期間
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="effective_from" class="block text-sm font-medium text-gray-700 mb-2">
                                        有効開始日 <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                           id="effective_from"
                                           name="effective_from" 
                                           value="{{ old('effective_from', $kpiTarget->effective_from) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 @error('effective_from') border-red-500 @enderror">
                                    @error('effective_from')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="effective_until" class="block text-sm font-medium text-gray-700 mb-2">
                                        有効終了日（オプション）
                                    </label>
                                    <input type="date" 
                                           id="effective_until"
                                           name="effective_until" 
                                           value="{{ old('effective_until', $kpiTarget->effective_until) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 @error('effective_until') border-red-500 @enderror">
                                    @error('effective_until')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">未設定の場合は無期限</p>
                                </div>
                            </div>
                        </div>

                        {{-- 変更内容の注意事項 --}}
                        <div class="bg-orange-50 border border-orange-200 p-6 rounded-lg">
                            <h4 class="text-sm font-semibold text-orange-800 mb-3 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                編集時の注意事項
                            </h4>
                            <ul class="text-sm text-orange-700 space-y-1">
                                <li>• 目標値を変更すると、進捗の計算基準が変わります</li>
                                <li>• 有効期間を変更すると、過去の実績データとの整合性に影響する可能性があります</li>
                                <li>• 週次目標は日次目標の5倍以上、月次目標は週次目標の4倍以上に設定してください</li>
                                <li>• この目標は現在アクティブなため、変更は即座に反映されます</li>
                            </ul>
                        </div>

                        {{-- アクションボタン --}}
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('kpi-targets.show', $kpiTarget) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                キャンセル
                            </a>
                            <button type="submit" 
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                                変更を保存
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for live validation hints --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dailyInput = document.getElementById('daily_call_target');
            const weeklyInput = document.getElementById('weekly_call_target');
            const monthlyInput = document.getElementById('monthly_call_target');
            
            function updateValidationHints() {
                const daily = parseInt(dailyInput.value) || 0;
                const weekly = parseInt(weeklyInput.value) || 0;
                const monthly = parseInt(monthlyInput.value) || 0;
                
                // Weekly validation hint
                const weeklyMin = daily * 5;
                const weeklyHint = weeklyInput.parentNode.querySelector('.text-gray-500');
                if (weeklyHint) {
                    if (daily > 0) {
                        weeklyHint.textContent = `5～1400件（最低${weeklyMin}件推奨）`;
                        weeklyHint.className = weekly >= weeklyMin ? 'mt-1 text-xs text-green-600' : 'mt-1 text-xs text-orange-600';
                    } else {
                        weeklyHint.textContent = '5～1400件（日次×5以上）';
                        weeklyHint.className = 'mt-1 text-xs text-gray-500';
                    }
                }
                
                // Monthly validation hint
                const monthlyMin = weekly * 4;
                const monthlyHint = monthlyInput.parentNode.querySelector('.text-gray-500, .text-green-600, .text-orange-600');
                if (monthlyHint) {
                    if (weekly > 0) {
                        monthlyHint.textContent = `20～6000件（最低${monthlyMin}件推奨）`;
                        monthlyHint.className = monthly >= monthlyMin ? 'mt-1 text-xs text-green-600' : 'mt-1 text-xs text-orange-600';
                    } else {
                        monthlyHint.textContent = '20～6000件（週次×4以上）';
                        monthlyHint.className = 'mt-1 text-xs text-gray-500';
                    }
                }
            }
            
            dailyInput.addEventListener('input', updateValidationHints);
            weeklyInput.addEventListener('input', updateValidationHints);
            monthlyInput.addEventListener('input', updateValidationHints);
            
            // Initial call
            updateValidationHints();
        });
    </script>
</x-app-layout>