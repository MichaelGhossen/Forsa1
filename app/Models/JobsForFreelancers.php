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
        'min_salary',
        'max_salary',
        'languages',
        'description',
        'requirements'

    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // public function j_obs_for_freelancers()
    // {
    //     return $this->belongsToMany(User::class, 'freelance_favorites');
    // }
    public function FreelanceFavorite()
{
    return $this->belongsToMany(User::class, 'FreelanceFavorite'); // Indicates a many-to-many relationship with User
}

}
