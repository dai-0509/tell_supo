# CLAUDE.md

このファイルは、Claude Code（claude.ai/code）がTellSupoプロジェクトで作業する際のガイダンスを提供します。

## プロジェクト概要

TellSupo（テルサポ）は、テレアポ業務の効率化とパフォーマンス向上を目的とした、KPI管理・顧客管理・架電履歴管理を統合したWebダッシュボードアプリケーションです。

**コンセプト**: “架電業務を見える化し、効率的なテレアポ営業を支援”

## アーキテクチャ

このプロジェクトは**MVCアーキテクチャ + Service Layer**に従います。

```
telsupo/
├── app/ # Laravel App Directory
│   ├── Http/
│   │   ├── Controllers/ # コントローラー層
│   │   ├── Middleware/
│   │   └── Requests/ # フォームリクエスト
│   ├── Models/ # Eloquent Models（ドメイン）
│   ├── Services/ # ビジネスロジック層
│   ├── Repositories/ # データアクセス層（Interface）
│   └── Providers/
├── database/
│   ├── migrations/ # データベース定義
│   ├── seeders/ # 初期データ
│   └── factories/ # テストデータ生成
├── resources/
│   ├── views/ # Blade Templates
│   ├── js/ # Vanilla JavaScript + AlpineJS
│   └── css/ # TailwindCSS + カスタムCSS
├── routes/
│   ├── web.php # Web Routes
│   └── api.php # API Routes
├── storage/
│   ├── app/public/ # アップロードファイル
│   └── logs/ # ログファイル
├── tests/
│   ├── Feature/ # 機能テスト
│   └── Unit/ # 単体テスト
└── public/
    ├── css/ # コンパイル済みCSS
    ├── js/ # コンパイル済みJS
    └── images/ # 静的画像
```

### 主要な設計パターン

1. **Repository Pattern**: データアクセスの抽象化
1. **Service Layer Pattern**: ビジネスロジックの分離
1. **Request/Response Pattern**: バリデーションとAPI統一
1. **Factory Pattern**: テストデータ生成
1. **Observer Pattern**: モデルイベント処理

## 開発コマンド

```bash
# 開発環境起動
php artisan serve
npm run dev # TailwindCSS + Vite

# データベース関連
php artisan migrate # マイグレーション実行
php artisan migrate:fresh --seed # 初期化+シーディング
php artisan db:seed # シーディングのみ

# テスト実行
php artisan test # 全テスト実行
php artisan test --filter=CustomerTest # 特定テスト実行

# キャッシュクリア
php artisan cache:clear
php artisan route:clear
php artisan config:clear

# アセット管理
npm run build # 本番用ビルド
npm run watch # ファイル監視
```

## テスト戦略

このプロジェクトは**Feature-Driven Testing**に従います。

```
tests/
├── Feature/ # エンドポイントと画面のテスト（80%カバレッジ目標）
│   ├── DashboardTest.php # ダッシュボード機能
│   ├── CustomerTest.php # 顧客管理
│   ├── CallLogTest.php # 架電履歴
│   └── ScriptTest.php # スクリプト管理
├── Unit/ # サービス・モデルの単体テスト
│   ├── Services/
│   └── Models/
└── Database/
    ├── factories/ # ファクトリ定義
    └── seeders/ # テスト用シーダー
```

優先度「高」の機能は必ずテストを作成します。

## 環境セットアップ

1. `.env.example`を`.env`にコピー
1. アプリケーションキー生成：

```bash
php artisan key:generate
```

1. データベース設定（SQLite使用）：

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

1. 初期セットアップ：

```bash
touch database/database.sqlite
php artisan migrate:fresh --seed
npm install && npm run dev
```

## コアビジネスエンティティ

- **Customer**: テレアポ対象の顧客情報
- **CallLog**: 架電履歴と結果
- **KpiTarget**: 週次・月次の目標設定
- **Script**: 架電用スクリプトテンプレート
- **User**: システム利用者（営業担当者）

## API設計

統一されたJSON APIレスポンス形式：

