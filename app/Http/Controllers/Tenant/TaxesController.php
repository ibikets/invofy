<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use App\Models\Finance\Tax;
use Illuminate\Http\Request;

class TaxesController extends Controller
{
    public function index(Request $request)
    {
        $q = Tax::query();
        if ($s = $request->query('q')) {
            $q->where('name','ilike',"%{$s}%");
        }
        return response()->json($q->orderBy('name')->paginate(20));
    }

    public function store(StoreTaxRequest $request)
    {
        $tax = Tax::create($request->validated());
        return response()->json($tax, 201);
    }

    public function show(Tax $tax)
    {
        return response()->json($tax);
    }

    public function update(UpdateTaxRequest $request, Tax $tax)
    {
        $tax->update($request->validated());
        return response()->json($tax);
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();
        return response()->noContent();
    }
}
