<?php

use App\Enums\PermissionEnum;

return [
    PermissionEnum::ROLE_PERMISSION => 'Access Role-Permissions',
    PermissionEnum::MANAGE_PERMISSION => 'Manage Permissions',
    PermissionEnum::MANAGE_ROLE => 'Manage Roles',
    PermissionEnum::MANAGE_ROLE_ASSIGNMENT => 'Manage Role-Assignment',

    PermissionEnum::CHANNELS_UPDATE => 'Update Channel Details',
    PermissionEnum::CHANNEL_VIDEOS_UPLOAD => 'Upload Videos to Channel',
    PermissionEnum::CHANNEL_SUBSCRIPTIONS_STORE => 'Store Channel Subscriptions',
    PermissionEnum::CHANNEL_SUBSCRIPTIONS_DESTROY => 'Delete Channel Subscriptions',

    PermissionEnum::VIDEOS_UPDATE => 'Update Video Details',
    // PermissionEnum::VIDEOS_GET_OBJECT_TAGS => 'Get Video Object Tags',

    PermissionEnum::COMMENTS_STORE => 'Store Comments',
    PermissionEnum::VOTES => 'Vote on Content',






    // const CHANNELS_UPDATE = self::CAN_PREFIX . CommonEnum::CHANNELS_UPDATE;
    // const CHANNEL_VIDEOS_UPLOAD = self::CAN_PREFIX . CommonEnum::CHANNEL_VIDEOS_UPLOAD;
    // const VIDEOS_UPDATE = self::CAN_PREFIX . CommonEnum::VIDEOS_UPDATE;
    // // const VIDEOS_GET_OBJECT_TAGS = self::CAN_PREFIX . CommonEnum::VIDEOS_GET_OBJECT_TAGS;

    // const CHANNEL_SUBSCRIPTIONS_STORE = self::CAN_PREFIX . CommonEnum::CHANNEL_SUBSCRIPTIONS_STORE;
    // const CHANNEL_SUBSCRIPTIONS_DESTROY = self::CAN_PREFIX . CommonEnum::CHANNEL_SUBSCRIPTIONS_DESTROY;
    // const COMMENTS_STORE = self::CAN_PREFIX . CommonEnum::COMMENTS_STORE;
    // const VOTES = self::CAN_PREFIX . CommonEnum::VOTES;
];