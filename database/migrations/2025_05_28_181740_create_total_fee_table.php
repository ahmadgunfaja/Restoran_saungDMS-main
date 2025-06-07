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
        Schema::table('payment_transactions', function (Blueprint $table) {
            // Tambahkan kolom total_fee untuk menyimpan total biaya transaksi
            $table->decimal('total_fee', 15, 2)->default(0.00)->after('amount')->comment('Total biaya transaksi termasuk biaya layanan dan pajak')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn('total_fee');
        });
    }
};
