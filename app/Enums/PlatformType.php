<?php

namespace App\Enums;

enum PlatformType: string
{
    case BLUESKY = 'bluesky';

    public function getPlatform(): string
    {
        return match ($this) {
            self::BLUESKY => 'bluesky',
        };
    }
}
