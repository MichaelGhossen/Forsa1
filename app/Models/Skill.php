<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;
    protected $table = 'skills';

    protected $fillable = [
        'name',
        'description',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function jobs()
    {
        return $this->belongsToMany(JObsForFreelancers::class, 'requirements', 'skill_id', 'j_obs_for_freelancers_id');

}
}
