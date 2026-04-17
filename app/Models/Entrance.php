<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrance extends Model
{
    use HasFactory;

    protected $table = 'entrances';

    protected $fillable = [
        'visitor_id',
        'status',
        'total_payment',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }
    
    public function companions()
    {
        return $this->hasMany(Companion::class, 'entrance_id');
    }
}
