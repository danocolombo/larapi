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
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('street', 100)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state_prov', 25)->default('UNK');
            $table->string('postal_code', 15)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
