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
        Schema::create('j_obs_for_freelancers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('min_duration');
            $table->date('max_duration');
            $table->text('languages');
            $table->text('description');
            $table->text('requirements');
            $table->decimal('min_salary', 10, 2)->nullable();
            $table->decimal('max_salary', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('j_obs_for_freelancers');
    }
};
