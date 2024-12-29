<?php

namespace App\Jobs;

use App\Enums\PlatformType;
use App\Enums\PostStatusType;
use App\Models\Account;
use App\Models\Post;
use App\Services\Bluesky;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PublishPost implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Account $account,
        protected Post $post,
        protected array $content = [],
        protected array $media = [],
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->post->update([
            'status' => PostStatusType::RUNNING->value,
        ]);

        switch ($this->account->platform) {
            case PlatformType::BLUESKY->value:
                Bluesky::make()->publish($this->account, $this->post, $this->content, $this->media);
                break;
        }
    }
}
