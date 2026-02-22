<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

                // Opsional: Untuk tahu koin/jasa ini dipakai di mesin mana (jika klien ingin laporan sedetail itu)
                $table->foreignId('machine_id')->nullable()->constrained('machines')->onDelete('set null');

                $table->integer('quantity');
                $table->integer('price'); // HARGA MATI SAAT TRANSAKSI TERJADI
                $table->integer('subtotal'); // quantity * price
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
