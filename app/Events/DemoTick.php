<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Public channel demo: broadcast to everyone watching the page, logged
 * in or not, with no authorization round trip at all.
 */
class DemoTick implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(public string $time)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('ticks')];
    }

    public function broadcastAs(): string
    {
        return 'Tick';
    }
}
