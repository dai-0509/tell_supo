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
            
            // 曜日別目標
            'monday_call_target' => 'required|integer|min:0|max:300',
            'tuesday_call_target' => 'required|integer|min:0|max:300',
            'wednesday_call_target' => 'required|integer|min:0|max:300',
            'thursday_call_target' => 'required|integer|min:0|max:300',
            'friday_call_target' => 'required|integer|min:0|max:300',
            'saturday_call_target' => 'nullable|integer|min:0|max:200',
            'sunday_call_target' => 'nullable|integer|min:0|max:200',
            
            // 週次・月次目標
            'weekly_call_target' => 'required|integer|min:5|max:1000',
            'monthly_call_target' => 'required|integer|min:20|max:6000',
            'monthly_appointment_target' => 'required|integer|min:0|max:500',
            
            // 成功率
            'target_success_rate' => 'nullable|numeric|min:0|max:100',
            'target_appointment_rate' => 'nullable|numeric|min:0|max:100',
            'historical_success_rate' => 'nullable|numeric|min:0|max:100',
            'historical_appointment_rate' => 'nullable|numeric|min:0|max:100',
            
            // 推奨値
            'recommended_monthly_calls' => 'nullable|integer|min:0|max:10000',
            'recommended_weekly_calls' => 'nullable|integer|min:0|max:2500',
            
            // 設定方法
            'setting_method' => 'required|in:manual,auto_distributed,ai_suggested',
            'weekday_distribution_ratio' => 'nullable|array',
            
            // 有効期間
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
            // 曜日別目標の合計が週次目標と一致するかチェック
            $weekdaySum = ($this->input('monday_call_target', 0) +
                          $this->input('tuesday_call_target', 0) +
                          $this->input('wednesday_call_target', 0) +
                          $this->input('thursday_call_target', 0) +
                          $this->input('friday_call_target', 0) +
                          $this->input('saturday_call_target', 0) +
                          $this->input('sunday_call_target', 0));
            
            $weekly = $this->input('weekly_call_target');
            $monthly = $this->input('monthly_call_target');

            if ($weekdaySum !== $weekly) {
                $validator->errors()->add(
                    'weekly_call_target',
                    '曜日別目標の合計（' . $weekdaySum . '件）が週次目標（' . $weekly . '件）と一致しません。'
                );
            }

            // 月次目標 >= 週次目標 × 4
            if ($monthly < ($weekly * 4)) {
                $validator->errors()->add(
                    'monthly_call_target',
                    '月次目標は週次目標の4倍以上にしてください。'
                );
            }

            // 平日の目標が0でないかチェック（土日は任意）
            $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
            $weekdaysTotal = 0;
            foreach ($weekdays as $day) {
                $weekdaysTotal += $this->input($day . '_call_target', 0);
            }

            if ($weekdaysTotal === 0 && $weekly > 0) {
                $validator->errors()->add(
                    'monday_call_target',
                    '平日のうち少なくとも1日は目標を設定してください。'
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
            // 曜日別目標
            'monday_call_target.required' => '月曜日の目標架電数は必須です。',
            'monday_call_target.integer' => '月曜日の目標架電数は整数で入力してください。',
            'monday_call_target.min' => '月曜日の目標架電数は0以上にしてください。',
            'monday_call_target.max' => '月曜日の目標架電数は300以下にしてください。',
            
            'tuesday_call_target.required' => '火曜日の目標架電数は必須です。',
            'tuesday_call_target.integer' => '火曜日の目標架電数は整数で入力してください。',
            'tuesday_call_target.min' => '火曜日の目標架電数は0以上にしてください。',
            'tuesday_call_target.max' => '火曜日の目標架電数は300以下にしてください。',
            
            'wednesday_call_target.required' => '水曜日の目標架電数は必須です。',
            'wednesday_call_target.integer' => '水曜日の目標架電数は整数で入力してください。',
            'wednesday_call_target.min' => '水曜日の目標架電数は0以上にしてください。',
            'wednesday_call_target.max' => '水曜日の目標架電数は300以下にしてください。',
            
            'thursday_call_target.required' => '木曜日の目標架電数は必須です。',
            'thursday_call_target.integer' => '木曜日の目標架電数は整数で入力してください。',
            'thursday_call_target.min' => '木曜日の目標架電数は0以上にしてください。',
            'thursday_call_target.max' => '木曜日の目標架電数は300以下にしてください。',
            
            'friday_call_target.required' => '金曜日の目標架電数は必須です。',
            'friday_call_target.integer' => '金曜日の目標架電数は整数で入力してください。',
            'friday_call_target.min' => '金曜日の目標架電数は0以上にしてください。',
            'friday_call_target.max' => '金曜日の目標架電数は300以下にしてください。',
            
            'saturday_call_target.integer' => '土曜日の目標架電数は整数で入力してください。',
            'saturday_call_target.min' => '土曜日の目標架電数は0以上にしてください。',
            'saturday_call_target.max' => '土曜日の目標架電数は200以下にしてください。',
            
            'sunday_call_target.integer' => '日曜日の目標架電数は整数で入力してください。',
            'sunday_call_target.min' => '日曜日の目標架電数は0以上にしてください。',
            'sunday_call_target.max' => '日曜日の目標架電数は200以下にしてください。',
            
            // 週次・月次目標
            'weekly_call_target.required' => '週次目標架電数は必須です。',
            'weekly_call_target.integer' => '週次目標架電数は整数で入力してください。',
            'weekly_call_target.min' => '週次目標架電数は5以上にしてください。',
            'weekly_call_target.max' => '週次目標架電数は1000以下にしてください。',
            
            'monthly_call_target.required' => '月次目標架電数は必須です。',
            'monthly_call_target.integer' => '月次目標架電数は整数で入力してください。',
            'monthly_call_target.min' => '月次目標架電数は20以上にしてください。',
            'monthly_call_target.max' => '月次目標架電数は6000以下にしてください。',
            
            'monthly_appointment_target.required' => '月次目標アポ獲得数は必須です。',
            'monthly_appointment_target.integer' => '月次目標アポ獲得数は整数で入力してください。',
            'monthly_appointment_target.min' => '月次目標アポ獲得数は0以上にしてください。',
            'monthly_appointment_target.max' => '月次目標アポ獲得数は500以下にしてください。',
            
            // 成功率
            'target_success_rate.numeric' => '目標通話成功率は数値で入力してください。',
            'target_success_rate.min' => '目標通話成功率は0以上にしてください。',
            'target_success_rate.max' => '目標通話成功率は100以下にしてください。',
            
            'target_appointment_rate.numeric' => '目標アポ獲得率は数値で入力してください。',
            'target_appointment_rate.min' => '目標アポ獲得率は0以上にしてください。',
            'target_appointment_rate.max' => '目標アポ獲得率は100以下にしてください。',
            
            // 設定方法
            'setting_method.required' => '設定方法は必須です。',
            'setting_method.in' => '設定方法は有効な値を選択してください。',
            
            // 有効期間
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
