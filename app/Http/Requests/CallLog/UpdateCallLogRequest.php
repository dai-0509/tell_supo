<?php

namespace App\Http\Requests\CallLog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 架電記録更新用FormRequest
 *
 * Laravel FormRequestクラスを継承し、バリデーションと認可を管理
 * input()やmerge()などのメソッドはFormRequestクラスから継承
 */
class UpdateCallLogRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを実行する権限があるかを判定
     *
     * @return bool 認証済みユーザーは架電記録を更新可能
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * バリデーションルールを定義
     *
     * @return array<string, mixed> バリデーションルールの配列
     */
    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id,user_id,'.auth()->id(),
            ],
            'started_at' => ['required', 'date', 'before_or_equal:now'],
            'ended_at' => ['nullable', 'date', 'after:started_at'],
            'result' => ['required', Rule::in(['通話成功', '受けブロ', '会話のみ', '見込みあり'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * バリデーションエラーメッセージをカスタマイズ
     *
     * @return array<string, string> エラーメッセージの配列
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => '顧客を選択してください。',
            'customer_id.exists' => '選択された顧客が存在しません。',
            'started_at.required' => '開始時刻を入力してください。',
            'started_at.before_or_equal' => '開始時刻は現在時刻以前である必要があります。',
            'ended_at.after' => '終了時刻は開始時刻より後である必要があります。',
            'result.required' => '通話結果を選択してください。',
            'result.in' => '無効な通話結果が選択されています。',
            'notes.max' => 'メモは1000文字以内で入力してください。',
        ];
    }
}
