<template>
    <form @submit.prevent="submit" @keydown="messageForm.errors.clear($event.target.name)">
        <div class="form-group">
            <v-select label="name" v-model="selected_user" name="to_user_id" id="to_user_id" :options="users" :placeholder="trans('message.select_recipient')" @select="messageForm.errors.clear('to_user_id')" @close="messageForm.to_user_id = selected_user.id" @remove="messageForm.to_user_id = ''"></v-select>
            <show-error :form-name="messageForm" prop-name="to_user_id"></show-error>
        </div>
        <div class="form-group">
            <input class="form-control message-input" type="text" value="" v-model="messageForm.subject" name="subject" :placeholder="trans('message.subject')">
            <show-error :form-name="messageForm" prop-name="subject"></show-error>
        </div>
        <div class="form-group">
            <html-editor name="body" :model.sync="messageForm.body" :isUpdate="uuid ? true : false" @clearErrors="messageForm.errors.clear('body')"></html-editor>
            <show-error :form-name="messageForm" prop-name="body"></show-error>
        </div>
        <div class="form-group">
            <file-upload-input :button-text="trans('message.attachment')" :token="messageForm.upload_token" module="message" :clear-file="clear_message_attachment" :module-id="messageForm.id"></file-upload-input>
        </div>
        <div class="form-group pull-right">
            <button v-if="messageForm.id" type="button" class="btn btn-danger waves-effect waves-light" v-confirm="{ok: confirmDelete(messageForm.uuid)}" v-tooltip="trans('message.delete_draft')"><i class="fas fa-trash"></i></button>
            <button type="button" @click="saveAsDraft" class="btn btn-info waves-effect waves-light" v-tooltip="trans('message.save_as_draft')"><i class="fas fa-edit"></i> {{trans('message.save_as_draft')}}</button>
            <router-link to="/message/draft" v-if="messageForm.id" class="btn btn-warning waves-effect waves-light" v-tooltip="trans('message.cancel')"><i class="fas fa-times"></i> {{trans('message.cancel')}}</router-link>
            <button type="submit" class="btn btn-success waves-effect waves-light" v-tooltip="trans('message.send')"><i class="fas fa-paper-plane"></i> {{trans('message.send')}}</button>
        </div>
    </form>
</template>


<script>
    import uuid from 'uuid/v4'
    import htmlEditor from '../../components/html-editor'
    import fileUploadInput from '../../components/file-upload-input'
    import vSelect from 'vue-multiselect'

    export default {
        components : { htmlEditor,fileUploadInput,vSelect },
        props: ['uuid'],
        data() {
            return {
                messageForm: new Form({
                    id: '',
                    to_user_id: null,
                    subject: '',
                    body: '',
                    upload_token: '',
                    is_draft: 0
                }),
                statistics: {
                    sent: 0,
                    inbox: 0,
                    draft: 0,
                    trash: 0
                },
                users : [],
                selected_user: null,
                clear_message_attachment: false,
            };
        },
        mounted(){
            if(!this.uuid)
                this.messageForm.upload_token = uuid();
            axios.get('/api/message/compose/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.users = response;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
            axios.post('/api/message/statistics')
                .then(response => response.data)
                .then(response => {
                    this.statistics.inbox = response.inbox;
                    this.statistics.sent = response.sent;
                    this.statistics.draft = response.draft;
                    this.statistics.trash = response.trash;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

            if(this.uuid)
                axios.get('/api/message/'+this.uuid+'/draft')
                    .then(response => response.data)
                    .then(response => {
                        this.messageForm.to_user_id = response.draft.to_user_id;
                        this.messageForm.subject = response.draft.subject;
                        this.messageForm.body = response.draft.body;
                        this.messageForm.upload_token = response.draft.upload_token;
                        this.messageForm.id = response.draft.id;
                        this.selected_user = response.selected_user;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
        },
        methods: {
            submit(){
                this.messageForm.is_draft = 0;
                this.messageForm.post('/api/message/compose')
                    .then(response => {
                        toastr.success(response.message);
                        this.selected_user = null;
                        this.clear_message_attachment = true;
                        this.$router.push('/message/sent');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            saveAsDraft(){
                this.messageForm.is_draft = 1;
                this.messageForm.post('/api/message/compose')
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/message/draft');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            confirmDelete(uuid){
                return dialog => this.deleteDraft(uuid);
            },
            deleteDraft(uuid){
                axios.delete('/api/message/'+uuid+'/draft')
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/message/draft');
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        }
    }
</script>
