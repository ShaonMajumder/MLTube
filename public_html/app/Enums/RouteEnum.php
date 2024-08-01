<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

class RouteEnum
{
    const CHANNELS_SHOW = 'channels.show';

    const CHANNELS_UPDATE = 'channels.update';
    const CHANNEL_VIDEOS_UPLOAD = 'channels.video.upload';
    const CHANNEL_SUBSCRIPTIONS_STORE = 'channel.subscriptions.store';
    const CHANNEL_SUBSCRIPTIONS_DESTROY = 'channel.subscriptions.destroy';

    const VIDEOS_UPDATE = 'videos.update';
    // const VIDEOS_GET_OBJECT_TAGS = 'videos.object_tags';
    
    const COMMENTS_STORE = 'comments.store';
    const VOTES = 'votes.vote';
}
