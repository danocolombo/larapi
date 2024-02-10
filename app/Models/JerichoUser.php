<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JerichoUser extends Model
{
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

        static::saving(function ($jericho_user) {
            // Check if first_name is empty and set it to null
            if (empty($jericho_user->first_name)) {
                $jericho_user->first_name = null;
            }
            // Check if last_name is empty and set it to null
            if (empty($jericho_user->last_name)) {
                $jericho_user->last_name = null;
            }
            // Check if email is empty and set it to null
            if (empty($jericho_user->email)) {
                $jericho_user->email = null;
            }
            // Check if phone is empty and set it to null
            if (empty($jericho_user->phone)) {
                $jericho_user->phone = null;
            }
            // Check if shirt is empty and set it to null
            if (empty($jericho_user->shirt)) {
                $jericho_user->shirt = null;
            }
            // Check if birthday is empty and set it to null
            if (empty($jericho_user->birthday)) {
                $jericho_user->birthday = null;
            }
            // Check if picture is empty and set it to null
            if (empty($jericho_user->picture)) {
                $jericho_user->picture = null;
            }
            // Check if default_org_id is empty and set it to null
            if (empty($jericho_user->default_org_id)) {
                $jericho_user->default_org_id = null;
            }
            // Check if location_id is empty and set it to null
            if (empty($jericho_user->location_id)) {
                $jericho_user->location_id = null;
            }
        });
    }
}
