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
            $table->unsignedBigInteger('cv_id');
            $table->unsignedBigInteger('job_owner_id');
            $table->foreign('j_obs_for_freelancers_id')->references('id')->on('j_obs_for_freelancers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cv_id')->references('id')->on('c_v_s')->onDelete('cascade');
            $table->foreign('job_owner_id')->references('id')->on('job_owners')->onDelete('cascade');
            $table->enum('order_status', ['processing', 'rejected', 'accepted'])
            ->default('processing');
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
