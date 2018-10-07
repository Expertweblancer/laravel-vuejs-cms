<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('sms.sms')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <show-tip module="sms" tip="tip_sms"></show-tip>
                            <h4 class="card-title">{{trans('sms.sms')}}</h4>
                            <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                <div class="row">
                                    <div class="col-12 col-sm-3">
                                        <div class="form-group">
                                            <label for="">{{trans('sms.api_key')}}</label>
                                            <input class="form-control" type="text" value="" v-model="configForm.nexmo_api_key" name="nexmo_api_key" :placeholder="trans('sms.api_key')">
                                            <show-error :form-name="configForm" prop-name="nexmo_api_key"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <div class="form-group">
                                            <label for="">{{trans('sms.api_secret')}}</label>
                                            <input class="form-control" type="text" value="" v-model="configForm.nexmo_api_secret" name="nexmo_api_secret" :placeholder="trans('sms.api_secret')">
                                            <show-error :form-name="configForm" prop-name="nexmo_api_secret"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <div class="form-group">
                                            <label for="">{{trans('sms.sender_mobile')}}</label>
                                            <input class="form-control" type="text" value="" v-model="configForm.nexmo_sender_mobile" name="nexmo_sender_mobile" :placeholder="trans('sms.sender_mobile')">
                                            <show-error :form-name="configForm" prop-name="nexmo_sender_mobile"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <div class="form-group">
                                            <label for="">{{trans('sms.receiver_mobile')}} <show-tip module="sms" tip="tip_receiver_mobile" type="field"></show-tip></label>
                                            <input class="form-control" type="text" value="" v-model="configForm.nexmo_receiver_mobile" name="nexmo_receiver_mobile" :placeholder="trans('sms.receiver_mobile')">
                                            <show-error :form-name="configForm" prop-name="nexmo_receiver_mobile"></show-error>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">{{trans('general.save')}}</button>
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
                    nexmo_api_key: '',
                    nexmo_api_secret: '',
                    nexmo_sender_mobile: '',
                    nexmo_receiver_mobile: '',
                    config_type: ''
                },false)
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

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
                this.configForm.config_type = 'sms';
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
