<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';
    protected $primaryKey = 'id'; // Specify the primary key
    public $incrementing = false; // Disable auto-incrementing
    protected $keyType = 'string'; // Set the key type as string

    protected $fillable = [
        'id',
        'sub',
        'username',
        'first_name',
        'last_name',
        'email',
        'phone',
        'shirt',
        'birthday',
        'picture',
        'default_org_id',
        'location_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($person) {
            // Check if first_name is empty and set it to null
            if (empty($person->first_name)) {
                $person->first_name = null;
            }
            // Check if last_name is empty and set it to null
            if (empty($person->last_name)) {
                $person->last_name = null;
            }
            // Check if email is empty and set it to null
            if (empty($person->email)) {
                $person->email = null;
            }
            // Check if phone is empty and set it to null
            if (empty($person->phone)) {
                $person->phone = null;
            }
            // Check if shirt is empty and set it to null
            if (empty($person->shirt)) {
                $person->shirt = null;
            }
            // Check if birthday is empty and set it to null
            if (empty($person->birthday)) {
                $person->birthday = null;
            }
            // Check if picture is empty and set it to null
            if (empty($person->picture)) {
                $person->picture = null;
            }
            // Check if default_org_id is empty and set it to null
            if (empty($person->default_org_id)) {
                $person->default_org_id = null;
            }
            // Check if location_id is empty and set it to null
            if (empty($person->location_id)) {
                $person->location_id = null;
            }
        });
    }
}
