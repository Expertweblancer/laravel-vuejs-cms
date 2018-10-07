<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('general.logo')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <h4 class="card-title">{{trans('general.logo_type',{type:trans('general.sidebar')})}}</h4>
                                    <upload-image id="sidebar_logo" upload-path="/configuration/logo/sidebar" remove-path="/configuration/logo/sidebar/remove" :image-source="logo.sidebar" @uploaded="updateSidebarLogo" @removed="updateSidebarLogo"></upload-image>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <h4 class="card-title">{{trans('general.logo_type',{type:trans('general.main')})}}</h4>
                                    <upload-image id="logo" upload-path="/configuration/logo/main" remove-path="/configuration/logo/main/remove" :image-source="logo.main" @uploaded="updateMainLogo" @removed="updateMainLogo"></upload-image>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import uploadImage from '../../../components/upload-image'

    export default {
        components : { uploadImage },
        data() {
            return {
                logo: {
                    main: '',
                    sidebar: ''
                }
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.logo.main = helper.getConfig('main_logo');
            this.logo.sidebar = helper.getConfig('sidebar_logo');
        },
        methods: {
            updateMainLogo(val){
                this.$store.dispatch('setConfig',{
                    main_logo: val
                });
            },
            updateSidebarLogo(val){
                this.$store.dispatch('setConfig',{
                    sidebar_logo: val
                });
            }
        }
    }
</script>
