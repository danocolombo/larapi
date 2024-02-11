<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    private $foreignKeys = [
        'default_groups' => 'organization_id',
        'groups' => 'meeting_id',
        'meetings' => 'organization_id',
        'affiliations' => 'person_id',
        'affiliations' => 'organization_id',
        'persons' => 'default_org_id',
        'persons' => 'location_id',
        'organizations' => 'location_id',
        // Add more table/foreign key pairs as needed
    ];
    public function up(): void
    {


        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('default_groups');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('meetings');
        Schema::dropIfExists('affiliations');
        Schema::dropIfExists('persons');
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
