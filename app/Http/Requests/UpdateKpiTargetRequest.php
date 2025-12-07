<?php

namespace App\Http\Requests;

use App\Models\UserKpiTarget;
use Illuminate\Support\Facades\Auth;

/**
 * KPI目標更新のリクエスト
 */
class UpdateKpiTargetRequest extends StoreKpiTargetRequest
{
    /**
     * ユーザーがこのリクエストの権限を持っているか判定する
     */
    public function authorize(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        // 編集対象のKPI目標が自分のものかチェック
        $kpiTargetId = $this->route('kpi_target');
        if ($kpiTargetId) {
            $kpiTarget = UserKpiTarget::find($kpiTargetId);
            return $kpiTarget && $kpiTarget->user_id === Auth::id();
        }

        return true;
    }

    /**
     * バリデーションルールを取得する（継承 + 更新時の調整）
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // 更新時は有効開始日を過去でもOKにする
        $rules['effective_from'] = 'required|date';

        return $rules;
    }

    /**
     * バリデーション後のデータ準備（更新用）
     */
    protected function prepareForValidation(): void
    {
        // user_idを自動設定（有効開始日は既存値を維持）
        $this->merge([
            'user_id' => Auth::id(),
        ]);
    }
}
