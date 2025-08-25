# UI実装ガイド - TelSupo

TailwindCSS + AlpineJS実装パターン集

## 基本構成

### レイアウト構造
```html
<!-- ベースレイアウト -->
<div class="min-h-screen bg-gray-50">
  <!-- ナビゲーション -->
  <nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- ナビ内容 -->
    </div>
  </nav>
  
  <!-- メインコンテンツ -->
  <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- ページ内容 -->
  </main>
</div>
```

### カラーパレット（ComDesk Lead参考）
```css
/* プライマリ（ComDesk風ブルー） */
--primary-50: #eff6ff;
--primary-500: #2563eb;
--primary-600: #1d4ed8;
--primary-700: #1e40af;

/* セカンダリ */
--gray-50: #f8fafc;
--gray-100: #f1f5f9;
--gray-200: #e2e8f0;
--gray-500: #64748b;
--gray-700: #334155;
--gray-900: #0f172a;

/* ステータス */
--success: #059669;  /* 成果表示用グリーン */
--warning: #d97706;
--error: #dc2626;
--info: #0284c7;

/* ComDesk風アクセント */
--accent-blue: #0ea5e9;
--accent-green: #10b981;
```

## コンポーネントパターン

### 1. 架電メーター（リアルタイム更新）
```html
<!-- 架電数メーター -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center" x-data="callMeter()">
  <h3 class="text-lg font-semibold text-gray-700 mb-4">今日の架電数</h3>
  
  <!-- メーター表示部分 -->
  <div class="relative w-48 h-48 mx-auto mb-6">
    <!-- 背景サークル -->
    <svg class="w-48 h-48 transform -rotate-90">
      <circle cx="96" cy="96" r="80" stroke="#e5e7eb" stroke-width="12" fill="none"></circle>
      <!-- 進捗サークル -->
      <circle 
        cx="96" cy="96" r="80" 
        stroke="#2563eb" stroke-width="12" fill="none"
        :stroke-dasharray="502.4"
        :stroke-dashoffset="502.4 - (callCount / dailyTarget * 502.4)"
        stroke-linecap="round"
        class="transition-all duration-500 ease-in-out">
      </circle>
    </svg>
    
    <!-- 中央の数値表示 -->
    <div class="absolute inset-0 flex flex-col items-center justify-center">
      <span class="text-5xl font-bold text-gray-900" x-text="callCount">42</span>
      <div class="text-sm text-gray-500 mt-2">
        <span>目標: </span><span x-text="dailyTarget">50</span><span>件</span>
      </div>
      <div class="text-lg font-semibold text-primary-600 mt-1">
        <span x-text="Math.round(callCount / dailyTarget * 100)">84</span><span>% 達成</span>
      </div>
    </div>
  </div>
  
  <!-- プラス・マイナスボタン -->
  <div class="flex items-center justify-center space-x-4">
    <button 
      @click="decrementCall()"
      :disabled="callCount <= 0"
      class="bg-red-100 hover:bg-red-200 disabled:opacity-50 disabled:cursor-not-allowed text-red-700 font-bold py-2 px-4 rounded-lg transition-colors"
    >
      ➖
    </button>
    
    <div class="text-sm text-gray-600 px-4">
      架電数調整<br>
      <span class="text-xs">（誤記録の修正・手動追加用）</span>
    </div>
    
    <button 
      @click="incrementCall()"
      class="bg-green-100 hover:bg-green-200 text-green-700 font-bold py-2 px-4 rounded-lg transition-colors"
    >
      ➕
    </button>
  </div>
  
  <!-- 最終更新時刻 -->
  <p class="text-xs text-gray-400 mt-4">
    最終更新: <span x-text="lastUpdated">14:45</span>
  </p>
</div>
```

