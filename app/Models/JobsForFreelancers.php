<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JObsForFreelancers extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'min_duration',
        'max_duration',
        // 'min_age',
        // 'max_age',
        'min_salary',
        'max_salary',
        // 'gender',
        'languages',
        'description',
        // 'location',
        'requirements'

    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'jobs_for_freelancers_users', 'jobs_for_freelancer_id', 'user_id');
    }
}
