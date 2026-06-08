<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CompensationRequest;
use App\Models\User;

class WeeklyStipendReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'week_start',
        'week_end',
        'total_classes',
        'present_count',
        'attendance_percentage',
        'stipend_amount',
        'settled_stipend_amount',
        'generation_status'
    ];

    public function compensationRequests()
    {
        return $this->hasMany(CompensationRequest::class, 'report_id');
    }

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}