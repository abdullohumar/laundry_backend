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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // Contoh: W1, W2, D1
            $table->enum('type', ['washer', 'dryer']);
            $table->enum('status', ['idle', 'in_use', 'maintenance'])->default('idle');
            $table->integer('position_x')->nullable(); // Untuk kordinat visual UI
            $table->integer('position_y')->nullable();
            $table->timestamp('started_at')->nullable(); // Penanda waktu mulai untuk auto-idle
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
