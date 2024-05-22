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
            // $table->integer('min_age');
            // $table->integer('max_age');
            // $table->string('gender')->nullable();
            $table->text('languages');
            $table->text('description');
            $table->text('requirements');
          //  $table->unsignedBigInteger('category_id')->nullable();
           // $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->decimal('min_salary', 10, 2)->nullable();
            $table->decimal('max_salary', 10, 2)->nullable();
            // $table->string('location')->nullable();
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
