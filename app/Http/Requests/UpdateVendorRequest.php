<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'display_name'  => ['sometimes','string','max:255'],
            'email'         => ['sometimes','nullable','email','max:255'],
            'phone'         => ['sometimes','nullable','string','max:50'],
            'address'       => ['sometimes','nullable','array'],
            'custom_fields' => ['sometimes','nullable','array'],
            'status'        => ['sometimes','in:active,archived'],
        ];
    }
}
