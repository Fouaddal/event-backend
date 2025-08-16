<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyStatistics extends Model
{
    protected $fillable = [
        'user_id', 'reservations_count', 'reviews_count', 
        'average_rating', 'total_revenue'
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}