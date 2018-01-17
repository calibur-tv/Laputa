<?php

namespace App\Jobs\Notification\Post;

use App\Api\V1\Repositories\PostRepository;
use App\Models\Notifications;
use App\Models\PostLike;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Agree implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $likeId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->likeId = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $like = PostLike::find($this->likeId);

        if (is_null($like))
        {
            return;
        }

        $repository = new PostRepository();
        $reply = $repository->item($like['post_id']);

        if (is_null($reply))
        {
            return;
        }

        $post = $repository->item($reply['parent_id']);

        Notifications::create([
            'from_user_id' => $like['user_id'],
            'to_user_id' => $reply['user_id'],
            'about_id' => $reply['id'] . ',' . $post['id'],
            'type' => 4
        ]);
    }
}
