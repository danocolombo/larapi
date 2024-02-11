<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affiliation extends Model
{
    protected $table = 'affiliations';
    protected $primaryKey = 'id'; // Specify the primary key
    public $incrementing = false; // Disable auto-incrementing
    protected $keyType = 'string'; // Set the key type as string

    protected $fillable = [
        'id',
        'role',
        'status',
        'user_id',
        'organization_id'
    ];
}
