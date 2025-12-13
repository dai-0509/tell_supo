# AGENT_CHAT_INDEX（TellSupo開発）

最終更新: 2025-11-04  
管理者: TellSupo開発チーム

## 🎯 このドキュメントの目的

- **ブランチ別チャット履歴の一元管理**
- **重要な技術的決定事項の記録**
- **次のアクションの明確化**
- **開発進捗の可視化**

## 📊 全体進捗サマリー

| カテゴリ | 進行中 | 完了 | 待機中 | 合計 |
|----------|--------|------|--------|------|
| 顧客管理（F005/F007） | 0 | 3 | 3 | 6 |
| 架電管理（F012） | 0 | 1 | 5 | 6 |
| UI/UX完全改修 | 0 | 1 | 0 | 1 |
| KPI管理（F006） | 0 | 1 | 0 | 1 |
| 履歴機能（F010/F011） | 0 | 0 | 4 | 4 |
| ダッシュボード（F001-F003） | 0 | 0 | 12 | 12 |
| MVP仕上げ（W4.5） | 0 | 0 | 3 | 3 |

**全体進捗**: 19% (6/33)

---

## 🔴 高優先度（コア機能）

### F007 顧客登録・編集

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f007-crud-01-spec | 統合CRUD実装（Model/FormRequest/Controller/Policy/UI/Tests） | ✅ 完了 | [Archive](https://claude.ai/archived) | 業界enum化・PHPDoc完全対応・17テスト100%通過 | - |

### F005 顧客一覧

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f005-index-basic | 基本一覧機能実装（paginate/空状態/ナビ新規登録） | ✅ 完了 | [PR #2](https://github.com/dai-0509/tell_supo/pull/2) | ナビゲーション新規登録ボタン・ページネーション実装完了 | - |
| feat/f005-index-02-scopes | 名前/電話検索・並び替けの Eloquent スコープ | ⏸️ 未開始 | - | - | 01-spec完了後に開始 |
| feat/f005-index-03-controller | index 実装（paginate(20)・N+1回避） | ⏸️ 未開始 | - | - | 02-scopes完了後に開始 |
| feat/f005-index-04-ui | 一覧テーブル＆空状態表示 | ⏸️ 未開始 | - | - | 03-controller完了後に開始 |
| feat/f005-index-05-search-sort | 検索/ソート UI とクエリ維持 | ⏸️ 未開始 | - | - | 04-ui完了後に開始 |
| feat/f005-search-sort | 検索・ソート・フィルタ機能統合実装 | ✅ 完了 | [PR #5](https://github.com/dai-0509/tell_supo/pull/5) | Reactコンポーネント統合・リアルタイム検索・ソート・フィルタ完全実装 | - |

### F012 架電記録（追加/編集）

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f012-calllog-basic | 架電記録基本機能統合実装（CRUD/UI/Tests/動的通話時間計算） | ✅ 完了 | [PR #3](https://github.com/dai-0509/tell_supo/pull/3) | duration_seconds削除・動的計算・15テスト100%通過 | - |
| feat/f012-calllog-02-migration | call_logs テーブル（FK/INDEX/列） | ❌ 統合済 | - | basicブランチで統合実装 | - |
| feat/f012-calllog-03-formrequest | 未来日時NG・開始<=終了・秒数範囲 | ❌ 統合済 | - | basicブランチで統合実装 | - |
| feat/f012-calllog-04-controller | 保存時の秒数自動計算・例外処理 | ❌ 統合済 | - | basicブランチで統合実装 | - |
| feat/f012-calllog-05-ui | 架電記録フォーム（顧客検索選択付き） | ❌ 統合済 | - | basicブランチで統合実装 | - |
| feat/f012-calllog-06-tests | 未来NG/逆転NG/正常のテスト | ❌ 統合済 | - | basicブランチで統合実装 | - |

### UI/UX完全改修

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/ui-improvement-complete | UI/UX全面改修・React統合・HubSpot風デザイン実装 | ✅ 完了 | [PR #4](https://github.com/dai-0509/tell_supo/pull/4) | サイドバー＋ヘッダー・Reactモーダル・ページネーション緑色統一・37ファイル大規模改修 | - |

### F006 KPI目標管理

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f006-kpi-analytics-foundation | KPI目標管理機能統合実装（CRUD/UI/バリデーション/整合性チェック） | ✅ 完了 | [PR #6](https://github.com/dai-0509/tell_supo/pull/6) | 日次/週次/月次目標管理・カスタム主キー実装・8テスト100%通過・Bladeビュー4画面完成 | - |

---

## 🟡 中優先度（ダッシュボード機能）

### F001-F003 ダッシュボード統合

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f001-f003-dashboard-basic | ダッシュボード基本KPI統合実装（週次目標・架電メータ・統計カード） | ⏸️ 準備完了 | - | - | F012完了により開始可能 |
| feat/f001-weekly-02-service | KpiService::weeklyProgress 実装 | ⏸️ 未開始 | - | - | 01-spec完了後に開始 |
| feat/f001-weekly-03-ui | 進捗バー/残件/色分けの表示 | ⏸️ 未開始 | - | - | 02-service完了後に開始 |
| feat/f001-weekly-04-tests | 週境界/分母0/未設定のテスト | ⏸️ 未開始 | - | - | 03-ui完了後に開始 |

### F002 今日の架電メータ

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f002-daily-01-spec | 日範囲/JST/オフセット方針の仕様確定 | ⏸️ 未開始 | - | - | F001完了後に開始 |
| feat/f002-daily-02-service | カウンタ＋オフセット永続化/リセット | ⏸️ 未開始 | - | - | 01-spec完了後に開始 |
| feat/f002-daily-03-ui | ±ボタン即時反映/下限0/キーボード操作 | ⏸️ 未開始 | - | - | 02-service完了後に開始 |
| feat/f002-daily-04-tests | 日境界/offset=0一致のテスト | ⏸️ 未開始 | - | - | 03-ui完了後に開始 |

### F003 統計カード（4枚）

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f003-cards-01-spec | 指標定義（今日/前日比/接続率/成約率/週累計） | ⏸️ 未開始 | - | - | F002完了後に開始 |
| feat/f003-cards-02-queries | 集計サービス/INDEX確認/軽キャッシュ | ⏸️ 未開始 | - | - | 01-spec完了後に開始 |
| feat/f003-cards-03-ui | 4カード共通デザイン/ゼロ割対策 | ⏸️ 未開始 | - | - | 02-queries完了後に開始 |
| feat/f003-cards-04-tests | 日/週/前日比の境界テスト | ⏸️ 未開始 | - | - | 03-ui完了後に開始 |

---

## 🟢 低優先度（履歴・MVP仕上げ）

### F010/F011 履歴一覧/検索

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f010-logs-01-spec | 一覧/検索の仕様/受入基準ドキュメント作成 | ⏸️ 未開始 | - | - | F003完了後に開始 |
| feat/f010-logs-02-index | JOIN＋paginate(20)＋結果バッジ表示 | ⏸️ 未開始 | - | - | 01-spec完了後に開始 |
| feat/f011-logs-03-filters | 顧客名/結果/日付（今日・週・月・範囲）フィルタ | ⏸️ 未開始 | - | - | 02-index完了後に開始 |
| feat/f011-logs-04-tests | 並び/検索/日付/ページング併用のテスト | ⏸️ 未開始 | - | - | 03-filters完了後に開始 |

### W4.5 MVP仕上げ・最小追加

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f006-filters-01-min | 顧客一覧のステータス/ABC 最小フィルタ | ⏸️ 未開始 | - | - | F010/F011完了後に開始 |
| feat/f009-prospect-memo-01 | 顧客詳細に見込みメモ（300字）＋一覧アイコン | ⏸️ 未開始 | - | - | 並行実装可能 |
| feat/f001-quick-target-01 | ダッシュボ上の「今週目標」クイック設定 | ⏸️ 未開始 | - | - | F001完了後に開始 |

---

## 🔄 ステータス定義

| ステータス | 説明 | アクション |
|-----------|------|-----------|
| ⏸️ 未開始 | 作業開始前 | ブランチ作成待ち |
| 🔄 進行中 | 開発作業中 | 実装・チャット中 |
| 👀 レビュー中 | PR作成済み | コードレビュー待ち |
| ✅ 完了 | マージ済み | 次のブランチへ |
| ❌ 中止 | 方針変更等 | 代替案検討 |
| 🚧 ブロック | 他ブランチ待ち | 依存解決待ち |

## 📝 使用方法

### 新しいブランチ開始時
1. ブランチを作成
2. 該当行のStatusを「🔄 進行中」に更新
3. ChatカラムにClaude ChatのURLを追加
4. `CLAUDE_TASK.md`を作成

### 重要な決定時
1. Last Decisionに要点を記録
2. 必要に応じてNext Promptを更新

### PR作成時
1. Statusを「👀 レビュー中」に更新
2. ChatカラムにPR URLを追加
3. `CLAUDE_TASK.md`の内容をPR本文に転記

### マージ時
1. Statusを「✅ 完了」に更新
2. ChatカラムをArchive URLに変更
3. 次のブランチの依存関係を確認

## 🎯 次のアクション

### 推奨開始順序
1. **feat/f005-search-sort** - 顧客一覧検索・ソート・フィルタ機能（UI基盤完成により実装可能）
2. **feat/f001-f003-dashboard-basic** - ダッシュボード基本KPI統合実装（React環境整備済み）
3. **feat/f010-logs-01-spec** - 架電記録履歴一覧/検索の仕様策定

### 並行実装可能
- **feat/f009-prospect-memo-01** - 見込みメモ機能（独立性高）

## 📚 関連ドキュメント

- [`claude.md`](./claude.md) - Claude基本連携ガイド
- [`claude-coding-standards.md`](./claude-coding-standards.md) - コーディング規約
- [`claude-code-playbook.md`](./claude-code-playbook.md) - 実装テンプレート
- [`branch-chat-guide.md`](./branch-chat-guide.md) - ブランチ運用ガイド
- [`development_roadmap.md`](../development_roadmap.md) - 全体ロードマップ

## 🔄 更新履歴

| 日付 | 更新者 | 変更内容 |
|------|--------|----------|
| 2025-12-07 | Claude | F006 KPI目標管理機能完了（PR #6）、F005検索・フィルタ機能完了（PR #5）、進捗率19%(6/33)に更新 |
| 2025-11-25 | Claude | UI/UX完全改修完了（PR #4）、React統合・HubSpot風デザイン実装、進捗率15%→19%に更新 |
| 2025-11-22 | Claude | F012架電記録機能完了、進捗率6%→15%に更新 |
| 2025-11-04 | システム | 初期テンプレート作成 |

---

**📝 注意**: このファイルは開発チーム全体で共有される重要なドキュメントです。更新時は必ず最新の情報を反映し、チーム内で情報共有を行ってください。
