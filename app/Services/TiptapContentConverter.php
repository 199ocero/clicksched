<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TiptapContentConverter
{
    public static function convertToText(array $content): string
    {
        if (empty($content) || ! isset($content['content'])) {
            return '';
        }

        $paragraphs = array_map(function ($paragraph) {

            if (! isset($paragraph['type']) || $paragraph['type'] !== 'paragraph') {
                return '';
            }

            $text = array_reduce($paragraph['content'] ?? [], function ($carry, $item) {
                if (isset($item['type']) && $item['type'] === 'text' && isset($item['text'])) {
                    $text = $item['text'];

                    if (isset($item['marks'])) {
                        foreach ($item['marks'] as $mark) {
                            if ($mark['type'] === 'link' && isset($mark['attrs']['href'])) {
                                $url = str_replace('http://', 'https://', $mark['attrs']['href']);
                                $text = ' '.$url.' ';
                                break;
                            }
                        }
                    }

                    return $carry.$text;
                } elseif (isset($item['type']) && $item['type'] === 'hardBreak') {
                    return $carry."\n";
                }

                return $carry;
            }, '');

            return trim($text);
        }, $content['content']);

        return trim(implode("\n", array_filter($paragraphs)));
    }

    public static function convertToBluesky(array $content): array
    {
        Log::info(self::convertToText($content));
        $content = Str::limit(self::convertToText($content), 300, '');
        Log::info($content);
        $entities = [
            'urls' => [],
            'mentions' => [],
            'hashtags' => [],
        ];
        $placeholders = [];
        $parsed = [];

        // Define patterns
        $patterns = [
            'urls' => '/(?<!@)https?:\/\/[^\s]+/i',
            'mentions' => '/@[\w.]+/',
            'hashtags' => '/#[\w]+/',
        ];

        // Extract entities and generate placeholders
        foreach ($patterns as $type => $pattern) {
            preg_match_all($pattern, $content, $matches);
            foreach ($matches[0] as $index => $match) {
                $placeholder = '{{'.strtoupper($type)."_PLACEHOLDER_$index}}";
                $content = str_replace($match, $placeholder, $content);
                $entities[$type][] = $match;
                $placeholders[$type][] = [
                    'placeholder' => $placeholder,
                    'value' => $match,
                ];
            }
        }

        // Split content into characters
        $chars = preg_split('//u', $content, -1, PREG_SPLIT_NO_EMPTY);
        $currentContent = '';

        // Classify content while parsing
        foreach ($chars as $char) {
            if ($char === "\n") {
                if (! empty($currentContent)) {
                    $parsed[] = self::classifyContent($currentContent);
                    $currentContent = '';
                }
                $parsed[] = ['type' => 'new_line', 'value' => $char];
            } elseif ($char === ' ') {
                if (! empty($currentContent)) {
                    $parsed[] = self::classifyContent($currentContent);
                    $currentContent = '';
                }
                $parsed[] = ['type' => 'space', 'value' => $char];
            } else {
                $currentContent .= $char;
            }
        }

        // Process any remaining content
        if (! empty($currentContent)) {
            $parsed[] = self::classifyContent($currentContent);
        }

        // Reinsert original entities from placeholders
        foreach (['urls', 'mentions', 'hashtags'] as $type) {
            if (isset($placeholders[$type])) {
                foreach ($placeholders[$type] as $placeholder) {
                    foreach ($parsed as &$item) {
                        if ($item['value'] === $placeholder['placeholder']) {
                            $item = [
                                'type' => $type === 'urls' ? 'link' : $type,
                                'value' => $placeholder['value'],
                            ];
                        }
                    }
                }
            }
        }

        return $parsed;
    }

    private static function classifyContent($content)
    {
        if (preg_match('/^(https?:\/\/)?([\da-z\.-]+\.[a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $content)) {
            return ['type' => 'link', 'value' => $content];
        }

        if (preg_match('/^@[\w.]+$/', $content)) {
            return ['type' => 'mention', 'value' => $content];
        }

        if (preg_match('/^#[\w]+$/', $content)) {
            return ['type' => 'hashtag', 'value' => $content];
        }

        return ['type' => 'text', 'value' => $content];
    }
}
