<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CV extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'company_id',
        'file_path',
        'job_owner_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function companies()
    {
        return $this->belongsToMany(Companies::class, 'cv_company');
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function ordersForFreelance()
    {
        return $this->hasMany(OrderForFreelance::class);
    }
}
