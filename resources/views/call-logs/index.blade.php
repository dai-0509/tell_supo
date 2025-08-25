@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="callLogList()">
    <!-- ページヘッダー -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">📞 架電履歴</h1>
                    </div>
                    <button 
                        @click="openModal"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                    >
                        ➕ 新規架電記録
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- フィルター・統計エリア -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- フィルター -->
                <div class="flex flex-col md:flex-row gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">📅 日付範囲</label>
                        <div class="flex items-center space-x-2">
                            <input 
                                type="date" 
                                x-model="dateFrom"
                                class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            >
                            <span class="text-gray-500">〜</span>
                            <input 
                                type="date" 
                                x-model="dateTo"
                                class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">結果</label>
                        <select 
                            x-model="selectedResult"
                            class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">すべて</option>
                            <option value="success">接触成功</option>
                            <option value="appointment">アポ獲得</option>
                            <option value="no_answer">不在</option>
                            <option value="busy">話中</option>
                            <option value="not_interested">興味なし</option>
                            <option value="callback">折り返し希望</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">顧客</label>
                        <select 
                            x-model="selectedCustomer"
                            class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 min-w-48"
                        >
                            <option value="">すべて</option>
                            <template x-for="customer in customers" :key="customer.id">
                                <option :value="customer.id" x-text="customer.company_name"></option>
                            </template>
                        </select>
                    </div>
                </div>
                
                <!-- 統計表示 -->
                <div class="flex items-center space-x-6 text-sm">
                    <div class="bg-blue-50 px-4 py-2 rounded-lg">
                        <span class="text-blue-600 font-semibold">📊 成功率:</span>
                        <span class="text-blue-900 font-bold ml-1" x-text="successRate + '%'">65%</span>
                    </div>
                    <div class="bg-gray-50 px-4 py-2 rounded-lg">
                        <span class="text-gray-600 font-semibold">総架電数:</span>
                        <span class="text-gray-900 font-bold ml-1" x-text="filteredCallLogs.length">127</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 架電履歴テーブル -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <!-- テーブルヘッダー -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">架電履歴</h3>
                    <div class="text-sm text-gray-500">
                        <span x-text="filteredCallLogs.length"></span>件の履歴
                    </div>
                </div>
            </div>

            <!-- ローディング状態 -->
            <div x-show="loading" class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="ml-2 text-gray-500">読み込み中...</span>
            </div>

            <!-- テーブル本体 -->
            <div x-show="!loading" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                日時
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                顧客名
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                担当者
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                結果
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                次回予定
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                メモ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="callLog in paginatedCallLogs" :key="callLog.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900" x-text="formatDateTime(callLog.called_at)"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900" x-text="callLog.customer.company_name"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900" x-text="callLog.customer.contact_name || '-'"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        :class="getResultBadgeClass(callLog.result)"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        x-text="getResultText(callLog.result)"
                                    ></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span x-text="callLog.next_call_date ? formatDate(callLog.next_call_date) : '-'"></span>
                                </td>
                                <td class="px-6 py-4 max-w-xs truncate text-sm text-gray-500">
                                    <span x-text="callLog.notes || '-'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button 
                                        @click="editCallLog(callLog)"
                                        class="text-blue-600 hover:text-blue-900 mr-3"
                                    >
                                        編集
                                    </button>
                                    <button 
                                        @click="deleteCallLog(callLog.id)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        削除
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- データなしの場合 -->
                <div x-show="filteredCallLogs.length === 0" class="text-center py-12">
                    <div class="text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <p>架電履歴がありません</p>
                        <p class="text-sm">新しい架電記録を追加してください</p>
                    </div>
                </div>
            </div>

            <!-- ページネーション -->
            <div x-show="!loading && totalPages > 1" class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button 
                            @click="previousPage()"
                            :disabled="currentPage === 1"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            前へ
                        </button>
                        <button 
                            @click="nextPage()"
                            :disabled="currentPage === totalPages"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                        >
                            次へ
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                <span x-text="((currentPage - 1) * itemsPerPage) + 1"></span>
                                から
                                <span x-text="Math.min(currentPage * itemsPerPage, filteredCallLogs.length)"></span>
                                件を表示（全<span x-text="filteredCallLogs.length"></span>件中）
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <button 
                                    @click="previousPage()"
                                    :disabled="currentPage === 1"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                                >
                                    ◀
                                </button>
                                
                                <template x-for="page in visiblePages" :key="page">
                                    <button 
                                        @click="currentPage = page"
                                        :class="page === currentPage ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                        x-text="page"
                                    ></button>
                                </template>
                                
                                <button 
                                    @click="nextPage()"
                                    :disabled="currentPage === totalPages"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                                >
                                    ▶
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 新規架電記録モーダル -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @click.away="closeModal">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="submitForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <span x-show="!editingCallLog">新規架電記録</span>
                            <span x-show="editingCallLog">架電記録編集</span>
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">顧客選択 *</label>
                                <select 
                                    x-model="form.customer_id"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">顧客を選択してください</option>
                                    <template x-for="customer in customers" :key="customer.id">
                                        <option :value="customer.id" x-text="customer.company_name + (customer.contact_name ? ' (' + customer.contact_name + ')' : '')"></option>
                                    </template>
                                </select>
                                <p x-show="errors.customer_id" class="mt-1 text-sm text-red-600" x-text="errors.customer_id"></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">架電日時</label>
                                <div class="mt-1 flex space-x-2">
                                    <input 
                                        type="date" 
                                        x-model="form.call_date"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    <input 
                                        type="time" 
                                        x-model="form.call_time"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    <button 
                                        type="button"
                                        @click="setCurrentTime"
                                        class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded-md transition-colors"
                                    >
                                        現在時刻
                                    </button>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">架電結果 *</label>
                                <div class="space-y-2">
                                    <div class="flex flex-wrap gap-3">
                                        <label class="inline-flex items-center">
                                            <input type="radio" x-model="form.result" value="success" class="text-blue-600">
                                            <span class="ml-2 text-sm">接触成功</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" x-model="form.result" value="appointment" class="text-green-600">
                                            <span class="ml-2 text-sm">アポ獲得</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" x-model="form.result" value="no_answer" class="text-yellow-600">
                                            <span class="ml-2 text-sm">不在</span>
                                        </label>
                                    </div>
                                    <div class="flex flex-wrap gap-3">
                                        <label class="inline-flex items-center">
                                            <input type="radio" x-model="form.result" value="busy" class="text-gray-600">
                                            <span class="ml-2 text-sm">話中</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" x-model="form.result" value="not_interested" class="text-red-600">
                                            <span class="ml-2 text-sm">興味なし</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" x-model="form.result" value="callback" class="text-purple-600">
                                            <span class="ml-2 text-sm">折り返し希望</span>
                                        </label>
                                    </div>
                                </div>
                                <p x-show="errors.result" class="mt-1 text-sm text-red-600" x-text="errors.result"></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">次回架電予定日</label>
                                <div class="mt-1 flex space-x-2">
                                    <input 
                                        type="date" 
                                        x-model="form.next_call_date"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    >
                                    <input 
                                        type="time" 
                                        x-model="form.next_call_time"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    >
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">メモ</label>
                                <textarea 
                                    x-model="form.notes"
                                    rows="3"
                                    placeholder="架電内容や次回のポイントなど..."
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            type="submit"
                            :disabled="submitting"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            <span x-show="!submitting">保存</span>
                            <span x-show="submitting">保存中...</span>
                        </button>
                        <button 
                            type="button"
                            @click="closeModal"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            キャンセル
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function callLogList() {
    return {
        callLogs: [
            {
                id: 1,
                customer_id: 1,
                customer: { company_name: '㈱サンプル', contact_name: '田中様' },
                called_at: '2025-08-25 14:30:00',
                result: 'appointment',
                next_call_date: '2025-08-27 10:00:00',
                notes: '商談の機会をいただけることになりました'
            },
            {
                id: 2,
                customer_id: 2,
                customer: { company_name: 'ABC商事', contact_name: '佐藤様' },
                called_at: '2025-08-25 13:45:00',
                result: 'no_answer',
                next_call_date: '2025-08-26 15:00:00',
                notes: ''
            },
            {
                id: 3,
                customer_id: 3,
                customer: { company_name: 'XYZ株式会社', contact_name: '鈴木様' },
                called_at: '2025-08-25 13:20:00',
                result: 'success',
                next_call_date: null,
                notes: '詳細資料の送付を希望'
            },
            {
                id: 4,
                customer_id: 4,
                customer: { company_name: 'DEF企業', contact_name: '高橋様' },
                called_at: '2025-08-25 12:50:00',
                result: 'busy',
                next_call_date: '2025-08-25 16:00:00',
                notes: ''
            },
            {
                id: 5,
                customer_id: 5,
                customer: { company_name: 'GHI会社', contact_name: '伊藤様' },
                called_at: '2025-08-25 12:30:00',
                result: 'not_interested',
                next_call_date: null,
                notes: '現在はサービス導入予定なし'
            }
        ],
        
        customers: [
            { id: 1, company_name: '㈱サンプル', contact_name: '田中様' },
            { id: 2, company_name: 'ABC商事', contact_name: '佐藤様' },
            { id: 3, company_name: 'XYZ株式会社', contact_name: '鈴木様' },
            { id: 4, company_name: 'DEF企業', contact_name: '高橋様' },
            { id: 5, company_name: 'GHI会社', contact_name: '伊藤様' }
        ],
        
        dateFrom: '2025-08-01',
        dateTo: '2025-08-31',
        selectedResult: '',
        selectedCustomer: '',
        loading: false,
        showModal: false,
        editingCallLog: null,
        submitting: false,
        currentPage: 1,
        itemsPerPage: 20,
        
        form: {
            customer_id: '',
            call_date: '',
            call_time: '',
            result: '',
            next_call_date: '',
            next_call_time: '',
            notes: ''
        },
        
        errors: {},
        
        get filteredCallLogs() {
            return this.callLogs.filter(callLog => {
                const callDate = new Date(callLog.called_at).toISOString().split('T')[0];
                const matchesDateRange = (!this.dateFrom || callDate >= this.dateFrom) && 
                                        (!this.dateTo || callDate <= this.dateTo);
                const matchesResult = !this.selectedResult || callLog.result === this.selectedResult;
                const matchesCustomer = !this.selectedCustomer || callLog.customer_id == this.selectedCustomer;
                
                return matchesDateRange && matchesResult && matchesCustomer;
            });
        },
        
        get paginatedCallLogs() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredCallLogs.slice(start, end);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredCallLogs.length / this.itemsPerPage);
        },
        
        get visiblePages() {
            const pages = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, this.currentPage + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        },
        
        get successRate() {
            const total = this.filteredCallLogs.length;
            if (total === 0) return 0;
            
            const successful = this.filteredCallLogs.filter(log => 
                log.result === 'success' || log.result === 'appointment'
            ).length;
            
            return Math.round((successful / total) * 100);
        },
        
        openModal() {
            this.showModal = true;
            this.resetForm();
            this.setCurrentTime();
        },
        
        closeModal() {
            this.showModal = false;
            this.editingCallLog = null;
            this.resetForm();
        },
        
        resetForm() {
            this.form = {
                customer_id: '',
                call_date: '',
                call_time: '',
                result: '',
                next_call_date: '',
                next_call_time: '',
                notes: ''
            };
            this.errors = {};
        },
        
        setCurrentTime() {
            const now = new Date();
            this.form.call_date = now.toISOString().split('T')[0];
            this.form.call_time = now.toTimeString().substring(0, 5);
        },
        
        editCallLog(callLog) {
            this.editingCallLog = callLog;
            const calledAt = new Date(callLog.called_at);
            const nextCallDate = callLog.next_call_date ? new Date(callLog.next_call_date) : null;
            
            this.form = {
                customer_id: callLog.customer_id,
                call_date: calledAt.toISOString().split('T')[0],
                call_time: calledAt.toTimeString().substring(0, 5),
                result: callLog.result,
                next_call_date: nextCallDate ? nextCallDate.toISOString().split('T')[0] : '',
                next_call_time: nextCallDate ? nextCallDate.toTimeString().substring(0, 5) : '',
                notes: callLog.notes || ''
            };
            this.showModal = true;
        },
        
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            
            try {
                // バリデーション
                if (!this.form.customer_id) {
                    this.errors.customer_id = '顧客を選択してください';
                }
                if (!this.form.result) {
                    this.errors.result = '架電結果を選択してください';
                }
                
                if (Object.keys(this.errors).length > 0) {
                    return;
                }
                
                // 実際のAPIコールの代わりにモックデータ
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                const customer = this.customers.find(c => c.id == this.form.customer_id);
                const calledAt = `${this.form.call_date} ${this.form.call_time}:00`;
                const nextCallDate = this.form.next_call_date && this.form.next_call_time 
                    ? `${this.form.next_call_date} ${this.form.next_call_time}:00`
                    : null;
                
                if (this.editingCallLog) {
                    // 編集
                    const index = this.callLogs.findIndex(c => c.id === this.editingCallLog.id);
                    if (index !== -1) {
                        this.callLogs[index] = {
                            ...this.callLogs[index],
                            customer_id: parseInt(this.form.customer_id),
                            customer: customer,
                            called_at: calledAt,
                            result: this.form.result,
                            next_call_date: nextCallDate,
                            notes: this.form.notes
                        };
                    }
                } else {
                    // 新規追加
                    const newCallLog = {
                        id: Math.max(...this.callLogs.map(c => c.id)) + 1,
                        customer_id: parseInt(this.form.customer_id),
                        customer: customer,
                        called_at: calledAt,
                        result: this.form.result,
                        next_call_date: nextCallDate,
                        notes: this.form.notes
                    };
                    this.callLogs.unshift(newCallLog);
                }
                
                this.closeModal();
                
            } catch (error) {
                console.error('Error submitting form:', error);
                this.errors.general = 'エラーが発生しました';
            } finally {
                this.submitting = false;
            }
        },
        
        async deleteCallLog(callLogId) {
            if (!confirm('この架電記録を削除しますか？')) {
                return;
            }
            
            try {
                // 実際のAPIコールの代わりにモックデータ
                this.callLogs = this.callLogs.filter(c => c.id !== callLogId);
            } catch (error) {
                console.error('Error deleting call log:', error);
                alert('削除に失敗しました');
            }
        },
        
        formatDateTime(dateTimeStr) {
            const date = new Date(dateTimeStr);
            return date.toLocaleDateString('ja-JP', { 
                month: 'numeric', 
                day: 'numeric' 
            }) + ' ' + date.toLocaleTimeString('ja-JP', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },
        
        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('ja-JP', { 
                month: 'numeric', 
                day: 'numeric' 
            }) + ' ' + date.toLocaleTimeString('ja-JP', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },
        
        getResultText(result) {
            const texts = {
                success: '接触成功',
                appointment: 'アポ獲得',
                no_answer: '不在',
                busy: '話中',
                not_interested: '興味なし',
                callback: '折り返し希望'
            };
            return texts[result] || result;
        },
        
        getResultBadgeClass(result) {
            const classes = {
                success: 'bg-blue-100 text-blue-800',
                appointment: 'bg-green-100 text-green-800',
                no_answer: 'bg-yellow-100 text-yellow-800',
                busy: 'bg-gray-100 text-gray-800',
                not_interested: 'bg-red-100 text-red-800',
                callback: 'bg-purple-100 text-purple-800'
            };
            return classes[result] || 'bg-gray-100 text-gray-800';
        },
        
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        
        init() {
            // フィルター変更時にページをリセット
            this.$watch('dateFrom', () => this.currentPage = 1);
            this.$watch('dateTo', () => this.currentPage = 1);
            this.$watch('selectedResult', () => this.currentPage = 1);
            this.$watch('selectedCustomer', () => this.currentPage = 1);
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection