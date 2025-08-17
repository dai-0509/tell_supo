# Architecture Design - TellSupo（テルサポ）

**作成日**: 2025年7月31日  
**バージョン**: 1.0  
**対象**: Claude Code実装 + Laravel学習最適化

-----

## 🎯 設計方針

### 戦略的判断
- **開発スピード重視**: MVP優先、段階的機能拡張
- **セキュリティレベルB**: 企業標準（暗号化・ログ・バックアップ）
- **Claude Code連携**: 明確な実装指示書として機能
- **Laravel学習重視**: 実務レベルの深掘り学習機会

### アーキテクチャ原則
1. **実装可能性優先**: 理想より実現可能な設計
2. **段階的成長**: MVP → 機能拡張 → 最適化
3. **セキュリティバランス**: 必要十分なレベル
4. **学習効果最大化**: Laravel実装力向上

-----

## 🏗️ 全体アーキテクチャ

### システム構成図

```
┌─────────────────────────────────────────────────────────┐
│                    TellSupo System                      │
├─────────────────────────────────────────────────────────┤
│  Frontend Layer                                         │
│  ┌─────────────────┐  ┌──────────────────────────────┐  │
│  │  Blade Templates │  │     TailwindCSS + AlpineJS   │  │
│  │                 │  │                              │  │
│  │  - Dashboard    │  │  - Interactive Components   │  │
│  │  - Customer CRUD│  │  - Modal Management          │  │
│  │  - Call Logs    │  │  - Form Validation           │  │
│  │  - KPI Charts   │  │  - Progress Bars             │  │
│  └─────────────────┘  └──────────────────────────────┘  │
├─────────────────────────────────────────────────────────┤
│  Application Layer (Laravel 12.0)                      │
│  ┌─────────────────┐  ┌──────────────────────────────┐  │
│  │   Controllers   │  │         Services             │  │
│  │                 │  │                              │  │
│  │  - Dashboard    │  │  - CustomerService           │  │
│  │  - Customer     │  │  - CallLogService            │  │
│  │  - CallLog      │  │  - KpiCalculationService     │  │
│  │  - Auth         │  │  - CsvImportService          │  │
│  └─────────────────┘  └──────────────────────────────┘  │
├─────────────────────────────────────────────────────────┤
│  Data Layer                                             │
│  ┌─────────────────┐  ┌──────────────────────────────┐  │
│  │   Eloquent      │  │        Database              │  │
│  │   Models        │  │                              │  │
│  │                 │  │  - Encrypted Customer Data   │  │
│  │  - User         │  │  - Call History Logs         │  │
│  │  - Customer     │  │  - KPI Targets               │  │
│  │  - CallLog      │  │  - User Activity Logs        │  │
│  │  - KpiTarget    │  │  - System Audit Trail        │  │
│  └─────────────────┘  └──────────────────────────────┘  │
├─────────────────────────────────────────────────────────┤
│  Security Layer                                         │
│  ┌─────────────────────────────────────────────────────┐│
│  │  - Laravel Breeze Authentication                   ││
│  │  - Database Encryption (Laravel Crypt)             ││
│  │  - HTTPS/TLS Communication                         ││
│  │  - CSRF Protection                                 ││
│  │  - SQL Injection Prevention                        ││
│  │  - XSS Protection                                  ││
│  │  - Activity Logging                                ││
│  │  - Automated Backup System                         ││
│  └─────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────┘
```

-----

## 📁 ディレクトリ構成

### Laravel標準 + Service Layer拡張

