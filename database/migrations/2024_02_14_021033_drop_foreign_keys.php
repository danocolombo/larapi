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
        Schema::table('default_groups', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            // Replace 'foreign_key_column_name' with the name of the foreign key column
        });
        Schema::table('affiliations', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            // Replace 'foreign_key_column_name' with the name of the foreign key column
        });
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['meeting_id']);
            // Replace 'foreign_key_column_name' with the name of the foreign key column
        });
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            // Replace 'foreign_key_column_name' with the name of the foreign key column
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            // Replace 'foreign_key_column_name' with the name of the foreign key column
        });
        Schema::table('persons', function (Blueprint $table) {
            $table->dropForeign(['default_org_id']);
            // Replace 'foreign_key_column_name' with the name of the foreign key column
        });
        Schema::table('persons', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            // Replace 'foreign_key_column_name' with the name of the foreign key column
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
