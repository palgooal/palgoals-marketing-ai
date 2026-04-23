<?php

namespace App\Support;

class AIOutputStatuses
{
    public const DRAFT = 'draft';

    public const REVIEWED = 'reviewed';

    public const APPROVED = 'approved';

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::DRAFT => 'Draft',
            self::REVIEWED => 'Reviewed',
            self::APPROVED => 'Approved',
        ];
    }

    public static function normalize(string $status): string
    {
        return match ($status) {
            'completed' => self::DRAFT,
            self::REVIEWED => self::REVIEWED,
            self::APPROVED => self::APPROVED,
            default => self::DRAFT,
        };
    }

    /**
     * @return list<string>
     */
    public static function databaseValuesFor(string $status): array
    {
        return match ($status) {
            self::DRAFT => [self::DRAFT, 'completed'],
            self::REVIEWED => [self::REVIEWED],
            self::APPROVED => [self::APPROVED],
            default => [$status],
        };
    }

    public static function canMarkReviewed(string $status): bool
    {
        return self::normalize($status) === self::DRAFT;
    }

    public static function canMarkApproved(string $status): bool
    {
        return self::normalize($status) !== self::APPROVED;
    }

    public static function canPublish(string $status): bool
    {
        return in_array(self::normalize($status), [self::REVIEWED, self::APPROVED], true);
    }
}
