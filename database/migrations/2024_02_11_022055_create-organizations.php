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
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('name', 50)->required();
            $table->string('code', 10)->required();
            $table->string('hero_message', 100)->nullable();
            // Define the foreign key constraint
            $table->uuid('location_id')->nullable();
            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
                ->onDelete('set null'); // Set NULL on delete of location record

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
