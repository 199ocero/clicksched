<?php

namespace App\Enums;

enum PostStatusType: string
{
    case DRAFT = 'draft';
    case QUEUED = 'queued';
    case RUNNING = 'running';
    case PUBLISHED = 'published';
    case SCHEDULED = 'scheduled';
    case FAILED = 'failed';

    public function getStatusLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::QUEUED => 'Queued',
            self::RUNNING => 'Running',
            self::PUBLISHED => 'Published',
            self::SCHEDULED => 'Scheduled',
            self::FAILED => 'Failed',
        };
    }

    public function getStatusDescription(): string
    {
        return match ($this) {
            self::DRAFT => 'Your post is currently in draft mode. You can edit it and publish it later.',
            self::QUEUED => 'Your post is currently queued for publishing. It will be published shortly.',
            self::RUNNING => 'Your post is currently being published. It will be published shortly.',
            self::PUBLISHED => 'Your post has been published.',
            self::SCHEDULED => 'Your post is scheduled to be published at a specific time.',
            self::FAILED => 'Your post failed to publish. Please try again.',
        };
    }
}
