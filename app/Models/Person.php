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
        'location_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($person) {
            // Check and set empty fields to null (optional)
            foreach ($person->getFillable() as $field) {
                if (empty($person->$field)) {
                    $person->$field = null;
                }
            }
        });
    }

    // Define the hasMany relationship with Affiliation:
    public function affiliations()
    {
        return $this->hasMany(Affiliation::class, 'person_id');
    }

    // Define the hasOne relationship with Organization (assuming the correct model name):
    public function defaultOrg()
    {
        return $this->hasOne(Organization::class, 'id', 'default_org_id');
    }
}
