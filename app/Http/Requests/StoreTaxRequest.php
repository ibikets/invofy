<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name'      => ['required','string','max:255'],
            'rate'      => ['required','numeric','min:0','max:1000'],
            'compound'  => ['boolean'],
            'inclusive' => ['boolean'],
            'active'    => ['boolean'],
        ];
    }
}
