<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $customer = $this->route('customer');
        return auth()->check() && $customer->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = auth()->id();
        $customerId = $this->route('customer')->id;

        return [
            'company_name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('customers')
                    ->where('user_id', $userId)
                    ->ignore($customerId),
            ],
            'contact_name' => ['nullable', 'string', 'max:60'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers')
                    ->where('user_id', $userId)
                    ->ignore($customerId),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'industry' => ['nullable', 'string', 'max:60'],
            'temperature_rating' => ['nullable', 'string', 'in:A,B,C,D,E,F'],
            'area' => ['nullable', 'string', 'max:60'],
            'status' => ['nullable', 'string', 'max:30'],
            'priority' => ['nullable', 'integer', 'between:1,5'],
            'memo' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // 電話番号の正規化（ハイフン・スペース・括弧を除去）
        if ($this->phone) {
            $this->merge([
                'phone' => preg_replace('/[^0-9]/', '', $this->phone),
            ]);
        }

        // メールアドレスの正規化
        if ($this->email) {
            $this->merge([
                'email' => strtolower(trim($this->email)),
            ]);
        }

        // 会社名の前後空白除去
        if ($this->company_name) {
            $this->merge([
                'company_name' => trim($this->company_name),
            ]);
        }
    }
}
