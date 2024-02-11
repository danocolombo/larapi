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
        Schema::create('meetings', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->date('meeting_date')->required();
            $table->string('title', 50)->required();
            $table->enum('meeting_type', ['Lesson', 'Other', 'Special', 'Testimony'])->required();
            $table->string('mtg_comp_key', 50);
            $table->string('announcements_contact', 50)->nullable();
            $table->smallInteger('attendance_count')->nullable();
            $table->string('av_contact', 50)->nullable();
            $table->string('cafe_contact', 50)->nullable();
            $table->smallInteger('cafe_count')->nullable();
            $table->string('children_contact', 50)->nullable();
            $table->smallInteger('children_count')->nullable();
            $table->string('cleanup_contact', 50)->nullable();
            $table->string('closing_contact', 50)->nullable();
            $table->decimal('donations', 6, 2)->nullable();
            $table->string('facilitator_contact', 50)->nullable();
            $table->string('greeter_contact1', 50)->nullable();
            $table->string('greeter_contact2', 50)->nullable();
            $table->string('meal', 50)->nullable();
            $table->string('meal_contact', 50)->nullable();
            $table->smallInteger('meal_count')->nullable();
            $table->smallInteger('newcomers_count')->nullable();
            $table->text('notes')->nullable();
            $table->string('nursery_contact', 50)->nullable();
            $table->smallInteger('nursery_count')->nullable();
            $table->string('resource_contact', 50)->nullable();
            $table->string('security_contact', 50)->nullable();
            $table->string('setup_contact', 50)->nullable();
            $table->string('support_contact', 50)->nullable();
            $table->string('transportation_contact', 50)->nullable();
            $table->smallInteger('transportation_count')->nullable();
            $table->string('worship', 50)->nullable();
            $table->string('youth_contact', 50)->nullable();
            $table->smallInteger('youth_count')->nullable();
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