```
telsupo/
├── app/
│   ├── Http/
│   │   ├── Controllers/              # コントローラー層
│   │   │   ├── DashboardController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── CallLogController.php
│   │   │   └── Auth/                 # 認証関連
│   │   ├── Middleware/               # カスタムミドルウェア
│   │   │   └── ActivityLogger.php    # 操作ログ記録
│   │   └── Requests/                 # フォームリクエスト
│   │       ├── CustomerRequest.php
│   │       └── CallLogRequest.php
│   ├── Models/                       # Eloquentモデル
│   │   ├── User.php
│   │   ├── Customer.php
│   │   ├── CallLog.php
│   │   └── KpiTarget.php
│   ├── Services/                     # ビジネスロジック層 ★学習重点
│   │   ├── CustomerService.php
│   │   ├── CallLogService.php
│   │   ├── KpiCalculationService.php
│   │   └── CsvImportService.php
│   ├── Repositories/ (将来拡張)      # データアクセス抽象化
│   └── Providers/
├── database/
│   ├── migrations/                   # データベーススキーマ
│   ├── seeders/                      # 初期データ・テストデータ
│   └── factories/                    # テストデータ生成
├── resources/
│   ├── views/                        # Blade Templates
│   │   ├── layouts/
│   │   │   └── app.blade.php         # 共通レイアウト
│   │   ├── dashboard/
│   │   │   └── index.blade.php       # KPIダッシュボード
│   │   ├── customers/
│   │   │   ├── index.blade.php       # 顧客一覧
│   │   │   ├── create.blade.php      # 顧客登録
│   │   │   └── edit.blade.php        # 顧客編集
│   │   └── call-logs/
│   │       ├── index.blade.php       # 架電履歴
│   │       └── create.blade.php      # 架電記録
│   ├── js/                           # JavaScript
│   │   ├── app.js                    # メインJS
│   │   ├── components/               # AlpineJSコンポーネント
│   │   │   ├── call-counter.js       # 架電カウンター
│   │   │   └── kpi-charts.js         # Chart.js実装
│   │   └── utils/                    # ユーティリティ
│   └── css/
│       ├── app.css                   # TailwindCSS設定
│       └── custom/                   # カスタムスタイル
├── storage/
│   ├── app/
│   │   ├── public/uploads/           # CSVアップロード
│   │   └── backups/                  # データベースバックアップ
│   └── logs/                         # アプリケーションログ
├── tests/
│   ├── Feature/                      # エンドポイントテスト
│   │   ├── DashboardTest.php
│   │   ├── CustomerTest.php
│   │   └── CallLogTest.php
│   └── Unit/                         # 単体テスト
│       └── Services/                 # サービス層テスト ★学習重点
└── public/
    ├── css/                          # コンパイル済みCSS
    ├── js/                           # コンパイル済みJS
    └── images/                       # 静的画像
```

-----

## 🔄 設計パターンと責任分担

### MVC + Service Layer パターン

#### Controller（コントローラー）
**責任範囲**:
- HTTPリクエスト/レスポンス処理
- 入力バリデーション（FormRequest使用）
- ビジネスロジックをServiceに委譲
- ビューへのデータ渡し

**実装方針**:
```php
// CustomerController例
class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}
    
    public function store(CustomerRequest $request)
    {
        $customer = $this->customerService->create($request->validated());
        return redirect()->route('customers.index')
            ->with('success', '顧客を登録しました');
    }
}
```

#### Service（サービス層）★Laravel学習重点
**責任範囲**:
- ビジネスロジックの実装
- 複数モデルの協調処理
- 外部サービス連携
- データ変換・計算処理

**学習ポイント**:
- 依存性注入の活用
- トランザクション管理
- 例外処理設計
- テスト可能な設計

#### Model（Eloquentモデル）
**責任範囲**:
- データベースとの対応関係
- リレーション定義
- アクセサ・ミューテータ
- バリデーションルール

#### View（Bladeテンプレート）
**責任範囲**:
- HTMLレンダリング
- TailwindCSSでのスタイリング
- AlpineJSでの基本的なインタラクション

-----

## 🗄️ データフロー設計

### 1. 顧客管理フロー
```
Excel CSV → CSVアップロード → バリデーション → データベース保存
                                     ↓
                          重複チェック → 既存データ更新/新規作成
```

### 2. 架電記録フロー
```
架電実施 → 記録フォーム入力 → バリデーション → CallLogService
                                      ↓
                               関連Customer更新 → KPI自動計算
```

### 3. KPI計算フロー
```
CallLog追加/更新 → KpiCalculationService → 集計処理 → ダッシュボード更新
        ↓                    ↓              ↓
   イベント発火 → リアルタイム更新 → Chart.js再描画
```

-----

## 🛡️ セキュリティレベルB実装

### 必須セキュリティ機能

