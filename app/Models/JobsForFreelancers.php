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
        'requirements',
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

}
