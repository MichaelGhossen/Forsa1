<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderForFreelance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'j_obs_for_freelancers_id',
    ];
    public function job()
    {
        return $this->belongsTo(JObsForFreelancers::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
