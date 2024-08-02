<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class UserEnum extends Enum
{
    const STATUS = [
        'ACTIVE' => StatusEnum::STATUS['ACTIVE'],
        'INACTIVE' => StatusEnum::STATUS['INACTIVE'],
        'SUSPENDED' => StatusEnum::STATUS['SUSPENDED'],
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

