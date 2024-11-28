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
            'login_id' => [
                'required',
                'string',
                'lowercase',
                'max:255',
                Rule::unique('users', 'login_id')->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザ名は必須です',
            'name.string' => 'ユーザ名は文字列でなければなりません',
            'name.max' => 'ユーザ名の制限は255文字です',

            'login_id.required' => 'メールアドレスは必須です',
            'login_id.string' => 'メールは有効な文字列でなければなりません',
            'login_id.lowercase' => 'メールアドレスは小文字でご入力ください',
            'login_id.max' => 'メールアドレスの制限は255文字です',
            'login_id.unique' => 'このメールアドレスは既に登録済みです',
        ];
    }
}
