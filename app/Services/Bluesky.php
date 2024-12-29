<?php

namespace App\Services;

use App\Enums\PostStatusType;
use App\Interfaces\SocialMediaPublisherInterface;
use App\Models\Account;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Revolution\Bluesky\Facades\Bluesky as RevolutionBluesky;
use Revolution\Bluesky\Record\Post as RevolutionPost;
use Revolution\Bluesky\RichText\TextBuilder;

class Bluesky implements SocialMediaPublisherInterface
{
    public function __construct()
    {
        //
    }

    public static function make(): self
    {
        return new self;
    }

    public function publish(Account $account, Post $post, array $content, array $media = []): mixed
    {
        try {
            return DB::transaction(function () use ($account, $post, $content) {
                $bluesky = RevolutionBluesky::login(identifier: $account->handle, password: Crypt::decryptString($account->access_token));

                $content = TiptapContentConverter::convertToBluesky($content);

                $blueskPost = RevolutionPost::build(function (TextBuilder $textBuilder) use ($content) {

                    $segments = $content;

                    foreach ($segments as $segment) {
                        if ($segment['type'] === 'text' || $segment['type'] === 'space') {
                            $textBuilder->text($segment['value']);
                        } elseif ($segment['type'] === 'mention') {
                            $textBuilder->mention($segment['value']);
                        } elseif ($segment['type'] === 'hashtag') {
                            $textBuilder->tag($segment['value']);
                        } elseif ($segment['type'] === 'link') {
                            $textBuilder->link($segment['value']);
                        } elseif ($segment['type'] === 'new_line') {
                            $textBuilder->newLine();
                        }
                    }
                });

                $response = $bluesky->post($blueskPost);

                if (isset($response->json()['error'])) {
                    throw new Exception($response->json()['message'] ?? 'Unknown error');
                }

                $post->update([
                    'status' => PostStatusType::PUBLISHED->value,
                    'post_id' => $response->json()['commit']['rev'] ?? null,
                    'published_at' => now(),
                ]);
            });
        } catch (Exception $e) {
            Log::error('Unexpected error during Bluesky post creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $post->update([
                'status' => PostStatusType::FAILED->value,
            ]);

            throw $e;
        }
    }
}
