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
        Schema::table('meetings', function (Blueprint $table) {
            $table->dateTime('meeting_date')->change(); // Change the type to dateTime
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
