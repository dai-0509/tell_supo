<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 架電記録モデル
 *
 * 顧客との通話履歴を管理するEloquentモデル
 * 開始/終了時間、通話結果、持続時間の自動計算機能を提供
 */
class CallLog extends Model
{
    /**
     * HasFactoryトレイト
     *
     * テスト用のファクトリクラス（CallLogFactory）との連携を提供
     */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'started_at',
        'ended_at',
        'result',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * この架電記録を行ったユーザーとのリレーションシップ
     *
     * @return BelongsTo<User, CallLog> ユーザーモデルへの関連
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 架電対象の顧客とのリレーションシップ
     *
     * @return BelongsTo<Customer, CallLog> 顧客モデルへの関連
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * 指定ユーザーの架電記録のみを取得するクエリスコープ
     *
     * @param  Builder<CallLog>  $query  Eloquentクエリビルダー
     * @param  int  $userId  フィルタリング対象のユーザーID
     * @return Builder<CallLog> フィルタリングされたクエリビルダー
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 指定顧客の架電記録のみを取得するクエリスコープ
     *
     * @param  Builder<CallLog>  $query  Eloquentクエリビルダー
     * @param  int  $customerId  フィルタリング対象の顧客ID
     * @return Builder<CallLog> フィルタリングされたクエリビルダー
     */
    public function scopeForCustomer(Builder $query, int $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * 指定した通話結果の架電記録のみを取得するクエリスコープ
     *
     * @param  Builder<CallLog>  $query  Eloquentクエリビルダー
     * @param  string  $result  フィルタリング対象の通話結果
     * @return Builder<CallLog> フィルタリングされたクエリビルダー
     */
    public function scopeByResult(Builder $query, string $result): Builder
    {
        return $query->where('result', $result);
    }

    /**
     * 本日の架電記録のみを取得するクエリスコープ
     *
     * @param  Builder<CallLog>  $query  Eloquentクエリビルダー
     * @return Builder<CallLog> フィルタリングされたクエリビルダー
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('started_at', Carbon::today());
    }

    /**
     * 昨日の架電記録のみを取得するクエリスコープ
     *
     * @param  Builder<CallLog>  $query  Eloquentクエリビルダー
     * @return Builder<CallLog> フィルタリングされたクエリビルダー
     */
    public function scopeYesterday(Builder $query): Builder
    {
        return $query->whereDate('started_at', Carbon::yesterday());
    }

    /**
     * 今週の架電記録のみを取得するクエリスコープ
     *
     * @param  Builder<CallLog>  $query  Eloquentクエリビルダー
     * @return Builder<CallLog> フィルタリングされたクエリビルダー
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('started_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ]);
    }

    /**
     * 今月の架電記録のみを取得するクエリスコープ
     *
     * @param  Builder<CallLog>  $query  Eloquentクエリビルダー
     * @return Builder<CallLog> フィルタリングされたクエリビルダー
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('started_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ]);
    }

    /**
     * 複数の通話結果で絞り込むクエリスコープ
     *
     * @param  Builder<CallLog>  $query  Eloquentクエリビルダー
     * @param  array<string>  $results  フィルタリング対象の通話結果配列
     * @return Builder<CallLog> フィルタリングされたクエリビルダー
     */
    public function scopeFilterByResults(Builder $query, array $results): Builder
    {
        if (empty($results)) {
            return $query;
        }
        return $query->whereIn('result', $results);
    }

    /**
     * 顧客名・メモでの検索を行うクエリスコープ
     *
     * @param  Builder<CallLog>  $query  Eloquentクエリビルダー
     * @param  string|null  $search  検索文字列
     * @return Builder<CallLog> 検索条件が適用されたクエリビルダー
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($query) use ($search) {
            $query->where('notes', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('company_name', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%");
                });
        });
    }

    /**
     * 通話時間を「分:秒」形式で取得するアクセサ
     *
     * @return string フォーマット済み通話時間（例: "05:30"）
     */
    public function getFormattedDurationAttribute(): string
    {
        if (! $this->started_at || ! $this->ended_at) {
            return '00:00';
        }

        $durationSeconds = $this->ended_at->diffInSeconds($this->started_at);
        $minutes = intval($durationSeconds / 60);
        $seconds = $durationSeconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * 通話結果の日本語ラベルを取得するアクセサ
     *
     * @return string 日本語での通話結果表示名
     */
    public function getResultLabelAttribute(): string
    {
        return match ($this->result) {
            '通話成功' => '通話成功',
            '受けブロ' => '受けブロ',
            '会話のみ' => '会話のみ',
            '見込みあり' => '見込みあり',
            default => '不明',
        };
    }

    /**
     * 通話結果の選択肢一覧を取得
     *
     * @return array<string, string> 通話結果のvalue => label配列
     */
    public static function getResultOptions(): array
    {
        return [
            '通話成功' => '通話成功',
            '受けブロ' => '受けブロ',
            '会話のみ' => '会話のみ',
            '見込みあり' => '見込みあり',
        ];
    }
}
