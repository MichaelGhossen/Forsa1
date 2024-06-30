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
        'salary',
        'languages',
        'description',
        'requirements',
        'user_id',
        'category_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function FreelanceFavorite()
    {
        return $this->hasMany(FreelanceFavorite::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'requirements', 'j_obs_for_freelancers_id', 'skill_id');
    }
    public function orders()
    {
        return $this->hasMany(OrderForFreelance::class, 'j_obs_for_freelancers_id');
    }
    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }
}
