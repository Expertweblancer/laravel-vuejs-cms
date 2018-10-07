<template>
    <header class="topbar">
        <nav class="navbar top-navbar navbar-expand-md navbar-light">
            <div class="navbar-header">
                <router-link class="navbar-brand" to="/">
                    <b>
                        <img :src="getSidebarLogo" alt="" class="dark-logo" />
                        <img :src="getSidebarLogo" alt="" class="light-logo" />
                    </b>
                    <span>
                     <img :src="getMainLogo" alt="" class="dark-logo" />
                     <img :src="getMainLogo" class="light-logo" alt="" /></span> </router-link>
            </div>
            <div class="navbar-collapse">
                <ul class="navbar-nav mr-auto mt-md-0 ">
                    <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="fas fa-bars"></i></a> </li>
                    <li class="nav-item" v-tooltip.right="trans('general.toggle_sidebar')"> <a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="icon-arrow-left-circle fas"></i></a> </li>
                </ul>
                <ul class="navbar-nav my-lg-0">
                    <li class="nav-item" v-tooltip.bottom="trans('todo.todo')" v-if="getConfig('todo') && hasPermission('access-todo')">
                        <router-link class="nav-link" to="/todo"><i class="far fa-check-circle"></i></router-link>
                    </li>
                    <li class="nav-item" v-tooltip.bottom="trans('message.message')" v-if="getConfig('message') && hasPermission('access-message')">
                        <router-link class="nav-link" to="/message"><i class="fas fa-envelope"></i></router-link>
                    </li>
                    <li class="nav-item" v-tooltip.bottom="trans('configuration.configuration')" v-if="hasPermission('access-configuration')">
                        <router-link class="nav-link" to="/configuration"><i class="fas fa-cogs"></i></router-link>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img :src="getAuthUser('avatar')" alt="user" class="profile-pic" /></a>
                        <div :class="['dropdown-menu', getConfig('user_direction') != 'rtl' ? 'dropdown-menu-right' : '']">
                            <ul class="dropdown-user">
                                <li>
                                    <div class="dw-user-box text-center">
                                        <div class="u-img"><img :src="getAuthUser('avatar')" alt="user"></div>
                                        <div class="u-text">
                                            <h4>{{getAuthUser('full_name')}}</h4>
                                            <p class="text-muted">{{getAuthUser('email')}}</p><router-link to="/profile" class="btn btn-rounded btn-danger btn-sm">{{trans('user.view_profile')}}</router-link></div>
                                    </div>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li><router-link to="/change-password"><i class="fas fa-key"></i> {{trans('user.change_password')}}</router-link></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="#" @click.prevent="logout"><i class="fas fa-power-off"></i> {{trans('auth.logout')}}</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</template>

<script>
    export default {
        mounted() {
        },
        methods : {
            logout(){
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login')
                })
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            getConfig(name){
                return helper.getConfig(name);
            },
            hasPermission(permission){
                return helper.hasPermission(permission);
            }
        },
        computed: {
            getMainLogo(){
                if(helper.getConfig('main_logo'))
                    return '/'+helper.getConfig('main_logo');
                else
                    return '/images/default_main_logo.png';
            },
            getSidebarLogo(){
                if(helper.getConfig('sidebar_logo'))
                    return '/'+helper.getConfig('sidebar_logo');
                else
                    return '/images/default_sidebar_logo.png';
            }
        }
    }
</script>
