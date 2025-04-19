<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'phone',
        'is_active',
        'current_plan_id',
        'plan_expires_at',
        'subscription_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'plan_expires_at' => 'datetime',
    ];

    /**
     * Get the user's plan.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    /**
     * Check if user has an active plan.
     *
     * @return bool
     */
    public function hasActivePlan()
{
    return $this->is_active && $this->current_plan_id && 
           ($this->plan_expires_at === null || $this->plan_expires_at > now());
}

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname}";
    }

    // App/Models/User.php
public function domains()
{
    return $this->hasMany(Domain::class);
}

public function campaigns()
{
    return $this->hasMany(Campaign::class);
}

public function trafficLogs()
{
    return $this->hasManyThrough(TrafficLog::class, Campaign::class);
}
}