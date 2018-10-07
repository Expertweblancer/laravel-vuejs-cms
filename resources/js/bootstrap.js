import Vue from 'vue'
import VueRouter from 'vue-router'
import axios from 'axios'
import Form from './services/form'
import helper from './services/helper'
import VTooltip from 'v-tooltip'
import VuejsDialog from "vuejs-dialog"
import showTip from './components/show-tip'
import paginationRecord from './components/pagination-record'
import showError from './components/show-error'
import moduleInfo from './components/module-info'

window._get = require('lodash/get');
window._eachRight = require('lodash/eachRight');
window._replace = require('lodash/replace');
window._has = require('lodash/has');
window._size = require('lodash/size');

window.Vue = Vue;
Vue.use(VueRouter);
window.axios = axios;
window.Form = Form;
window.helper = helper;
Vue.prototype.trans = (string, args) => {
    let value = _get(window.i18n, string);

    _eachRight(args, (paramVal, paramKey) => {
        value = _replace(value, `:${paramKey}`, paramVal);
    });
    return value;
};
Vue.prototype.$last = function (item, list) {
  return item == list[list.length - 1]
};

Vue.use(VTooltip);
Vue.use(VuejsDialog)
Vue.component('show-tip',showTip);
Vue.component('pagination-record',paginationRecord);
Vue.component('show-error',showError);
Vue.component('module-info',moduleInfo);

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
