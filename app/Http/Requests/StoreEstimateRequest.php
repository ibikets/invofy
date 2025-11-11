<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstimateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_id' => ['required','ulid','exists:customers,id'],
            'issue_date'  => ['nullable','date'],
            'expiry_date' => ['nullable','date','after_or_equal:issue_date'],
            'currency'    => ['nullable','string','size:3'],
            'exchange_rate'=>['nullable','numeric','min:0'],
            'notes'       => ['nullable','string'],
            'items'       => ['required','array','min:1'],
            'items.*.item_id'       => ['nullable','ulid','exists:items,id'],
            'items.*.name'          => ['required','string','max:255'],
            'items.*.description'   => ['nullable','string'],
            'items.*.qty'           => ['required','numeric','min:0'],
            'items.*.unit'          => ['nullable','string','max:20'],
            'items.*.unit_price'    => ['required','numeric','min:0'],
            'items.*.discount'      => ['nullable','numeric','min:0'],
            'items.*.tax_profile_id'=> ['nullable','ulid','exists:tax_profiles,id'],
            'items.*.sort_order'    => ['nullable','integer','min:0'],
        ];
    }
}
