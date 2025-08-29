<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class ProviderRequest extends Model
{
     use HasApiTokens, HasFactory;

    protected $fillable = [
    'name',
    'email',
    'email_confirmation', // Add this
    'password',
    'provider_type',
    'otp',
    'email_verified_at',
    'status',
    'otp_verified',
    'services',
    'specializations'
];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_verified' => 'boolean',
        'services' => 'array',
        'specializations' => 'array' // For storing event types
    ];
    
    // Event types constants
    public const EVENT_TYPES = [
        'creative_cultural',
        'social_celebrations',
        'music_performance',
        'wellness_lifestyle',
        'entertainment_fun',
        'media_content',
        'educational_academic',
        'training_development'
    ];
}