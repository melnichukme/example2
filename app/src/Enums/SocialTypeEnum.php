<?php

namespace App\Enums;

enum SocialTypeEnum: int
{
    case SPOTIFY = 4;
    case YANDEX = 3;
    case VKONTAKTE = 2;
    case SHAZAM = 8;

    /**
     * @param SocialTypeEnum $self
     * @return string
     */
    public static function getType(self $self): string
    {
        return match ($self) {
            self::SPOTIFY => 'spotify',
            self::YANDEX => 'yandex-music',
            self::VKONTAKTE => 'vkontakte',
            self::SHAZAM => 'shazam',
        };
    }

    /**
     * @param int $id
     * @return string
     */
    public static function getSlugById(int $id): string
    {
        return match ($id) {
            self::SPOTIFY->value => 'spotify',
            self::YANDEX->value => 'yandex-music',
            self::VKONTAKTE->value => 'vkontakte',
            self::SHAZAM->value => 'shazam',
            default => null
        };
    }
}
