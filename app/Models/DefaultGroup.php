<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultGroup extends Model
{
    protected $primaryKey = 'id'; // Specify the primary key
    public $incrementing = false; // Disable auto-incrementing
    protected $keyType = 'string'; // Set the key type as string

    protected $fillable = [
        'id',
        'title',
        'location',
        'gender',
        'attendance',
        'facilitator',
        'organization_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($group) {
            // Define validation rules for string attributes
            $stringRules = [
                'location',
                'facilitator',
                'cofacilitator'
            ];

            // Validate string attributes and set to null if validation fails
            foreach ($stringRules as $attribute) {
                if (empty($group->$attribute) || !is_string($group->$attribute)) {
                    $group->$attribute = null;
                }
            }
        });
    }
}
