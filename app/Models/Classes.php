<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $fillable = ['name', 'description', 'image', 'status', 'is_deleted'];

    protected $casts = [
        'status' => 'integer',
    ];

    public function getClassStatusAttribute()
    {
        return match ($this->status) {
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
            default => 'Unknown',
        };
    }

    public function users()
    {
        return $this->hasMany(UsersClassesMapping::class, 'class_id');
    }
}
