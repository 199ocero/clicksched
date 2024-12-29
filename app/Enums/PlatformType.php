<?php

namespace App\Enums;

enum PlatformType: string
{
    case BLUESKY = 'bluesky';

    public function getPlatformLabel(): string
    {
        return match ($this) {
            self::BLUESKY => 'Bluesky',
        };
    }
}
