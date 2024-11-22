<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザ名は必須です',
            'name.string' => 'ユーザ名は文字列でなければなりません',
            'name.max' => 'ユーザ名の制限は255文字です',

            'email.required' => 'メールアドレスは必須です',
            'email.string' => 'メールは有効な文字列でなければなりません',
            'email.email' => 'メールアドレスは有効なものでなければなりません',
            'email.lowercase' => 'メールアドレスは小文字でご入力ください',
            'email.max' => 'メールアドレスの制限は255文字です',
            'email.unique' => 'このメールアドレスは既に登録済みです',
        ];
    }
}
