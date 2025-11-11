<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'issue_date'  => ['sometimes','nullable','date'],
            'due_date'    => ['sometimes','nullable','date','after_or_equal:issue_date'],
            'currency'    => ['sometimes','nullable','string','size:3'],
            'exchange_rate'=>['sometimes','nullable','numeric','min:0'],
            'notes'       => ['sometimes','nullable','string'],
            'status'      => ['sometimes','in:draft,sent,partially_paid,paid,overdue,cancelled'],
            'items'       => ['sometimes','array','min:1'],
            'items.*.item_id'       => ['sometimes','nullable','ulid','exists:items,id'],
            'items.*.name'          => ['sometimes','string','max:255'],
            'items.*.description'   => ['sometimes','nullable','string'],
            'items.*.qty'           => ['sometimes','numeric','min:0'],
            'items.*.unit'          => ['sometimes','nullable','string','max:20'],
            'items.*.unit_price'    => ['sometimes','numeric','min:0'],
            'items.*.discount'      => ['sometimes','nullable','numeric','min:0'],
            'items.*.tax_profile_id'=> ['sometimes','nullable','ulid','exists:tax_profiles,id'],
            'items.*.sort_order'    => ['sometimes','integer','min:0'],
        ];
    }
}
