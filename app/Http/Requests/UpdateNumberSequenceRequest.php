<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNumberSequenceRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'prefix'      => ['sometimes','string','max:20'],
            'padding'     => ['sometimes','integer','between:1,12'],
            'next_number' => ['sometimes','integer','min:1'],
        ];
    }
}
