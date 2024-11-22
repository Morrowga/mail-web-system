<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
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
            "title" => ['required'],
            "subject" => ['required'],
            "template_category_id" => ['required'],
            "message_content" => ['required'],
        ];
    }


    public function messages(): array
    {
        return [
            'title.required' => 'テンプレート名は必須です.',
            'subject' => '件名は必須です.',
            'template_category_id' => 'テンプレートカテゴリは必須です.',
            'message_content' => '本文はは必須です.',
        ];
    }
}
