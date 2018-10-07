import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex);
import createPersistedState from 'vuex-persistedstate'
import * as Cookies from 'js-cookie'

const store = new Vuex.Store({
    state: {
        auth: {                 // to store auth user data
            id: '',
            first_name: '',
            last_name: '',
            email: '',
            avatar: '',
            roles: [],
            color_theme: '',
            direction: '',
            locale: '',
            sidebar: ''
        },
        is_auth: false,         // to store auth status
        two_factor_code: null,  // to store two factor code if required
        config: {},             // to store all configuration variables
        permissions: [],        // to store all permissions of authenticated user
        last_activity: null,    // to store last activity time of user
        default_role: {
            admin: '',
            user: ''
        }
    },
    mutations: {
        setAuthStatus (state) {
            state.is_auth = true;
        },
        setAuthUserDetail (state, auth) {
            for (let key of Object.keys(auth)) {
                state.auth[key] = auth[key] !== null ? auth[key] : '';
            }
            if ('avatar' in auth)
                state.auth.avatar = auth.avatar !== null ? auth.avatar : '';
            state.is_auth = true;
            state.auth.roles = auth.roles;
        },
        resetAuthUserDetail (state) {
            for (let key of Object.keys(state.auth)) {
                state.auth[key] = '';
            }
            state.is_auth = false;
            state.auth.roles = [];
            state.last_activity = null;
            localStorage.removeItem('auth_token');
            axios.defaults.headers.common['Authorization'] = null;
        },
        setConfig (state, config) {
            for (let key of Object.keys(config)) {
                state.config[key] = config[key];
            }
        },
        resetConfig (state) {
            for (let key of Object.keys(state.config)) {
                state.config[key] = '';
            }
        },
        setPermission (state, data) {
            state.permissions = [];
            data.forEach( permission => state.permissions.push(permission.name));
        },
        setTwoFactorCode(state, data) {
            state.two_factor_code = data;
        },
        resetTwoFactorCode(state) {
            state.two_factor_code = null;
        },
        setLastActivity(state) {
            state.last_activity = moment().format();
        },
        setDefaultRole(state, data) {
            state.default_role = data;
        }
    },
    actions: {
        setAuthStatus ({ commit }) {
            commit('setAuthStatus');
        },
        setAuthUserDetail ({ commit }, auth) {
            commit('setAuthUserDetail',auth);
        },
        resetAuthUserDetail ({commit}){
            commit('resetAuthUserDetail');
        },
        setConfig ({ commit }, data) {
            commit('setConfig',data);
        },
        setPermission({ commit }, data) {
            commit('setPermission',data);
        },
        resetConfig({ commit }) {
            commit('resetConfig',data);
        },
        setTwoFactorCode({ commit}, data) {
            commit('setTwoFactorCode',data);
        },
        resetTwoFactorCode({ commit }) {
            commit('resetTwoFactorCode');
        },
        setLastActivity({ commit }) {
            commit('setLastActivity');
        },
        setDefaultRole({ commit }, data) {
            commit('setDefaultRole',data)
        }
    },
    getters: {
        getAuthUser: (state) => (name) => {
            return state.auth[name];
        },
        getAuthUserFullName: (state) => {
            return state.auth['first_name']+' '+state.auth['last_name'];
        },
        getAuthStatus: (state) => {
            return state.is_auth;
        },
        hasRole: (state) => (name) => {
            return (state.auth.roles.indexOf(name) >= 0) ? true : false
        },
        getConfig: (state) => (name) => {
            return state.config[name];
        },
        hasPermission: (state) => (name) => {
            return (state.permissions.indexOf(name) > -1) ? true : false;
        },
        getTwoFactorCode: (state) => {
            return state.two_factor_code;
        },
        getLastActivity: (state) => {
            return state.last_activity;
        },
        getDefaultRole: (state) => (name) => {
            return state.default_role[name];
        }
    },
    plugins: [
        createPersistedState({ storage: window.sessionStorage })
    ]
});

export default store;
