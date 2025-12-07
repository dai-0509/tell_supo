<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('KPI目標管理') }}
            </h2>
            <div class="flex space-x-3">
                @if($activeTarget)
                    <form method="POST" action="{{ route('kpi-targets.reset') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                                onclick="return confirm('すべてのKPI目標をリセットしますか？')">
                            リセット
                        </button>
                    </form>
                @endif
                <a href="{{ route('kpi-targets.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    新しい目標設定
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 現在の目標 --}}
            @if($activeTarget)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">現在の目標</h3>
                            <div class="flex space-x-2">
                                <a href="{{ route('kpi-targets.show', $activeTarget) }}"
                                   class="text-blue-600 hover:text-blue-900">詳細</a>
                                <a href="{{ route('kpi-targets.edit', $activeTarget) }}"
                                   class="text-green-600 hover:text-green-900">編集</a>
                                <form method="POST" action="{{ route('kpi-targets.destroy', $activeTarget) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('この目標を無効化しますか？')">
                                        無効化
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- 日次目標 --}}
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">日次目標</h4>
                                <p class="text-2xl font-bold text-blue-600">{{ $activeTarget->daily_call_target }}件</p>
                                <p class="text-sm text-blue-600 mt-1">架電数</p>
                            </div>

                            {{-- 週次目標 --}}
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-green-800 mb-2">週次目標</h4>
                                <p class="text-2xl font-bold text-green-600">{{ $activeTarget->weekly_call_target }}件</p>
                                <p class="text-sm text-green-600 mt-1">架電数</p>
                            </div>

                            {{-- 月次目標 --}}
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-purple-800 mb-2">月次目標</h4>
                                <p class="text-2xl font-bold text-purple-600">{{ $activeTarget->monthly_call_target }}件</p>
                                <p class="text-sm text-purple-600 mt-1">架電数</p>
                                <p class="text-lg font-semibold text-purple-600 mt-2">{{ $activeTarget->monthly_appointment_target }}件</p>
                                <p class="text-sm text-purple-600">アポ獲得</p>
                            </div>
                        </div>

                        {{-- 成功率目標 --}}
                        @if($activeTarget->target_success_rate || $activeTarget->target_appointment_rate)
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($activeTarget->target_success_rate)
                                    <div class="bg-yellow-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-yellow-800 mb-2">目標通話成功率</h4>
                                        <p class="text-xl font-bold text-yellow-600">{{ $activeTarget->target_success_rate }}%</p>
                                    </div>
                                @endif

                                @if($activeTarget->target_appointment_rate)
                                    <div class="bg-orange-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-orange-800 mb-2">目標アポ獲得率</h4>
                                        <p class="text-xl font-bold text-orange-600">{{ $activeTarget->target_appointment_rate }}%</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4 text-sm text-gray-600">
                            <p><strong>有効期間:</strong>
                                {{ $activeTarget->effective_from }}
                                @if($activeTarget->effective_until)
                                    ～ {{ $activeTarget->effective_until }}
                                @else
                                    ～ （無期限）
                                @endif
                            </p>
                            <p class="mt-1"><strong>設定日:</strong> {{ $activeTarget->created_at->format('Y年m月d日 H:i') }}</p>
                        </div>
                    </div>
                </div>
            @else
                {{-- 目標未設定の場合 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="text-center py-8">
                            <i class="fas fa-target text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">KPI目標が設定されていません</h3>
                            <p class="text-gray-500 mb-6">効率的な営業活動のため、目標を設定しましょう。</p>
                            <a href="{{ route('kpi-targets.create') }}"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                                最初の目標を設定する
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 目標履歴 --}}
            @if($recentTargets->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">最近の目標履歴</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            設定日
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            日次目標
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            週次目標
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            月次目標
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            有効期間
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            状態
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            操作
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentTargets as $target)
                                        <tr class="{{ $target->is_active ? 'bg-green-50' : 'bg-gray-50' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $target->created_at->format('Y/m/d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $target->daily_call_target }}件
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $target->weekly_call_target }}件
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $target->monthly_call_target }}件 / {{ $target->monthly_appointment_target }}アポ
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $target->effective_from }}
                                                @if($target->effective_until) ～ {{ $target->effective_until }} @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($target->is_active)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        有効
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        無効
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('kpi-targets.show', $target) }}"
                                                   class="text-blue-600 hover:text-blue-900 mr-3">詳細</a>
                                                @if($target->is_active)
                                                    <a href="{{ route('kpi-targets.edit', $target) }}"
                                                       class="text-green-600 hover:text-green-900">編集</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
