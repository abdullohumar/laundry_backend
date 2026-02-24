<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        /** @var \App\Models\User $user */
        $user = $request->user();

        // 1. Validasi Wajib: Kasir harus punya shift aktif!
        $activeShift = $user->shifts()->where('status', 'open')->first();
        if (!$activeShift) {
            return response()->json(['message' => 'Silakan buka shift terlebih dahulu sebelum bertransaksi.'], 403);
        }

        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string',
            'payment_method' => 'required|in:cash,qris,transfer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.machine_id' => 'nullable|exists:machines,id',
        ]);

        // Gunakan DB Transaction agar jika ada error di tengah jalan, database tidak rusak
        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $coinBought = 0; // Untuk menghitung promo loyalitas

            // 2. Buat Header Transaksi
            $transaction = $activeShift->transactions()->create([
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'payment_method' => $request->payment_method,
                'total_amount' => 0, // Akan diupdate di bawah
            ]);

            // 3. Proses setiap barang di keranjang
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                
                // MENGUNCI HARGA: Simpan harga saat ini ke detail
                $transaction->details()->create([
                    'product_id' => $product->id,
                    'machine_id' => $item['machine_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $product->price, 
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;

                // Hitung jumlah koin yang dibeli untuk promo
                if ($product->type === 'coin') {
                    $coinBought += $item['quantity'];
                }

                // Kurangi stok jika barang fisik (addon)
                if ($product->type === 'addon') {
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi.");
                    }
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Update Total Belanja Akhir
            $transaction->update(['total_amount' => $totalAmount]);

            // 4. LOGIKA PROMO LOYALITAS (Beli 10 Gratis 1)
            if ($request->customer_id && $coinBought > 0) {
                $customer = Customer::lockForUpdate()->find($request->customer_id);
                $customer->increment('coin_balance', $coinBought);

                // Ambil setting dinamis dari tabel settings (atau default jika belum diatur)
                $targetKoin = Setting::where('key', 'promo_coin_target')->value('value') ?? 10;
                $rewardId = Setting::where('key', 'promo_reward_product_id')->value('value');

                // Jika saldo koin sudah memenuhi syarat, dan Super Admin sudah mengatur hadiahnya
                while ($customer->coin_balance >= $targetKoin && $rewardId) {
                    $customer->decrement('coin_balance', $targetKoin);
                    
                    // Sisipkan detergen gratis (harga 0)
                    $transaction->details()->create([
                        'product_id' => $rewardId,
                        'quantity' => 1,
                        'price' => 0,
                        'subtotal' => 0,
                    ]);
                }
            }

            // Jika semua lancar, permanenkan data
            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil',
                'data' => $transaction->load('details.product', 'customer')
            ], 201);

        } catch (\Exception $e) {
            // Jika ada error (misal stok habis), batalkan semua proses query di atas
            DB::rollBack();
            return response()->json(['message' => 'Transaksi gagal: ' . $e->getMessage()], 422);
        }
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
