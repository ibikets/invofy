<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name'     => ['sometimes','string','max:255'],
            'tax_ids'  => ['sometimes','nullable','array'],
            'tax_ids.*'=> ['ulid','exists:taxes,id'],
            'active'   => ['sometimes','boolean'],
        ];
    }
}
