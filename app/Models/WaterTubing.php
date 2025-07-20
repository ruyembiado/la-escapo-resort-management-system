<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterTubing extends Model
{
    use HasFactory;

    protected $table = 'water_tubings';

    protected $fillable = [
        'visitor_id',
        'category',
        'members',
        'age',
        'fee',
        'total_payment',
        'payment_status',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }
}
