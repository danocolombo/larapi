<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingListItem extends Model
{
    protected $table = 'meetings';
    protected $fillable = [
        'id',
        "organization_id",
        'meeting_date',
        "meeting_type",
        "title",
        "support_contact",
        'attendance_count',
        'meal_count',
        'meal',
        'worship'
    ];
    protected $keyType = 'string'; // Define id as string
}
