<?php

return [
    'config' => [
        'apiKey'=> env('FIREBASE_APIKEY'),
        'authDomain'=> env('FIREBASE_AUTHDOMAIN'),
        'projectId'=>env('FIREBASE_PROJECTID'),
        'storageBucket'=>env('FIREBASE_STORAGEBUCKET'),
        'messagingSenderId'=> env('FIREBASE_MESSAGINGSENDERID'),
        'appId'=> env('FIREBASE_APPID'),
        'measurementId'=> env('FIREBASE_MEASUREMENTID'),
    ],
    'urls' => [
        'subscribe' => 'https://iid.googleapis.com/iid/v1:batchAdd',
        'unsubscribe' => 'https://iid.googleapis.com/iid/v1:batchRemove',
        'send_message' => 'https://fcm.googleapis.com/v1/projects/%s/messages:send',
    ],
    'key' => [
        'topic'=> env('APP_NAME') . 'webpush',
        'vapid'=>env('FIREBASE_VAPID'),
        'logEventName' => env('APP_NAME') . '_push_notification_received',
        'propertyId' => env('FIREBASE_ANALYTICS_PROPERTY_ID')
    ]

];
