<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'display_name'    => ['sometimes','string','max:255'],
            'email'           => ['sometimes','nullable','email','max:255'],
            'emails'          => ['sometimes','nullable','array'],
            'emails.*'        => ['email'],
            'phone'           => ['sometimes','nullable','string','max:50'],
            'currency'        => ['sometimes','nullable','string','size:3'],
            'tax_exempt'      => ['sometimes','boolean'],
            'billing_address' => ['sometimes','nullable','array'],
            'shipping_address'=> ['sometimes','nullable','array'],
            'custom_fields'   => ['sometimes','nullable','array'],
            'status'          => ['sometimes','in:active,archived'],
        ];
    }
}
