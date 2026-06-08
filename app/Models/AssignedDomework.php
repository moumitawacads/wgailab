<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedDomework extends Model
{
    use HasFactory;
    protected $fillable=['user_id','session_id','domework_id','domework_answer','status'];

    public function domework()
    {
        return $this->belongsTo(Domework::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }
}
