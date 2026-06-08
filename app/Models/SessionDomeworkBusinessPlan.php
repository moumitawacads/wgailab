<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionDomeworkBusinessPlan extends Model
{
    use HasFactory;
    protected $fillable=['domework_id','businessplan_id','session_id'];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function domework()
    {
        return $this->belongsTo(Domework::class);
    }

    public function businessPlan()
    {
        return $this->belongsTo(BusinessPlan::class, 'businessplan_id');
    }
    
}
