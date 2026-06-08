<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_HOLD = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'og_password',
        'status',
        'is_admin',
        'role',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'social_link'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'integer',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_HOLD => 'Hold',
            default => 'Unknown',
        };
    }

    public function classes()
    {
        return $this->hasMany(UsersClassesMapping::class);
    }

    // public function instructorClasses()
    // {
    //     return $this->hasMany(UsersClassesMapping::class, 'instructor_id');
    // }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function stipend_users()
    {
        return $this->hasMany(WeeklyStipendReport::class, 'user_id');
    }
}
