<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function current(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $shift = $user->shifts()->where('status', 'open')->first();

        return response()->json([
            'is_open' => $shift ? true : false,
            'data' => $shift
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function start(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user->shifts()->where('status', 'open')->exists()) {
            return response()->json([
                'message' => 'Anda masih memiliki shift yang terbuka'
            ], 400);
        }

        $request->validate([
            'starting_cash' => 'required|integer|min:0'
        ]);

        $shift = $user->shifts()->create([
            'starting_cash' => $request->starting_cash,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        return response()->json([
            'message' => 'Shift berhasil dibuka',
            'data' => $shift
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function close(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $shift = $user->shifts()->where('status', 'open')->first();

        if (!$shift) {
            return response()->json(['message' => 'Tidak ada shift aktif yang bisa ditutup.'], 400);
        }

        $request->validate(['ending_cash' => 'required|integer|min:0']);

        $shift->update([
            'ending_cash' => $request->ending_cash,
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return response()->json(['message' => 'Shift berhasil ditutup', 'data' => $shift]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
