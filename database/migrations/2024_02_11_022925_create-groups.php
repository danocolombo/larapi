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
        Schema::create('groups', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('grp_comp_key', 50)->required();
            $table->string('title', 100)->required();
            $table->string('location', 50)->nullable();
            $table->string('gender', 1);
            $table->smallInteger('attendance')->nullable();
            $table->string('facilitator', 50)->nullable();
            $table->string('cofacilitator', 50)->nullable();
            $table->text('notes')->nullable();
            $table->uuid('meeting_id'); // Use UUID for the foreign key
            // Define the foreign key constraint
            $table->foreign('meeting_id')
                ->references('id')->on('meetings')
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
