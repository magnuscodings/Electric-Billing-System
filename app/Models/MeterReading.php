<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeterReading extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'meterId',
        'reading',
        'consumption'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($meterReading) {
            $previousReading = self::where('meterId', $meterReading->meterId)
                ->orderBy('id', 'desc')
                ->first();

            if (!$meterReading->consumption) {
                $meterReading->consumption = $previousReading
                    ? ($meterReading->reading - $previousReading->reading)
                    : 0;
            }
        });
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class, 'meterId');
    }

    public function billing()
    {
        return $this->hasOne(Billing::class, 'meterReadingId');
    }
}
