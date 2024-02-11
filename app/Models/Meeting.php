<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $primaryKey = 'id'; // Specify the primary key
    public $incrementing = false; // Disable auto-incrementing
    protected $keyType = 'string'; // Set the key type as string

    protected $fillable = [
        'id',
        'meeting_date',
        'title',
        'meeting_type',
        'mtg_comp_key',
        'announcements_contact',
        'attendance_count',
        'av_contact',
        'cafe_contact',
        'cafe_count',
        'children_contact',
        'children_count',
        'cleanup_contact',
        'closing_contact',
        'donations',
        'facilitator_contact',
        'greeter_contact1',
        'greeter_contact2',
        'meal',
        'meal_contact',
        'meal_count',
        'newcomers_count',
        'notes',
        'nursery_contact',
        'nursery_count',
        'resource_contact',
        'security_contact',
        'setup_contact',
        'support_contact',
        'transportation_contact',
        'transportation_count',
        'worship',
        'youth_contact',
        'youth_count',
        'organization_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($meeting) {
            // Define validation rules for string attributes
            $stringRules = [
                'mtg_comp_key',
                'announcements_contact',
                'av_contact',
                'cafe_contact',
                'children_contact',
                'cleanup_contact',
                'closing_contact',
                'facilitator_contact',
                'greeter_contact1',
                'greeter_contact2',
                'meal',
                'meal_contact',
                'notes',
                'nursery_contact',
                'resource_contact',
                'security_contact',
                'setup_contact',
                'support_contact',
                'transportation_contact',
                'worship',
                'youth_contact',
            ];

            // Validate string attributes and set to null if validation fails
            foreach ($stringRules as $attribute) {
                if (empty($meeting->$attribute) || !is_string($meeting->$attribute)) {
                    $meeting->$attribute = null;
                }
            }

            // Define numeric attributes
            $numericAttributes = [
                'attendance_count',
                'cafe_count',
                'children_count',
                'meal_count',
                'newcomers_count',
                'nursery_count',
                'transportation_count',
                'youth_count',
            ];

            // Validate numeric attributes and set to null if validation fails
            foreach ($numericAttributes as $attribute) {
                if (!is_numeric($meeting->$attribute) || $meeting->$attribute < 1) {
                    $meeting->$attribute = null;
                }
            }

            // Validate donations
            if (!is_numeric($meeting->donations) || $meeting->donations < 0 || $meeting->donations > 9999.99) {
                $meeting->donations = null;
            }
        });
    }
    public function setDonationsAttribute($value)
    {
        // Check if the provided value is not null and not 0
        if ($value !== null && $value !== 0) {
            // Format the donations value to two decimal places
            $this->attributes['donations'] = number_format($value, 2);
        } else {
            // If the value is null or 0, set donations to null
            $this->attributes['donations'] = null;
        }
    }
    // Define a validation rule for the organization_id attribute
    public static function getValidationRules()
    {
        return [
            'organization_id' => 'required|exists:organizations,id',
            // Add other validation rules for other attributes if needed
        ];
    }
}
