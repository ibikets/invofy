<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNumberSequenceRequest;
use App\Http\Requests\UpdateNumberSequenceRequest;
use App\Models\Settings\NumberSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NumberSequencesController extends Controller
{
    public function index()
    {
        return response()->json(
            NumberSequence::query()->orderBy('entity_type')->get()
        );
    }

    public function store(StoreNumberSequenceRequest $request)
    {
        $seq = NumberSequence::create($request->validated());
        return response()->json($seq, 201);
    }

    public function show(NumberSequence $numberSequence)
    {
        return response()->json($numberSequence);
    }

    public function update(UpdateNumberSequenceRequest $request, NumberSequence $numberSequence)
    {
        $numberSequence->update($request->validated());
        return response()->json($numberSequence);
    }

    public function destroy(NumberSequence $numberSequence)
    {
        $numberSequence->delete();
        return response()->noContent();
    }

    // helper for testing: get next formatted
    public function next(string $entity)
    {
        $seq = NumberSequence::firstOrCreate(
            ['entity_type' => $entity],
            ['prefix' => strtoupper(substr($entity,0,3)).'-', 'padding' => 5, 'next_number' => 1]
        );

        $formatted = DB::transaction(function () use ($seq) {
            // lock row FOR UPDATE to avoid race conditions
            $s = NumberSequence::whereKey($seq->getKey())->lockForUpdate()->first();
            $num = $s->next_number;
            $s->next_number = $num + 1;
            $s->save();

            return $s->prefix . str_pad((string)$num, $s->padding, '0', STR_PAD_LEFT);
        });

        return response()->json(['next' => $formatted]);
    }
}
