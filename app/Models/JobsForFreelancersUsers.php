<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JObsForFreelancers_Users extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'jobs_for_freelancer_id',
    ];
    public function jobForFreelancer()
    {
        return $this->belongsTo(JObsForFreelancers::class, 'jobs_for_freelancer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
