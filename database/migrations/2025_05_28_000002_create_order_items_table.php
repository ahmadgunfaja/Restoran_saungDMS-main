<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
  public function up(): void
  {
    Schema::create('order_items', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('order_id');
      $table->string('sku');
      $table->string('name');
      $table->decimal('price', 15, 2);
      $table->integer('quantity');
      $table->timestamps();

      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('order_items');
  }
}
