# CLAUDE_TASK.md — feat/f012-calllog-basic

## 固定前提（共通）
前提: Laravel 11 / PHP 8.2 / MySQL 8 / Blade+Tailwind / JST。Controller薄く。N+1禁止。
出力順: ①差分 ②新規 ③実行コマンド ④テスト ⑤自己チェック。疑似コード禁止。

## このブランチの目的
F012: 架電記録機能の基本実装（顧客との通話履歴管理）

## Scope
- **In**: 架電記録のCRUD、顧客との関連付け、時間管理
  - database/migrations/create_call_logs_table.php
  - app/Models/CallLog.php
  - app/Http/Requests/CallLog/Store|UpdateCallLogRequest.php
  - app/Http/Controllers/CallLogController.php
  - app/Policies/CallLogPolicy.php
  - resources/views/pages/call-logs/
  - tests/Feature/CallLogTest.php
- **Out**: 高度な分析機能、外部API連携
  - 通話録音機能は対象外
  - 詳細な統計・グラフ機能は後続で実装

## DoD（Definition of Done）
- [ ] call_logsテーブル作成（customer_id FK、開始/終了時間、結果等）
- [ ] CallLogモデル（Customer関連、バリデーション）
- [ ] CallLogController（CRUD、N+1対策）
- [ ] FormRequest（時間検証、未来日NG等）
- [ ] Policy（ユーザー認可）
- [ ] 基本UI（記録追加/一覧/詳細）
- [ ] Feature test（CRUD/認可/時間検証）
- [ ] Pint差分0 / `php artisan test` 緑

## Next Prompt（コピペ用）
```
依頼: F012-01: 架電記録機能の基本実装
要件: 顧客との通話履歴管理、開始/終了時間、通話結果記録
DB設計: call_logs(customer_id, started_at, ended_at, result, notes)
バリデーション: 未来日NG、開始<=終了時間、秒数範囲

前提: Laravel 11 / PHP 8.2 / PSR-12 / N+1禁止
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
```

## 進捗・決定事項
- 2025-11-13: ブランチ作成、F012架電記録機能実装開始