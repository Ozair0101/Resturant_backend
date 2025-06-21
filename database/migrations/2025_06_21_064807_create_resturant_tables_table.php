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
        Schema::create('resturant_tables', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->string('capacity');
            $table->enum('status', ['reserved', 'non_reserved'])->default('non_reserved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resturant_tables');
    }
};
