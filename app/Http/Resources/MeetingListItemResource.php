<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MeetingListItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'meeting_date' => $this->meeting_date,
            'meeting_type' => $this->meeting_type,
            'title' => $this->title,
            'support_contact' => $this->support_contact,
            'attendance_count' => $this->attendance_count,
            'meal_count' => $this->meal_count,
            'meal' => $this->meal,
            'worship' => $this->worship
        ];
    }
}
