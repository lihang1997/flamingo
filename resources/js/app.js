/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')
require('./http')

window.Vue = require('vue')

/**
 * Then we will import vue-router、vuex component, and set for vue object. 
 */
import VueRouter from 'vue-router'
Vue.use(VueRouter)

/**
 * Replace vue-router push method to avoid router error.
 */
const rawRouterPush = VueRouter.prototype.push
VueRouter.prototype.push = function(to) {
    return rawRouterPush.call(this, to).catch(error => error)
}

/*
 * Then we will load router configurations, and create router object.
 */
import routes from './routes'
const router = new VueRouter({
    mode: 'hash',
    routes
})

/**
 * Then we load element-ui components library, and set for vue object. 
 */
import ELementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
Vue.use(ELementUI)

/**
 * Then we load moment components library, and set for vue object. 
 */
import moment from 'moment'
Vue.prototype.$moment = moment

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import App from './App.vue'

const app = new Vue({
    el: '#app',
    router,
    render: h => h(App)
});
