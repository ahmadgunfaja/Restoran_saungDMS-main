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
      $table->unsignedBigInteger('order_id');
      $table->unsignedBigInteger('user_id');
      $table->decimal('amount', 15, 2);
      $table->string('payment_type')->nullable();
      $table->string('transaction_status')->default('pending');
      $table->string('snap_token')->nullable();
      $table->json('payment_response')->nullable();
      $table->timestamps();

      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
