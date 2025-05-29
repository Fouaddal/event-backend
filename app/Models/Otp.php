<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $fillable = ['email', 'otp', 'expires_at'];
    public $timestamps = true;

    public function isExpired()
    {
        return Carbon::now()->greaterThan($this->expires_at);
    }
}
