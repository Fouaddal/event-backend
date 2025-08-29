<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['user_id', 'image_path', 'caption'];

    public function company()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

}