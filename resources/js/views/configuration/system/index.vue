<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('configuration.system_configuration')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <h4 class="card-title">{{trans('configuration.system_configuration')}}</h4>
                            <show-tip module="configuration" tip="tip_system_configuration"></show-tip>
                            <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.color_theme')}}</label>
                                                    <select v-model="configForm.color_theme" class="custom-select col-12">
                                                      <option v-for="option in systemConfigVariables.color_themes" v-bind:value="option.value">
                                                        {{ option.text }}
                                                      </option>
                                                    </select>
                                                    <show-error :form-name="configForm" prop-name="color_theme"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.direction')}}</label>
                                                    <select v-model="configForm.direction" class="custom-select col-12">
                                                      <option v-for="option in systemConfigVariables.directions" v-bind:value="option.value">
                                                        {{ option.text }}
                                                      </option>
                                                    </select>
                                                    <show-error :form-name="configForm" prop-name="direction"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.sidebar')}}</label>
                                                    <select v-model="configForm.sidebar" class="custom-select col-12">
                                                      <option v-for="option in systemConfigVariables.sidebar" v-bind:value="option.value">
                                                        {{ option.text }}
                                                      </option>
                                                    </select>
                                                    <show-error :form-name="configForm" prop-name="sidebar"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.date_format')}}</label>
                                                    <select v-model="configForm.date_format" class="custom-select col-12">
                                                      <option v-for="option in systemConfigVariables.date_formats" v-bind:value="option.value">
                                                        {{ option.text }}
                                                      </option>
                                                    </select>
                                                    <show-error :form-name="configForm" prop-name="date_format"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.time_format')}}</label>
                                                    <select v-model="configForm.time_format" class="custom-select col-12">
                                                      <option v-for="option in systemConfigVariables.time_formats" v-bind:value="option.value">
                                                        {{ option.text }}
                                                      </option>
                                                    </select>
                                                    <show-error :form-name="configForm" prop-name="time_format"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.notification_position')}}</label>
                                                    <select v-model="configForm.notification_position" class="custom-select col-12">
                                                      <option v-for="option in systemConfigVariables.notification_positions" v-bind:value="option.value">
                                                        {{ option.text }}
                                                      </option>
                                                    </select>
                                                    <show-error :form-name="configForm" prop-name="notification_position"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6" v-if="getConfig('multilingual')">
                                                <div class="form-group">
                                                    <label for="">{{trans('locale.locale')}}</label>
                                                    <select v-model="configForm.locale" class="custom-select col-12">
                                                      <option v-for="option in systemConfigVariables.locales" v-bind:value="option.value">
                                                        {{ option.text }}
                                                      </option>
                                                    </select>
                                                    <show-error :form-name="configForm" prop-name="locale"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.timezone')}}</label>
                                                    <select v-model="configForm.timezone" class="custom-select col-12">
                                                      <option v-for="option in systemConfigVariables.timezones" v-bind:value="option.value">
                                                        {{ option.text }}
                                                      </option>
                                                    </select>
                                                    <show-error :form-name="configForm" prop-name="timezone"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.page_length')}}</label>
                                                    <select v-model="configForm.page_length" class="custom-select col-12">
                                                      <option v-for="option in getConfig('pagination')" v-bind:value="option">
                                                        {{ option+' '+trans('general.per_page') }}
                                                      </option>
                                                    </select>
                                                    <show-error :form-name="configForm" prop-name="page_length"></show-error>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="">{{trans('configuration.footer_credit')}}</label>
                                            <input class="form-control" type="text" value="" v-model="configForm.footer_credit" name="footer_credit" :placeholder="trans('configuration.footer_credit')">
                                            <show-error :form-name="configForm" prop-name="footer_credit"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.mode')}} <show-tip type="field" module="configuration" tip="tip_mode"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.mode" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.https')}} <show-tip type="field" module="configuration" tip="tip_https"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.https" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.error_display')}} <show-tip type="field" module="configuration" tip="tip_error_log"></show-tip></label>
                                                    <div>
                                                        <switches class="" v-model="configForm.error_display" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.multilingual')}} <show-tip type="field" module="configuration" tip="tip_multilingual"></show-tip> </label>
                                                    <div>
                                                        <switches class="" v-model="configForm.multilingual" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.ip_filter')}} <show-tip type="field" module="configuration" tip="tip_ip_filter"></show-tip> </label>
                                                    <div>
                                                        <switches class="" v-model="configForm.ip_filter" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.activity_log')}}</label>
                                                    <div>
                                                        <switches class="" v-model="configForm.activity_log" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.email_log')}}</label>
                                                    <div>
                                                        <switches class="" v-model="configForm.email_log" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.email_template')}}</label>
                                                    <div>
                                                        <switches class="" v-model="configForm.email_template" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.todo')}}</label>
                                                    <div>
                                                        <switches class="" v-model="configForm.todo" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.message')}}</label>
                                                    <div>
                                                        <switches class="" v-model="configForm.message" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.backup')}}</label>
                                                    <div>
                                                        <switches class="" v-model="configForm.backup" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label for="">{{trans('configuration.maintenance_mode')}} <show-tip type="field" module="configuration" tip="tip_maintenance_mode"></show-tip> </label>
                                                    <div>
                                                        <switches class="" v-model="configForm.maintenance_mode" theme="bootstrap" color="success"></switches>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group" v-if="configForm.maintenance_mode">
                                                    <label for="">{{trans('configuration.maintenance_mode_message')}}</label>
                                                    <autosize-textarea row="1" class="form-control" v-model="configForm.maintenance_mode_message" :placeholder="trans('configuration.maintenance_mode_message')" rows="3" name="maintenance_mode_message"></autosize-textarea>
                                                    <show-error :form-name="configForm" prop-name="maintenance_mode_message"></show-error>
                                                </div>
                                            </div>
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
    import switches from 'vue-switches'
    import autosizeTextarea from '../../../components/autosize-textarea'

    export default {
        components : { switches,autosizeTextarea },
        data() {
            return {
                configForm: new Form({
                    color_theme: '',
                    direction: '',
                    sidebar: '',
                    date_format: '',
                    time_format: '',
                    notification_position: '',
                    timezone: '',
                    page_length: 10,
                    locale: '',
                    footer_credit: '',
                    mode: 0,
                    https: 0,
                    error_display: 0,
                    multilingual: 0,
                    ip_filter: 0,
                    activity_log: 0,
                    email_log: 0,
                    email_template: 0,
                    todo: 0,
                    message: 0,
                    backup: 0,
                    maintenance_mode: 0,
                    maintenance_mode_message: '',
                    config_type: ''
                }, false),
                systemConfigVariables: {
                    color_themes: [],
                    directions: [],
                    sidebar: [],
                    date_formats: [],
                    time_formats: [],
                    notification_positions: [],
                    timezones: [],
                    locales: []
                },
                direction: '',
                locale: '',
                sidebar: ''
            };
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
                    this.direction = response.direction;
                    this.locale = response.locale;
                    this.sidebar = response.sidebar;
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
            axios.get('/api/configuration/variable?type=system')
                .then(response => response.data)
                .then(response => {
                    this.systemConfigVariables.color_themes = response.color_themes;
                    this.systemConfigVariables.directions = response.directions;
                    this.systemConfigVariables.sidebar = response.sidebar;
                    this.systemConfigVariables.date_formats = response.date_formats;
                    this.systemConfigVariables.time_formats = response.time_formats;
                    this.systemConfigVariables.notification_positions = response.notification_positions;
                    this.systemConfigVariables.timezones = response.timezones;
                    this.systemConfigVariables.locales = response.locales;
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            submit(){
                this.configForm.config_type = 'system';
                this.configForm.mode = (this.configForm.mode) ? 1 : 0;
                this.configForm.https = (this.configForm.https) ? 1 : 0;
                this.configForm.error_display = (this.configForm.error_display) ? 1 : 0;
                this.configForm.multilingual = (this.configForm.multilingual) ? 1 : 0;
                this.configForm.ip_filter = (this.configForm.ip_filter) ? 1 : 0;
                this.configForm.activity_log = (this.configForm.activity_log) ? 1 : 0;
                this.configForm.email_log = (this.configForm.email_log) ? 1 : 0;
                this.configForm.email_template = (this.configForm.email_template) ? 1 : 0;
                this.configForm.todo = (this.configForm.todo) ? 1 : 0;
                this.configForm.message = (this.configForm.message) ? 1 : 0;
                this.configForm.backup = (this.configForm.backup) ? 1 : 0;
                this.configForm.maintenance_mode = (this.configForm.maintenance_mode) ? 1 : 0;
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
