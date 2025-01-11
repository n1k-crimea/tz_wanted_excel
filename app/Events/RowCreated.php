<?php

namespace App\Events;

use App\Models\Row;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RowCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $row;

    /**
     * Create a new event instance.
     */
    public function __construct(Row $row)
    {
        $this->row = $row;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('rows');
    }

    public function broadcastAs(): string
    {
        return 'RowCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'row_id' => $this->row->row_id,
            'name' => $this->row->name,
            'date' => $this->row->date->toDateString(),
        ];
    }
}
