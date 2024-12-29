<?php

namespace App\Interfaces;

use App\Models\Account;
use App\Models\Post;

interface SocialMediaPublisherInterface
{
    /**
     * Publish content to a social media platform.
     *
     * @param  Account  $account  The social media account to publish from
     * @param  array  $content  The content to be published
     * @param  array  $media  Optional media files to be attached to the post
     * @return mixed The result of the publishing operation (could be a response, post ID, etc.)
     */
    public function publish(Account $account, Post $post, array $content, array $media = []): mixed;
}
