@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- ページヘッダー -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">🏠 ダッシュボード</h1>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->format('Y年m月d日') }}（{{ now()->isoFormat('dddd') }}）
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- 架電メーター（リアルタイム更新） -->
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="callMeter()">
                <h2 class="text-xl font-semibold text-gray-800 mb-6">今日の架電数</h2>
                
                <!-- 横向きプログレスバー -->
                <div class="mb-6">
                    <!-- 数値表示 -->
                    <div class="flex justify-between items-end mb-3">
                        <div class="flex items-baseline space-x-2">
                            <span class="text-4xl font-bold text-black" x-text="callCount">42</span>
                            <span class="text-lg text-gray-800">件</span>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold text-blue-700">
                                <span x-text="Math.round(callCount / dailyTarget * 100)">84</span><span>% 達成</span>
                            </div>
                            <div class="text-sm text-gray-700">
                                目標: <span x-text="dailyTarget">50</span>件
                            </div>
                        </div>
                    </div>
                    
                    <!-- プログレスバー -->
                    <div class="w-full bg-gray-200 rounded-full h-6">
                        <div 
                            class="bg-gradient-to-r from-blue-800 via-blue-700 to-blue-600 h-6 rounded-full transition-all duration-700 ease-in-out shadow-sm"
                            :style="{ width: Math.min(callCount / dailyTarget * 100, 100) + '%' }"
                        >
                        </div>
                    </div>
                    
                    <!-- 目標ライン表示 -->
                    <div class="flex justify-between text-xs text-gray-400 mt-2">
                        <span>0</span>
                        <span>目標達成</span>
                        <span x-text="dailyTarget">50</span>
                    </div>
                </div>
                
                <!-- プラス・マイナスボタン -->
                <div class="flex items-center justify-center space-x-4">
                    <button 
                        @click="decrementCall()"
                        :disabled="callCount <= 0"
                        class="bg-red-100 hover:bg-red-200 disabled:opacity-50 disabled:cursor-not-allowed text-red-700 font-bold py-2 px-4 rounded-lg transition-colors text-sm"
                    >
                        ➖
                    </button>
                    
                    <div class="text-center px-4">
                        <div class="text-sm text-gray-700 font-medium">架電メーター</div>
                    </div>
                    
                    <button 
                        @click="incrementCall()"
                        class="bg-green-100 hover:bg-green-200 text-green-700 font-bold py-2 px-4 rounded-lg transition-colors text-sm"
                    >
                        ➕
                    </button>
                </div>
                
                <!-- 最終更新時刻 -->
                <p class="text-xs text-gray-400 mt-4 text-center">
                    最終更新: <span x-text="lastUpdated">14:45</span>
                </p>
            </div>
        </div>

        <!-- KPIカードセクション -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- 週次進捗率カード -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">週次進捗率</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">78%</p>
                        <p class="text-sm text-blue-600 mt-1">🎯 残り2日</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- 成功率カード -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">架電成功率</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">65%</p>
                        <p class="text-sm text-green-600 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                📈 +5%↑
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- アポ獲得カード -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">アポ獲得数</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">8件</p>
                        <p class="text-sm text-green-600 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                🤝 +2件↑
                            </span>
                        </p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-lg">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- 平均架電時間カード -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">平均架電時間</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">3.2分</p>
                        <p class="text-sm text-blue-600 mt-1">📊 -0.3分</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- メインコンテンツエリア -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- クイックアクションエリア -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">クイックアクション</h3>
                    <div class="space-y-3">
                        <a href="#" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors text-center">
                            ➕ 新規架電記録
                        </a>
                        <a href="#" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors text-center">
                            🔍 顧客検索
                        </a>
                        <a href="#" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition-colors text-center">
                            📋 今日のタスク
                        </a>
                    </div>
                </div>
            </div>

            <!-- 週次架電グラフ -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">📊 今週の架電数推移</h3>
                    </div>
                    <div class="h-64 w-full">
                        <canvas id="weeklyCallsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- 今日の架電履歴 -->
        <div class="mt-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">今日の架電履歴（最新5件）</h3>
                        <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">すべて見る →</a>
                    </div>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">時刻</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">顧客名</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">結果</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">次回予定</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">14:30</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">㈱サンプル</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        アポ獲得
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-08-27 10:00</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">13:45</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">ABC商事</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        不在
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-08-26 15:00</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">13:20</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">XYZ株式会社</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        接触成功
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">未定</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">12:50</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">DEF企業</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        話中
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2025-08-25 16:00</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">12:30</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">GHI会社</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        興味なし
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function callMeter() {
    return {
        callCount: 42,
        dailyTarget: 50,
        lastUpdated: '14:45',
        
        async incrementCall() {
            try {
                // 実際のAPIコールの代わりにモックデータ
                this.callCount++;
                this.updateLastUpdated();
                console.log('架電数を追加しました');
            } catch (error) {
                console.error('Error incrementing call:', error);
            }
        },
        
        async decrementCall() {
            if (this.callCount <= 0) return;
            
            try {
                // 実際のAPIコールの代わりにモックデータ
                this.callCount--;
                this.updateLastUpdated();
                console.log('架電数を修正しました');
            } catch (error) {
                console.error('Error decrementing call:', error);
            }
        },
        
        updateLastUpdated() {
            const now = new Date();
            this.lastUpdated = now.toLocaleTimeString('ja-JP', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },
        
        init() {
            console.log('Call meter initialized');
        }
    }
}

// Chart.js初期化
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('weeklyCallsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['月', '火', '水', '木', '金'],
                datasets: [{
                    label: '架電数',
                    data: [25, 35, 28, 42, 38],
                    borderColor: '#1e40af',
                    backgroundColor: 'rgba(30, 64, 175, 0.3)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointBackgroundColor: '#1e40af',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 50
                    }
                }
            }
        });
    }
});
</script>
@endsection