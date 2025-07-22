<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PicnicTable extends Model
{
    use HasFactory;

    protected $table = 'picnic_tables';

    protected $fillable = [
        'visitor_id',
        'quantity',
        'fee',
        'total_payment',
        'payment_status',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }
}
