<?php

namespace App\Modules\Queues\Events;

use App\Modules\Queues\Models\Queue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Queue $queue,
        public string $action // 'joined', 'called', 'serving', 'completed', 'cancelled'
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('branch.'.$this->queue->branch_id.'.queue'),
            new Channel('public.queue.updates'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'queueId' => $this->queue->id,
            'branchId' => $this->queue->branch_id,
            'ticketNumber' => $this->queue->ticket_number,
            'status' => $this->queue->status->value,
            'action' => $this->action,
            'serviceType' => $this->queue->service_type,
            'joinedAt' => $this->queue->joined_at->toIso8601String(),
            'estimatedWaitTime' => $this->queue->estimated_wait_time,
        ];
    }

    public function broadcastAs(): string
    {
        return 'queue.updated';
    }

    public function throttle(int $seconds = 1): int
    {
        return $seconds;
    }
}
