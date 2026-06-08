<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedBusinessPlan extends Model
{
    use HasFactory;

    protected $fillable=['user_id','session_id','businessplan_id','businessplan_answer','status'];

    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class, 'businessplan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relatedSessions()
    {
        return $this->hasMany(
            AssignedBusinessPlan::class,
            'businessplan_id',
            'businessplan_id'
        )
        ->with('session')
        ->distinct('session_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

}
