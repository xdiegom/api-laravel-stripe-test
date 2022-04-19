<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'card_number' => ['required'],
            'expiry_month' => ['required'],
            'expiry_year' => ['required'],
            'cvc' => ['required'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'string']
        ];
    }
}
