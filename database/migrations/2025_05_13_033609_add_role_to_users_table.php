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
    Schema::create('kokis', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('no_hp');
        $table->string('password');
        $table->string('role')->default('koki');  // Role koki
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('kokis');
}

};
