<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class RouteEnum
{
    const ROLE_PERMISSION = CommonEnum::ROLE_PERMISSION;

    const CHANNELS_SHOW = CommonEnum::CHANNELS_SHOW;

    const CHANNELS_UPDATE = CommonEnum::CHANNELS_UPDATE;
    const CHANNEL_VIDEOS_UPLOAD = CommonEnum::CHANNEL_VIDEOS_UPLOAD;
    const CHANNEL_SUBSCRIPTIONS_STORE = CommonEnum::CHANNEL_SUBSCRIPTIONS_STORE;
    const CHANNEL_SUBSCRIPTIONS_DESTROY = CommonEnum::CHANNEL_SUBSCRIPTIONS_DESTROY;

    const VIDEOS_UPDATE = CommonEnum::VIDEOS_UPDATE;
    // const VIDEOS_GET_OBJECT_TAGS = CommonEnum::VIDEOS_GET_OBJECT_TAGS;
    
    const COMMENTS_STORE = CommonEnum::COMMENTS_STORE;
    const VOTES = CommonEnum::VOTES;
}
