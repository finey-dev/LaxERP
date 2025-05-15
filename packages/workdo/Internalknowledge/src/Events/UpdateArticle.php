<?php

namespace Workdo\Internalknowledge\Events;

use Illuminate\Queue\SerializesModels;

class UpdateArticle
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $article;

    public function __construct($request, $article)
    {
        $this->request = $request;
        $this->article = $article;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
