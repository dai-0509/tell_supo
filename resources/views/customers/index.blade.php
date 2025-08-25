@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="customerList()">
    <!-- ページヘッダー -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">👥 顧客管理</h1>
                    </div>
                    <button 
                        @click="openModal"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                    >
                        ➕ 新規顧客追加
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- 検索・フィルターエリア -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">🔍 検索</label>
                    <input 
                        type="text" 
                        x-model="searchTerm"
                        placeholder="会社名・担当者名で検索"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
                <div class="md:w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">業界</label>
                    <select 
                        x-model="selectedIndustry"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">すべて</option>
                        <option value="IT">IT</option>
                        <option value="製造">製造</option>
                        <option value="金融">金融</option>
                        <option value="不動産">不動産</option>
                        <option value="小売">小売</option>
                        <option value="その他">その他</option>
                    </select>
                </div>
                <div class="md:w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">企業規模</label>
                    <select 
                        x-model="selectedSize"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">すべて</option>
                        <option value="大企業">大企業</option>
                        <option value="中堅企業">中堅企業</option>
                        <option value="中小企業">中小企業</option>
                        <option value="スタートアップ">スタートアップ</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 顧客一覧テーブル -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <!-- テーブルヘッダー（統計情報付き） -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <h3 class="text-lg font-semibold text-gray-800">顧客一覧</h3>
                        <div class="text-sm text-gray-500">
                            <span x-text="filteredCustomers.length"></span>件 / <span x-text="customers.length"></span>件中
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <select class="text-sm border-gray-300 rounded-md">
                            <option>一括操作</option>
                            <option>削除</option>
                            <option>エクスポート</option>
                        </select>
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
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                会社名
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                担当者
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                電話番号
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                業界
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                架電数
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                最終架電
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="customer in paginatedCustomers" :key="customer.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="rounded border-gray-300">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900" x-text="customer.company_name"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900" x-text="customer.contact_name || '-'"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900" x-text="customer.phone"></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" x-text="customer.industry || '-'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-semibold" x-text="customer.call_count || 0"></span>回
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span x-text="customer.last_called || '-'"></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button 
                                        @click="editCustomer(customer)"
                                        class="text-blue-600 hover:text-blue-900 mr-3"
                                    >
                                        編集
                                    </button>
                                    <button 
                                        @click="deleteCustomer(customer.id)"
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
                <div x-show="filteredCustomers.length === 0" class="text-center py-12">
                    <div class="text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p>顧客データがありません</p>
                        <p class="text-sm">新しい顧客を追加してください</p>
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
                                <span x-text="Math.min(currentPage * itemsPerPage, filteredCustomers.length)"></span>
                                件を表示（全<span x-text="filteredCustomers.length"></span>件中）
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

    <!-- 新規顧客追加モーダル -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" @click.away="closeModal">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form @submit.prevent="submitForm">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                            <span x-show="!editingCustomer">新規顧客追加</span>
                            <span x-show="editingCustomer">顧客情報編集</span>
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">会社名 *</label>
                                <input 
                                    type="text" 
                                    x-model="form.company_name"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                >
                                <p x-show="errors.company_name" class="mt-1 text-sm text-red-600" x-text="errors.company_name"></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">担当者名</label>
                                <input 
                                    type="text" 
                                    x-model="form.contact_name"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">電話番号 *</label>
                                <input 
                                    type="tel" 
                                    x-model="form.phone"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                >
                                <p x-show="errors.phone" class="mt-1 text-sm text-red-600" x-text="errors.phone"></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">業界</label>
                                <select 
                                    x-model="form.industry"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">選択してください</option>
                                    <option value="IT">IT</option>
                                    <option value="製造">製造</option>
                                    <option value="金融">金融</option>
                                    <option value="不動産">不動産</option>
                                    <option value="小売">小売</option>
                                    <option value="その他">その他</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">企業規模</label>
                                <select 
                                    x-model="form.company_size"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">選択してください</option>
                                    <option value="大企業">大企業</option>
                                    <option value="中堅企業">中堅企業</option>
                                    <option value="中小企業">中小企業</option>
                                    <option value="スタートアップ">スタートアップ</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">備考</label>
                                <textarea 
                                    x-model="form.notes"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            type="submit"
                            :disabled="loading"
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
function customerList() {
    return {
        customers: [
            {
                id: 1,
                company_name: '㈱サンプル',
                contact_name: '田中様',
                phone: '03-1234-5678',
                industry: 'IT',
                company_size: '中堅企業',
                notes: 'システム開発に興味あり',
                call_count: 8,
                last_called: '2025-08-24'
            },
            {
                id: 2,
                company_name: 'ABC商事',
                contact_name: '佐藤様',
                phone: '03-2345-6789',
                industry: '製造',
                company_size: '大企業',
                notes: '',
                call_count: 3,
                last_called: '2025-08-23'
            },
            {
                id: 3,
                company_name: 'XYZ株式会社',
                contact_name: '鈴木様',
                phone: '03-3456-7890',
                industry: '金融',
                company_size: '大企業',
                notes: '',
                call_count: 5,
                last_called: '2025-08-22'
            },
            {
                id: 4,
                company_name: 'DEF企業',
                contact_name: '高橋様',
                phone: '03-4567-8901',
                industry: '不動産',
                company_size: '中小企業',
                notes: '',
                call_count: 2,
                last_called: '2025-08-21'
            },
            {
                id: 5,
                company_name: 'GHI会社',
                contact_name: '伊藤様',
                phone: '03-5678-9012',
                industry: '小売',
                company_size: 'スタートアップ',
                notes: '',
                call_count: 1,
                last_called: '2025-08-20'
            }
        ],
        searchTerm: '',
        selectedIndustry: '',
        selectedSize: '',
        loading: false,
        showModal: false,
        editingCustomer: null,
        submitting: false,
        currentPage: 1,
        itemsPerPage: 20,
        
        form: {
            company_name: '',
            contact_name: '',
            phone: '',
            industry: '',
            company_size: '',
            notes: ''
        },
        
        errors: {},
        
        get filteredCustomers() {
            return this.customers.filter(customer => {
                const matchesSearch = !this.searchTerm || 
                    customer.company_name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                    (customer.contact_name && customer.contact_name.toLowerCase().includes(this.searchTerm.toLowerCase()));
                
                const matchesIndustry = !this.selectedIndustry || customer.industry === this.selectedIndustry;
                const matchesSize = !this.selectedSize || customer.company_size === this.selectedSize;
                
                return matchesSearch && matchesIndustry && matchesSize;
            });
        },
        
        get paginatedCustomers() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredCustomers.slice(start, end);
        },
        
        get totalPages() {
            return Math.ceil(this.filteredCustomers.length / this.itemsPerPage);
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
        
        openModal() {
            this.showModal = true;
            this.resetForm();
        },
        
        closeModal() {
            this.showModal = false;
            this.editingCustomer = null;
            this.resetForm();
        },
        
        resetForm() {
            this.form = {
                company_name: '',
                contact_name: '',
                phone: '',
                industry: '',
                company_size: '',
                notes: ''
            };
            this.errors = {};
        },
        
        editCustomer(customer) {
            this.editingCustomer = customer;
            this.form = { ...customer };
            this.showModal = true;
        },
        
        async submitForm() {
            this.submitting = true;
            this.errors = {};
            
            try {
                // バリデーション
                if (!this.form.company_name.trim()) {
                    this.errors.company_name = '会社名は必須です';
                }
                if (!this.form.phone.trim()) {
                    this.errors.phone = '電話番号は必須です';
                }
                
                if (Object.keys(this.errors).length > 0) {
                    return;
                }
                
                // 実際のAPIコールの代わりにモックデータ
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                if (this.editingCustomer) {
                    // 編集
                    const index = this.customers.findIndex(c => c.id === this.editingCustomer.id);
                    if (index !== -1) {
                        this.customers[index] = { ...this.form, id: this.editingCustomer.id };
                    }
                } else {
                    // 新規追加
                    const newCustomer = {
                        ...this.form,
                        id: Math.max(...this.customers.map(c => c.id)) + 1,
                        call_count: 0,
                        last_called: null
                    };
                    this.customers.push(newCustomer);
                }
                
                this.closeModal();
                
            } catch (error) {
                console.error('Error submitting form:', error);
                this.errors.general = 'エラーが発生しました';
            } finally {
                this.submitting = false;
            }
        },
        
        async deleteCustomer(customerId) {
            if (!confirm('この顧客を削除しますか？')) {
                return;
            }
            
            try {
                // 実際のAPIコールの代わりにモックデータ
                this.customers = this.customers.filter(c => c.id !== customerId);
            } catch (error) {
                console.error('Error deleting customer:', error);
                alert('削除に失敗しました');
            }
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
            // 検索・フィルター変更時にページをリセット
            this.$watch('searchTerm', () => this.currentPage = 1);
            this.$watch('selectedIndustry', () => this.currentPage = 1);
            this.$watch('selectedSize', () => this.currentPage = 1);
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection