<template>
	<aside class="left-sidebar">
        <div class="scroll-sidebar">
            <div class="user-profile">
                <div class="profile-img"> <img :src="getAuthUser('avatar')" alt="user" /> </div>
            </div>
            <nav class="sidebar-nav m-t-20">
                <div class="text-center" v-if="getConfig('maintenance_mode')"><span class="badge badge-danger m-b-10">{{trans('configuration.under_maintenance')}}</span></div>
                <div class="text-center" v-if="!getConfig('mode')"><span class="badge badge-danger m-b-10">{{trans('configuration.test_mode')}}</span></div>
                <main-menu></main-menu>
            </nav>
        </div>
        <div class="sidebar-footer">
            <router-link v-if="hasPermission('access-configuration')" to="/configuration" class="link" v-tooltip="trans('configuration.configuration')"><i class="fas fa-cogs"></i></router-link>
            <router-link to="/profile" class="link" v-tooltip="trans('user.profile')"><i class="fas fa-user"></i></router-link>
            <a href="#" class="link" v-tooltip="trans('auth.logout')" @click.prevent="logout"><i class="fas fa-power-off"></i></a>
        </div>
    </aside>
</template>

<script>
    import mainMenu from './menu'

    export default {
        components: {mainMenu},
        mounted() {
        },
        methods : {
            logout(){
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login');
                })
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            getConfig(config){
                return helper.getConfig(config);
            }
        },
        computed: {
        }
    }
</script>