```json
{
  "success": true,
  "data": { ... },
  "message": "操作が完了しました",
  "meta": {
    "current_page": 1,
    "total": 100
  }
}
```

エラーレスポンス：

```json
{
  "success": false,
  "message": "エラーが発生しました",
  "errors": {
    "field_name": ["バリデーションエラーメッセージ"]
  },
  "error_code": "VALIDATION_ERROR"
}
```

## 重要な設計上の決定

1. **使いやすさ最優先**: 直感的なUI、3クリック以内での操作完了
1. **リアルタイム更新**: 架電数の即座の反映、プログレスバー更新
1. **データ整合性**: 外部キー制約、バリデーション、トランザクション処理
1. **拡張性**: 段階的機能追加、モジュラー設計
1. **パフォーマンス**: ページネーション、Eager Loading、インデックス最適化

## データベース設計原則

- 全テーブルにUUID主キー使用（`id`)
- `created_at`, `updated_at`を全テーブルに設定
- ソフトデリート対応（`deleted_at`）
- 適切な外部キー制約とインデックス設定
- Enum型での状態管理（ステータス、優先度等）

## フロントエンド設計方針

- **TailwindCSS**: ユーティリティファースト、レスポンシブ対応
- **AlpineJS**: リアクティブなUI（モーダル、フォーム、カウンター）
- **Chart.js**: KPIグラフとパフォーマンス可視化
- **Vanilla JavaScript**: 軽量なインタラクション
- **Progressive Enhancement**: 基本機能はサーバーサイド、拡張はJS

## 避けるべき一般的な落とし穴

1. **N+1問題**: 必ずEager Loadingを使用（`with()`）
1. **SQL Injection**: クエリビルダーかEloquent ORMを使用
1. **CSRF攻撃**: 全フォームに`@csrf`ディレクティブ
1. **XSS攻撃**: ユーザー入力は必ず`{{ $variable }}`でエスケープ
1. **過度な複雑化**: 最初はシンプルに、必要になってから抽象化
1. **テストなし開発**: 優先度「高」機能は必ずテスト作成
1. **DB直接操作**: 必ずマイグレーション経由でスキーマ変更

## プロジェクトドキュメントガイド

プロジェクトには`.claude/`ディレクトリに包括的なドキュメントがあります。各ドキュメントをいつ参照すべきかを示します。

### 📋 プロジェクト要件とコンセプト

- **`.claude/00_project/01_requirements_specification.md`** - 完全な要件定義書
- **`.claude/00_project/02_inception_deck.md`** - プロジェクトビジョンと目標設定
- **使用する場面**: 機能仕様の確認、ビジネスロジックの理解、要件の詳細確認時

### 🏗️ 技術設計ドキュメント（15個）

#### システムアーキテクチャ・設計

- **`.claude/01_development_docs/01_architecture_design.md`** - Laravel MVC + Service Layer詳細
- **`.claude/01_development_docs/02_database_design.md`** - ER図、テーブル定義、制約
- **`.claude/01_development_docs/03_api_design.md`** - REST API仕様、エンドポイント設計
- **使用する場面**: 新機能追加、データベース変更、API実装時

#### UI/UX設計

- **`.claude/01_development_docs/04_screen_transition_design.md`** - 画面遷移とUIフロー
- **`.claude/01_development_docs/10_frontend_design.md`** - Blade + TailwindCSS + AlpineJS設計
- **使用する場面**: 画面実装、UI改善、ユーザーフロー設計時

#### 品質・保守・運用

- **`.claude/01_development_docs/05_seo_requirements.md`** - SEO対策とメタタグ設計
- **`.claude/01_development_docs/06_error_handling_design.md`** - エラーハンドリング戦略
- **`.claude/01_development_docs/07_type_definitions.md`** - データ型定義、バリデーションルール
- **`.claude/01_development_docs/08_development_setup.md`** - 開発環境構築手順
- **`.claude/01_development_docs/09_test_strategy.md`** - テスト戦略とTDD実装
- **使用する場面**: 開発環境構築、テスト作成、エラー対応、SEO改善時

