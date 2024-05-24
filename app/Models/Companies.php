<?php

namespace App\Models;
//use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class Companies extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'name',
        'email',
        'password',
        'commercial_register',
        'user_type',

    ];
 //   protected $fillable = ['user_id'];
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function cvs()
    {
        return $this->belongsToMany(CV::class, 'cv_company');
    }
    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, '_jobs_companies');
    }
}
