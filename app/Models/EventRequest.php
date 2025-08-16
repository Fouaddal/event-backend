<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/EventRequest.php

class EventRequest extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'date_time', 'location',
        'image', 'ticket_price', 'capacity', 'category', 'status'
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function pendingEvents()
{
    $events = EventRequest::where('status', 'pending')->with('company')->get();
    return view('admin.pending-events', compact('events'));
}
}

