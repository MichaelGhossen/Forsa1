<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
             'company_id',
        ];

        protected $casts = [

        ];

        public function category()
        {
            return $this->belongsTo(Category::class);
        }
        public function company(): BelongsTo
        {
            return $this->belongsTo(Companies::class);
        }

        public function companies(): BelongsToMany
        {
            return $this->belongsToMany(JobsCompanies::class, '_jobs_companies');
        }
        public function user()
        {
        return $this->belongsTo(User::class);
        }

        public function favorites()
        {
            return $this->hasMany(Favorite::class);
        }



    public function orders()
    {
    return $this->hasMany(Order::class);
    }
}
