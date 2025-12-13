<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    /**
     * 顧客登録リクエストの認証を判定する
     *
     * @return bool 認証済みユーザーの場合true
     */
    public function authorize(): bool
    {
        // Auth::check()はLaravelの標準ヘルパー関数
        return Auth::check();
    }

    /**
     * 顧客登録時のバリデーションルールを取得する
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> バリデーションルール配列
     */
    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'company_name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('customers')->where('user_id', $userId),
            ],
            'contact_name' => ['nullable', 'string', 'max:60'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers')->where('user_id', $userId),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'industry' => ['nullable', 'string', 'in:IT,製造業,小売業,金融業,医療・福祉,教育,建設・不動産,運輸・物流,飲食・宿泊,士業・コンサル,その他'],
            'temperature_rating' => ['nullable', 'string', 'in:A,B,C,D,E,F'],
            'area' => ['nullable', 'string', 'max:60'],
            'status' => ['nullable', 'string', 'max:30'],
            'priority' => ['nullable', 'integer', 'between:1,5'],
            'memo' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * カスタムバリデーションエラーメッセージを取得する
     *
     * @return array<string, string> フィールド別エラーメッセージ配列
     */
    public function messages(): array
    {
        return [
            'company_name.required' => '会社名は必須です',
            'company_name.max' => '会社名は120文字以内で入力してください',
            'company_name.unique' => 'この会社名は既に登録されています',
            'contact_name.max' => '担当者名は60文字以内で入力してください',
            'email.email' => '有効なメールアドレスを入力してください',
            'email.max' => 'メールアドレスは255文字以内で入力してください',
            'email.unique' => 'このメールアドレスは既に登録されています',
            'phone.max' => '電話番号は30文字以内で入力してください',
            'industry.max' => '業界は60文字以内で入力してください',
            'temperature_rating.in' => '温度感はA、B、C、D、E、Fのいずれかを選択してください',
            'area.max' => 'エリアは60文字以内で入力してください',
            'status.max' => 'ステータスは30文字以内で入力してください',
            'priority.between' => '優先度は1から5の間で選択してください',
            'memo.max' => 'メモは2000文字以内で入力してください',
        ];
    }

    /**
     * バリデーション前にリクエストデータを正規化する
     */
    protected function prepareForValidation(): void
    {
        // 電話番号の正規化（ハイフン・スペース・括弧を除去）
        // input()メソッドはFormRequestの親クラス（Illuminate\Http\Request）から継承
        if ($this->input('phone')) {
            // merge()メソッドもIlluminate\Http\Requestから継承
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->input('phone')),
            ]);
        }

        // メールアドレスの正規化
        if ($this->input('email')) {
            $this->merge([
                'email' => strtolower(trim($this->input('email'))),
            ]);
        }

        // 会社名の前後空白除去
        if ($this->input('company_name')) {
            $this->merge([
                'company_name' => trim($this->input('company_name')),
            ]);
        }

        // user_idを自動設定
        // Auth::id()はLaravelの標準ヘルパー関数
        $this->merge([
            'user_id' => Auth::id(),
        ]);
    }
}
