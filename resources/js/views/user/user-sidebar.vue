<template>
    <div class="col-3 col-sm-3">
        <div class="list-group">
            <router-link :to="`/user/${id}/basic`" :class="[menu === 'basic' ? 'active' : '', 'list-group-item']" v-tooltip="trans('user.basic')"><span class="hidden-sm-up"><i class="fas fa-user"></i></span>
                <span class="hidden-xs-down">
                    {{trans('user.basic')}}
                </span>
            </router-link>
            <router-link :to="`/user/${id}/contact`" :class="[menu === 'contact' ? 'active' : '', 'list-group-item']" v-tooltip="trans('user.contact')"><span class="hidden-sm-up"><i class="fas fa-phone"></i></span>
                <span class="hidden-xs-down">
                    {{trans('user.contact')}}
                </span>
            </router-link>
            <router-link :to="`/user/${id}/avatar`" :class="[menu === 'avatar' ? 'active' : '', 'list-group-item']" v-tooltip="trans('user.avatar')"><span class="hidden-sm-up"><i class="far fa-address-book"></i></span>
                <span class="hidden-xs-down">
                    {{trans('user.avatar')}}
                </span>
            </router-link>
            <router-link :to="`/user/${id}/social`" :class="[menu === 'social' ? 'active' : '', 'list-group-item']" v-tooltip="trans('user.social')"><span class="hidden-sm-up"><i class="fas fa-share-alt"></i></span>
                <span class="hidden-xs-down">
                    {{trans('user.social')}}
                </span>
            </router-link>
            <router-link v-if="id != getAuthUser('id')" :to="`/user/${id}/password`" :class="[menu === 'password' ? 'active' : '', 'list-group-item']" v-tooltip="trans('user.reset_password')"><span class="hidden-sm-up"><i class="fas fa-key"></i></span>
                <span class="hidden-xs-down">
                    {{trans('user.reset_password')}}
                </span>
            </router-link>
            <router-link v-if="getConfig('email_template')" :to="`/user/${id}/email`" :class="[menu === 'email' ? 'active' : '', 'list-group-item']" v-tooltip="trans('user.email')"><span class="hidden-sm-up"><i class="fas fa-envelope"></i></span>
                <span class="hidden-xs-down">
                    {{trans('user.email')}}
                </span>
            </router-link>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            menu: {
                default: ''
            },
            id: {
                required: true
            }
        },
        mounted(){
        },
        methods:{
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            getConfig(config){
                return helper.getConfig(config);
            }
        }
    }
</script>
