# 基本要件定義 - TelSupo

テレアポ業務効率化システムの核心機能

## システム概要

**目的**: テレアポ業務の効率化とパフォーマンス向上  
**対象ユーザー**: テレアポ営業担当者、営業管理者  
**コンセプト**: "架電業務を見える化し、効率的なテレアポ営業を支援"

## 基本機能要件（Phase 1）

### 1. 認証・ユーザー管理 🔐
- ユーザー登録・ログイン（Laravel Breeze）
- パスワードリセット機能
- セッション管理

### 2. ダッシュボード 📊
**今日のKPI表示**
- 今日の架電数（リアルタイム更新）
- 週次目標に対する進捗率
- 架電成功率
- 今週の架電数推移グラフ

**クイックアクション**
- 新規架電記録ボタン
- 顧客検索バー
- 今日のタスク表示

### 3. 顧客管理 👥
**顧客情報**
- 会社名、担当者名、電話番号
- 業界、企業規模
- 備考（メモ）

**機能**
- 顧客一覧表示（ページネーション）
- 新規顧客追加
- 顧客情報編集
- 顧客検索（会社名、担当者名）

### 4. 架電履歴管理 📞
**架電記録**
- 架電日時（自動記録）
- 対象顧客
- 架電結果（接触成功/失敗/アポ獲得/その他）
- 次回架電予定日
- 架電メモ

**機能**
- 架電履歴一覧
- 新規架電記録追加
- 架電結果の統計表示
- 今日の架電履歴表示

### 5. KPI管理 🎯
**目標設定**
- 週次架電目標数
- 月次架電目標数
- アポ獲得目標数

**進捗表示**
- 目標達成率（週次/月次）
- 架電数推移グラフ
- 成功率の推移

## データモデル

### User（ユーザー）
```
- id: UUID
- name: 氏名
- email: メールアドレス
- password: パスワード
- created_at, updated_at
```

### Customer（顧客）
```
- id: UUID
- company_name: 会社名 [必須]
- contact_name: 担当者名
- phone: 電話番号 [必須]
- industry: 業界
- company_size: 企業規模
- notes: 備考
- user_id: 担当ユーザー [外部キー]
- created_at, updated_at
```

### CallLog（架電履歴）
```
- id: UUID
- customer_id: 顧客ID [外部キー]
- user_id: 架電者ID [外部キー]
- called_at: 架電日時
- result: 架電結果 [enum]
- next_call_date: 次回架電予定日
- notes: 架電メモ
- created_at, updated_at
```

### KpiTarget（KPI目標）
```
- id: UUID
- user_id: ユーザーID [外部キー]
- target_type: 目標期間 [weekly/monthly]
- target_date: 対象日付
- call_target: 架電目標数
- appointment_target: アポ目標数
- created_at, updated_at
```

## 画面構成

### 1. ダッシュボード（/dashboard）
- KPIカード（今日の架電数、進捗率等）
- 週次架電数グラフ（Chart.js）
- 今日の架電履歴（最新5件）
- クイックアクションボタン

### 2. 顧客管理（/customers）
- 顧客一覧テーブル
- 検索フォーム
- 新規顧客追加ボタン
- 編集・詳細表示機能

### 3. 架電履歴（/call-logs）
- 架電履歴一覧テーブル
- フィルタ機能（日付、結果別）
- 新規架電記録ボタン
- 統計情報表示

### 4. KPI管理（/kpi）
- 目標設定フォーム
- 進捗グラフ（週次/月次）
- 達成率表示

## ビジネスルール

### 架電結果の分類
```php
enum CallResult: string {
    case SUCCESS = 'success';        // 接触成功
    case NO_ANSWER = 'no_answer';    // 不在・応答なし  
    case BUSY = 'busy';              // 話中
    case APPOINTMENT = 'appointment'; // アポ獲得
    case NOT_INTERESTED = 'not_interested'; // 興味なし
    case CALLBACK = 'callback';      // 折り返し希望
}
```

### KPI計算ロジック
- **架電成功率** = 接触成功数 ÷ 総架電数 × 100
- **アポ獲得率** = アポ獲得数 ÷ 接触成功数 × 100
- **目標達成率** = 実績値 ÷ 目標値 × 100

### データ検証
- 同一顧客への同日重複架電は警告表示
- 架電結果が「アポ獲得」の場合、次回架電日必須
- 週次目標は月曜開始、月次目標は1日開始

## 技術要件

### フロントエンド
- **TailwindCSS**: レスポンシブ対応、統一デザイン
- **AlpineJS**: フォーム処理、モーダル、リアルタイム更新
- **Chart.js**: KPIグラフ表示

### バックエンド
- **Laravel 12.0**: MVC + Service Layer
- **SQLite**: 開発環境（本番はMySQL/PostgreSQL）
- **Laravel Breeze**: 認証システム

### パフォーマンス
- ページネーション（20件/ページ）
- Eager Loading（N+1問題対策）
- インデックス最適化

## セキュリティ要件

- CSRF保護（全フォーム）
- XSS対策（出力エスケープ）
- SQL Injection対策（Eloquent ORM使用）
- 認証必須（全ページ）
- パスワード暗号化（bcrypt）

この要件定義に基づいて段階的に開発を進めます。