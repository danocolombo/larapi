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
        Schema::create('app', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Define the UUID primary key
            $table->timestamps();
            $table->string('app_name', 25)->default('MTR');
            $table->string('android_version', 10)->nullable();
            $table->string('ios_version', 10)->nullable();
            $table->string('web_version', 10)->nullable();
            $table->string('default_profile_picture', 100)->nullable();
            $table->string('logo_picture', 200)->nullable();
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
