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
        Schema::create('cv_company', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cv_id');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();

            $table->foreign('cv_id')->references('id')->on('c_v_s')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_company');
    }
};
