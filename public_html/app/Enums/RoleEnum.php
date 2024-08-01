<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class RoleEnum extends Enum
{
    const ADMIN = 'admin';
    const SYSTEM = 'system';
    const VIEWER = 'viewer';
    const CHANNEL_OWNER = 'channel-owner';
}

