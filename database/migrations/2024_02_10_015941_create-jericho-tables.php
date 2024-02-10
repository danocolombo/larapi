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
        Schema::dropIfExists('system');
        Schema::dropIfExists('default_groups');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('meetings');
        Schema::dropIfExists('affiliations');
        Schema::dropIfExists('jericho_users');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('locations');

        Schema::create('system', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('app_name', 25)->default('MTR');
            $table->string('android_version', 10)->nullable();
            $table->string('ios_version', 10)->nullable();
            $table->string('web_version', 10)->nullable();
            $table->string('default_profile_picture', 100)->nullable();
            $table->string('logo_picture', 200)->nullable();
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('street', 100)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state_prov', 25)->default('UNK');
            $table->string('postal_code', 15)->nullable();
        });

        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('name', 50)->required();
            $table->string('code', 10)->required();
            $table->string('hero_message', 100)->nullable();
            $table->uuid('location_id')->nullable(); // Use UUID for the foreign key
            // Define the foreign key constraint
            $table->foreign('location_id')
                ->references('id')->on('locations');
        });

        Schema::create('jericho_users', function (Blueprint $table) {
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

        Schema::create('affiliations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('role', 20)->default('guest');
            $table->string('status', 20)->default('active');
            $table->uuid('user_id'); // Use UUID for the foreign key
            // Define the foreign key constraint
            $table->foreign('user_id')
                ->references('id')->on('jericho_users')
                ->onDelete('cascade'); // Add onDelete('cascade') for automatic deletion
            $table->uuid('organization_id'); // Use UUID for the foreign key
            // Define the foreign key constraint
            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->onDelete('cascade'); // Add onDelete('cascade') for automatic deletion
        });

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
                ->references('id')->on('organizations');
        });

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
                ->references('id')->on('meetings');
        });

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
