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
        Schema::table('locations', function (Blueprint $table) {
            $table->string('aws_id')->nullable(); // Example: adding a new nullable string column
        });
        Schema::table('affiliations', function (Blueprint $table) {
            $table->string('aws_id')->nullable(); // Example: adding a new nullable string column
        });
        Schema::table('default_groups', function (Blueprint $table) {
            $table->string('aws_id')->nullable(); // Example: adding a new nullable string column
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->string('aws_id')->nullable(); // Example: adding a new nullable string column
        });
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('aws_id')->nullable(); // Example: adding a new nullable string column
        });
        Schema::table('persons', function (Blueprint $table) {
            $table->string('aws_id')->nullable(); // Example: adding a new nullable string column
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
