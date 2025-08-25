@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="kpiManagement()">
    <!-- ページヘッダー -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">🎯 KPI管理</h1>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->format('Y年m月') }}の実績
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- 目標設定エリア -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">📅 目標設定</h2>
                <div class="text-sm text-gray-500" x-text="getCurrentPeriodText()">
                    2025年8月 第35週 (8/25-8/31)
                </div>
            </div>
            
            <form @submit.prevent="updateTargets" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">週次架電目標</label>
                        <div class="relative">
                            <input 
                                type="number" 
                                x-model="targets.weekly_calls"
                                min="0"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-12"
                            >
                            <span class="absolute right-3 top-2 text-sm text-gray-500">件</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">週次アポ目標</label>
                        <div class="relative">
                            <input 
                                type="number" 
                                x-model="targets.weekly_appointments"
                                min="0"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-12"
                            >
                            <span class="absolute right-3 top-2 text-sm text-gray-500">件</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">月次架電目標</label>
                        <div class="relative">
                            <input 
                                type="number" 
                                x-model="targets.monthly_calls"
                                min="0"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-12"
                            >
                            <span class="absolute right-3 top-2 text-sm text-gray-500">件</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">月次アポ目標</label>
                        <div class="relative">
                            <input 
                                type="number" 
                                x-model="targets.monthly_appointments"
                                min="0"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 pr-12"
                            >
                            <span class="absolute right-3 top-2 text-sm text-gray-500">件</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button 
                        type="submit"
                        :disabled="updatingTargets"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors disabled:opacity-50"
                    >
                        <span x-show="!updatingTargets">目標更新</span>
                        <span x-show="updatingTargets">更新中...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- 進捗状況 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- 週次進捗 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">週次進捗</h3>
                
                <!-- 架電数進捗 -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">架電数</span>
                        <span class="text-lg font-bold text-gray-900">
                            <span x-text="performance.weekly_calls">78</span>/<span x-text="targets.weekly_calls">100</span> 
                            (<span x-text="Math.round(performance.weekly_calls / targets.weekly_calls * 100)">78</span>%)
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div 
                            class="bg-blue-600 h-3 rounded-full transition-all duration-700"
                            :style="`width: ${Math.min(performance.weekly_calls / targets.weekly_calls * 100, 100)}%`"
                        ></div>
                    </div>
                    <div class="flex mt-2 space-x-1">
                        <template x-for="i in 5" :key="i">
                            <div 
                                :class="i <= Math.ceil(performance.weekly_calls / targets.weekly_calls * 5) ? 'bg-blue-600' : 'bg-gray-300'"
                                class="flex-1 h-2 rounded"
                            ></div>
                        </template>
                    </div>
                </div>
                
                <!-- アポ数進捗 -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">アポ数</span>
                        <span class="text-lg font-bold text-gray-900">
                            <span x-text="performance.weekly_appointments">15</span>/<span x-text="targets.weekly_appointments">20</span> 
                            (<span x-text="Math.round(performance.weekly_appointments / targets.weekly_appointments * 100)">75</span>%)
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div 
                            class="bg-green-600 h-3 rounded-full transition-all duration-700"
                            :style="`width: ${Math.min(performance.weekly_appointments / targets.weekly_appointments * 100, 100)}%`"
                        ></div>
                    </div>
                    <div class="flex mt-2 space-x-1">
                        <template x-for="i in 5" :key="i">
                            <div 
                                :class="i <= Math.ceil(performance.weekly_appointments / targets.weekly_appointments * 5) ? 'bg-green-600' : 'bg-gray-300'"
                                class="flex-1 h-2 rounded"
                            ></div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- 月次進捗 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">月次進捗</h3>
                
                <!-- 架電数進捗 -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">架電数</span>
                        <span class="text-lg font-bold text-gray-900">
                            <span x-text="performance.monthly_calls">315</span>/<span x-text="targets.monthly_calls">400</span> 
                            (<span x-text="Math.round(performance.monthly_calls / targets.monthly_calls * 100)">79</span>%)
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div 
                            class="bg-blue-600 h-3 rounded-full transition-all duration-700"
                            :style="`width: ${Math.min(performance.monthly_calls / targets.monthly_calls * 100, 100)}%`"
                        ></div>
                    </div>
                    <div class="flex mt-2 space-x-1">
                        <template x-for="i in 5" :key="i">
                            <div 
                                :class="i <= Math.ceil(performance.monthly_calls / targets.monthly_calls * 5) ? 'bg-blue-600' : 'bg-gray-300'"
                                class="flex-1 h-2 rounded"
                            ></div>
                        </template>
                    </div>
                </div>
                
                <!-- アポ数進捗 -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">アポ数</span>
                        <span class="text-lg font-bold text-gray-900">
                            <span x-text="performance.monthly_appointments">62</span>/<span x-text="targets.monthly_appointments">80</span> 
                            (<span x-text="Math.round(performance.monthly_appointments / targets.monthly_appointments * 100)">78</span>%)
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div 
                            class="bg-green-600 h-3 rounded-full transition-all duration-700"
                            :style="`width: ${Math.min(performance.monthly_appointments / targets.monthly_appointments * 100, 100)}%`"
                        ></div>
                    </div>
                    <div class="flex mt-2 space-x-1">
                        <template x-for="i in 5" :key="i">
                            <div 
                                :class="i <= Math.ceil(performance.monthly_appointments / targets.monthly_appointments * 5) ? 'bg-green-600' : 'bg-gray-300'"
                                class="flex-1 h-2 rounded"
                            ></div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- 成功率推移 -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">8月成功率</h3>
                <div class="h-32 flex items-end justify-center">
                    <canvas id="successRateChart" class="w-full h-full"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <div class="text-sm text-gray-500">今月平均</div>
                    <div class="text-2xl font-bold text-green-600" x-text="averageSuccessRate + '%'">65%</div>
                </div>
            </div>
        </div>

        <!-- 詳細グラフエリア -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-800">📊 月間パフォーマンス推移</h2>
                <div class="flex items-center space-x-4">
                    <div class="flex space-x-2">
                        <button 
                            @click="chartType = 'calls'"
                            :class="chartType === 'calls' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="px-3 py-1 rounded text-sm font-medium transition-colors"
                        >
                            架電数
                        </button>
                        <button 
                            @click="chartType = 'appointments'"
                            :class="chartType === 'appointments' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="px-3 py-1 rounded text-sm font-medium transition-colors"
                        >
                            アポ数
                        </button>
                        <button 
                            @click="chartType = 'success_rate'"
                            :class="chartType === 'success_rate' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                            class="px-3 py-1 rounded text-sm font-medium transition-colors"
                        >
                            成功率
                        </button>
                    </div>
                    <select 
                        x-model="selectedPeriod"
                        class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="current_month">今月</option>
                        <option value="last_month">先月</option>
                        <option value="last_3_months">過去3ヶ月</option>
                    </select>
                </div>
            </div>
            
            <div class="h-80">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- KPI詳細分析 -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 週別比較 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 週別パフォーマンス</h3>
                <div class="space-y-3">
                    <template x-for="week in weeklyComparison" :key="week.week">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900" x-text="week.period"></div>
                                <div class="text-sm text-gray-500">
                                    架電: <span x-text="week.calls">125</span>件 | 
                                    アポ: <span x-text="week.appointments">18</span>件
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold" :class="week.trend === 'up' ? 'text-green-600' : week.trend === 'down' ? 'text-red-600' : 'text-gray-600'">
                                    <span x-show="week.trend === 'up'">📈 +</span>
                                    <span x-show="week.trend === 'down'">📉 </span>
                                    <span x-show="week.trend === 'same'">➡️ </span>
                                    <span x-text="week.change">15</span>%
                                </div>
                                <div class="text-sm text-gray-500">前週比</div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 目標達成予測 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">🔮 目標達成予測</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-blue-900">月次架電目標</span>
                            <span class="text-sm text-blue-600" x-text="monthlyCallsPrediction.status">達成可能</span>
                        </div>
                        <div class="text-sm text-blue-700">
                            現在のペースで<span x-text="monthlyCallsPrediction.predicted">385</span>件達成予想
                            <span x-text="monthlyCallsPrediction.needed > 0 ? '(あと' + monthlyCallsPrediction.needed + '件必要)' : ''"></span>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-green-900">月次アポ目標</span>
                            <span class="text-sm text-green-600" x-text="monthlyAppointmentsPrediction.status">達成可能</span>
                        </div>
                        <div class="text-sm text-green-700">
                            現在のペースで<span x-text="monthlyAppointmentsPrediction.predicted">76</span>件達成予想
                            <span x-text="monthlyAppointmentsPrediction.needed > 0 ? '(あと' + monthlyAppointmentsPrediction.needed + '件必要)' : ''"></span>
                        </div>
                    </div>
                    
                    <!-- 改善提案 -->
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <div class="font-medium text-yellow-900 mb-2">💡 改善提案</div>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <template x-for="suggestion in suggestions" :key="suggestion">
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span x-text="suggestion"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function kpiManagement() {
    return {
        targets: {
            weekly_calls: 100,
            weekly_appointments: 20,
            monthly_calls: 400,
            monthly_appointments: 80
        },
        
        performance: {
            weekly_calls: 78,
            weekly_appointments: 15,
            monthly_calls: 315,
            monthly_appointments: 62
        },
        
        updatingTargets: false,
        chartType: 'calls',
        selectedPeriod: 'current_month',
        averageSuccessRate: 65,
        
        weeklyComparison: [
            { week: 1, period: '第1週 (8/1-8/7)', calls: 95, appointments: 14, trend: 'down', change: -5 },
            { week: 2, period: '第2週 (8/8-8/14)', calls: 102, appointments: 16, trend: 'up', change: 7 },
            { week: 3, period: '第3週 (8/15-8/21)', calls: 118, appointments: 17, trend: 'up', change: 16 },
            { week: 4, period: '第4週 (8/22-8/28)', calls: 78, appointments: 15, trend: 'down', change: -34 }
        ],
        
        monthlyCallsPrediction: {
            predicted: 385,
            needed: 15,
            status: '要努力'
        },
        
        monthlyAppointmentsPrediction: {
            predicted: 76,
            needed: 4,
            status: '達成可能'
        },
        
        suggestions: [
            '1日あたり5件の追加架電で月次目標達成',
            '午後の架電成功率が低い傾向 - 時間帯の見直し検討',
            '週末前の金曜日が最も成功率が高い - 重要顧客は金曜日に集中'
        ],
        
        async updateTargets() {
            this.updatingTargets = true;
            try {
                // 実際のAPIコールの代わりにモックデータ
                await new Promise(resolve => setTimeout(resolve, 1000));
                console.log('目標を更新しました:', this.targets);
            } catch (error) {
                console.error('Error updating targets:', error);
                alert('目標の更新に失敗しました');
            } finally {
                this.updatingTargets = false;
            }
        },
        
        getCurrentPeriodText() {
            const now = new Date();
            const year = now.getFullYear();
            const month = now.getMonth() + 1;
            const date = now.getDate();
            
            // 週の計算（簡易版）
            const weekOfMonth = Math.ceil(date / 7);
            const startDate = (weekOfMonth - 1) * 7 + 1;
            const endDate = Math.min(weekOfMonth * 7, new Date(year, month, 0).getDate());
            
            return `${year}年${month}月 第${weekOfMonth}週 (${month}/${startDate}-${month}/${endDate})`;
        },
        
        init() {
            // Chart.js初期化
            this.$nextTick(() => {
                this.initSuccessRateChart();
                this.initPerformanceChart();
            });
            
            // チャートタイプ変更時の再描画
            this.$watch('chartType', () => {
                this.updatePerformanceChart();
            });
            
            this.$watch('selectedPeriod', () => {
                this.updatePerformanceChart();
            });
        },
        
        initSuccessRateChart() {
            const ctx = document.getElementById('successRateChart');
            if (ctx) {
                this.successRateChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['1週', '2週', '3週', '4週'],
                        datasets: [{
                            label: '成功率',
                            data: [60, 65, 68, 70],
                            borderColor: '#059669',
                            backgroundColor: 'rgba(5, 150, 105, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { display: false },
                            y: { 
                                display: false,
                                min: 50,
                                max: 80
                            }
                        }
                    }
                });
            }
        },
        
        initPerformanceChart() {
            const ctx = document.getElementById('performanceChart');
            if (ctx) {
                this.performanceChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: Array.from({length: 25}, (_, i) => i + 1),
                        datasets: [{
                            label: '架電数',
                            data: [12, 15, 18, 14, 20, 16, 22, 18, 25, 20, 15, 19, 23, 17, 21, 24, 19, 16, 20, 18, 22, 15, 19, 17, 20],
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: '日'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: '件数'
                                }
                            }
                        }
                    }
                });
            }
        },
        
        updatePerformanceChart() {
            if (!this.performanceChart) return;
            
            let data, label, color, backgroundColor;
            
            switch (this.chartType) {
                case 'appointments':
                    data = [2, 3, 2, 1, 4, 2, 3, 2, 4, 3, 2, 3, 4, 2, 3, 4, 3, 2, 3, 2, 4, 2, 3, 2, 3];
                    label = 'アポ数';
                    color = '#059669';
                    backgroundColor = 'rgba(5, 150, 105, 0.1)';
                    break;
                case 'success_rate':
                    data = [60, 65, 70, 55, 75, 60, 68, 62, 72, 67, 58, 63, 69, 61, 66, 71, 64, 59, 65, 62, 68, 57, 63, 60, 67];
                    label = '成功率 (%)';
                    color = '#d97706';
                    backgroundColor = 'rgba(217, 119, 6, 0.1)';
                    break;
                default:
                    data = [12, 15, 18, 14, 20, 16, 22, 18, 25, 20, 15, 19, 23, 17, 21, 24, 19, 16, 20, 18, 22, 15, 19, 17, 20];
                    label = '架電数';
                    color = '#2563eb';
                    backgroundColor = 'rgba(37, 99, 235, 0.1)';
            }
            
            this.performanceChart.data.datasets[0].data = data;
            this.performanceChart.data.datasets[0].label = label;
            this.performanceChart.data.datasets[0].borderColor = color;
            this.performanceChart.data.datasets[0].backgroundColor = backgroundColor;
            this.performanceChart.update();
        }
    }
}
</script>
@endsection