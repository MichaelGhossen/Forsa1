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
        'user_type',
        'flag',
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
    public function jobSeeker()
    {
        return $this->hasOne(JobSeeker::class);
    }
    public function jobowner()
    {
        return $this->hasOne(JobOwner::class);
    }
    public function cv()
    {
        return $this->hasOne(CV::class);
    }
    public function jobs()
{
    return $this->hasMany(Job::class);
}
public function jobsfreelance()
{
    return $this->hasMany(JObsForFreelancers::class);
}
// User.php
  public function favorites()
{
    return $this->hasMany(Favorite::class);
}

public function freelance_favorites()
{
    return $this->hasMany(FreelanceFavorite::class);
}
public function orders()
{
    return $this->hasMany(Order::class);
}
public function account()
{
    return $this->hasOne(Account::class);
}
public function skills()
{
    return $this->belongsToMany(Skill::class);
}

public function chooseSkills($skillIds)
{
    $this->skills()->sync($skillIds);
}

public function orderforfreelances()
{
    return $this->hasMany(OrderForFreelance::class, 'user_id');
}

}
