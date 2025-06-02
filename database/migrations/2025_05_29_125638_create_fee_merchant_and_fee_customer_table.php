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
            $table->decimal('fee_merchant', 15, 2)->default(0)->after('amount')->comment('Fee charged to merchant');
            $table->decimal('fee_customer', 15, 2)->default(0)->after('fee_merchant')->comment('Fee charged to customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['fee_merchant', 'fee_customer']);
        });
    }
};