#### 1. 認証・認可
```php
// Laravel Breeze + カスタム強化
- パスワード強度要求（8文字以上、英数字+記号）
- セッションタイムアウト（2時間）
- ログイン試行回数制限
- パスワードリセット機能
```

#### 2. データ暗号化
```php
// 機密データの暗号化
- 顧客電話番号: encrypted cast使用
- メールアドレス: encrypted cast使用
- 通話メモ: encrypted cast使用
```

#### 3. 操作ログ記録
```php
// ActivityLoggerミドルウェア
- 全CRUD操作のログ記録
- ユーザー識別情報の記録
- IP アドレス・User Agent記録
- 異常操作の検知・アラート
```

#### 4. データ保護
```php
// 自動バックアップシステム
- 日次データベースバックアップ
- ファイルアップロードのウイルススキャン
- データ保持期間設定（デフォルト3年）
```

-----

## 🚀 Claude Code実装指示

### フェーズ1: 基盤構築（予想8-12時間）

#### 1.1 Laravel セットアップ
```bash
# 実行コマンド
composer create-project laravel/laravel telsupo "12.*"
cd telsupo
composer require laravel/breeze
php artisan breeze:install blade
```

#### 1.2 データベース設計・実装
- マイグレーションファイル作成
- Eloquentモデル実装（リレーション含む）
- ファクトリー・シーダー作成

#### 1.3 認証システム強化
- Laravel Breeze基本設定
- パスワード強度バリデーション追加
- セッション設定調整

### フェーズ2: コア機能実装（予想15-20時間）

#### 2.1 顧客管理機能
- CustomerController + Service実装
- CRUD画面（Blade + TailwindCSS）
- 検索・フィルタリング機能

#### 2.2 架電履歴管理
- CallLogController + Service実装
- 記録フォーム + 一覧画面
- KPI自動計算連携

#### 2.3 ダッシュボード機能
- KpiCalculationService実装
- Chart.js グラフ表示
- リアルタイム更新（AlpineJS）

### フェーズ3: 拡張機能（予想8-12時間）

#### 3.1 CSV機能
- インポート・エクスポート機能
- バリデーション・エラーハンドリング

#### 3.2 セキュリティ強化
- 操作ログ機能
- データ暗号化実装
- バックアップシステム

-----

## 📋 Claude Code連携チェックリスト

### 実装前確認事項
- [ ] 要件定義の詳細確認
- [ ] データベース設計の最終確認
- [ ] セキュリティ要件の具体化
- [ ] UI/UXデザインの方向性確認

### 実装中確認事項
- [ ] Laravel ベストプラクティス準拠
- [ ] セキュリティ対策の実装確認
- [ ] パフォーマンス考慮（N+1問題等）
- [ ] テスト可能な設計

### 実装後確認事項
- [ ] 機能テスト実施
- [ ] セキュリティテスト実施
- [ ] パフォーマンステスト実施
- [ ] ユーザビリティ確認

-----

## 🎯 Laravel学習重点ポイント

### Service Layer実装での学習目標
1. **依存性注入**: コンストラクタインジェクション活用
2. **トランザクション**: DB::transaction使用方法
3. **イベント処理**: Eloquent Events活用
4. **例外処理**: カスタム例外クラス作成
5. **テスト設計**: Service層の単体テスト

### 実装レビュー観点
- コードの可読性・保守性
- SOLID原則の適用
- Laravel規約への準拠
- セキュリティ考慮
- パフォーマンス最適化

-----

## 🔄 拡張性設計

### 将来的な機能拡張対応
- **API化**: REST API提供準備
- **マルチテナント**: 企業別データ分離
- **権限管理**: 役割ベースアクセス制御
- **レポート機能**: PDF出力・メール送信
- **外部連携**: CRM・SFA連携API

### スケーラビリティ考慮
- **データベース**: 読み取り専用レプリカ対応準備
- **キャッシュ**: Redis導入準備
- **ファイルストレージ**: S3対応準備
- **ログ管理**: ELKスタック対応準備

-----

**この Architecture Design は、Claude Codeへの明確な実装指示と、Laravel学習の最適化を両立させる設計書として機能します。**