<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AccountUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string' , 'max:255'],
            'role_id' => ['required'],
            'login_id' => ['required', 'string' , 'lowercase', 'max:255',
                Rule::unique('users', 'login_id')->ignore($userId), // Ignore the current user's email
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ユーザ名は必須です',
            'name.string' => 'ユーザ名は文字列でなければなりません',
            'name.max' => 'ユーザ名の制限は255文字です',

            'role_id.required' => 'Role is required',

            'login_id.required' => 'メールアドレスは必須です',
            'login_id.string' => 'メールは有効な文字列でなければなりません',
            'login_id.lowercase' => 'メールアドレスは小文字でご入力ください',
            'login_id.max' => 'メールアドレスの制限は255文字です',
            'login_id.unique' => 'このメールアドレスは既に登録済みです',
        ];
    }
}
