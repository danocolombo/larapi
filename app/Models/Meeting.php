<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

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

    public function save(array $options = [])
    {
        // Validate the organization_id before saving the meeting
        $validator = Validator::make(['organization_id' => $this->organization_id], self::getValidationRules());

        // Check if the validation fails
        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . $validator->errors()->first());
        }

        // Call the parent save method to save the meeting
        return parent::save($options);
    }

    // Define a validation rule for the organization_id attribute
    public static function getValidationRules()
    {
        return [
            'organization_id' => 'required|exists:organizations,id',
            // Add other validation rules for other attributes if needed
        ];
    }
    public function filterNulls()
    {
        return collect($this->toArray())->filter(function ($value) {
            return !is_null($value);
        })->all();
    }
    public function groups()
    {
        return $this->hasMany(Group::class);
    }
    public function meetingDetailsOne()
    {
        return $this->with('groups')->find($this->id);
    }
    public function meetingDetailsTwo()
    {
        $meeting = $this->with('groups')->find($this->id);

        // Filter null values from non-group attributes
        $filteredMeeting = collect($meeting->toArray())->filter(function ($value, $key) {
            return !is_null($value) && $key !== 'groups';
        })->all();

        // Keep groups array as is
        $filteredMeeting['groups'] = $meeting->groups->map(function ($group) {
            return [
                'id' => $group->id,
                'title' => $group->title,
                'meeting_id' => $group->meeting_id,
            ];
        })->toArray();

        return $filteredMeeting;
    }
    public function meetingDetails()
    {
        $meeting = $this->with('groups')->find($this->id);

        // Filter null values from non-group attributes
        $filteredMeeting = collect($meeting->toArray())->filter(function ($value, $key) {
            return !is_null($value) && $key !== 'groups';
        })->all();

        // Utilize the Groups model for group-related processing
        $filteredMeeting['groups'] = $meeting->groups->map(function ($group) {
            return $group->formatForDetails(); // Call a method on the Group model
        })->toArray();

        return $filteredMeeting;
    }
}
// class Group extends Model {

// }
