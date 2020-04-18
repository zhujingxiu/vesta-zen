<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ServiceCacheEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 如果服务信息有改变的话，需要清空缓存
     *
     * @return void
     */
    public $mode ;
    public function __construct($cacheMode)
    {
        //
        $this->mode = $cacheMode;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
