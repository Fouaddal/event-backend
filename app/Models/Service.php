<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id', 'type', 'name', 'description', 'price', 'is_approved'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function eventServices()
    {
        return $this->hasMany(EventService::class);
    }

    protected $casts = [
    'is_approved' => 'boolean',
];

}