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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            // Customer bisa null jika pelanggan tidak mau memberikan nomor HP
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            
            $table->string('customer_name')->nullable(); // Nama manual jika tidak masuk tabel customers
            $table->integer('total_amount'); // Total keseluruhan belanja
            $table->enum('payment_method', ['cash', 'qris', 'transfer']);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
