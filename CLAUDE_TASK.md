# CLAUDE_TASK.md — F006: KPI管理画面実装

## 固定前提（共通）
前提: Laravel 11 / PHP 8.2 / MySQL 8 / Blade+Tailwind / JST。Controller薄く。N+1禁止。
出力順: ①差分 ②新規 ③実行コマンド ④テスト ⑤自己チェック。疑似コード禁止。

## このブランチの目的
F006: KPI管理画面の実装（ナビゲーションの「KPI管理」メニュー）
- ユーザーがKPI目標値を設定する専用画面
- 日次・週次・月次目標の設定・編集・削除
- 後のダッシュボード画面実装の基盤となる目標データ管理

## Scope

### In（実装範囲）
- **KPI目標設定画面UI**
  - 日次目標設定フォーム
  - 週次目標設定フォーム
  - 月次目標設定フォーム
  - 目標一覧表示
  - 編集・削除機能

- **必要最小限のデータベース**
  - `kpi_targets` テーブルのみ
  - 目標データ保存用

- **コントローラー・バリデーション**
  - KpiTargetController（CRUD操作）
  - FormRequest（目標値バリデーション）

- **ルート・ナビゲーション**
  - `/kpi-management` ルート追加
  - ナビゲーションメニューに「KPI管理」追加

### Out（対象外）
- ダッシュボード画面（次のブランチで実装）
- KPI集計・計算機能（ダッシュボード実装時に追加）
- グラフ・チャート機能（ダッシュボード側）
- レポート機能（将来実装）
- ランキング機能（将来実装 - 後述）

## KPI目標設定項目

### 設定可能な目標
```php
// 架電関連目標
- 日次目標架電数: 1日あたりの目標架電件数（例: 50件/日）
- 週次目標架電数: 1週間あたりの目標架電件数（例: 300件/週）
- 月次目標架電数: 1ヶ月あたりの目標架電件数（例: 1200件/月）

// 成果関連目標
- 目標接続率: 架電に対する接続成功の目標割合（例: 60%）
- 目標成約率: 接続に対する見込み獲得の目標割合（例: 30%）

// 顧客関連目標
- 日次新規顧客目標: 1日あたりの新規顧客獲得目標（例: 5件/日）
- 月次新規顧客目標: 1ヶ月あたりの新規顧客獲得目標（例: 100件/月）
```

## データベース設計

### kpi_targets テーブル
```sql
CREATE TABLE kpi_targets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- 目標種別
    target_type ENUM('daily', 'weekly', 'monthly') NOT NULL,
    target_category ENUM('calls', 'connection_rate', 'conversion_rate', 'new_customers') NOT NULL,
    
    -- 目標値
    target_value DECIMAL(10,2) NOT NULL,
    
    -- 期間設定（月次・週次用）
    period_start DATE NULL,
    period_end DATE NULL,
    
    -- 管理
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    UNIQUE KEY unique_user_target (user_id, target_type, target_category),
    KEY idx_user_active (user_id, is_active),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## UI設計

### KPI管理画面レイアウト
```
┌─────────────────────────────────────────┐
│ 🎯 KPI管理                              │
├─────────────────────────────────────────┤
│                                         │
│ 📈 日次目標                             │
│ ┌─────────────────────────────────────┐ │
│ │ 目標架電数: [50] 件/日               │ │
│ │ 目標接続率: [60] %                   │ │
│ │ 新規顧客数: [5] 件/日                │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ 📊 週次目標                             │
│ ┌─────────────────────────────────────┐ │
│ │ 目標架電数: [300] 件/週              │ │
│ │ 目標成約率: [30] %                   │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ 📅 月次目標                             │
│ ┌─────────────────────────────────────┐ │
│ │ 目標架電数: [1200] 件/月             │ │
│ │ 新規顧客数: [100] 件/月              │ │
│ └─────────────────────────────────────┘ │
│                                         │
│           [保存] [リセット]               │
└─────────────────────────────────────────┘
```

### 設定項目の仕様
- **入力形式**: 数値入力フィールド
- **バリデーション**: 必須、正数、上限チェック
- **保存方式**: 個別保存 or 一括保存
- **初期値**: 未設定時はプレースホルダー表示

## DoD（Definition of Done）

### 技術要件
- [ ] Laravel Pint差分なし
- [ ] `php artisan test` 緑（Feature・Unitテスト実装）
- [ ] N+1クエリ発生なし
- [ ] マイグレーション rollback/migrate 正常動作

### 機能要件
- [ ] KPI管理画面へのアクセス（ナビゲーションから）
- [ ] 各目標値の設定・保存機能
- [ ] 既存目標の編集機能
- [ ] 目標のリセット機能
- [ ] バリデーションエラー表示

### UI/UX要件
- [ ] モバイル対応（Tailwind Responsive）
- [ ] 直感的な操作性
- [ ] 成功・エラーメッセージ表示
- [ ] ローディング状態表示

### セキュリティ要件
- [ ] ユーザー認証必須
- [ ] CSRF保護
- [ ] 自分のデータのみアクセス可能

## Next Prompt（コピペ用）
```
依頼: F006-01: KPI管理画面の実装
必須機能: 日次・週次・月次の目標設定画面
目標項目: 架電数、接続率、成約率、新規顧客数
UI: Tailwind使用、モバイル対応、直感的なフォーム
機能: 設定・編集・削除・バリデーション

