<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class ChannelEnum extends Enum
{
    const STATUS = [
        'ACTIVE' => StatusEnum::STATUS['ACTIVE'],
        'INACTIVE' => StatusEnum::STATUS['INACTIVE'],
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

