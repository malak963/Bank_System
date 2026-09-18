<?php

namespace App\Modules\Branches\Events;

use App\Modules\Branches\Models\Branch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BranchCapacityUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Branch $branch,
        public array $capacityData
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('public.branch.capacity'),
            new PrivateChannel('branch.'.$this->branch->id.'.capacity'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'branchId' => $this->branch->id,
            'branchCode' => $this->branch->code,
            'branchName' => $this->branch->name,
            'capacity' => $this->capacityData,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'branch.capacity.updated';
    }
}
