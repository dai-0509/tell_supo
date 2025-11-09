# ブランチ別チャット運用ガイド（Claude連携）

最終更新: 2025-11-04  
対象: TellSupo（テルサポ）開発チーム（個人開発/エージェント併用）

## 🎯 目的

- **「このチャットは"どのブランチで何をする回"か」** を常に明確化し、認識ズレをなくす
- 共通ドキュメントを **1か所** に集約し、各ブランチでは **使い捨ての最小メモ** のみを使う
- main ブランチを **クリーン** に保つ（作業メモは残さない/履歴はPRとインデックスへ）

## 📁 ドキュメント構成（常駐）

プロジェクトの共通ポリシーはリポジトリ内の **docs/** に常駐させます。

```
docs/
├── claude.md                    # Claude使い分け・セットアップ・依頼手順の総合ガイド
├── claude-coding-standards.md   # コーディング規約（PSR-12/Laravel方針/N+1/テスト等）
├── claude-code-playbook.md      # 依頼テンプレ・出力形式・自己チェックの運用台帳
├── branch-chat-guide.md         # このファイル（ブランチ別運用ガイド）
└── AGENT_CHAT_INDEX.md          # ブランチ別チャットの目次（現行会話の所在/決定事項）
```

> これらは **main に常駐**。更新はPRで差分レビュー。

## 📝 各ブランチの最小メモ（使い捨て）

### ファイル仕様
- **ファイル名**: `CLAUDE_TASK.md`
- **置き場所**: ブランチ直下（root）
- **役割**: そのブランチの **Scope / DoD / Next Prompt / 出力順** を **数十行** で定義
- **ライフサイクル**: **PR前に本文へ転記** → **`git rm` で削除** → **mainには残さない**

### `CLAUDE_TASK.md` 雛形

```markdown
# CLAUDE_TASK.md — <branch名>

## 固定前提（共通）
前提: Laravel 11 / PHP 8.2 / MySQL 8 / Blade+Tailwind / JST。Controller薄く。N+1禁止。
出力順: ①差分 ②新規 ③実行コマンド ④テスト ⑤自己チェック。疑似コード禁止。

## このブランチの目的
- 例）F007 FormRequest実装（電話のハイフン除去+unique/Updateはignore）

## Scope
- **In**: 触るファイル/責務
  - app/Http/Requests/Customer/
  - tests/Feature/Customer/
- **Out**: 触らない範囲（混入防止）
  - Controller、Model、Service は触らない
  - UI関連は対象外

## DoD（Definition of Done）
- [ ] Pint差分0 / `php artisan test` 緑（正常/異常/境界）
- [ ] UI/UX/ルートの受入基準一致
- [ ] 影響範囲/ロールバックをPRに記載
- [ ] CLAUDE_TASK.md の内容をPR本文に転記

## Next Prompt（コピペ用）
```
依頼: F007-03: CustomerRequest（Store/Update）を実装
必須: name, phone, email / オプション: address, notes
バリデーション: phoneのunique（Updateは自分以外）、ハイフン自動除去
エラーメッセージ: 日本語

前提: Laravel 11 / PHP 8.2 / PSR-12 / N+1禁止
出力順: ①差分 ②新規 ③コマンド ④テスト ⑤チェック
```

## 進捗・決定事項
- 2025-11-04 15:30: ブランチ作成、基本方針確定
- 2025-11-04 16:00: 実装方針決定（電話番号正規化はprepareForValidation使用）
```

## 🔀 ブランチ/PRのルール

### 命名規則
```
feat/<F番号>-<短名>-<段階>

例:
- feat/f007-crud-03-formrequest
- feat/f005-index-02-scopes  
- feat/f012-calllog-05-ui
```

### サイズガイドライン
- **機能単位**: 密接に関連する機能は1ブランチで完結させる
- **完結性優先**: 中途半端な状態でのコミットを避ける
- **レビュー効率**: 機能全体を俯瞰してレビューできる粒度を選択
- **例外**: 大型機能（1週間以上）や他チーム依存がある場合は分割検討

### PR要件
- **テンプレート使用**: 後述のPRテンプレートに従う
- **スクリーンショット必須**: 成功/失敗/空状態の3枚
- **CI通過**: Pint、テスト、カスタムガードすべて緑

## 🔄 ワークフロー（5ステップ）

### 1. ブランチ作成
```bash
git checkout main
git pull origin main
git checkout -b feat/f007-crud-03-formrequest
```

### 2. 作業指示書作成
```bash
# ブランチ直下に作成
touch CLAUDE_TASK.md
# 上記雛形をベースに内容を記入
```

### 3. AGENT_CHAT_INDEX に登録
```markdown
## F007 顧客登録・編集

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f007-crud-03-formrequest | FormRequest実装 | 🔄 進行中 | [Claude Chat #123](https://example.com) | 電話番号正規化方針決定 | CustomerRequest実装依頼 |
```

### 4. 実装 & チャット
- `CLAUDE_TASK.md` を都度更新
- 重要な決定事項は **AGENT_CHAT_INDEX** の **Last Decision** に反映
- チャットが長くなったら **Next Prompt** を更新

### 5. PR作成前の準備
```bash
# CLAUDE_TASK.md の内容をPR本文にコピー
# AGENT_CHAT_INDEX.md を更新（決定事項、Next Prompt等）

# 作業メモを削除
git rm CLAUDE_TASK.md
git commit -m "docs: Remove CLAUDE_TASK.md before PR"

# PR作成
gh pr create --title "feat: Add CustomerRequest validation with phone normalization" \
             --body "$(cat PR_TEMPLATE.md)"
```

### 6. マージ後の整理
```bash
# AGENT_CHAT_INDEX を Done に更新
# Status: 🔄 進行中 → ✅ 完了
# Chat: アクティブリンク → アーカイブリンク
```

## 🛡️ CIガード（main に CLAUDE_TASK.md を残さない）

### `.github/workflows/no-claude-task-on-main.yml`
```yaml
name: no-claude-task-on-main
on:
  pull_request:
    branches: [ "main" ]

jobs:
  guard:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Fail if CLAUDE_TASK.md exists in PR head
        run: |
          set -e
          if git ls-files | grep -E '(^|/)CLAUDE_TASK\.md$' >/dev/null; then
            echo "::error title=Remove CLAUDE_TASK.md before merge::Move its content to the PR body and AGENT_CHAT_INDEX, then delete the file."
            exit 1
          fi
          echo "OK: no CLAUDE_TASK.md found"
```

## 📋 PRテンプレート

### `.github/PULL_REQUEST_TEMPLATE.md`
```markdown
## 概要
<!-- 何を実装したか、なぜ必要だったか -->

## このPRの目的
<!-- CLAUDE_TASK.md の「目的」セクションから転記 -->

## Scope
<!-- CLAUDE_TASK.md の「Scope」セクションから転記 -->
### In (変更対象)
- 

### Out (対象外)
- 

## 主な変更点
<!-- 実装した機能や修正点を箇条書き -->
- 
- 
- 

## スクリーンショット
<!-- 必須: 成功/失敗/空状態の3パターン -->
### 成功パターン
![success](url)

### 失敗パターン（バリデーションエラー）
![error](url)

### 空状態
![empty](url)

## テスト結果
<!-- php artisan test の結果抜粋 -->
```
$ php artisan test --filter=CustomerTest
PHPUnit 10.x.x

...                                                                3 / 3 (100%)

Time: 00:01.234, Memory: 32.00 MB

OK (3 tests, 8 assertions)
```

## チェックリスト
### 事前確認
- [ ] `CLAUDE_TASK.md` の内容を **PR本文** に転記済み
- [ ] `CLAUDE_TASK.md` を **削除** 済み（`git rm`）
- [ ] `docs/AGENT_CHAT_INDEX.md` を更新済み

### 品質確認
- [ ] `composer exec pint -- --test` 緑
- [ ] `php artisan test` 緑
- [ ] N+1クエリチェック済み
- [ ] バリデーションルール確認済み

### ドキュメント
- [ ] 影響範囲を明記
- [ ] ロールバック手順を記載（必要に応じて）
- [ ] 関連Issueリンク済み（該当する場合）

## 影響範囲
<!-- どの機能に影響するか -->
- 

## ロールバック手順
<!-- 問題が発生した場合の戻し方 -->
- 

## 補足事項
<!-- その他、レビュアーに伝えたいことがあれば -->
- 
```

## 🗂️ ローカル専用メモ（Git管理しない）

### .gitignore 追記
```gitignore
# Claude作業メモ（ローカル専用）
CLAUDE_TASK.local.md
**/claude-memo-*.md
**/.claude-temp/
```

### 運用方法
```bash
# ローカル専用メモ（Gitに載らない）
touch CLAUDE_TASK.local.md

