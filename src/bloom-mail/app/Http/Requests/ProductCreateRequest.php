<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
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
            "treatment_begin_date" => ['required'],
            "shop_id" => ['required'],
            "content_time_frame" => ['required'],
            "product_detail" => ['required'],
            "price" => ['required'],
            "sale_start_date" => ['required'],
            "sale_end_date" => ['required'],
        ];
    }
}
