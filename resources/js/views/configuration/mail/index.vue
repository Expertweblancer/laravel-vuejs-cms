<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('mail.mail_configuration')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <show-tip module="mail" tip="tip_mail_configuration"></show-tip>
                            <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                <h4 class="card-title">{{trans('mail.mail')}}</h4>
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <label for="">{{trans('mail.driver')}}</label>
                                            <select v-model="configForm.driver" class="custom-select col-12">
                                              <option v-for="option in mail_drivers" v-bind:value="option.value">
                                                {{ option.text }}
                                              </option>
                                            </select>
                                            <show-error :form-name="configForm" prop-name="driver"></show-error>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{trans('mail.from_name')}}</label>
                                            <input class="form-control" type="text" value="" v-model="configForm.from_name" name="from_name" :placeholder="trans('mail.from_name')">
                                            <show-error :form-name="configForm" prop-name="from_name"></show-error>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{trans('mail.from_address')}}</label>
                                            <input class="form-control" type="text" value="" v-model="configForm.from_address" name="from_address" :placeholder="trans('mail.from_address')">
                                            <show-error :form-name="configForm" prop-name="from_address"></show-error>
                                        </div>
                                        <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">{{trans('general.save')}}</button>
                                    </div>
                                    <div class="col-12 col-sm-8">
                                        <div v-if="configForm.driver === 'mailgun'">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.domain')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.mailgun_domain" name="mailgun_domain" :placeholder="trans('mail.domain')">
                                                        <show-error :form-name="configForm" prop-name="mailgun_domain"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.secret')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.mailgun_secret" name="mailgun_secret" :placeholder="trans('mail.secret')">
                                                        <show-error :form-name="configForm" prop-name="mailgun_secret"></show-error>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="configForm.driver === 'mandrill'">
                                            <div class="form-group">
                                                <label for="">{{trans('mail.secret')}}</label>
                                                <input class="form-control" type="text" value="" v-model="configForm.mandrill_secret" name="mandrill_secret" :placeholder="trans('mail.secret')">
                                                <show-error :form-name="configForm" prop-name="mandrill_secret"></show-error>
                                            </div>
                                        </div>
                                        <div v-if="configForm.driver === 'smtp'">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.host')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.smtp_host" name="smtp_host" :placeholder="trans('mail.host')">
                                                        <show-error :form-name="configForm" prop-name="smtp_host"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.port')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.smtp_port" name="smtp_port" :placeholder="trans('mail.port')">
                                                        <show-error :form-name="configForm" prop-name="smtp_port"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.username')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.smtp_username" name="smtp_username" :placeholder="trans('mail.username')">
                                                        <show-error :form-name="configForm" prop-name="smtp_username"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.password')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.smtp_password" name="smtp_password" :placeholder="trans('mail.password')">
                                                        <show-error :form-name="configForm" prop-name="smtp_password"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.encryption')}} <show-tip type="field" module="mail" tip="tip_encryption"></show-tip> </label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.smtp_encryption" name="smtp_encryption" :placeholder="trans('mail.encryption')">
                                                        <show-error :form-name="configForm" prop-name="smtp_encryption"></show-error>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="configForm.driver === 'mailgun'">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.host')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.mailgun_host" name="mailgun_host" :placeholder="trans('mail.host')">
                                                        <show-error :form-name="configForm" prop-name="mailgun_host"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.port')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.mailgun_port" name="mailgun_port" :placeholder="trans('mail.port')">
                                                        <show-error :form-name="configForm" prop-name="mailgun_port"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.username')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.mailgun_username" name="mailgun_username" :placeholder="trans('mail.username')">
                                                        <show-error :form-name="configForm" prop-name="mailgun_username"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.password')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.mailgun_password" name="mailgun_password" :placeholder="trans('mail.password')">
                                                        <show-error :form-name="configForm" prop-name="mailgun_password"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('mail.encryption')}} <show-tip type="field" module="mail" tip="tip_encryption"></show-tip> </label>
                                                        <input class="form-control" type="text" value="" v-model="configForm.mailgun_encryption" name="mailgun_encryption" :placeholder="trans('mail.encryption')">
                                                        <show-error :form-name="configForm" prop-name="mailgun_encryption"></show-error>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        components : { },
        data() {
            return {
                configForm: new Form({
                    driver : '',
                    from_name: '',
                    from_address: '',
                    smtp_host: '',
                    smtp_port: '',
                    smtp_username: '',
                    smtp_password: '',
                    smtp_encryption: '',
                    mailgun_host: '',
                    mailgun_port: '',
                    mailgun_username: '',
                    mailgun_password: '',
                    mailgun_encryption: '',
                    mailgun_domain: '',
                    mailgun_secret: '',
                    mandrill_secret: '',
                    config_type: ''
                }, false),
                mail_drivers: []
            };
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            axios.get('/api/configuration/variable?type=mail')
                .then(response => response.data)
                .then(response => {
                    this.mail_drivers = response.mail_drivers;
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
                this.configForm.config_type = 'mail';
                this.configForm.post('/api/configuration')
                    .then(response => {
                        this.$store.dispatch('setConfig',this.configForm);
                        toastr.success(response.message);
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
