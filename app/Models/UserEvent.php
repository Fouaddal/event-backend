<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'type',
        'is_public',
        'date',
        'time',
        'location',
        'invitation_code',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   public function services()
{
    return $this->hasMany(EventService::class, 'event_id');
}


    public function providers()
    {
        return $this->belongsToMany(User::class, 'event_provider', 'user_event_id', 'user_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function offers()
{
    return $this->belongsToMany(Offer::class, 'event_offer')
        ->withPivot('status')
        ->withTimestamps();
}

}
