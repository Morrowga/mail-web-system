<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopCreateRequest extends FormRequest
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
            "shop_type" => ['required'],
            "address" => ['required'],
            "opening_time" => ['required'],
            "closing_time" => ['required'],
            "phone_no" => ['required'],
            "reception_start_time" => ['required'],
            "reception_end_time" => ['required'],
            "close_day" => ['required'],
            "close_day_text" => ['nullable'],
            "room_numbers" => ['required'],
            "access" => ['required'],
            "parking_nearby" => ['required'],
            "store_direction" => ['required'],
            "gmap_location" => ['required'],
            "gmap_photos" => ['required'],
            "youtube" => ['required'],
            "shop_images" => ['nullable'],
            "top_statement" => ['required'],
            "store_sub_title" => ['required'],
            "store_btm_text" => ['required'],
            "store_sub_title_two" => ['required'],
            "store_btm_text_two" => ['required'],
            "status" => ['required']
        ];
    }
}
