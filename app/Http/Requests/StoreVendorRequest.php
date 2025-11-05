<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'display_name'  => ['required','string','max:255'],
            'email'         => ['nullable','email','max:255'],
            'phone'         => ['nullable','string','max:50'],
            'address'       => ['nullable','array'],
            'custom_fields' => ['nullable','array'],
            'status'        => ['in:active,archived'],
        ];
    }
}
