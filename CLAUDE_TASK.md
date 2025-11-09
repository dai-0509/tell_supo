# CLAUDE_TASK.md — feat/f007-crud-01-spec

## 固定前提（共通）
前提: Laravel 11 / PHP 8.2 / MySQL 8 / Blade+Tailwind / JST。Controller薄く。N+1禁止。
出力順: ①差分 ②新規 ③実行コマンド ④テスト ⑤自己チェック。疑似コード禁止。

## このブランチの目的
F007-01: 顧客登録・編集機能の仕様/受入基準ドキュメント作成

## Scope
- **In**: 仕様書作成、要件定義、受入基準
- **Out**: 実装コードは含まない

## DoD（Definition of Done）
- [ ] 機能仕様書完成
- [ ] 受入基準明確化
- [ ] バリデーションルール定義
- [ ] UI/UX要件整理

## Next Prompt（コピペ用）
```
依頼: F007-02: customersテーブルにDB制約（UNIQUE/INDEX）を追加
要件: email unique制約、phone部分インデックス、company_name検索インデックス
前提: Laravel 11 / PHP 8.2 / PSR-12 / 後方互換性考慮
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
```

## 進捗・決定事項
- 2025-11-08 作成: ブランチ作成、仕様策定開始
- 2025-11-08 実装: Model、FormRequest、Controller、Policy実装完了