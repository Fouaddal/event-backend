<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_confirmation',
        'password',
        'type',
        'provider_type',
        'is_approved',
        'otp',
        'email_verified_at',
        'services',
        'specializations',
        'description',
        'usual_price',
        'profile_image'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'services' => 'array',
        'specializations' => 'array',
        'is_approved' => 'boolean'
    ];

    
    // Relationships
    public function gallery(): HasMany
    {
        return $this->hasMany(Gallery::class, 'user_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'user_id');
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(CompanyStatistics::class, 'user_id');
    }

    public function reservations()
    {
        return $this->hasManyThrough(Reservation::class, Event::class, 'user_id', 'event_id');
    }

    // Helper methods
    public function isProvider(): bool
    {
        return $this->type === 'provider';
    }

    public function isCompany(): bool
    {
        return $this->isProvider() && $this->provider_type === 'company';
    }

    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }

    // Statistics helpers
    public function calculateReservationsCount(): int
    {
        return $this->events()->withCount('reservations')->get()->sum('reservations_count');
    }

    public function calculateReviewsCount(): int
    {
        return $this->events()->withCount('reviews')->get()->sum('reviews_count');
    }

    public function calculateAverageRating(): float
    {
        return round($this->events()->withAvg('reviews', 'rating')->get()->avg('reviews_avg_rating') ?? 0, 1);
    }

    public function calculateTotalRevenue(): float
    {
        return $this->events()->withSum('reservations', 'total_price')->get()->sum('reservations_sum_total_price') ?? 0;
    }

    public function isIndividual(): bool
{
      return $this->isProvider() && $this->provider_type === 'individual';
}


// One-to-one with individual statistics
public function individualStatistics()
{
    return $this->hasOne(\App\Models\IndividualStatistics::class, 'user_id');
}

// One-to-many with offers
public function offers()
{
    return $this->hasMany(\App\Models\Offer::class, 'user_id');
}

public function providedEvents()
{
    return $this->belongsToMany(UserEvent::class, 'event_provider', 'user_id', 'user_event_id')
                ->withPivot('status')
                ->withTimestamps();
}

    
}