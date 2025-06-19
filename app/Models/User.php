<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'provider_type',
        'is_approved',
        'otp',
        'email_verified_at',
        'services',
        'specializations' // Added for event types
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'services' => 'array',
        'specializations' => 'array', // For storing event types
        'is_approved' => 'boolean'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'provider_id');
    }
    
    // Helper method to check if user is provider
    public function isProvider()
    {
        return $this->type === 'provider';
    }
}