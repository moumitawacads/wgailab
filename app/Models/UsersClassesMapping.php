<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class UsersClassesMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'user_id',
        'instructor_id',
        'schedule_date',
        'schedule_time',
        'created_by',
        'status',
        'session_id',
        'zoom_join_url',
        'registrant_id',
        'instructor_host_key',
        'instructor_start_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mainclass()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // public function instructor(){
    //     return $this->belongsTo(User::class,'instructor_id');
    // }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'schedule_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}
