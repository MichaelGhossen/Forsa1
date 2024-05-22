<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

        protected $fillable = [
            'title',
            'min_duration',
            'max_duration',
            'min_age',
            'max_age',
            'min_salary',
            'max_salary',
            'gender',
            'languages',
            'description',
            'category_id',
            'location',

        ];

        protected $casts = [

        ];

        public function category()
        {
            return $this->belongsTo(Category::class);
        }

        public function jobApplications()
        {
            return $this->hasMany(JobApplication::class);
        }
        public function users()
        {
            return $this->belongsToMany(User::class, 'job_applications', 'job_id', 'user_id');
        }
}
