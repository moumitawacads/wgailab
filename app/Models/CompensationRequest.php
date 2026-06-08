<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\WeeklyStipendReport;

class CompensationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_id',
        'week_start',
        'week_end',
        'notes',
        'status',
    ];

    // Relation: who requested
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relation: linked stipend report
    public function report()
    {
        return $this->belongsTo(WeeklyStipendReport::class, 'report_id');
    }
}