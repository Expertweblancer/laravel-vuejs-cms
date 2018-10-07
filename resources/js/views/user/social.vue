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
                                <user-sidebar menu="social" :id="id"></user-sidebar>
                                <div class="col-9 col-sm-9">
                                    <h4 class="card-title">{{trans('user.social')}}</h4>
                                    <form @submit.prevent="submit" @keydown="userForm.errors.clear($event.target.name)">
                                        <div class="form-group">
                                            <label for="">{{trans('user.sm_profile',{name: 'Facebook'})}}</label>
                                            <input class="form-control" type="text" value="" v-model="userForm.facebook_profile" name="facebook_profile" :placeholder="trans('user.sm_profile',{name: 'Facebook'})">
                                            <show-error :form-name="userForm" prop-name="facebook_profile"></show-error>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{trans('user.sm_profile',{name: 'Twitter'})}}</label>
                                            <input class="form-control" type="text" value="" v-model="userForm.twitter_profile" name="twitter_profile" :placeholder="trans('user.sm_profile',{name: 'Twitter'})">
                                            <show-error :form-name="userForm" prop-name="twitter_profile"></show-error>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{trans('user.sm_profile',{name: 'LinkedIn'})}}</label>
                                            <input class="form-control" type="text" value="" v-model="userForm.linkedin_profile" name="linkedin_profile" :placeholder="trans('user.sm_profile',{name: 'LinkedIn'})">
                                            <show-error :form-name="userForm" prop-name="linkedin_profile"></show-error>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{trans('user.sm_profile',{name: 'Google Plus'})}}</label>
                                            <input class="form-control" type="text" value="" v-model="userForm.google_plus_profile" name="google_plus_profile" :placeholder="trans('user.sm_profile',{name: 'Google Plus'})">
                                            <show-error :form-name="userForm" prop-name="google_plus_profile"></show-error>
                                        </div>
                                        <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">{{trans('general.save')}}</button>
                                    </form>
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
    import userSummary from './summary'

    export default {
        components : { userSidebar,userSummary },
        data() {
            return {
                id:this.$route.params.id,
                userForm: new Form({
                    facebook_profile: '',
                    twitter_profile: '',
                    linkedin_profile: '',
                    google_plus_profile: ''
                },false),
                user: ''
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
                    this.userForm.facebook_profile = response.user.profile.facebook_profile;
                    this.userForm.twitter_profile = response.user.profile.twitter_profile;
                    this.userForm.linkedin_profile = response.user.profile.linkedin_profile;
                    this.userForm.google_plus_profile = response.user.profile.google_plus_profile;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    this.$router.push('/user');
                })
        },
        methods: {
            submit(){
                this.userForm.patch('/api/user/'+this.id+'/social')
                    .then(response => {
                        toastr.success(response.message);
                        this.user.profile.facebook_profile = this.userForm.facebook_profile;
                        this.user.profile.twitter_profile = this.userForm.twitter_profile;
                        this.user.profile.linkedin_profile = this.userForm.linkedin_profile;
                        this.user.profile.google_plus_profile = this.userForm.google_plus_profile;
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
