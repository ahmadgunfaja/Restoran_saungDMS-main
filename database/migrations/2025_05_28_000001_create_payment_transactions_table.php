<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTransactionsTable extends Migration
{
  public function up(): void
  {
    Schema::create('payment_transactions', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id');
      $table->string('merchant_ref');
      $table->string('payment_channel');
      $table->decimal('amount', 15, 2);
      $table->string('customer_name');
      $table->string('customer_email');
      $table->string('customer_phone');
      $table->json('order_items'); // simpan array order_items (sku, name, price, quantity)
      $table->string('callback_url')->nullable();
      $table->string('return_url')->nullable();
      $table->unsignedBigInteger('expired_time');
      $table->string('signature');
      $table->string('status')->default('pending');
      $table->text('payment_response')->nullable();
      $table->timestamps();

      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('payment_transactions');
  }
};
