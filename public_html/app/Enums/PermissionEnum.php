<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class PermissionEnum extends Enum
{
    const CAN_PREFIX = 'can.';
    const CHANNELS_UPDATE = self::CAN_PREFIX.'channels.update';
    const CHANNEL_VIDEOS_UPLOAD = self::CAN_PREFIX.'channels.video.upload';
    const CHANNEL_SUBSCRIPTIONS_STORE = self::CAN_PREFIX.'channel.subscriptions.store';
    const CHANNEL_SUBSCRIPTIONS_DESTROY = self::CAN_PREFIX.'channel.subscriptions.destroy';

    const VIDEOS_UPDATE = self::CAN_PREFIX.'videos.update';
    // const VIDEOS_GET_OBJECT_TAGS = self::CAN_PREFIX.'videos.object_tags';
    
    const COMMENTS_STORE = self::CAN_PREFIX.'comments.store';
    const VOTES = self::CAN_PREFIX.'votes.vote';
}
