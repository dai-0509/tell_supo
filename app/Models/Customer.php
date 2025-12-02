<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 顧客モデル
 *
 * 顧客の基本情報（会社名、担当者、連絡先等）を管理するEloquentモデル
 * ユーザー単位でのデータ分離、架電記録との関連付けを提供
 */
class Customer extends Model
{
    /**
     * HasFactoryトレイト
     *
     * テスト用のファクトリクラス（CustomerFactory）との連携を提供
     */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'industry',
        'temperature_rating',
        'area',
        'status',
        'priority',
        'memo',
    ];

    protected $casts = [
        'priority' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    // Relationships
    /**
     * この顧客を所有するユーザーとのリレーション
     *
     * @return BelongsTo ユーザーとの多対一リレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * この顧客に関連する通話履歴とのリレーション
     *
     * @return HasMany 通話履歴との一対多リレーション
     */
    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }

    // Scopes
    /**
     * 指定されたユーザーの顧客のみを取得するスコープ
     *
     * @param  Builder  $query  クエリビルダー
     * @param  int  $userId  ユーザーID
     * @return Builder 絞り込み済みのクエリビルダー
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * 会社名で部分一致検索するスコープ
     *
     * @param  Builder  $query  クエリビルダー
     * @param  string  $companyName  検索する会社名
     * @return Builder 絞り込み済みのクエリビルダー
     */
    public function scopeSearchByCompany(Builder $query, string $companyName): Builder
    {
        return $query->where('company_name', 'like', "%{$companyName}%");
    }

    /**
     * 温度感で絞り込むスコープ
     *
     * @param  Builder  $query  クエリビルダー
     * @param  string  $temperature  温度感（A-F）
     * @return Builder 絞り込み済みのクエリビルダー
     */
    public function scopeByTemperature(Builder $query, string $temperature): Builder
    {
        return $query->where('temperature_rating', $temperature);
    }

    /**
     * ステータスで絞り込むスコープ
     *
     * @param  Builder  $query  クエリビルダー
     * @param  string  $status  ステータス
     * @return Builder 絞り込み済みのクエリビルダー
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * 会社名・担当者名での統合検索スコープ
     *
     * @param  Builder  $query  クエリビルダー
     * @param  string|null  $search  検索キーワード
     * @return Builder 絞り込み済みのクエリビルダー
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('company_name', 'like', "%{$search}%")
              ->orWhere('contact_name', 'like', "%{$search}%");
        });
    }

    /**
     * 複数ステータスでフィルタするスコープ
     *
     * @param  Builder  $query  クエリビルダー
     * @param  array  $statuses  ステータス配列
     * @return Builder 絞り込み済みのクエリビルダー
     */
    public function scopeFilterByStatuses(Builder $query, array $statuses): Builder
    {
        if (empty($statuses)) {
            return $query;
        }

        return $query->whereIn('status', $statuses);
    }

    /**
     * 複数温度感でフィルタするスコープ
     *
     * @param  Builder  $query  クエリビルダー
     * @param  array  $temperatures  温度感配列
     * @return Builder 絞り込み済みのクエリビルダー
     */
    public function scopeFilterByTemperatures(Builder $query, array $temperatures): Builder
    {
        if (empty($temperatures)) {
            return $query;
        }

        return $query->whereIn('temperature_rating', $temperatures);
    }

    /**
     * 複数業界でフィルタするスコープ
     *
     * @param  Builder  $query  クエリビルダー
     * @param  array  $industries  業界配列
     * @return Builder 絞り込み済みのクエリビルダー
     */
    public function scopeFilterByIndustries(Builder $query, array $industries): Builder
    {
        if (empty($industries)) {
            return $query;
        }

        return $query->whereIn('industry', $industries);
    }

    /**
     * 複数エリアでフィルタするスコープ
     *
     * @param  Builder  $query  クエリビルダー
     * @param  array  $areas  エリア配列
     * @return Builder 絞り込み済みのクエリビルダー
     */
    public function scopeFilterByAreas(Builder $query, array $areas): Builder
    {
        if (empty($areas)) {
            return $query;
        }

        return $query->whereIn('area', $areas);
    }
}
