<?php

use App\Enums\PermissionEnum;
use App\Models\Permission;

return [
    PermissionEnum::ROLE_PERMISSION => 'Access Role-Permissions',
    PermissionEnum::MANAGE_PERMISSION => 'Manage Permissions',
    PermissionEnum::MANAGE_ROLE => 'Manage Roles',
    PermissionEnum::MANAGE_ROLE_ASSIGNMENT => 'Manage Role-Assignment',

    PermissionEnum::CHANNEL_OWNED => 'My Channel',
    PermissionEnum::CHANNEL_SUBSCRIBERS => 'Channel Subscribers',
    PermissionEnum::CHANNEL_UPDATE => 'Update Channel Details',
    PermissionEnum::CHANNEL_VIDEOS_UPLOAD => 'Upload Videos to Channel',

    PermissionEnum::CHANNEL_SUBSCRIPTIONS_STORE => 'Store Channel Subscriptions',
    PermissionEnum::CHANNEL_SUBSCRIPTIONS_DESTROY => 'Delete Channel Subscriptions',
    PermissionEnum::USER_CHANNEL_SUBSCRIPTIONS => 'List Subscribers',
    PermissionEnum::VIDEOS_UPDATE => 'Update Video Details',
    // PermissionEnum::VIDEOS_GET_OBJECT_TAGS => 'Get Video Object Tags',

    PermissionEnum::COMMENTS_STORE => 'Store Comments',
    PermissionEnum::VOTES => 'Vote on Content',
    PermissionEnum::MYACCOUNT_SHOW => 'Show MyAccount'
];