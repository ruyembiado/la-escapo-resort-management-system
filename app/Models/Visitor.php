<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'visitors';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'gender',
        'age',
        'members',
        'address',
        'date_visit',
        'check_in',
        'check_out',
        'is_pwd'
    ];

    public function entrance()
    {
        return $this->hasOne(Entrance::class, 'visitor_id');
    }

    public function accommodation()
    {
        return $this->hasOne(Accommodation::class, 'visitor_id');
    }

    public function cottage()
    {
        return $this->hasOne(Cottage::class, 'visitor_id');
    }

    public function meal()
    {
        return $this->hasOne(Meal::class, 'visitor_id');
    }

    public function beverage()
    {
        return $this->hasOne(Beverage::class, 'visitor_id');
    }

    public function kawabath()
    {
        return $this->hasOne(KawaBath::class, 'visitor_id');
    }

    public function watertubing()
    {
        return $this->hasOne(WaterTubing::class, 'visitor_id');
    }

    public function picnictable()
    {
        return $this->hasOne(PicnicTable::class, 'visitor_id');
    }

    public function massage()
    {
        return $this->hasOne(Massage::class, 'visitor_id');
    }
}
