<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $primaryKey = 'id'; // Specify the primary key
    public $incrementing = false; // Disable auto-incrementing
    protected $keyType = 'string'; // Set the key type as string

    protected $fillable = [
        'id',
        'name',
        'code',
        'hero_message',
        'location_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($organization) {
            // Check if hero_message is empty and set it to null
            if (empty($organization->hero_message)) {
                $organization->hero_message = null;
            }
        });
    }
}
