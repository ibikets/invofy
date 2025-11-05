<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name'      => ['sometimes','string','max:255'],
            'rate'      => ['sometimes','numeric','min:0','max:1000'],
            'compound'  => ['sometimes','boolean'],
            'inclusive' => ['sometimes','boolean'],
            'active'    => ['sometimes','boolean'],
        ];
    }
}
