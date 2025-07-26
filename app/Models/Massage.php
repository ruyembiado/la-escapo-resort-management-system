<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Massage extends Model
{
    use HasFactory;

    protected $table = 'massages';

    protected $fillable = [
        'visitor_id',
        'category',
        'no_of_hours',
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
