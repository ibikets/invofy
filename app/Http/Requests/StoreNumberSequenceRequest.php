<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNumberSequenceRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'entity_type' => ['required','string','max:50'],
            'prefix'      => ['required','string','max:20'],
            'padding'     => ['required','integer','between:1,12'],
            'next_number' => ['required','integer','min:1'],
        ];
    }
}