### 2. ダッシュボードKPIカード（ComDesk風）
```html
<!-- パフォーマンス指標カード -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
  <div class="flex items-center justify-between">
    <div>
      <p class="text-sm font-medium text-gray-600">今日の架電数</p>
      <p class="text-3xl font-bold text-gray-900 mt-2">42</p>
      <p class="text-sm text-success mt-1">
        <span class="inline-flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
          </svg>
          +15% vs 昨日
        </span>
      </p>
    </div>
    <div class="p-3 bg-blue-50 rounded-lg">
      <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
      </svg>
    </div>
  </div>
</div>

<!-- 進捗率表示カード -->
<div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl shadow-sm text-white p-6">
  <div class="flex items-center justify-between">
    <div>
      <p class="text-primary-100 text-sm font-medium">週次目標達成率</p>
      <p class="text-3xl font-bold mt-2">78%</p>
    </div>
    <div class="text-right">
      <div class="w-20 h-20 relative">
        <!-- 円形プログレスバー -->
        <svg class="w-20 h-20 transform -rotate-90">
          <circle cx="40" cy="40" r="30" stroke="rgba(255,255,255,0.3)" stroke-width="6" fill="none"></circle>
          <circle cx="40" cy="40" r="30" stroke="white" stroke-width="6" fill="none" 
                  stroke-dasharray="188" stroke-dashoffset="41" stroke-linecap="round"></circle>
        </svg>
        <div class="absolute inset-0 flex items-center justify-center">
          <span class="text-lg font-bold">78</span>
        </div>
      </div>
    </div>
  </div>
</div>
```

### 2. データテーブル
```html
<div class="bg-white shadow overflow-hidden sm:rounded-md">
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          顧客名
        </th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          架電回数
        </th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      <tr>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
          株式会社サンプル
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          5回
        </td>
      </tr>
    </tbody>
  </table>
</div>
```

### 3. フォーム
```html
<form class="space-y-6" x-data="customerForm()">
  <div>
    <label class="block text-sm font-medium text-gray-700">
      会社名
    </label>
    <input 
      type="text" 
      x-model="form.company_name"
      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
    >
    <p x-show="errors.company_name" class="mt-2 text-sm text-red-600" x-text="errors.company_name"></p>
  </div>
  
  <div class="flex justify-end">
    <button 
      type="submit"
      class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
      :disabled="loading"
    >
      <span x-show="!loading">保存</span>
      <span x-show="loading">保存中...</span>
    </button>
  </div>
</form>
```

## AlpineJS実装パターン

### 1. 架電メーター管理
```javascript
function callMeter() {
  return {
    callCount: 42,           // 現在の架電数
    dailyTarget: 50,         // 日次目標
    lastUpdated: '14:45',    // 最終更新時刻
    
    // 架電数を増やす
    async incrementCall() {
      try {
        const response = await fetch('/api/calls/increment', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          this.callCount = data.total_calls;
          this.updateLastUpdated();
          this.showSuccess('架電数を追加しました');
        }
      } catch (error) {
        console.error('Error incrementing call:', error);
        this.showError('更新に失敗しました');
      }
    },
    
    // 架電数を減らす（誤記録修正用）
    async decrementCall() {
      if (this.callCount <= 0) return;
      
      try {
        const response = await fetch('/api/calls/decrement', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          this.callCount = data.total_calls;
          this.updateLastUpdated();
          this.showSuccess('架電数を修正しました');
        }
      } catch (error) {
        console.error('Error decrementing call:', error);
        this.showError('更新に失敗しました');
      }
    },
    
    // 最終更新時刻を更新
    updateLastUpdated() {
      const now = new Date();
      this.lastUpdated = now.toLocaleTimeString('ja-JP', { 
        hour: '2-digit', 
        minute: '2-digit' 
      });
    },
    
    // 成功メッセージ表示
    showSuccess(message) {
      // Toast通知やFlashメッセージの表示
      console.log('Success:', message);
    },
    
    // エラーメッセージ表示
    showError(message) {
      // エラー通知の表示
      console.error('Error:', message);
    },
    
    // 初期化時にデータを取得
    async init() {
      await this.loadTodaysCalls();
    },
    
    // 今日の架電数を取得
    async loadTodaysCalls() {
      try {
        const response = await fetch('/api/calls/today');
        const data = await response.json();
        this.callCount = data.total_calls;
        this.dailyTarget = data.daily_target;
      } catch (error) {
        console.error('Error loading calls:', error);
      }
    }
  }
}
```

