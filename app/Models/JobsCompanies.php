<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobsCompanies extends Model
{
    use HasFactory;
    protected $table = '_jobs_companies';

    protected $fillable = [
        'job_id',
        'company_id',
    ];
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Companies::class);
    }
}
