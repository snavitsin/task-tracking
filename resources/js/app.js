require('./bootstrap');

import VueIcon from 'vue-icon'
import Vue from 'vue';
import PortalVue from 'portal-vue';

Vue.use(PortalVue);
Vue.use(VueIcon, 'v-icon');

Vue.component('modal', require('./components/Modal.vue').default);
Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('task-list', require('./components/TaskList.vue').default);

const app = new Vue({
    el: '#app',
});