<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AppointmentBooked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info("Broadcasting AppointmentBooked event for receiver ID: {$this->notification->user_id}", [
            'notification' => $this->notification,
        ]);

        return [
            new PrivateChannel("appointment.{$this->notification->user_id}"),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'user_id' => $this->notification->user_id,
            'message' => $this->notification->message,
            'read' => $this->notification->read,
            'created_at' => $this->notification->created_at,
            'updated_at' => $this->notification->updated_at,
        ];
    }
}
