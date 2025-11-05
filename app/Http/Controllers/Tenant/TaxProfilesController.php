<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaxProfileRequest;
use App\Http\Requests\UpdateTaxProfileRequest;
use App\Models\Finance\TaxProfile;
use Illuminate\Http\Request;

class TaxProfilesController extends Controller
{
    public function index(Request $request)
    {
        $q = TaxProfile::query();
        if ($s = $request->query('q')) {
            $q->where('name','ilike',"%{$s}%");
        }
        return response()->json($q->orderBy('name')->paginate(20));
    }

    public function store(StoreTaxProfileRequest $request)
    {
        $profile = TaxProfile::create($request->validated());
        return response()->json($profile, 201);
    }

    public function show(TaxProfile $taxProfile)
    {
        return response()->json($taxProfile);
    }

    public function update(UpdateTaxProfileRequest $request, TaxProfile $taxProfile)
    {
        $taxProfile->update($request->validated());
        return response()->json($taxProfile);
    }

    public function destroy(TaxProfile $taxProfile)
    {
        $taxProfile->delete();
        return response()->noContent();
    }
}
