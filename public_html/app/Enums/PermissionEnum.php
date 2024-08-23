<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class PermissionEnum extends Enum
{
    const CAN_PREFIX = 'can.';

    const ROLE_PERMISSION = self::CAN_PREFIX . CommonEnum::ROLE_PERMISSION;
    const MANAGE_PERMISSION = self::CAN_PREFIX . CommonEnum::MANAGE_PERMISSION;
    const MANAGE_ROLE = self::CAN_PREFIX . CommonEnum::MANAGE_ROLE;
    const MANAGE_ROLE_ASSIGNMENT = self::CAN_PREFIX . CommonEnum::MANAGE_ROLE_ASSIGNMENT;

    const CHANNEL_OWNED = self::CAN_PREFIX . CommonEnum::CHANNEL_OWNED;
    const CHANNEL_SUBSCRIBERS = self::CAN_PREFIX . CommonEnum::CHANNEL_SUBSCRIBERS;
    const CHANNEL_UPDATE = self::CAN_PREFIX . CommonEnum::CHANNEL_UPDATE;
    const CHANNEL_VIDEOS_UPLOAD = self::CAN_PREFIX . CommonEnum::CHANNEL_VIDEOS_UPLOAD;
    const VIDEOS_UPDATE = self::CAN_PREFIX . CommonEnum::VIDEOS_UPDATE;
    // const VIDEOS_GET_OBJECT_TAGS = self::CAN_PREFIX . CommonEnum::VIDEOS_GET_OBJECT_TAGS;
    
    const CHANNEL_SUBSCRIPTIONS_STORE = self::CAN_PREFIX . CommonEnum::CHANNEL_SUBSCRIPTIONS_STORE;
    const CHANNEL_SUBSCRIPTIONS_DESTROY = self::CAN_PREFIX . CommonEnum::CHANNEL_SUBSCRIPTIONS_DESTROY;
    const COMMENTS_STORE = self::CAN_PREFIX . CommonEnum::COMMENTS_STORE;
    const VOTES = self::CAN_PREFIX . CommonEnum::VOTES;
    const USER_CHANNEL_SUBSCRIPTIONS = self::CAN_PREFIX . CommonEnum::USER_CHANNEL_SUBSCRIPTIONS;

    // const USERS_SHOW = self::CAN_PREFIX . CommonEnum::USERS_SHOW;

    const MYACCOUNT_SHOW = self::CAN_PREFIX . CommonEnum::MYACCOUNT_SHOW;
    

    const ROLE_MANAGEMENT_PERMISSIONS = [
        self::ROLE_PERMISSION,
        self::MANAGE_PERMISSION,
        self::MANAGE_ROLE,
        self::MANAGE_ROLE_ASSIGNMENT
    ];

    const CHANNEL_OWNER_PERMISSIONS = [
        self::CHANNEL_OWNED,
        self::CHANNEL_UPDATE,
        self::CHANNEL_VIDEOS_UPLOAD,
        self::VIDEOS_UPDATE,
        self::CHANNEL_SUBSCRIBERS
        // self::VIDEOS_GET_OBJECT_TAGS,
        
    ];

    const VIEWER_PERMISSIONS = [
        self::MYACCOUNT_SHOW,
        self::CHANNEL_SUBSCRIPTIONS_STORE,
        self::CHANNEL_SUBSCRIPTIONS_DESTROY,
        self::COMMENTS_STORE,
        self::VOTES,
        self::USER_CHANNEL_SUBSCRIPTIONS
    ];
}
