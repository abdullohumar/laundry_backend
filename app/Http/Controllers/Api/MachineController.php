<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $machines = Machine::all();
        return response()->json(['data' => $machines]);
    }

    public function updateStatus(Request $request, Machine $machine)
    {
        $request->validate([
            'status' => 'required|in:idle,in_use,maintenance'
        ]);

        $machine->update([
            'status' => $request->status,
            'started_at' => $request->status === 'in_use' ? now() : null
        ]);

        return response()->json(['message' => 'Status mesin diperbarui', 'data' => $machine]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:machines',
            'type' => 'required|in:washer,dryer',
        ]);

        $machine = Machine::create($request->all());
        return response()->json(['message' => 'Mesin ditambahkan', 'data' => $machine], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Machine $machine)
    {
        $machine->update($request->all());
        return response()->json(['message' => 'Data mesin diperbarui', 'data' => $machine]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machine $machine)
    {
        $machine->delete();
        return response()->json(['message' => 'Mesin dihapus']);
    }
}
