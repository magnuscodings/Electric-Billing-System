<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billing extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'meterReadingId',    // Foreign key linking to the meter
        'rate',              // Rate per kWh
        'totalAmount',       // Total bill amount
        'billingDate',       // default at 15 of the month also known as dueDate
        'status',            // 0 = Unpaid, 1 = paid
        'paymentDate',       // New: When the payment was made
        //'generatedBy',       // New: User ID who generated the bill
    ];

    protected $casts = [
        'billingDate' => 'date',
        'paymentDate' => 'date',
        'rate' => 'decimal:2',
        'totalAmount' => 'decimal:2',
    ];

    // Status constants
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_OVERDUE = 2;
    const STATUS_PARTIALLY_PAID = 3;

    // Add creating event handler
    protected static function boot()
    {
        parent::boot();

        // Set clientId from meter reading relationship
        static::creating(function ($billing) {
            if (!isset($billing->clientId) && isset($billing->meterReadingId)) {
                $meterReading = MeterReading::with('meter.client')->find($billing->meterReadingId);
                if ($meterReading && $meterReading->meter && $meterReading->meter->client) {
                    $billing->clientId = $meterReading->meter->client->id;
                }
            }
        });
    }

    //Relationships
    public function meterReading()
    {
        return $this->belongsTo(MeterReading::class, 'meterReadingId');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId');
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generatedBy');
    }

    //Scopes
    public function scopeUnpaid($query)
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    public function scopeOverdue($query)
    {
        return $query->where('billingDate', '<', now())
            ->where('status', self::STATUS_UNPAID);
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('billingDate', [$startDate, $endDate]);
    }

    // Accessor for status label
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_UNPAID => 'Unpaid',
            self::STATUS_PAID => 'Paid',
            self::STATUS_OVERDUE => 'Overdue',
            self::STATUS_PARTIALLY_PAID => 'Partially Paid',
            default => 'Unknown'
        };
    }
}
