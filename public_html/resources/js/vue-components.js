/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


Vue.config.ignoredElements = ['video-js']


Vue.component('comments', require('./components/comments.vue').default)
Vue.component('votes', require('./components/votes.vue').default )
Vue.component('subscribe-button', require('./components/subscribe-button.vue').default )
Vue.component('search', require('./components/search.vue').default )
Vue.component('avatar', require('./components/profile_avatar.vue').default )
Vue.component('avatar-user', require('./components/user_avatar.vue').default )
require('./components/channel-uploads')


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
