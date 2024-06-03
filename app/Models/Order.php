<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'cv_id',
        'company_id',
        'order_status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
    public function company()
    {
        return $this->belongsTo(Companies::class);
    }
    public function cv()
    {
        return $this->belongsTo(Cv::class);
    }
}
