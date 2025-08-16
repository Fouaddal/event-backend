<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'user_id', 
        'title', 
        'description', 
        'date_time', 
        'ticket_price', 
        'location', 
        'image', 
        'status', 
        'admin_notes',
        'capacity',
        'category'
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'ticket_price' => 'decimal:2'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Helper method to check if event is approved
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    // Scope for approved events
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    public function user()
{
    return $this->belongsTo(User::class);
}


public function tickets()
{
    return $this->hasMany(Ticket::class);
}




}