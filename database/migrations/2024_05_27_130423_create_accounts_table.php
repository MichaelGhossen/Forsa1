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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            //$table->unsignedBigInteger('job_owner_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
           // $table->foreign('job_owner_id')->references('id')->on('job_owners')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
