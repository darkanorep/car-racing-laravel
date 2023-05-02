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
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_id')->constrained();
            $table->unsignedBigInteger('car_1')->nullable();
            $table->unsignedBigInteger('car_2')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->string('bet_amount');
            $table->foreign('car_1')->references('id')->on('users');
            $table->foreign('car_2')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
