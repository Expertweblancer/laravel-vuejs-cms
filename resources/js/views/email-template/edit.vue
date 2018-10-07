<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('template.edit_template')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/email-template')"><i class="far fa-envelope"></i> <span class="d-none d-sm-inline">{{trans('template.template')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <form @submit.prevent="submit" @keydown="templateForm.errors.clear($event.target.name)">
                                <div class="form-group">
                                    <label for="">{{trans('template.subject')}}</label>
                                    <input class="form-control" type="text" value="" v-model="templateForm.subject" name="subject" :placeholder="trans('template.subject')">
                                    <show-error :form-name="templateForm" prop-name="subject"></show-error>
                                </div>
                                <div class="form-group">
                                    <html-editor name="body" :model.sync="templateForm.body" isUpdate="true" @clearErrors="templateForm.errors.clear('body')"></html-editor>
                                    <show-error :form-name="templateForm" prop-name="body"></show-error>
                                </div>
                                <div class="help-block">{{trans('template.available_fields')}}: {{fields}}</div>
                                <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">{{trans('general.save')}}</button>
                                <button type="button" class="btn btn-danger waves-effect waves-light pull-right m-t-10 m-r-10" @click="$router.push('/email-template')">{{trans('general.cancel')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import htmlEditor from '../../components/html-editor'

    export default {
        components : { htmlEditor },
        data() {
            return {
                id:this.$route.params.id,
                templateForm: new Form({
                    subject: '',
                    body: '',
                }),
                fields: ''
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('email_template')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            axios.get('/api/email-template/'+this.id)
                .then(response => response.data)
                .then(response => {
                    this.templateForm.subject = response.email_template.subject;
                    this.templateForm.body = response.email_template.body;
                    this.fields = response.fields;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    this.$router.push('/email-template');
                })
        },
        methods: {
            submit(){
                this.templateForm.patch('/api/email-template/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/email-template');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