#### デプロイ・監視・セキュリティ

- **`.claude/01_development_docs/11_cicd_design.md`** - CI/CDパイプライン（GitHub Actions等）
- **`.claude/01_development_docs/12_e2e_test_design.md`** - E2Eテスト設計（Dusk等）
- **`.claude/01_development_docs/13_security_design.md`** - セキュリティ設計（認証、認可、脆弱性対策）
- **`.claude/01_development_docs/14_performance_optimization.md`** - パフォーマンス最適化戦略
- **`.claude/01_development_docs/15_performance_monitoring.md`** - パフォーマンス監視とアラート設計
- **使用する場面**: 本番デプロイ、パフォーマンス改善、セキュリティ監査時

### 🎨 デザインシステム（5個）

- **`.claude/02_design_system/00_basic_design.md`** - デザインシステム概要とクイックスタート
- **`.claude/02_design_system/01_design_principles.md`** - カラーパレット、タイポグラフィ、スペーシング
- **`.claude/02_design_system/02_component_design.md`** - UIコンポーネント設計（ボタン、カード、フォーム等）
- **`.claude/02_design_system/03_animation_system.md`** - アニメーション設計（Chart.js、プログレスバー）
- **`.claude/02_design_system/04_layout_system.md`** - レイアウトシステム、グリッド設計
- **使用する場面**: UI実装、デザイン統一、コンポーネント作成時

### 📚 ライブラリ・技術ドキュメント（4個）

- **`.claude/03_library_docs/01_laravel_best_practices.md`** - Laravel 12.0ベストプラクティス集
- **`.claude/03_library_docs/02_tailwindcss_patterns.md`** - TailwindCSS実装パターン
- **`.claude/03_library_docs/03_alpinejs_integration.md`** - AlpineJS + Blade統合パターン
- **`.claude/03_library_docs/04_chartjs_implementation.md`** - Chart.js実装ガイド（KPIダッシュボード用）
- **使用する場面**: ライブラリ実装、フレームワーク最適化、技術調査時

### クイックリファレンスマップ

|タスク          |主要ドキュメント                       |
|-------------|-------------------------------|
|新機能の追加       |要件定義 → アーキテクチャ → データベース → API設計|
|データベース変更     |データベース設計 → マイグレーション → テスト戦略    |
|API実装        |API設計 → エラーハンドリング → テスト戦略      |
|画面実装         |画面遷移設計 → フロントエンド設計 → デザインシステム  |
|TailwindCSS実装|TailwindCSS パターン → コンポーネント設計   |
|AlpineJS実装   |AlpineJS統合 → フロントエンド設計         |
|Chart.js実装   |Chart.js実装ガイド → KPIダッシュボード要件   |
|テスト作成        |テスト戦略 → Laravel ベストプラクティス      |
|パフォーマンス改善    |パフォーマンス最適化 → 監視設計              |
|セキュリティ実装     |セキュリティ設計 → Laravel ベストプラクティス   |
|デプロイ準備       |CI/CD設計 → 開発セットアップ             |
|エラー対応        |エラーハンドリング → ログ分析               |

## プロジェクトの進め方（開発フェーズ）

### Phase 1: 基盤構築（優先度：高の機能）

1. 認証システム（Laravel Breeze）
1. 基本的なダッシュボード画面
1. 顧客管理（一覧、追加、編集）
1. 架電履歴管理（記録、一覧）
1. KPI管理（週次目標、今日の架電メータ）

### Phase 2: 機能拡張（優先度：中の機能）

1. 検索・フィルタリング機能
1. Chart.jsによるグラフ表示
1. CSVインポート・エクスポート
1. スクリプト管理

### Phase 3: 最適化・運用準備（優先度：低の機能）

1. パフォーマンス最適化
1. SEO対策
1. 本番デプロイ環境整備
1. 監視・ログ分析

このCLAUDE.mdファイルは、開発時の司令塔として機能し、各種ドキュメントへの適切な参照を提供します。