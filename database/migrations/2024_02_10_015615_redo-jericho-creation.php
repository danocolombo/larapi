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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
