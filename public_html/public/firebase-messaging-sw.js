importScripts('https://www.gstatic.com/firebasejs/9.8.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.8.0/firebase-messaging-compat.js');

self.addEventListener('install', event => {
    self.skipWaiting();
})

self.addEventListener("push", (event) => {
    const notification = event.data.json();
    var option = {
        body: notification.data.body,
        icon: "/assets/icons/192X192.png",
        badge: "/assets/icons/72X72.png",
        image: notification.data.image,
        data: {
            url: notification.data.link,
            id: notification.data.id,
        },
    };

    if (notification.data.cta_text) {
        option.actions = [
            {
                action: "action1",
                title: notification.data.cta_text,
            },
        ];
        option.data.cta_link = notification.data.cta_link;
    }
    event.waitUntil(
        self.registration.showNotification(notification.data.title, option)
    );

    // Send a message to all clients in frontend
    self.clients.matchAll({ includeUncontrolled: true, type: 'window' }).then(clients => {
        clients.forEach(client => {
            client.postMessage({
                type: 'NOTIFICATION_DISPLAYED',
                data: {
                    id: notification.data.id,
                    title: notification.data.title,
                }
            });
        });
    });
});


self.addEventListener(
    "notificationclick",
    (event) => {
        switch (event.action) {
            case "action1":
                clients.openWindow(event.notification.data.cta_link); //which we got from above
                break;
        }
        event.waitUntil(clients.openWindow(event.notification.data.url));

        fetch("/notification-clicked?id=" + event.notification.data.id, {
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        }).then((r) => console.log(r.json()));
    },
    false
);