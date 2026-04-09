<?php

namespace App\Enums;

enum ContentType: string
{
    case VIDEO = 'video';
    case IMAGE = 'image';
    case DESCRIPTION = 'description';

    public function label(): string
    {
        return match($this) {
            self::VIDEO => 'Video',
            self::IMAGE => 'Image',
            self::DESCRIPTION => 'Description',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}