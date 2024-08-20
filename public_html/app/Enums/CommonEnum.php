<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Common Enum keeps track of string constant used to pointout different resources like Model,Permission,Route 
 */
class CommonEnum extends Enum
{
    // Models
    const VIDEO = 'video';
    const COMMENT = 'comment';

    // Routes, Permissions
    const ROLE_PERMISSION = 'role-permission';
    const MANAGE_PERMISSION = 'permissions';
    const MANAGE_ROLE = 'roles';
    const MANAGE_ROLE_ASSIGNMENT = 'roles-assignment';
    
    const CHANNELS_SHOW = 'channels.show';
    
    const CHANNELS_UPDATE = 'channels.update';
    const CHANNEL_VIDEOS_UPLOAD = 'channels.video.upload';
    const VIDEOS_UPDATE = 'videos.update';
    // const VIDEOS_GET_OBJECT_TAGS = 'videos.object_tags';
 
    const CHANNEL_SUBSCRIPTIONS_STORE = 'channel.subscriptions.store';
    const CHANNEL_SUBSCRIPTIONS_DESTROY = 'channel.subscriptions.destroy';
    const COMMENTS_STORE = 'comments.store';
    const VOTES = 'votes.vote';


    // cache
    const THEME = 'theme';
}
