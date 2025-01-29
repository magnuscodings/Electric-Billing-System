<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meter extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'meterCode',
        'stallNumber',
        'clientId',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    public function readings()
    {
        return $this->hasMany(MeterReading::class, 'meterId');
    }

    public function latestReading()
    {
        return $this->hasOne(MeterReading::class, 'meterId')->latestOfMany();
    }

    public function previousReading()
    {
        return $this->hasOne(MeterReading::class, 'meterId')
            ->orderBy('id', 'desc')
            ->skip(1)
            ->take(1);
    }
}
