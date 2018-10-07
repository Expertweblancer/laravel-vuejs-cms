<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('auth.authentication')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <h4 class="card-title">{{trans('auth.authentication')}}</h4>
                            <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.token_lifetime')+' '+trans('auth.in_minute')}} <show-tip module="auth" tip="tip_token_lifetime" type="field"></show-tip></label>
                                                    <input class="form-control" type="number" value="" v-model="configForm.token_lifetime" name="token_lifetime" :placeholder="trans('auth.token_lifetime')">
                                                    <show-error :form-name="configForm" prop-name="token_lifetime"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for=""><small>{{trans('auth.reset_password_token_lifetime')+' '+trans('auth.in_minute')}}</small> <show-tip module="auth" tip="tip_reset_password_token_lifetime" type="field"></show-tip></label>
                                                    <input class="form-control" type="number" value="" v-model="configForm.reset_password_token_lifetime" name="reset_password_token_lifetime" :placeholder="trans('auth.reset_password_token_lifetime')">
                                                    <show-error :form-name="configForm" prop-name="reset_password_token_lifetime"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.registration')}} <show-tip module="auth" tip="tip_registration" type="field"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.registration" theme="bootstrap" color="success" v-bind:true-value="1" v-bind:false-value="0"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for=""><small>{{trans('auth.password_strength_meter')}}</small> <show-tip module="auth" tip="tip_password_strength_meter" type="field"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.password_strength_meter" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.email_verification')}} <show-tip module="auth" tip="tip_email_verification" type="field"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.email_verification" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.account_approval')}} <show-tip module="auth" tip="tip_account_approval" type="field"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.account_approval" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.terms_and_conditions')}} <show-tip module="auth" tip="tip_terms_and_conditions" type="field"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.terms_and_conditions" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.reset_password')}} <show-tip module="auth" tip="tip_reset_password" type="field"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.reset_password" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.two_factor_security')}} <show-tip module="auth" tip="tip_two_factor_security" type="field"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.two_factor_security" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.lock_screen')}} <show-tip module="auth" tip="tip_lock_screen" type="field"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.lock_screen" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group" v-if="configForm.lock_screen">
                                                    <label for="">{{trans('auth.lock_screen_timeout')+' '+trans('auth.in_minute')}} <show-tip module="auth" tip="tip_lock_screen_timeout" type="field"></show-tip></label>
                                                    <input class="form-control" type="number" value="" v-model="configForm.lock_screen_timeout" name="lock_screen_timeout" :placeholder="trans('auth.lock_screen_timeout')">
                                                    <show-error :form-name="configForm" prop-name="lock_screen_timeout"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.login_throttle')}} <show-tip module="auth" tip="tip_login_throttle" type="field"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.login_throttle" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                            </div>
                                        </div>
                                        <div v-if="configForm.login_throttle">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('auth.login_throttle_attempt')}} <show-tip module="auth" tip="tip_login_throttle_attempt" type="field"></show-tip></label>
                                                        <input class="form-control" type="number" value="" v-model="configForm.login_throttle_attempt" name="login_throttle_attempt" :placeholder="trans('auth.login_throttle_attempt')">
                                                        <show-error :form-name="configForm" prop-name="login_throttle_attempt"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('auth.login_throttle_timeout')+' '+trans('auth.in_minute')}} <show-tip module="auth" tip="tip_login_throttle_timeout" type="field"></show-tip></label>
                                                        <input class="form-control" type="number" value="" v-model="configForm.login_throttle_timeout" name="login_throttle_timeout" :placeholder="trans('auth.login_throttle_timeout')">
                                                        <show-error :form-name="configForm" prop-name="login_throttle_timeout"></show-error>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="">{{trans('auth.social_login')}} <show-tip module="auth" tip="tip_social_login" type="field"></show-tip></label>
                                            <div>
                                                <switches class="" v-model="configForm.social_login" theme="bootstrap" color="success"></switches>
                                            </div>
                                        </div>
                                        <div v-if="configForm.social_login">
                                            <div v-for="provider in configForm.providers">
                                                <div class="form-group">
                                                    <label for="">{{trans('auth.provider_login',{type: provider.provider})}}</label>
                                                    <div>
                                                        <switches class="" v-model="provider.login" :name="provider.provider+'_login'" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="provider.login">
                                                    <div class="col-12 col-sm-4">
                                                        <div class="form-group">
                                                            <label for="">{{trans('auth.provider_client_id',{type: provider.provider})}}</label>
                                                            <input class="form-control" type="text" value="" v-model="provider.client" :name="provider.provider+'_client'" :placeholder="trans('auth.provider_client_id',{type: provider.provider})">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-4">
                                                        <div class="form-group">
                                                            <label for="">{{trans('auth.provider_secret',{type: provider.provider})}}</label>
                                                            <input class="form-control" type="text" value="" v-model="provider.secret" :name="provider.provider+'secret'" :placeholder="trans('auth.provider_secret',{type: provider.provider})">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-4">
                                                        <div class="form-group">
                                                            <label for="">{{trans('auth.provider_redirect_url',{type: provider.provider})}}</label>
                                                            <input class="form-control" type="text" value="" v-model="provider.redirect_url" :name="provider.provider+'redirect_url'" :placeholder="trans('auth.provider_redirect_url',{type: provider.provider})">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info waves-effect waves-light m-t-10 pull-right">{{trans('general.save')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import switches from 'vue-switches'

    export default {
        components : { switches },
        data() {
            return {
                configForm: new Form({
                    config_type: '',
                    token_lifetime: '',
                    reset_password_token_lifetime: '',
                    registration: 0,
                    password_strength_meter: 0,
                    email_verification: 0,
                    account_approval: 0,
                    terms_and_conditions: 0,
                    reset_password: 0,
                    two_factor_security: 0,
                    recaptcha: 0,
                    recaptcha_key: '',
                    recaptcha_secret: '',
                    login_recaptcha: 0,
                    reset_password_recaptcha: 0,
                    register_recaptcha: 0,
                    social_login: 0,
                    lock_screen: 0,
                    lock_screen_timeout: '',
                    login_throttle: 0,
                    login_throttle_attempt: '',
                    login_throttle_timeout: '',
                    providers: []
                },false),
                social_login_providers: []
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            axios.get('/api/configuration/variable?type=social_login')
                .then(response => response.data)
                .then(response => {
                    this.social_login_providers = response.social_login_providers;
                    this.social_login_providers.forEach(provider => {
                        this.configForm.providers.push({
                            provider: provider,
                            login: helper.getConfig(provider+'_login'),
                            client: helper.getConfig(provider+'_client'),
                            secret: helper.getConfig(provider+'_secret'),
                            redirect_url: helper.getConfig(provider+'_redirect_url'),
                        });
                    });
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
            axios.get('/api/configuration')
                .then(response => response.data)
                .then(response => {
                    this.configForm = helper.formAssign(this.configForm, response);
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            submit(){
                this.configForm.config_type = 'authentication';
                this.configForm.registration = (this.configForm.registration) ? 1 : 0;
                this.configForm.password_strength_meter = (this.configForm.password_strength_meter) ? 1 : 0;
                this.configForm.email_verification = (this.configForm.email_verification) ? 1 : 0;
                this.configForm.account_approval = (this.configForm.account_approval) ? 1 : 0;
                this.configForm.terms_and_conditions = (this.configForm.terms_and_conditions) ? 1 : 0;
                this.configForm.reset_password = (this.configForm.reset_password) ? 1 : 0;
                this.configForm.two_factor_security = (this.configForm.two_factor_security) ? 1 : 0;
                this.configForm.recaptcha = (this.configForm.recaptcha) ? 1 : 0;
                this.configForm.login_recaptcha = (this.configForm.login_recaptcha) ? 1 : 0;
                this.configForm.reset_password_recaptcha = (this.configForm.reset_password_recaptcha) ? 1 : 0;
                this.configForm.register_recaptcha = (this.configForm.register_recaptcha) ? 1 : 0;
                this.configForm.social_login = (this.configForm.social_login) ? 1 : 0;
                this.configForm.lock_screen = (this.configForm.lock_screen) ? 1 : 0;
                this.configForm.login_throttle = (this.configForm.login_throttle) ? 1 : 0;
                this.configForm.post('/api/configuration')
                    .then(response => {
                        this.$store.dispatch('setConfig',this.configForm);
                        toastr.success(response.message);
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
