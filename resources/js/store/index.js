import Vue from 'vue'
import Vuex from 'vuex'
import Api from '../api';
import { get, set } from 'lodash';

Vue.use(Vuex);

const YANDEX_API_KEY = process.env.YANDEX_API_KEY;

const store = new Vuex.Store({
  state: {
    isLoading: false,
    isLoginSidebarShown: false,
    isPinSidebarShown: false,
    isMenuShown: false,
    user: null,
    settings: {
      apiKey: YANDEX_API_KEY,
      lang: 'ru_RU',
      coordorder: 'latlong',
      enterprise: false,
      version: '2.1'
    }
  },
  getters: {
    isAuth(state) {
      return !!state.user;
    },

    csrf: () => document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

    checkRole(state){
      return role => !!state.user && !!state.user.user_roles ? state.user.user_roles.find(i => i.slug == role) : null;
    },
  },
  mutations: {
    setData(state, {path, value}) {
      return set(state, path, value);
    },
  },
  actions: {
    async fetchData(payload, params) {
      return Api.post(params.url, params.params);
    }
  },
});


export default store;


