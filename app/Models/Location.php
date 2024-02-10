<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $primaryKey = 'id'; // Specify the primary key
    public $incrementing = false; // Disable auto-incrementing
    protected $keyType = 'string'; // Set the key type as string

    protected $fillable = [
        'id',
        'street',
        'city',
        'state_prov',
        'postal_code'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($location) {
            // Check if street is empty and set it to null
            if (empty($location->street)) {
                $location->street = null;
            }
            // Check if city is empty and set it to null
            if (empty($location->city)) {
                $location->city = null;
            }
            // Check if state_prov is empty and set it 'UNK'
            if (empty($location->state_prov)) {
                $location->state_prov = 'UNK';
            }
            // Check if postal_code is empty and set it to null
            if (empty($location->postal_code)) {
                $location->postal_code = null;
            }
        });
    }
}