### 2. データフォーム管理
```javascript
function customerForm() {
  return {
    form: {
      company_name: '',
      contact_name: '',
      phone: ''
    },
    errors: {},
    loading: false,
    
    async submitForm() {
      this.loading = true;
      this.errors = {};
      
      try {
        const response = await fetch('/customers', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(this.form)
        });
        
        const data = await response.json();
        
        if (!response.ok) {
          this.errors = data.errors || {};
        } else {
          // 成功処理
          window.location.href = '/customers';
        }
      } catch (error) {
        console.error('Error:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
```

### 2. モーダル管理
```javascript
function modal() {
  return {
    isOpen: false,
    
    open() {
      this.isOpen = true;
      document.body.classList.add('overflow-hidden');
    },
    
    close() {
      this.isOpen = false;
      document.body.classList.remove('overflow-hidden');
    }
  }
}
```

### 3. 検索・フィルタリング
```javascript
function customerList() {
  return {
    customers: [],
    searchTerm: '',
    loading: true,
    
    get filteredCustomers() {
      return this.customers.filter(customer => 
        customer.company_name.toLowerCase().includes(this.searchTerm.toLowerCase())
      );
    },
    
    async loadCustomers() {
      this.loading = true;
      try {
        const response = await fetch('/api/customers');
        this.customers = await response.json();
      } catch (error) {
        console.error('Error loading customers:', error);
      } finally {
        this.loading = false;
      }
    },
    
    init() {
      this.loadCustomers();
    }
  }
}
```

## レスポンシブ対応

### ブレークポイント
- `sm`: 640px以上
- `md`: 768px以上  
- `lg`: 1024px以上
- `xl`: 1280px以上

### モバイルファースト実装
```html
<!-- モバイル: 1列, タブレット: 2列, デスクトップ: 3列 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  <!-- カード -->
</div>

<!-- モバイルでスタック、デスクトップで横並び -->
<div class="flex flex-col lg:flex-row gap-4">
  <div class="lg:w-2/3">メインコンテンツ</div>
  <div class="lg:w-1/3">サイドバー</div>
</div>
```

## Chart.js実装

### KPIダッシュボード用グラフ
```javascript
// 週次架電数グラフ
const weeklyCallsChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['月', '火', '水', '木', '金'],
    datasets: [{
      label: '架電数',
      data: [12, 19, 15, 25, 22],
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        display: false
      }
    },
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
```

## パフォーマンス最適化

### 遅延読み込み
```javascript
// 画像の遅延読み込み
function lazyLoad() {
  return {
    loaded: false,
    load() {
      this.loaded = true;
    }
  }
}
```

### 無限スクロール
```javascript
function infiniteScroll() {
  return {
    page: 1,
    hasMore: true,
    loading: false,
    
    async loadMore() {
      if (this.loading || !this.hasMore) return;
      
      this.loading = true;
      // データ読み込み処理
      this.page++;
      this.loading = false;
    },
    
    checkScroll() {
      if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 1000) {
        this.loadMore();
      }
    }
  }
}
```

## アクセシビリティ

### 基本対応
```html
<!-- フォーカス可能要素 -->
<button 
  class="focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
  aria-label="顧客を追加"
>

<!-- スクリーンリーダー対応 -->
<div aria-live="polite" x-text="statusMessage"></div>

<!-- キーボードナビゲーション -->
<div 
  @keydown.escape="close()"
  @keydown.enter="submit()"
>
```

これらのパターンを組み合わせて、一貫性のあるUIを構築してください。