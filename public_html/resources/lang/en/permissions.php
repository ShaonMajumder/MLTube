<?php

use App\Enums\PermissionEnum;

return [
    PermissionEnum::CHANNELS_UPDATE => 'Update Channel Details',
    PermissionEnum::CHANNEL_VIDEOS_UPLOAD => 'Upload Videos to Channel',
    PermissionEnum::CHANNEL_SUBSCRIPTIONS_STORE => 'Store Channel Subscriptions',
    PermissionEnum::CHANNEL_SUBSCRIPTIONS_DESTROY => 'Delete Channel Subscriptions',

    PermissionEnum::VIDEOS_UPDATE => 'Update Video Details',
    // PermissionEnum::VIDEOS_GET_OBJECT_TAGS => 'Get Video Object Tags',

    PermissionEnum::COMMENTS_STORE => 'Store Comments',
    PermissionEnum::VOTES => 'Vote on Content',
];