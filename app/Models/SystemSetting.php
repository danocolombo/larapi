<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $primaryKey = 'id'; // Specify the primary key
    public $incrementing = false; // Disable auto-incrementing
    protected $keyType = 'string'; // Set the key type as string

    protected $fillable = [
        'id',
        'app_name',
        'android_version',
        'ios_version',
        'web_version',
        'default_profile_picture',
        'logo_picture'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($app) {
            // Check if android_version is empty and set it to null
            if (empty($app->android_version)) {
                $app->android_version = null;
            }
            // Check if ios_version is empty and set it to null
            if (empty($app->ios_version)) {
                $app->ios_version = null;
            }
            // Check if web_version is empty and set it to null
            if (empty($app->web_version)) {
                $app->web_version = null;
            }
            // Check if default_profile_picture is empty and set it to null
            if (empty($app->default_profile_picture)) {
                $app->default_profile_picture = null;
            }
            // Check if logo_picture is empty and set it to null
            if (empty($app->logo_picture)) {
                $app->logo_picture = null;
            }
        });
    }
}
