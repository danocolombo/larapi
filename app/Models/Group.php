<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $primaryKey = 'id'; // Specify the primary key
    public $incrementing = false; // Disable auto-incrementing
    protected $keyType = 'string'; // Set the key type as string

    protected $fillable = [
        'id',
        'grp_comp_key',
        'title',
        'location',
        'gender',
        'attendance',
        'facilitator',
        'cofacilitator',
        'notes',
        'meeting_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($group) {
            // Define validation rules for string attributes
            $stringRules = [
                'location',
                'gender',
                'facilitator',
                'cofacilitator',
                'notes'
            ];

            // Validate string attributes and set to null if validation fails
            foreach ($stringRules as $attribute) {
                if (empty($group->$attribute) || !is_string($group->$attribute)) {
                    $group->$attribute = null;
                }
            }

            // Define numeric attributes
            $numericAttributes = [
                'attendance'
            ];

            // Validate numeric attributes and set to null if validation fails
            foreach ($numericAttributes as $attribute) {
                if (!is_numeric($group->$attribute) || $group->$attribute < 1) {
                    $group->$attribute = null;
                }
            }
        });
    }
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
    public function formatForDetails()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'location' => $this->location,
            'gender' => $this->gender,
            'facilitator' => $this->facilitator,
            'cofacilitator' => $this->cofacilitator,
            'notes' => $this->notes,
        ];

        $emptyKeys = array_fill_keys(array_keys($data), null);
        $nonEmptyData = array_diff($data, $emptyKeys);

        return $nonEmptyData;
    }
}
