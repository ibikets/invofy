<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'type'            => ['required','in:service,good'],
            'sku'             => ['nullable','string','max:100'],
            'name'            => ['required','string','max:255'],
            'description'     => ['nullable','string'],
            'unit'            => ['nullable','string','max:20'],
            'default_price'   => ['required','numeric','min:0'],
            'default_discount'=> ['nullable','numeric','min:0'],
            'tax_profile_id'  => ['nullable','ulid','exists:tax_profiles,id'],
            'custom_fields'   => ['nullable','array'],
            'active'          => ['boolean'],
        ];
    }
}
