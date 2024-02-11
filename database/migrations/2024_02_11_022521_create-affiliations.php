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
        Schema::dropIfExists('affiliations');
        Schema::create('affiliations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('role', 20)->default('guest');
            $table->string('status', 20)->default('active');
            $table->uuid('person_id'); // Use UUID for the foreign key
            // Define the foreign key constraint
            $table->foreign('person_id')
                ->references('id')->on('persons')
                ->onDelete('cascade'); // Add onDelete('cascade') for automatic deletion
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
