<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventResponse extends Model
{
    use HasFactory;

    // The table name (optional if follows convention)
    protected $table = 'event_responses';

    // Mass assignable fields
    protected $fillable = [
        'user_event_id',
        'name',
        'response'
    ];

    /**
     * Relationship to the event
     */
    public function event()
    {
        return $this->belongsTo(UserEvent::class, 'user_event_id');
    }
}
