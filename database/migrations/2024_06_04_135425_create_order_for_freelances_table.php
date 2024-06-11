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
        Schema::create('order_for_freelances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('j_obs_for_freelancers_id')->nullable(false);
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('j_obs_for_freelancers_id')->references('id')->on('j_obs_for_freelancers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_for_freelances');
    }
};
