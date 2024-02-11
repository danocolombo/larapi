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
        Schema::table('affiliations', function (Blueprint $table) {
            // Replace 'foreign_key_column' with the name of your foreign key column
            $table->dropForeign(['user_id']);
        });

        Schema::table('affiliations', function (Blueprint $table) {
            // Replace 'user_id' with the name of your foreign key column
            $table->foreign('user_id')
                ->references('id')->on('persons')
                ->onDelete('cascade');
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