前提: Laravel 11 / PHP 8.2 / Blade+Tailwind / N+1禁止
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
```

## 📈 将来実装予定機能

### KPIランキング機能（F008予定）
営業マンのモチベーション向上と成果者の行動パターン学習を目的とした項目別ランキング画面

#### 想定するランキング項目
```php
// 基本ランキング
- 月次架電数ランキング: 当月の総架電件数順
- アポ獲得数ランキング: 当月のアポ獲得件数順
- 通話成功率ランキング: 当月の通話成功率順
- アポ獲得率ランキング: 当月の通話→アポ変換率順

// 継続性ランキング
- 連続架電日数ランキング: 毎日架電を継続している日数
- 目標達成率ランキング: 設定目標に対する達成度
- 成長率ランキング: 前月比の伸び率

// チーム/期間別ランキング
- 週次ランキング: 当週の各指標
- 新人ランキング: 登録から3ヶ月以内のユーザー限定
- ベテランランキング: 6ヶ月以上のユーザー限定
```

#### UI設計構想
```
┌─────────────────────────────────────────┐
│ 🏆 KPIランキング                        │
├─────────────────────────────────────────┤
│ 📊 [今月] [今週] [全期間]               │
│                                         │
│ 🥇 月次架電数ランキング                 │
│ ┌─────────────────────────────────────┐ │
│ │ 1位 田中太郎    452件 📞            │ │
│ │ 2位 佐藤花子    441件 📞            │ │
│ │ 3位 山田次郎    398件 📞            │ │
│ │ ...                                 │ │
│ │ 12位 あなた     298件 📞 ⬆ 3位UP   │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ 🎯 アポ獲得数ランキング                 │
│ 🔄 通話成功率ランキング                 │
│ 📈 成長率ランキング                     │
└─────────────────────────────────────────┘
```

#### データベース設計（追加予定）
```sql
-- ランキング用キャッシュテーブル
CREATE TABLE kpi_rankings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    ranking_type ENUM('monthly_calls', 'appointments', 'success_rate', 'growth_rate') NOT NULL,
    period_key VARCHAR(10) NOT NULL, -- '2025-12', '2025-W49'
    period_type ENUM('monthly', 'weekly', 'all_time') NOT NULL,
    
    user_id BIGINT UNSIGNED NOT NULL,
    rank_position INT UNSIGNED NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    previous_rank INT UNSIGNED NULL,
    rank_change INT NULL, -- +3, -1, 0
    
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_ranking (ranking_type, period_key, user_id),
    INDEX idx_ranking_period (ranking_type, period_key, rank_position),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### 実装時の考慮事項
- **匿名化オプション**: フルネーム表示 vs イニシャル表示の設定
- **フィルタリング**: 部署別、経験年数別でのランキング表示
- **更新頻度**: リアルタイム vs 日次更新のバランス
- **モチベーション設計**: 順位下降時のフォロー機能

## 進捗・決定事項
- 2025-12-06: ブランチ作成、要件定義完了
- KPI管理画面とダッシュボードの分離方針確定
- 目標設定項目定義完了（架電・成果・顧客関連）
- シンプルなCRUD操作ベースのUI設計採用
- 包括的なKPIデータベース設計完了（5テーブル構成）
- 将来のランキング機能仕様策定