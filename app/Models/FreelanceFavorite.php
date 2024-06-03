<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelanceFavorite extends Model
{
    use HasFactory;
    protected $fillable = ['j_obs_for_freelancers_id', 'user_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function j_obs_for_freelancers()
    {
        return $this->hasOne(JObsForFreelancers::class);
    }

}
