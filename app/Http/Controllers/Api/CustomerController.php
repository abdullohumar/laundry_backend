<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::latest()->get();
        return response()->json(['data' => $customers]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'name' => 'nullable|string|max:255'
        ]);

        $phone = $request->phone_number;
        if(str_starts_with($phone, '08')) {
            $phone = '628' . substr($phone,2);
        } elseif (str_starts_with($phone, '+628')) {
            $phone = '628' . substr($phone,4);
        }

        // Cek apakah nomor sudah ada
        $existingCustomer = Customer::where('phone_number', $phone)->first();
        if($existingCustomer) {
            return response()->json([
                'message' => 'Nomor Telepon Sudah Terdaftar',
                'data' => $existingCustomer
            ], 409);
        }

        $customer = Customer::create([
            'phone_number' => $phone,
            'name' => $request->name,
            'coin_balance' => 0
        ]);

        return response()->json([
            'message' => 'Pelanggan berhasil ditambahkan',
            'data' => $customer
        ], 201);
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
