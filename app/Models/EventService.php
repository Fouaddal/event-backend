<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventService extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'service_id', 'quantity', 'price', 'total'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}