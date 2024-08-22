<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Common Enum keeps track of string constant used to pointout different resources like Model,Permission,Route 
 */
class CommonEnum extends Enum
{
    const HOME = 'home';
    
    // Models
    const VIDEO = 'video';
    const COMMENT = 'comment';

    // Routes, Permissions
    const ROLE_PERMISSION = 'role-permission';
    // const MANAGE_PERMISSION = 'permissions';
    // const MANAGE_ROLE = 'roles';
    // const MANAGE_ROLE_ASSIGNMENT = 'roles-assignment';
    const MANAGE_PERMISSION = 'laratrust.permissions.index';
    const MANAGE_ROLE = 'laratrust.roles.index';
    const MANAGE_ROLE_ASSIGNMENT = 'laratrust.roles-assignment.index';
    
    const CHANNELS_SHOW = 'channels.show';
    
    const CHANNELS_UPDATE = 'channels.update';
    const CHANNEL_VIDEOS_UPLOAD = 'channels.video.upload';
    const VIDEOS_UPDATE = 'videos.update';
    const VIDEOS_SHOW = 'videos.show';
    // const VIDEOS_GET_OBJECT_TAGS = 'videos.object_tags';
 
    // viewer user
    const CHANNEL_SUBSCRIPTIONS_STORE = 'channel.subscriptions.store';
    const CHANNEL_SUBSCRIPTIONS_DESTROY = 'channel.subscriptions.destroy';
    const CHANNEL_SUBSCRIPTIONS = 'channel.subscriptions';
    const COMMENTS_STORE = 'comments.store';
    const VOTES = 'votes.vote';
    const USER_CHANNEL_SUBSCRIBED = 'user.channel_subscribed';

    // cache
    const THEME = 'theme';
    const THEME_UPDATE = self::THEME . '.update';
    const CACHES_CLEAR = 'caches.clear';


    const ROUTES = 'routes';
}
