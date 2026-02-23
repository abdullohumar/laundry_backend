<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $query = Transaction::with(['shift.user', 'customer', 'detail.product']);
        // LOGIKA FILTER PINTAR:
        // Jika dia BUKAN super_admin, maka dia adalah karyawan biasa.
        // Karyawan HANYA boleh melihat transaksi dari shift yang dia buka sendiri.
        if(!$user->hasRole('super_admin')) {
            $query->whereHas('shift', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $transactions = $query->latest()->get();
        return response()->json([
            'message' => 'Data transaksi berhasil diambil',
            'data' => $transactions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
