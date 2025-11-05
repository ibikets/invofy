<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name'     => ['required','string','max:255'],
            'tax_ids'  => ['nullable','array'],
            'tax_ids.*'=> ['ulid','exists:taxes,id'],
            'active'   => ['boolean'],
        ];
    }
}
