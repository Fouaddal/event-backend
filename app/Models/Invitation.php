<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'guest_email'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}