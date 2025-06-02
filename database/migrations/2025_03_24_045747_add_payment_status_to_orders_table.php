<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_status')) {
            $table->string('payment_status')->default('pending'); // Menambahkan kolom untuk status pembayaran
        }

        if (!Schema::hasColumn('orders', 'qris_screenshot')) {
            $table->string('qris_screenshot')->nullable(); // Menambahkan kolom untuk menyimpan path screenshot QRIS
        }
        });
    }
    
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->dropColumn('qris_screenshot');
        });
    }
    
};
