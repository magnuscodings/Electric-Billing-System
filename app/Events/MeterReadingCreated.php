<?php

namespace App\Events;

use App\Models\MeterReading;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeterReadingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $meterReading;
    public $new_count;

    public function __construct(MeterReading $meterReading, int $new_count)
    {
        $this->meterReading = $meterReading;
        $this->new_count = $new_count;
    }

    public function broadcastOn()
    {
        return ['meter-readings'];
    }

    public function broadcastAs()
    {
        return 'meter-reading-created';
    }
}
