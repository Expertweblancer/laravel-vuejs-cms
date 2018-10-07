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
                                <user-sidebar menu="password" :id="id"></user-sidebar>
                                <div class="col-9 col-sm-9">
                                    <form @submit.prevent="submit" @keydown="passwordForm.errors.clear($event.target.name)">
                                        <h4 class="card-title">{{trans('auth.change_password')}}</h4>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.new_password')}}</label>
                                                    <div v-if="getConfig('password_strength_meter')">
                                                        <password v-model="passwordForm.new_password" name="new_password" defaultClass="form-control" :placeholder="trans('auth.new_password')" :required="false"></password>
                                                    </div>
                                                    <div v-else>
                                                        <input type="password" name="new_password" class="form-control" :placeholder="trans('auth.new_password')" v-model="passwordForm.new_password">
                                                    </div>
                                                    <show-error :form-name="passwordForm" prop-name="new_password"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.new_password_confirmation')}}</label>
                                                    <input class="form-control" type="password" value="" v-model="passwordForm.new_password_confirmation" name="new_password_confirmation" :placeholder="trans('auth.new_password_confirmation')">
                                                    <show-error :form-name="passwordForm" prop-name="new_password_confirmation"></show-error>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-info waves-effect waves-light m-t-10 pull-right">{{trans('auth.change_password')}}</button>
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
    import password from 'vue-password-strength-meter'

    export default {
        components : { userSidebar,userSummary,password },
        data() {
            return {
                id:this.$route.params.id,
                passwordForm: new Form({
                    new_password: '',
                    new_password_confirmation: ''
                }),
                user: ''
            };
        },
        mounted(){
            if(this.id == helper.getAuthUser('id'))
                this.$router.push('/user');

            if(!helper.hasPermission('force-reset-user-password')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            axios.get('/api/user/'+this.id)
                .then(response => response.data)
                .then(response => {
                    this.user = response.user;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            submit(){
                this.passwordForm.patch('/api/user/'+this.id+'/force-reset-password')
                    .then(response => {
                        toastr.success(response.message);
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getConfig(config){
                return helper.getConfig(config);
            }
        }
    }
</script>
