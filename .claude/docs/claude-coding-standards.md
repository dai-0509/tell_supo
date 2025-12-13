# Claude コーディング規約（TellSupo）

最終更新: 2025-11-04  
適用範囲: TellSupo全体

## 🎯 基本原則

### コード品質
- **PSR-12準拠** - Laravel Pintで自動適用
- **型宣言必須** - 引数・戻り値に型を明記
- **早期リターン** - ネストを避ける
- **単一責任** - 1クラス1責務

### パフォーマンス
- **N+1クエリ禁止** - 必ずwith句使用
- **インデックス必須** - 検索カラムは全てインデックス
- **ページネーション** - 一覧は必ずpaginate()

### データベース設計
- **IDカラム命名**: `{テーブル名}_id` 形式で可読性を確保
  - 例: `customer_id`, `call_log_id`, `kpi_target_id`
  - 理由: JOIN時やログでどのテーブルのIDか即座に判別可能
- **外部キー命名**: 参照先テーブル名を含める
  - 例: `user_id`, `customer_id` (users.id, customers.idを参照)
- **複合ユニークキー**: 意味のある名前を付ける
  - 例: `unique_user_active_target` (user_id + is_active + effective_from)

## 📁 ディレクトリ構成

```
app/
├── Http/
│   ├── Controllers/          # 薄いController
│   │   ├── CustomerController.php
│   │   └── CallLogController.php
│   ├── Requests/            # FormRequest
│   │   ├── Customer/
│   │   │   ├── StoreCustomerRequest.php
│   │   │   └── UpdateCustomerRequest.php
│   │   └── CallLog/
│   └── Middleware/
├── Models/                  # Eloquentモデル
│   ├── Customer.php
│   ├── CallLog.php
│   └── Traits/
├── Services/               # ビジネスロジック
│   ├── CustomerService.php
│   ├── CallLogService.php
│   └── KpiService.php
└── Enums/                  # 定数定義
    ├── CallResult.php
    └── CustomerStatus.php
```

## 🏗️ アーキテクチャパターン

### Controller（薄く）
```php
<?php

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $this->customerService->create($request->validated());
        
        return redirect()
            ->route('customers.show', $customer)
            ->with('success', '顧客を登録しました');
    }
}
```

### Service（厚く）
```php
<?php

class CustomerService
{
    public function create(array $data): Customer
    {
        // 電話番号正規化
        $data['phone'] = $this->normalizePhone($data['phone']);
        
        DB::beginTransaction();
        try {
            $customer = Customer::create($data);
            
            // 関連処理
            $this->createInitialCallLog($customer);
            
            DB::commit();
            return $customer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    private function normalizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
```

### FormRequest
```php
<?php

class StoreCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'unique:customers,phone'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => '顧客名は必須です',
            'phone.unique' => 'この電話番号は既に登録されています',
            'email.email' => '有効なメールアドレスを入力してください',
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => preg_replace('/[^0-9]/', '', $this->phone),
        ]);
    }
}
```

## 🗄️ データベース規約

### マイグレーション
```php
<?php

Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('phone', 20)->unique();
    $table->string('email')->unique();
    $table->string('address', 500)->nullable();
    $table->text('notes')->nullable();
    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
    
    // インデックス
    $table->index(['status', 'created_at']);
    $table->index('name');
});
```

### モデル定義
```php
<?php

class Customer extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'address', 'notes', 'status'
    ];
    
    protected $casts = [
        'status' => CustomerStatus::class,
        'created_at' => 'datetime:Y-m-d H:i',
    ];
    
    // リレーション
    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }
    
    // スコープ
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CustomerStatus::ACTIVE);
    }
    
    public function scopeSearchByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}
```

## 🧪 テスト規約

### Feature Test
```php
<?php

class CustomerTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_customer(): void
    {
        $data = [
            'name' => '田中太郎',
            'phone' => '090-1234-5678',
            'email' => 'tanaka@example.com',
        ];
        
        $response = $this->post(route('customers.store'), $data);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'name' => '田中太郎',
            'phone' => '09012345678', // ハイフン除去確認
            'email' => 'tanaka@example.com',
        ]);
    }
    
    public function test_cannot_create_customer_with_duplicate_phone(): void
    {
        Customer::factory()->create(['phone' => '09012345678']);
        
        $data = [
            'name' => '田中次郎',
            'phone' => '090-1234-5678',
            'email' => 'jiro@example.com',
        ];
        
        $response = $this->post(route('customers.store'), $data);
        
        $response->assertSessionHasErrors('phone');
        $this->assertDatabaseMissing('customers', ['name' => '田中次郎']);
    }
}
```

