<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FolderRequest extends FormRequest
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
        return [
            "name" => ['required'],
            "search_character" => ['required'],
            "method" => ['required']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'フォルダ名は必須です',
            'search_character.required' => '検索文字フィールドは必須です',
            'method.required' => '方法は必須です',
        ];
    }
}
