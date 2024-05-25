<?php

namespace App\Models;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // ADD THIS

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'image',
        //'cv',
        'user_type'

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
    ];
    // public function freelancer()
    // {
    //     return $this->hasOne(Freelancer::class);
    // }
    public function jobSeeker()
    {
        return $this->hasOne(JobSeeker::class);
    }
    public function jobowner()
    {
        return $this->hasOne(JobOwner::class);
    }
    // public function company()
    // {
    //     return $this->hasOne(Companies::class);
    // }
    // public function jobApplications()
    // {
    //     return $this->hasMany(JobApplication::class);
    // }
    // public function jobs()
    // {
    //     return $this->belongsToMany(Job::class, 'job_applications', 'user_id', 'job_id');
     // }
    // public function jobsForFreelancers()
    // {
    //     return $this->belongsToMany(JobsForFreelancers::class, 'jobs_for_freelancers_users', 'user_id', 'jobs_for_freelancer_id');
    // }
    public function cv()
    {
        return $this->hasOne(CV::class);
    }
    public function jobs()
{
    return $this->hasMany(Job::class);
}
// User.php
public function favorites()
{
    return $this->belongsToMany(Job::class, 'favorites', 'user_id', 'job_id');
}
public function freelance_favorites()
{
    return $this->belongsToMany(JObsForFreelancers::class, 'freelance_favorites', 'user_id', 'j_obs_for_freelancers_id');
}

}
