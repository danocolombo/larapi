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
        Schema::create('persons', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('sub', 50)->required();
            $table->string('username', 50)->required();
            $table->string('first_name', 20)->nullable();
            $table->string('last_name', 25)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('shirt', 20)->nullable();
            $table->string('birthday', 20)->nullable();
            $table->string('picture', 100)->nullable();
            $table->uuid('default_org_id')->nullable(); // Use UUID for the foreign key
            // Define the foreign key constraint
            $table->foreign('default_org_id')
                ->references('id')->on('organizations');
            $table->uuid('location_id')->nullable(); // Use UUID for the foreign key
            // Define the foreign key constraint
            $table->foreign('location_id')
                ->references('id')->on('locations');
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
