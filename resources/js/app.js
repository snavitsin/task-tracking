require('./bootstrap');

import 'tippy.js/themes/light.css';
import 'tippy.js/dist/tippy.css';
import '../sass/app.scss';

import { vTippy, vMask } from './directives';

import VeeValidate, { Validator } from 'vee-validate';
import ru from 'vee-validate/dist/locale/ru';

import VueIcon from 'vue-icon'
import Vue from 'vue';
import PortalVue from 'portal-vue';
import Notifications from 'vue-notification';

window.Vue = require('vue').default;

Validator.localize('ru', ru);
//Validator.localize('ru');

Vue.use(VeeValidate, {
    locale: ru,
    errorBagName: 'veeErrors',
    fieldsBagName: 'veeFields',
});

Vue.use(PortalVue);
Vue.use(Notifications);
Vue.use(VueIcon, 'v-icon');
Vue.directive('tippy', vTippy);
Vue.directive('mask', vMask);

Vue.component('main-page', require('./components/MainPage.vue').default);
Vue.component('login-page', require('./components/Pages/LoginPage.vue').default);
Vue.component('error-page', require('./components/Pages/ErrorPage.vue').default);
Vue.component('task-page', require('./components/Pages/TaskPage.vue').default);
Vue.component('projects-page', require('./components/Pages/ProjectsPage.vue').default);
Vue.component('project-page', require('./components/Pages/ProjectPage.vue').default);
Vue.component('subdivisions-page', require('./components/Pages/SubdivisionsPage.vue').default);
Vue.component('subdivision-page', require('./components/Pages/SubdivisionPage.vue').default);
Vue.component('app', require('./components/App.vue').default);

Vue.component('modal', require('./components/Modal.vue').default);
Vue.component('main-loader', require('./components/MainLoader.vue').default);
Vue.component('pagination', require('./components/Pagination.vue').default);
Vue.component('task-list', require('./components/TaskList.vue').default);

Vue.config.devtools = true;

import App from './components/App';
import store from './store';
import Api from './api';

const app = new Vue({
    el: '#app',
    store: store,
    api: Api,
    components: { App },
});