# 内容をPR本文にコピペ後、自然消滅
# → Git履歴を汚さない
```

## 📊 AGENT_CHAT_INDEX の運用詳細

### テーブル形式での管理
```markdown
## F007 顧客登録・編集

| ブランチ | 目的 | Status | Chat | Last Decision | Next Prompt |
|---------|------|--------|------|---------------|-------------|
| feat/f007-crud-01-spec | 仕様書作成 | ✅ 完了 | [Archive](archive-url) | 基本仕様確定 | - |
| feat/f007-crud-02-db | DB制約追加 | ✅ 完了 | [Archive](archive-url) | インデックス方針決定 | - |
| feat/f007-crud-03-formrequest | FormRequest実装 | 🔄 進行中 | [Claude Chat #123](active-url) | 電話番号正規化方針 | CustomerRequest実装依頼 |
| feat/f007-crud-04-controller | Controller実装 | ⏸️ 待機中 | - | - | FormRequest完了後に開始 |
```

### Status定義
- **🔄 進行中**: 開発作業中
- **👀 レビュー中**: PR作成済み、レビュー待ち
- **✅ 完了**: マージ済み
- **⏸️ 待機中**: 他ブランチの完了待ち
- **❌ 中止**: 方針変更等で中止

### 更新タイミング
- **ブランチ作成時**: 新しい行を追加
- **重要決定時**: Last Decision を更新
- **チャット区切り時**: Next Prompt を更新
- **PR作成時**: Status を「レビュー中」に
- **マージ時**: Status を「完了」、Chat を Archive に

## 🚀 効率的な運用Tips

### チャット継続時の引き継ぎ
```markdown
<!-- 新しいチャットの最初に送信 -->
継続依頼:

前回の決定事項:
- 電話番号正規化はprepareForValidationで実装
- uniqueバリデーションはUpdateRequestで「自分以外」制約

次のタスク:
[CLAUDE_TASK.md の Next Prompt をコピペ]
```

### 並行ブランチでの調整
```markdown
<!-- AGENT_CHAT_INDEX で依存関係を明記 -->
| feat/f007-crud-04-controller | Controller実装 | ⏸️ 待機中 | - | - | feat/f007-crud-03-formrequest 完了後 |
```

### 緊急時の情報復旧
```markdown
<!-- PR本文から重要な決定事項を AGENT_CHAT_INDEX に転記 -->
Last Decision: CustomerRequestで電話番号正規化、Updateは自分除外unique
```

## ❓ FAQ

**Q: ブランチごとに `claude.md` は必要？**  
A: 不要。**共通の `docs/claude.md`、`coding-standards`、`code-playbook` を使用**。ブランチには `CLAUDE_TASK.md` のみ。

**Q: 作業メモを履歴として残したい**  
A: **PR本文** と **AGENT_CHAT_INDEX** に重要な決定事項を転記。詳細履歴はPRコメントで追える。

**Q: チャットを跨ぐと文脈が失われる**  
A: 必ず **Next Prompt** を `AGENT_CHAT_INDEX` に保持。新チャット開始時にコピペして継続。

**Q: 複数人で同じブランチを作業する場合**  
A: `CLAUDE_TASK.md` を共有し、**Last Decision** を頻繁に更新。重要な決定はSlack等でも共有。

## 🔗 関連ドキュメント

- [`claude.md`](./claude.md) - Claude基本連携ガイド
- [`claude-coding-standards.md`](./claude-coding-standards.md) - 詳細コーディング規約
- [`claude-code-playbook.md`](./claude-code-playbook.md) - 実装テンプレート集
- [`AGENT_CHAT_INDEX.md`](./AGENT_CHAT_INDEX.md) - チャット履歴管理

## 📞 サポート

- 運用方法の質問: Issues作成
- 緊急時の相談: Slack #telsupo-dev
- ドキュメント改善提案: PR作成