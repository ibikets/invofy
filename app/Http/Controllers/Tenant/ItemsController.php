<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Catalog\Item;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $q = Item::query();
        if ($s = $request->query('q')) {
            $q->where(function ($w) use ($s) {
                $w->where('name','ilike',"%{$s}%")
                    ->orWhere('sku','ilike',"%{$s}%");
            });
        }
        return response()->json($q->orderBy('name')->paginate(20));
    }

    public function store(StoreItemRequest $request)
    {
        $item = Item::create($request->validated());
        return response()->json($item, 201);
    }

    public function show(Item $item)
    {
        return response()->json($item);
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $item->update($request->validated());
        return response()->json($item);
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return response()->noContent();
    }
}
