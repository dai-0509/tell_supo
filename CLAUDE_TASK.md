# CLAUDE_TASK.md — feat/f005-index-basic

## 固定前提（共通）
前提: Laravel 11 / PHP 8.2 / MySQL 8 / Blade+Tailwind / JST。Controller薄く。N+1禁止。
出力順: ①差分 ②新規 ③実行コマンド ④テスト ⑤自己チェック。疑似コード禁止。

## このブランチの目的
F005: 顧客一覧画面の基本実装（paginate + 空状態 + モバイル対応）

## Scope
- **In**: 一覧表示、ページネーション、空状態、基本レスポンシブ
  - app/Http/Controllers/CustomerController.php (index メソッド)
  - resources/views/pages/customers/index.blade.php
  - tests/Feature/CustomerIndexTest.php
- **Out**: 検索・ソート機能（次フェーズで実装）
  - 検索フォーム、フィルタ機能は対象外
  - 詳細な認証設定（F007で既に実装済み）

## DoD（Definition of Done）
- [ ] paginate(20)で一覧表示
- [ ] N+1クエリ対策済み
- [ ] 空状態のUI実装
- [ ] モバイル対応（レスポンシブ）
- [ ] 新規登録ボタン配置
- [ ] Pint差分0 / `php artisan test` 緑
- [ ] Feature test（一覧/ページング/空状態/認証）
- [ ] CLAUDE_TASK.md の内容をPR本文に転記

## Next Prompt（コピペ用）
```
依頼: F005-01: 顧客一覧画面の基本実装
要件: paginate(20)、N+1対策、空状態UI、モバイル対応
表示項目: 会社名、担当者名、電話番号、メール、業界、更新日時
アクション: 詳細表示、編集、新規登録ボタン

前提: Laravel 11 / PHP 8.2 / PSR-12 / N+1禁止
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
```

## 進捗・決定事項
- 2025-11-11: ブランチ作成、F005基本一覧実装開始
- 設計方針: シンプルな一覧表示から始めて段階的に機能追加