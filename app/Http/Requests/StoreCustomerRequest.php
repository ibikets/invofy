<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'display_name'    => ['required','string','max:255'],
            'email'           => ['nullable','email','max:255'],
            'emails'          => ['nullable','array'],
            'emails.*'        => ['email'],
            'phone'           => ['nullable','string','max:50'],
            'currency'        => ['nullable','string','size:3'],
            'tax_exempt'      => ['boolean'],
            'billing_address' => ['nullable','array'],
            'shipping_address'=> ['nullable','array'],
            'custom_fields'   => ['nullable','array'],
            'status'          => ['in:active,archived'],
        ];
    }
}
