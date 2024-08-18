<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class ModelEnum extends Enum
{
    const MODELS = [
        CommonEnum::COMMENT => 'App\\Models\\Comment',
        CommonEnum::VIDEO => 'App\\Models\\Video'
    ];
}
