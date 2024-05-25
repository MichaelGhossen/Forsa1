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
        // Schema::create('jobs_for_freelancers_users', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('jobs_for_freelancer_id');
        //     $table->unsignedBigInteger('user_id');
        //     $table->timestamps();
        //     $table->foreign('jobs_for_freelancer_id')->references('id')->on('j_obs_for_freelancers')->onDelete('cascade');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('jobs_for_freelancers_users');
    }
};
