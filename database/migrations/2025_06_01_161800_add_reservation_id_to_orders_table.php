<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('orders', function (Blueprint $table) {
      $table->unsignedBigInteger('reservation_id')->nullable()->after('id');
      $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('set null');
    });
  }

  public function down(): void
  {
    Schema::table('orders', function (Blueprint $table) {
      $table->dropForeign(['reservation_id']);
      $table->dropColumn('reservation_id');
    });
  }
};
