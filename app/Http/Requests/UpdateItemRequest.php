<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'type'            => ['sometimes','in:service,good'],
            'sku'             => ['sometimes','nullable','string','max:100'],
            'name'            => ['sometimes','string','max:255'],
            'description'     => ['sometimes','nullable','string'],
            'unit'            => ['sometimes','nullable','string','max:20'],
            'default_price'   => ['sometimes','numeric','min:0'],
            'default_discount'=> ['sometimes','nullable','numeric','min:0'],
            'tax_profile_id'  => ['sometimes','nullable','ulid','exists:tax_profiles,id'],
            'custom_fields'   => ['sometimes','nullable','array'],
            'active'          => ['sometimes','boolean'],
        ];
    }
}
