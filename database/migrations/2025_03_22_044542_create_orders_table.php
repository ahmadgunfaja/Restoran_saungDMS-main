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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone');
            $table->string('email');
            $table->foreignId('table_id')->constrained()->onDelete('cascade');  // Relasi dengan tabel 'tables'
            $table->decimal('total_price', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');  // Status Pembayaran
            $table->string('qris_screenshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
