<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class StatusEnum extends Enum
{
    const STATUS = [
        'ACTIVE' => 'active',
        'INACTIVE' => 'inactive',
        'SUSPENDED' => 'suspended'
    ];

    /**
     * Get the list of statuses.
     *
     * @return array
     */
    public static function getStatuses(): array
    {
        return self::STATUS;
    }
}

