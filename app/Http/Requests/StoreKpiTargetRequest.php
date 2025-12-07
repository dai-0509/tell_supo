<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * KPI目標設定のリクエスト
 */
class StoreKpiTargetRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストの権限を持っているか判定する
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * バリデーションルールを取得する
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'daily_call_target' => 'required|integer|min:1|max:200',
            'weekly_call_target' => 'required|integer|min:5|max:1400',
            'monthly_call_target' => 'required|integer|min:20|max:6000',
            'monthly_appointment_target' => 'required|integer|min:0|max:500',
            'target_success_rate' => 'nullable|numeric|min:0|max:100',
            'target_appointment_rate' => 'nullable|numeric|min:0|max:100',
            'effective_from' => 'required|date|after_or_equal:today',
            'effective_until' => 'nullable|date|after:effective_from',
        ];
    }

    /**
     * カスタムバリデーションルール
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $daily = $this->input('daily_call_target');
            $weekly = $this->input('weekly_call_target');
            $monthly = $this->input('monthly_call_target');

            // 週次目標 >= 日次目標 × 5（平日想定）
            if ($weekly < ($daily * 5)) {
                $validator->errors()->add(
                    'weekly_call_target',
                    '週次目標は日次目標の5倍以上にしてください（平日想定）。'
                );
            }

            // 月次目標 >= 週次目標 × 4
            if ($monthly < ($weekly * 4)) {
                $validator->errors()->add(
                    'monthly_call_target',
                    '月次目標は週次目標の4倍以上にしてください。'
                );
            }
        });
    }

    /**
     * カスタムエラーメッセージ
     */
    public function messages(): array
    {
        return [
            'daily_call_target.required' => '日次目標架電数は必須です。',
            'daily_call_target.integer' => '日次目標架電数は整数で入力してください。',
            'daily_call_target.min' => '日次目標架電数は1以上にしてください。',
            'daily_call_target.max' => '日次目標架電数は200以下にしてください。',
            
            'weekly_call_target.required' => '週次目標架電数は必須です。',
            'weekly_call_target.integer' => '週次目標架電数は整数で入力してください。',
            'weekly_call_target.min' => '週次目標架電数は5以上にしてください。',
            'weekly_call_target.max' => '週次目標架電数は1400以下にしてください。',
            
            'monthly_call_target.required' => '月次目標架電数は必須です。',
            'monthly_call_target.integer' => '月次目標架電数は整数で入力してください。',
            'monthly_call_target.min' => '月次目標架電数は20以上にしてください。',
            'monthly_call_target.max' => '月次目標架電数は6000以下にしてください。',
            
            'monthly_appointment_target.required' => '月次目標アポ獲得数は必須です。',
            'monthly_appointment_target.integer' => '月次目標アポ獲得数は整数で入力してください。',
            'monthly_appointment_target.min' => '月次目標アポ獲得数は0以上にしてください。',
            'monthly_appointment_target.max' => '月次目標アポ獲得数は500以下にしてください。',
            
            'target_success_rate.numeric' => '目標通話成功率は数値で入力してください。',
            'target_success_rate.min' => '目標通話成功率は0以上にしてください。',
            'target_success_rate.max' => '目標通話成功率は100以下にしてください。',
            
            'target_appointment_rate.numeric' => '目標アポ獲得率は数値で入力してください。',
            'target_appointment_rate.min' => '目標アポ獲得率は0以上にしてください。',
            'target_appointment_rate.max' => '目標アポ獲得率は100以下にしてください。',
            
            'effective_from.required' => '有効開始日は必須です。',
            'effective_from.date' => '有効開始日は有効な日付で入力してください。',
            'effective_from.after_or_equal' => '有効開始日は今日以降にしてください。',
            
            'effective_until.date' => '有効終了日は有効な日付で入力してください。',
            'effective_until.after' => '有効終了日は有効開始日より後にしてください。',
        ];
    }

    /**
     * バリデーション後のデータ準備
     */
    protected function prepareForValidation(): void
    {
        // 有効開始日が未設定の場合は今日を設定
        if (!$this->filled('effective_from')) {
            $this->merge([
                'effective_from' => now()->toDateString(),
            ]);
        }

        // user_idを自動設定
        $this->merge([
            'user_id' => Auth::id(),
        ]);
    }
}
