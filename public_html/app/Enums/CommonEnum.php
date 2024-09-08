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
    const HOME = 'home';
    
    const ROLE_PERMISSION = 'role-permission';
    const MANAGE_PERMISSION = 'laratrust.permissions.index';
    const MANAGE_ROLE = 'laratrust.roles.index';
    const MANAGE_ROLE_ASSIGNMENT = 'laratrust.roles-assignment.index';
    
    const CHANNELS_SHOW = 'channels.show';

    const CHANNEL_OWNED = 'channel.owned';
    const CHANNEL_UPDATE = 'channels.update';
    const CHANNEL_VIDEOS_UPLOAD = 'channels.video.upload';
    const CHANNEL_SUBSCRIBERS = 'channels.subscribers';
    const VIDEOS_UPDATE = 'videos.update';
    const VIDEOS_SHOW = 'videos.show';
    // const VIDEOS_GET_OBJECT_TAGS = 'videos.object_tags';
 
    // viewer user
    const CHANNEL_SUBSCRIPTIONS_STORE = 'channel.subscriptions.store';
    const CHANNEL_SUBSCRIPTIONS_DESTROY = 'channel.subscriptions.destroy';
    const COMMENTS_STORE = 'comments.store';
    const VOTES = 'votes.vote';
    
    const MYACCOUNT = 'myaccount';
    const MYACCOUNT_SHOW = "users.myaccount";
    const USERS_SHOW = "users.show";
    const USER_CHANNEL_SUBSCRIPTIONS = 'user.channel.subscriptions';

    // cache
    const THEME = 'theme';
    const THEME_UPDATE = self::THEME . '.update';
    const CACHES_CLEAR = 'caches.clear';
    const ML_TAGS = 'ml.tags';
    const ML_TAGS_CONFIDENCE = self::ML_TAGS . '.confidence';

    const ROUTES = 'routes';

    // New Administrative Routes
    const ADMIN = 'admin';
    const MANAGE_SITE = 'manage-site';
    const ADMIN_MANAGE_SITE = self::ADMIN . '.' . self::MANAGE_SITE ;
    const ADMIN_MANAGE_SITE_CLEAR_ALL = 'admin.site-manage.clear.all';
    const ADMIN_MANAGE_SITE_CLEAR_ALL_SESSIONS = 'admin.site-manage.clear.all.sessions';
    const ADMIN_MANAGE_SITE_CLEAR_ALL_COOKIES = 'admin.site-manage.clear.all.cookies';
    const ADMIN_MANAGE_SITE_CLEAR_ALL_CACHES = 'admin.site-manage.clear.all.caches';

    const ADMIN_MANAGE_SITE_CLEAR_PERSONAL_SESSION = 'admin.site-manage.clear.personal.session';
    const ADMIN_MANAGE_SITE_CLEAR_PERSONAL_COOKIES = 'admin.site-manage.clear.personal.cookies';
    const ADMIN_MANAGE_SITE_CLEAR_PERSONAL_CACHE = 'admin.site-manage.clear.personal.cache';
}
