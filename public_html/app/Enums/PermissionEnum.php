<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class PermissionEnum extends Enum
{
    const CAN_PREFIX = 'can.';

    const CHANNELS_UPDATE = self::CAN_PREFIX . CommonEnum::CHANNELS_UPDATE;
    const CHANNEL_VIDEOS_UPLOAD = self::CAN_PREFIX . CommonEnum::CHANNEL_VIDEOS_UPLOAD;
    const VIDEOS_UPDATE = self::CAN_PREFIX . CommonEnum::VIDEOS_UPDATE;
    // const VIDEOS_GET_OBJECT_TAGS = self::CAN_PREFIX . CommonEnum::VIDEOS_GET_OBJECT_TAGS;

    const CHANNEL_OWNER_PERMISSIONS = [
        self::CHANNELS_UPDATE,
        self::CHANNEL_VIDEOS_UPLOAD,
        self::VIDEOS_UPDATE,
        // self::VIDEOS_GET_OBJECT_TAGS,
    ];
    
    const CHANNEL_SUBSCRIPTIONS_STORE = self::CAN_PREFIX . CommonEnum::CHANNEL_SUBSCRIPTIONS_STORE;
    const CHANNEL_SUBSCRIPTIONS_DESTROY = self::CAN_PREFIX . CommonEnum::CHANNEL_SUBSCRIPTIONS_DESTROY;
    const COMMENTS_STORE = self::CAN_PREFIX . CommonEnum::COMMENTS_STORE;
    const VOTES = self::CAN_PREFIX . CommonEnum::VOTES;

    const VIEWER_PERMISSIONS = [
        self::CHANNEL_SUBSCRIPTIONS_STORE,
        self::CHANNEL_SUBSCRIPTIONS_DESTROY,
        self::COMMENTS_STORE,
        self::VOTES,
    ];
}
