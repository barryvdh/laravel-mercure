<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Private channel demo: only the recipient's own "room.{id}" subscriber
 * cookie grants access to this topic (see routes/channels.php), so only
 * a browser logged in as that user ever receives it.
 */
class DemoPrivateMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(
        public User $sender,
        public int $recipientId,
        public string $text,
    ) {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('room.'.$this->recipientId)];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'from' => $this->sender->name,
            'text' => $this->text,
        ];
    }
}
