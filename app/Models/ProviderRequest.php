<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ProviderRequest extends  Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'provider_type',
        'otp',
        'email_verified_at',
        'status', // 'pending', 'approved', 'rejected'
        'otp_verified'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_verified' => 'boolean',
    ];
}