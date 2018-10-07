<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('user.user')}} 
                <span class="card-subtitle" v-if="user.profile">{{user.profile.first_name+' '+user.profile.last_name}} ({{user.email}})</span>

                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/user')"><i class="fas fa-list"></i> <span class="d-none d-sm-inline">{{trans('user.user_list')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12 col-sm-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <user-sidebar menu="avatar" :id="id"></user-sidebar>
                                <div class="col-9 col-sm-9">
                                    <h4 class="card-title">{{trans('user.avatar')}}</h4>
                                    <upload-image id="avatar" :upload-path="`/user/profile/avatar/${id}`" :remove-path="`/user/profile/avatar/remove/${id}`" :image-source="avatar.user" @uploaded="updateAvatar" @removed="updateAvatar"></upload-image>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <user-summary :user="user"></user-summary>
            </div>
        </div>
    </div>
</template>


<script>
    import userSidebar from './user-sidebar'
    import uploadImage from '../../components/upload-image'
    import userSummary from './summary'

    export default {
        components : { userSidebar,uploadImage,userSummary },
        data() {
            return {
                id:this.$route.params.id,
                user: '',
                avatar: {
                    user: ''
                }
            };
        },
        mounted(){
            if(!helper.hasPermission('edit-user')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            axios.get('/api/user/'+this.id)
                .then(response => response.data)
                .then(response => {
                    this.user = response.user;
                    this.avatar.user = response.user.profile.avatar;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    this.$router.push('/user');
                })
        },
        methods: {
            updateAvatar(val){
                if(helper.getAuthUser('id') == this.id){
                    this.$store.dispatch('setAuthUserDetail',{
                        avatar: val
                    });
                }
                this.user.profile.avatar = val;
            }
        }
    }
</script>
