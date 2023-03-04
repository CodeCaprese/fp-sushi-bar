<?php

namespace App\Http\Requests;

use App\Http\FPLib\Settings;
use Illuminate\Foundation\Http\FormRequest;

class SeatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = "required|gte:1|lte:" . Settings::amountOfSeats();
        return [
            "numberOfCustomers" => $rules,
        ];
    }
}
