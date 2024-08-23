<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class RouteEnum
{
    const HOME = CommonEnum::HOME;
    
    const ROLE_PERMISSION = CommonEnum::ROLE_PERMISSION;
    const MANAGE_PERMISSION = CommonEnum::MANAGE_PERMISSION;
    const MANAGE_ROLE = CommonEnum::MANAGE_ROLE;
    const MANAGE_ROLE_ASSIGNMENT = CommonEnum::MANAGE_ROLE_ASSIGNMENT;

    const CHANNELS_SHOW = CommonEnum::CHANNELS_SHOW;

    const CHANNEL_OWNED = CommonEnum::CHANNEL_OWNED;
    const CHANNEL_SUBSCRIBERS = CommonEnum::CHANNEL_SUBSCRIBERS;
    const CHANNEL_UPDATE = CommonEnum::CHANNEL_UPDATE;
    const CHANNEL_VIDEOS_UPLOAD = CommonEnum::CHANNEL_VIDEOS_UPLOAD;
    const VIDEOS_UPDATE = CommonEnum::VIDEOS_UPDATE;
    const VIDEOS_SHOW = CommonEnum::VIDEOS_SHOW;
    // const VIDEOS_GET_OBJECT_TAGS = CommonEnum::VIDEOS_GET_OBJECT_TAGS;
    

    const CHANNEL_SUBSCRIPTIONS_STORE = CommonEnum::CHANNEL_SUBSCRIPTIONS_STORE;
    const CHANNEL_SUBSCRIPTIONS_DESTROY = CommonEnum::CHANNEL_SUBSCRIPTIONS_DESTROY;
    const COMMENTS_STORE = CommonEnum::COMMENTS_STORE;
    const VOTES = CommonEnum::VOTES;
    const USER_CHANNEL_SUBSCRIPTIONS = CommonEnum::USER_CHANNEL_SUBSCRIPTIONS;

    const MYACCOUNT_SHOW = CommonEnum::MYACCOUNT_SHOW;
    const USERS_SHOW = CommonEnum::USERS_SHOW;

    const THEME_UPDATE = CommonEnum::THEME_UPDATE;
    const CACHES_CLEAR = CommonEnum::CACHES_CLEAR;
}
