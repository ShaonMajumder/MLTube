<div class="notify-alert-box">
    <img src="{{ asset('icons/favicon/favicon.ico') }}" alt="">
    <p>We'd like to send you notifications of the latest posts and updates</p>
    <div class="buttons">
        <button id="notify-cancel-btn">Cancel</button>
        <button id="notify-allow-btn">Allow</button>
    </div>
</div>
<script type="module">
    // Import Firebase modules
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";
    import { getAnalytics, logEvent } from 'https://www.gstatic.com/firebasejs/10.12.2/firebase-analytics.js';

    // Initialize Firebase
    let configData= {!! json_encode(config('firebase.config')) !!};
    let app = initializeApp(configData);
    let messaging = getMessaging(app);
    const analytics = getAnalytics();
    const firebaseLogEventName = "{{ config('firebase.key.logEventName') }}"

    // Constants
    const NOTIFICATION_DISPLAYED = 'NOTIFICATION_DISPLAYED';
    const NOTIFY_STORAGE_KEY = 'notify';
    const DEVICE_TOKEN_KEY = 'device_token';

    // DOM elements
    const notifyAlertBox = document.querySelector('.notify-alert-box');
    const notifyAllowBtn = document.getElementById('notify-allow-btn');
    const notifyCancelBtn = document.getElementById('notify-cancel-btn');

    // Helper functions
    const setNotifyStatus = (value) => {
        const notiObject = { value, timestamp: Date.now() };
        localStorage.setItem(NOTIFY_STORAGE_KEY, JSON.stringify(notiObject));
        notifyAlertBox.style.display = 'none';
    };
    /* OLD
    function notifyTrue(){
        var notiobject = {value: "true", timestamp: new Date().getTime()}
        localStorage.setItem("notify", JSON.stringify(notiobject));
        document.querySelector('.notify-alert-box').style.display='none'
    }

    function notifyFalse(){
        var notiobject = {value: "false", timestamp: new Date().getTime()}
        localStorage.setItem("notify", JSON.stringify(notiobject));
        document.querySelector('.notify-alert-box').style.display='none'
    }
    */

    const checkNotificationPermission = async () => {
        const permission = await Notification.requestPermission();
        return permission === 'granted';
    };

    const setupServiceWorker = async () => {
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.register("/firebase-messaging-sw.js")
                                    .then(registration => {
                                        console.log('Service Worker registered successfully:', registration);
                                    })
                                    .catch(error => {
                                        console.error('Service Worker registration failed:', error);
                                    });
            
        } else {
            console.log("Service workers are not supported.");
        }

        // Check if service workers are supported
        /* OLD
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            navigator.serviceWorker.register("/firebase-messaging-sw.js").then(function () { 

            }).catch(console.log);
        } else {
            console.log("Service workers are not supported.");
        }
        */
    };

    const setupMessaging = async () => {
        try {
            const token = await getToken(messaging, { vapidKey: "{{config('firebase.key.vapid')}}" });
            if (token) {
                localStorage.setItem(DEVICE_TOKEN_KEY, token);
                await saveSub(token);
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        } catch (err) {
            console.error('An error occurred while retrieving token:', err);
        }
    };

    // OLD
    // function saveSub(token){
    //     $.ajax({
    //         type: 'post',
    //         url : '{{ URL('save-push-notification-sub')}}',
    //         data:{
    //             '_token':"{{ csrf_token() }}",
    //             'device_token':token
    //         },
    //         success:function(data){
    //             console.log('token generated successfully')
    //         }
    //     })
    // }

    const saveSub = async (token) => {
        try {
            const response = await fetch('{{ URL('save-push-notification-sub')}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ device_token: token })
            });
            if (response.ok) {
                console.log('Token generated successfully');
            } else {
                console.error('Failed to save token');
            }
        } catch (error) {
            console.error('Error saving token:', error);
        }
    };

    // Event listeners
    notifyAllowBtn.addEventListener('click', async () => {
        setNotifyStatus('true'); // OLD notifyTrue();
        notifyOption(); // <- OLD
        /* NEW ->
        const isGranted = await checkNotificationPermission();
        if (isGranted) {
            await setupMessaging();
        }
        */
    });

    notifyCancelBtn.onclick=async()=>{
        setNotifyStatus(false); // OLD notifyFalse();
    }


    // Initialize
    const init = async () => {
        await setupServiceWorker();
        
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.ready.then((registration) => {
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data.type === NOTIFICATION_DISPLAYED) {
                        logEvent(analytics, firebaseLogEventName, {
                            notification_title: event.data.data.title,
                            notification_id: event.data.data.id,
                        });
                    }
                });
            });
        }

        /* OLD
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.ready.then((registration) => {
                navigator.serviceWorker.addEventListener('message', (event) => {

                    let pushNotificationLogData = event.data.data;
                    if (event.data.type === 'NOTIFICATION_DISPLAYED') {
                        
                        logEvent(analytics, firebaseLogEventName, {
                            notification_title: pushNotificationLogData.title,
                            notification_id: pushNotificationLogData.id,
                        });
                    }
                    
                });
            });
        }
        */

        setTimeout(() => {
            notifyAlertBox.style.top = '0';
        }, 1000);

        /* OLD
        setTimeout(function(){
            document.querySelector('.notify-alert-box').style.top='0'
        },1000)
        */

        checknotificationStatus() // <- OLD

        //// NEW ->
        /*
        const storedNotify = localStorage.getItem(NOTIFY_STORAGE_KEY);
        if (storedNotify) {
            const { value, timestamp } = JSON.parse(storedNotify);
            if (value === 'true') {
                notifyAlertBox.style.display = 'none';
                await setupMessaging();
            } else if (value === 'false') {
                const hoursSinceDeclined = (Date.now() - timestamp) / (1000 * 60 * 60);
                if (hoursSinceDeclined < 24) {
                    notifyAlertBox.style.display = 'none';
                }
            }
        }

        navigator.permissions.query({name: 'notifications'}).then(function(permission) {  
            permission.onchange = function() {  
                if (permission.state !== 'granted' && permission.state !== 'denied') {
                    localStorage.removeItem(NOTIFY_STORAGE_KEY);
                }
            };
        });
        */
    };

    init();

    /////////////// old code V
    function notifyOption(){
        if(Notification.permission=='granted'){
            initialization();
        }else if(Notification.permission !== 'denied'){
            Notification.requestPermission().then(permission=>{
                if(permission=='granted'){
                    initialization();
                }
            })
        }
    }
   
    function checknotificationStatus(){
        navigator.permissions.query({name: 'notifications'}).then(function(permission) {  
        permission.onchange = function() {  
            if(permission.state!=='granted' && permission.state!=='denied'){
                localStorage.removeItem('notify')
            }
            
        };
        });
        if(localStorage.getItem("notify")){
            let noti_object = JSON.parse(localStorage.getItem("notify"))
            let dateString;
            let status;
            let now;
            if(noti_object){
                dateString = noti_object.timestamp;
                status = noti_object.value;
                if(status=='false'){
                    now = new Date().getTime().toString();
                    let difference=(now - dateString)/1000/60/60; //get min from timestamp
                    if(difference<24){
                        document.querySelector('.notify-alert-box').style.display='none'
                    }
                }else{
                    document.querySelector('.notify-alert-box').style.display='none';
                    notifyOption()
                }
            }
        }
    }

    async function initialization() {
        let isSubscribed=null
        if (
            "Notification" in window &&
            "Notification" in window &&
            "permission" in Notification
        ) {
            const permission = await Notification.requestPermission();
            if (permission === "granted") {
                navigator.serviceWorker.ready.then((sw) => {
                    sw.pushManager.getSubscription().then(function (subscription) {
                        isSubscribed = !(subscription === null);
                        if (isSubscribed) {
                            console.log("User is subscribed");
                        } else {
                            console.log("User is not subscribed");
                            setupMessaging();
                            sw.pushManager
                                .subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey:"{{config('firebase.key.vapid')}}",
                                })
                                .then((subscribtion) => {
                                console.log('user is subscribed');
                                })
                                .then((subscribtion) => {});
                            
                        }
                    });
                });
            } else if (permission === "denied") {
                console.log("User denied Push Notification Permission");
            }
        } else {
            console.log("Notification API is not supported.");
        }
    }
    
</script>