### Factory
```php
<?php

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->unique()->numerify('0#########'),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'notes' => $this->faker->optional()->realText(200),
            'status' => CustomerStatus::ACTIVE,
        ];
    }
}
```

## 🎨 Blade Template規約

### レイアウト継承
```php
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TellSupo')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        @yield('content')
    </div>
</body>
</html>
```

### フォーム共通化
```php
<!-- resources/views/customers/form.blade.php -->
<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">
            顧客名 <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            name="name" 
            id="name" 
            value="{{ old('name', $customer->name ?? '') }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            required
        >
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
```

## 📊 パフォーマンス規約

### N+1クエリ対策
```php
// ❌ NG - N+1発生
$customers = Customer::all();
foreach ($customers as $customer) {
    echo $customer->callLogs->count();
}

// ✅ OK - 事前ロード
$customers = Customer::withCount('callLogs')->get();
foreach ($customers as $customer) {
    echo $customer->call_logs_count;
}

// ✅ OK - リレーション込み
$customers = Customer::with(['callLogs' => function ($query) {
    $query->latest()->limit(5);
}])->get();
```

### インデックス戦略
```php
// 複合インデックス（順序重要）
$table->index(['status', 'created_at']);  // status -> created_at の順

// 部分インデックス
$table->index('phone')->where('deleted_at', null);

// ユニークインデックス
$table->unique(['email', 'deleted_at']);
```

## 🚨 エラーハンドリング

### 例外処理
```php
public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
{
    DB::beginTransaction();
    
    try {
        $this->customerService->update($customer, $request->validated());
        
        DB::commit();
        
        return redirect()
            ->route('customers.show', $customer)
            ->with('success', '顧客情報を更新しました');
            
    } catch (ValidationException $e) {
        DB::rollBack();
        throw $e;
        
    } catch (Exception $e) {
        DB::rollBack();
        
        Log::error('Customer update failed', [
            'customer_id' => $customer->id,
            'error' => $e->getMessage(),
        ]);
        
        return redirect()
            ->back()
            ->withInput()
            ->with('error', '更新に失敗しました');
    }
}
```

## 📏 命名規約

### クラス名
- Controller: `CustomerController`
- Model: `Customer`
- Request: `StoreCustomerRequest`, `UpdateCustomerRequest`
- Service: `CustomerService`
- Enum: `CustomerStatus`

### メソッド名
- CRUD: `index`, `show`, `create`, `store`, `edit`, `update`, `destroy`
- 取得: `getActiveCustomers`, `findByPhone`
- 判定: `isActive`, `hasCallLogs`
- 処理: `normalizePhone`, `calculateTotal`

### 変数名
```php
// ✅ OK
$customerCount = Customer::count();
$isActive = $customer->isActive();
$phoneNumber = $request->input('phone');

// ❌ NG
$cnt = Customer::count();
$flg = $customer->isActive();
$tel = $request->input('phone');
```

## 🔧 設定ファイル

### config/telsupo.php
```php
<?php

return [
    'pagination' => [
        'per_page' => 20,
        'max_per_page' => 100,
    ],
    
    'phone' => [
        'pattern' => '/^0\d{9,10}$/',
        'normalize' => true,
    ],
    
    'call_log' => [
        'max_duration' => 7200, // 2時間
        'timezone' => 'Asia/Tokyo',
    ],
];
```

## 🏷️ Git コミット規約

### コミットメッセージ
- **言語**: 日本語で記述
- **形式**: `type: 概要` + 詳細説明
- **文字数**: 概要は50文字以内、詳細は72文字で改行

### コミットタイプ
```
feat: 新機能追加
fix: バグ修正
docs: ドキュメント更新
style: コードスタイル修正
refactor: リファクタリング
test: テスト追加・修正
chore: その他の作業
```

### 例
```
feat: 顧客CRUD機能のバックエンド実装

- Customerモデルにリレーションとスコープを追加
- FormRequestでバリデーションと正規化を実装
- Controllerに認可付きCRUD操作を実装
- 日本語エラーメッセージに対応

バックエンド実装完了、UI開発準備完了
```

## ✅ チェックリスト

実装完了前に必ず確認：

- [ ] PSR-12準拠（Pint実行）
- [ ] 型宣言完備
- [ ] N+1クエリなし
- [ ] インデックス設定
- [ ] 日本語エラーメッセージ
- [ ] Feature/Unitテスト
- [ ] バリデーション網羅
- [ ] エラーハンドリング
- [ ] ログ出力適切
- [ ] 日本語コミットメッセージ