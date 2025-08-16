<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'ticket_count',
        'total_price',
        'status',
        'reservation_date'
    ];

    protected $casts = [
        'reservation_date' => 'datetime',
        'total_price' => 'decimal:2'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}