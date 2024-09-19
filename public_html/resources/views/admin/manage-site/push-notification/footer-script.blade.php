<div class="notify-alert-box">
    <img src="/assets/icons/tm-favicon.ico" alt="">
    <p>We'd like to send you notifications of the latest posts and updates</p>
    <div class="buttons">
        <button id="notify-cancel-btn">Cancel</button>
        <button id="notify-allow-btn">Allow</button>
    </div>
</div>
<script type="module">
    // Import the functions you need from the SDKs you need
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging.js";
    import { getAnalytics, logEvent } from 'https://www.gstatic.com/firebasejs/10.12.2/firebase-analytics.js';

    let configData= {!! json_encode(config('firebase.config')) !!};
    let app = initializeApp(configData);
    let messaging = getMessaging(app);
    const analytics = getAnalytics();
    const firebaseLogEventName = "{{ config('firebase.key.logEventName') }}"

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.ready.then((registration) => {

            // Listen for messages from the service worker
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
    
    // Check if service workers are supported
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.register("/firebase-messaging-sw.js").then(function () { 

        }).catch(console.log);
    } else {
        console.log("Service workers are not supported.");
    }

    setTimeout(function(){
        document.querySelector('.notify-alert-box').style.top='0'
    },1000)

    checknotificationStatus()



    document.querySelector('#notify-allow-btn').onclick=async()=>{
        notifyTrue();
        notifyOption();
    }
    function notifyTrue(){
        document.querySelector('.notify-alert-box').style.display='none'
        var notiobject = {value: "true", timestamp: new Date().getTime()}
        localStorage.setItem("notify", JSON.stringify(notiobject));
    }
    document.querySelector('#notify-cancel-btn').onclick=async()=>{
        notifyFalse();
    }
    function notifyFalse(){
        document.querySelector('.notify-alert-box').style.display='none'
        var notiobject = {value: "false", timestamp: new Date().getTime()}
        localStorage.setItem("notify", JSON.stringify(notiobject));
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

    // Request permission and get the token
    async function setupMessaging() {
        try {
            const token = await getToken(messaging, { vapidKey: "{{config('firebase.key.vapid')}}" });
            // console.log('this is my FCM token',token)
            if (token) {
                localStorage.setItem('device_token',token)
                saveSub(token)
                
            } else {
                console.log('No registration token available. Request permission to generate one.');
            }
        } catch (err) {
        console.log('An error occurred while retrieving token. ', err);
        }
    }

    function saveSub(token){
        $.ajax({
            type: 'post',
            url : '{{ URL('save-push-notification-sub')}}',
            data:{
                '_token':"{{ csrf_token() }}",
                'device_token':token
            },
            success:function(data){
                console.log('token generated successfully')
            }
        })
    }
</script>