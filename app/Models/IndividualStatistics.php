<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class IndividualStatistics extends Model
{
    protected $fillable = [
        'user_id', 'reservations_count', 'reviews_count', 
        'average_rating', 'total_revenue'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
