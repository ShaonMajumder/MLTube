<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class PermissionEnum extends Enum
{
    const CAN_PREFIX = 'can.';

    const CHANNELS_UPDATE = self::CAN_PREFIX.'channels.update'; // own
    const CHANNEL_VIDEOS_UPLOAD = self::CAN_PREFIX.'channels.video.upload'; // own
    const VIDEOS_UPDATE = self::CAN_PREFIX.'videos.update'; // own
    // const VIDEOS_GET_OBJECT_TAGS = self::CAN_PREFIX.'videos.object_tags';

    const CHANNEL_OWNER_PERMISSIONS = [
        self::CHANNELS_UPDATE,
        self::CHANNEL_VIDEOS_UPLOAD,
        self::VIDEOS_UPDATE,
        // self::VIDEOS_GET_OBJECT_TAGS,
    ];
    
    const CHANNEL_SUBSCRIPTIONS_STORE = self::CAN_PREFIX.'channel.subscriptions.store'; // own
    const CHANNEL_SUBSCRIPTIONS_DESTROY = self::CAN_PREFIX.'channel.subscriptions.destroy'; // own
    const COMMENTS_STORE = self::CAN_PREFIX.'comments.store';
    const VOTES = self::CAN_PREFIX.'votes.vote';

    const VIEWER_PERMISSIONS = [
        self::CHANNEL_SUBSCRIPTIONS_STORE,
        self::CHANNEL_SUBSCRIPTIONS_DESTROY,
        self::COMMENTS_STORE,
        self::VOTES,
    ];
}
