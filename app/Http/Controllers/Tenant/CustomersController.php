<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Directory\Customer;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    // GET /{tenant}/customers
    public function index(Request $request)
    {
        $q = Customer::query();

        if ($search = $request->query('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('display_name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        return response()->json($q->orderBy('display_name')->paginate(20));
    }

    // POST /{tenant}/customers
    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());
        return response()->json($customer, 201);
    }

    // GET /{tenant}/customers/{customer}
    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    // PUT/PATCH /{tenant}/customers/{customer}
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        return response()->json($customer);
    }

    // DELETE /{tenant}/customers/{customer}
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->noContent();
    }
}
