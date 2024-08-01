<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class UserTypeEnum extends Enum
{
    // Define user types
    const ADMIN = 'admin';
    const SYSTEM_ADMIN = 'systemadmin';
    const VIEWER = 'viewer';
}

