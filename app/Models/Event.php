<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'provider_id', 'title', 'type', 'is_public', 'date', 'location', 'invitation_code'
    ];

    public function services()
    {
        return $this->hasMany(EventService::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}