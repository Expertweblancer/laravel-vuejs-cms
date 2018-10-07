<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('auth.change_password')}} <span class="card-subtitle">{{getAuthUser('full_name')}} ({{getAuthUser('email')}})</span>
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/profile')"><i class="fas fa-user"></i> <span class="d-none d-sm-inline">{{trans('user.profile')}}</span></button>
            </h3>
        </div>
        <div class="row">
            <div class="col-12 col-sm-8">
                <div class="card">
                    <div class="card-body p-4">
                        <form @submit.prevent="changePassword" @keydown="passwordForm.errors.clear($event.target.name)">
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('auth.current_password')}}</label>
                                        <input class="form-control" type="password" value="" v-model="passwordForm.current_password" name="current_password" :placeholder="trans('auth.current_password')">
                                        <show-error :form-name="passwordForm" prop-name="current_password"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
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
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label for="">{{trans('auth.new_password_confirmation')}}</label>
                                        <input class="form-control" type="password" value="" v-model="passwordForm.new_password_confirmation" name="new_password_confirmation" :placeholder="trans('auth.new_password_confirmation')">
                                        <show-error :form-name="passwordForm" prop-name="new_password_confirmation"></show-error>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">{{trans('auth.change_password')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import password from 'vue-password-strength-meter'

    export default {
        components: {password},
        data() {
            return {
                passwordForm: new Form({
                    current_password : '',
                    new_password: '',
                    new_password_confirmation: ''
                }),
                content: 'change password'
            };
        },
        mounted(){
        },
        methods: {
            changePassword(){
                this.passwordForm.post('/api/change-password')
                    .then(response => {
                        toastr.success(response.message);
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getConfig(config){
                return helper.getConfig(config);
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            }
        }
    }
</script>
