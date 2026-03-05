<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        // 1. Siapkan Base Query yang sudah terfilter sesuai Role
        $query = Transaction::query();
        if (!$isSuperAdmin) {
            $query->whereHas('shift', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // 2. Hitung Omzet Hari Ini
        $todayRevenue = (clone $query)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        // 3. Hitung Omzet Bulan Ini
        $monthRevenue = (clone $query)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // 4. Hitung Total Transaksi Hari Ini
        $todayTransactions = (clone $query)
            ->whereDate('created_at', Carbon::today())
            ->count();

        // 5. Data Grafik Pendapatan 7 Hari Terakhir
        $last7Days = (clone $query)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json([
            'message' => 'Berhasil mengambil data analitik',
            'data' => [
                'revenue_today' => (int) $todayRevenue,
                'revenue_this_month' => (int) $monthRevenue,
                'transactions_today' => $todayTransactions,
                'chart_last_7_days' => $last7Days
            ]
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
