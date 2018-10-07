<template>
    <section id="wrapper">
        <div class="login-register" style="background-image:url(/images/background.jpg);">
            <div class="login-box card">
            <div class="card-body p-4">
                <form class="form-horizontal form-material" id="registerform" @submit.prevent="submit" @keydown="registerForm.errors.clear($event.target.name)">
                    <h3 class="box-title m-b-20">{{trans('auth.sign_up')}}</h3>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group ">
                                <input type="text" name="first_name" class="form-control" :placeholder="trans('auth.first_name')" v-model="registerForm.first_name">
                                <show-error :form-name="registerForm" prop-name="first_name"></show-error>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group ">
                                <input type="text" name="last_name" class="form-control" :placeholder="trans('auth.last_name')" v-model="registerForm.last_name">
                                <show-error :form-name="registerForm" prop-name="last_name"></show-error>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <input type="text" name="email" class="form-control" :placeholder="trans('auth.email')" v-model="registerForm.email">
                        <show-error :form-name="registerForm" prop-name="email"></show-error>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <div v-if="getConfig('password_strength_meter')">
                                    <password v-model="registerForm.password" name="password" defaultClass="form-control" :placeholder="trans('auth.password')" :required="false"></password>
                                </div>
                                <div v-else>
                                    <input type="password" name="password" class="form-control" :placeholder="trans('auth.password')" v-model="registerForm.password">
                                </div>
                                <show-error :form-name="registerForm" prop-name="password"></show-error>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <input type="password" name="password_confirmation" class="form-control" :placeholder="trans('auth.confirm_password')" v-model="registerForm.password_confirmation">
                                <show-error :form-name="registerForm" prop-name="password_confirmation"></show-error>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" v-if="getConfig('terms_and_conditions')">
                        <div class="col-12">
                            <div class="checkbox checkbox-success p-t-0 p-l-10">
                                <input id="checkbox-signup" type="checkbox" v-model="registerForm.tnc" name="tnc">
                                <label for="checkbox-signup"> <a target="_blank" href="/terms-and-conditions">{{trans('auth.accept_tnc')}}</a></label>
                            </div>
                            <show-error :form-name="registerForm" prop-name="tnc"></show-error>
                        </div>
                    </div>
                    <div class="g-recaptcha" v-if="getConfig('recaptcha') && getConfig('register_recaptcha')" :data-sitekey="getConfig('recaptcha_key')"></div>
                    <div class="form-group text-center m-t-20">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">{{trans('auth.register')}}</button>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-12 text-center">
                            <p>{{trans('auth.already_have_account?')}} <router-link to="/login" class="text-info m-l-5"><b>{{trans('auth.sign_in')}}</b></router-link></p>
                        </div>
                    </div>
                </form>
            </div>
            <guest-footer></guest-footer>
          </div>
        </div>

    </section>
</template>

<script>
    import guestFooter from '../../layouts/guest-footer.vue'
    import password from 'vue-password-strength-meter'

    export default {
        data() {
            return {
                registerForm: new Form ({
                    email: '',
                    password: '',
                    password_confirmation: '',
                    first_name: '',
                    last_name: '',
                    tnc: false
                })
            }
        },
        components: {
            guestFooter,password
        },
        mounted(){
            if(!helper.featureAvailable('registration')){
                helper.featureNotAvailableMsg();
                return this.$router.push('/home');
            }
        },
        methods: {
            submit(){
                this.registerForm.post('/api/auth/register')
                    .then(response =>  {
                        toastr.success(response.message);
                        this.$router.push('/login');
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getConfig(config){
                return helper.getConfig(config);
            }
        }
    }
</script>
