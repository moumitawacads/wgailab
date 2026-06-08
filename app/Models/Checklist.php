<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'link',
        'target_type',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'target_type' => 'string'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'checklist_user')
            ->withPivot('is_completed', 'completed_at')
            ->withTimestamps();
    }

    public function completedUsers()
    {
        return $this->belongsToMany(User::class, 'checklist_user')
            ->wherePivot('is_completed', true);
    }

    public function incompleteUsers()
    {
        return $this->belongsToMany(User::class, 'checklist_user')
            ->wherePivot('is_completed', false);
    }
}
