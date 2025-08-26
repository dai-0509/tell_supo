<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Script extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'script_type',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * ユーザーとのリレーション
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * アクティブなスクリプトのスコープ
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * スクリプトタイプ別のスコープ
     */
    public function scopeByType($query, $type)
    {
        return $query->where('script_type', $type);
    }

    /**
     * オープニングスクリプトのスコープ
     */
    public function scopeOpening($query)
    {
        return $query->where('script_type', 'opening');
    }

    /**
     * フォローアップスクリプトのスコープ
     */
    public function scopeFollowup($query)
    {
        return $query->where('script_type', 'followup');
    }

    /**
     * クロージングスクリプトのスコープ
     */
    public function scopeClosing($query)
    {
        return $query->where('script_type', 'closing');
    }

    /**
     * 使用回数の多い順でソート
     */
    public function scopeMostUsed($query)
    {
        return $query->orderBy('usage_count', 'desc');
    }

    /**
     * 使用回数をインクリメント
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * スクリプトの文字数を取得
     */
    public function getCharacterCountAttribute()
    {
        return mb_strlen($this->content);
    }

    /**
     * 推定読み上げ時間を取得（1分間に300文字として計算）
     */
    public function getEstimatedReadingTimeAttribute()
    {
        $charactersPerMinute = 300;
        $minutes = ceil($this->character_count / $charactersPerMinute);
        return $minutes;
    }

    /**
     * スクリプトタイプのラベルを取得
     */
    public function getTypeLabel()
    {
        return match($this->script_type) {
            'opening' => 'オープニング',
            'followup' => 'フォローアップ',
            'closing' => 'クロージング',
            'objection_handling' => '反駁処理',
            'appointment_setting' => 'アポ設定',
            default => 'その他',
        };
    }
}