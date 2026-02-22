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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Promo Ramadhan", "Jumat Berkah"
            
            // Relasi ke produk yang harus dibeli (misal: ID Koin)
            $table->foreignId('buy_product_id')->constrained('products')->onDelete('cascade');
            $table->integer('buy_qty'); // Jumlah syarat beli (misal: 10)
            
            // Relasi ke produk hadiah (misal: ID Detergen)
            $table->foreignId('free_product_id')->constrained('products')->onDelete('cascade');
            $table->integer('free_qty'); // Jumlah hadiah (misal: 1)
            
            $table->dateTime('start_date'); // Kapan promo dimulai
            $table->dateTime('end_date'); // Kapan promo berakhir
            $table->boolean('is_active')->default(true); // Saklar on/off manual
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
