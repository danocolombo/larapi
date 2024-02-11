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
        Schema::create('default_groups', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('title', 100)->required();
            $table->string('location', 50)->nullable();
            $table->string('gender', 1);
            $table->string('facilitator', 50)->nullable();
            $table->uuid('organization_id'); // Use UUID for the foreign key
            // Define the foreign key constraint
            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->onDelete('cascade'); // Add onDelete('cascade') for automatic deletion
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
