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
        Schema::create('prize_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bet_id')->nullable()->constrained();
            $table->bigInteger('prize_pool')->default(0);
            $table->foreignId('race_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_pools');
    }
};
