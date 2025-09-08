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
        Schema::create('locations', function (Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('location_code')->nullable();
            $table->string('location_name')->nullable();
            $table->unsignedBigInteger('location_code_parent')->nullable();
            $table->string('available_sources')->nullable();
            $table->string('language_name')->nullable();
            $table->string('keywords')->nullable();
            $table->string('serps')->nullable();

            $table->unique(['location_code','language_code']);

            $table->string('language_code')->nullable();
            $table->string('country_iso_code')->nullable();
            $table->string('location_type')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
