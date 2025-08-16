<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'price',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   public function events()
{
    return $this->belongsToMany(UserEvent::class, 'event_offer')
        ->withPivot('status')
        ->withTimestamps();
}


}
