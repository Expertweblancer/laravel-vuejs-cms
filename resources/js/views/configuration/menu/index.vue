<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('general.menu')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <show-tip module="configuration" tip="tip_menu"></show-tip>
                            <h4 class="card-title">{{trans('general.menu')}}</h4>
                            <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>{{trans('configuration.show_menu',{menu: trans('user.user')})}}</td>
                                                <td>
                                                    <switches v-model="configForm.show_user_menu" theme="bootstrap" color="success"></switches>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{trans('configuration.show_menu',{menu: trans('todo.todo')})}}</td>
                                                <td>
                                                    <switches v-model="configForm.show_todo_menu" theme="bootstrap" color="success"></switches>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{trans('configuration.show_menu',{menu: trans('message.message')})}}</td>
                                                <td>
                                                    <switches v-model="configForm.show_message_menu" theme="bootstrap" color="success"></switches>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{trans('configuration.show_menu',{menu: trans('configuration.configuration')})}}</td>
                                                <td>
                                                    <switches v-model="configForm.show_configuration_menu" theme="bootstrap" color="success"></switches>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{trans('configuration.show_menu',{menu: trans('backup.backup')})}}</td>
                                                <td>
                                                    <switches v-model="configForm.show_backup_menu" theme="bootstrap" color="success"></switches>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{trans('configuration.show_menu',{menu: trans('template.email_template')})}}</td>
                                                <td>
                                                    <switches v-model="configForm.show_email_template_menu" theme="bootstrap" color="success"></switches>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{trans('configuration.show_menu',{menu: trans('mail.email_log')})}}</td>
                                                <td>
                                                    <switches v-model="configForm.show_email_log_menu" theme="bootstrap" color="success"></switches>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{trans('configuration.show_menu',{menu: trans('activity.activity_log')})}}</td>
                                                <td>
                                                    <switches v-model="configForm.show_activity_log_menu" theme="bootstrap" color="success"></switches>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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

    export default {
        components : { switches },
        data() {
            return {
                configForm: new Form({
                    show_user_menu: 0,
                    show_todo_menu: 0,
                    show_message_menu: 0,
                    show_configuration_menu: 0,
                    show_backup_menu: 0,
                    show_email_template_menu: 0,
                    show_email_log_menu: 0,
                    show_activity_log_menu: 0,
                    config_type: 'menu'
                },false)
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.configForm.show_user_menu = helper.getConfig('show_user_menu');
            this.configForm.show_todo_menu = helper.getConfig('show_todo_menu');
            this.configForm.show_message_menu = helper.getConfig('show_message_menu');
            this.configForm.show_configuration_menu = helper.getConfig('show_configuration_menu');
            this.configForm.show_backup_menu = helper.getConfig('show_backup_menu');
            this.configForm.show_email_template_menu = helper.getConfig('show_email_template_menu');
            this.configForm.show_email_log_menu = helper.getConfig('show_email_log_menu');
            this.configForm.show_activity_log_menu = helper.getConfig('show_activity_log_menu');
        },
        methods: {
            submit(){
                this.configForm.config_type = 'menu';
                this.configForm.show_user_menu = (this.configForm.show_user_menu) ? 1 : 0;
                this.configForm.show_todo_menu = (this.configForm.show_todo_menu) ? 1 : 0;
                this.configForm.show_message_menu = (this.configForm.show_message_menu) ? 1 : 0;
                this.configForm.show_configuration_menu = (this.configForm.show_configuration_menu) ? 1 : 0;
                this.configForm.show_backup_menu = (this.configForm.show_backup_menu) ? 1 : 0;
                this.configForm.show_email_template_menu = (this.configForm.show_email_template_menu) ? 1 : 0;
                this.configForm.show_email_log_menu = (this.configForm.show_email_log_menu) ? 1 : 0;
                this.configForm.show_activity_log_menu = (this.configForm.show_activity_log_menu) ? 1 : 0;
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
