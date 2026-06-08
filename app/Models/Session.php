<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_name',
        'session_order',
        'session_objectives',
        'session_duration',
        'zoom_link',
        'instructor_ids',
        'participant_ids',
        'status',
        'zoom_meeting_id',
        'zoom_meeting_password',
        'zoom_meeting_url',
        'zoom_registration_url',
        'zoom_start_url',
        'zoom_instructor_host_keys'
    ];

    protected function sessionObjectives(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? new HtmlString($value) : null,
        );
    }

    public function schedules()
    {
        return $this->hasMany(UsersClassesMapping::class, 'session_id');
    }

    public function sessionLinks()
    {
        return $this->hasMany(SessionDomeworkBusinessPlan::class);
    }

    public function assignedDomeworks()
    {
        return $this->hasMany(AssignedDomework::class);
    }

    public function assignedBusinessPlans()
    {
        return $this->hasMany(AssignedBusinessPlan::class);
    }

    public function getZoomMeetingLinkForSession($sessionId)
    {
        $mapping = UsersClassesMapping::where('session_id', $sessionId)
            ->where('user_id', auth()->user()->id)
            ->first();

        return $mapping ? $mapping->zoom_join_url : null;
    }

    public function domeworkAssignments()
    {
        return $this->hasMany(SessionDomeworkBusinessPlan::class);
    }

    public function assignedDomework()
    {
        return $this->hasOne(SessionDomeworkBusinessPlan::class)
            ->whereNotNull('domework_id')
            ->with('domework');
    }
}
