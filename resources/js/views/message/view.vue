<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{message.subject}} 
                <span class="card-subtitle" v-if="message.user_from">{{message.user_from.email}}</span>
                
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/message')"><i class="fas fa-envelope"></i> <span class="d-none d-sm-inline">{{trans('message.message')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <message-sidebar menu="" :statistics="statistics"></message-sidebar>
                                <div class="col-10 col-sm-10">
                                    <img :src="getAvatar(message.user_from)" alt="user" class="message-avatar pull-left m-r-10" />
                                    <span class="pull-right"><i class="far fa-clock"></i> {{message.created_at | momentDateTime}}</span>

                                    <h4 class="card-title">
                                        <i class="fas fa-star fa-lg starred custom-button" @click="toggleImportant(message)" v-if="isImportant(message)" v-tooltip="trans('message.important')"></i>
                                        <i class="far fa-star fa-lg custom-button" @click="toggleImportant(message)" v-else v-tooltip="trans('message.mark_important')"></i>
                                        {{message.subject}}
                                    </h4>

                                    <button class="btn btn-danger btn-sm pull-right" :key="message.id" v-confirm="{ok: confirmDelete(message)}"><i class="fas fa-trash"></i> </button>
                                    <p class="text-strong" v-if="message">{{message.user_from.email+' '+trans('message.to')+' '+message.user_to.email}} </p>

                                    <div v-html="message.body"></div>
                                    <div v-if="message.has_attachment">
                                        <ul style="list-style: none;padding: 0;" class="m-t-10">
                                            <li v-for="attachment in attachments">
                                                <a :href="`/message/${message.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                                            </li>
                                        </ul>
                                    </div>

                                    <hr />

                                    <div class="m-l-40">
                                        <div v-for="reply in replies" class="m-t-20">
                                            <img :src="getAvatar(reply.user_from)" alt="user" class="message-avatar pull-left m-r-10" />
                                            <span class="pull-right"><i class="far fa-clock"></i> {{reply.created_at | momentDateTime}}</span>
                                            <h4 class="card-title">
                                                <i class="fas fa-star fa-lg starred custom-button" @click="toggleImportant(reply)" v-if="isImportant(reply)" v-tooltip="trans('message.important')"></i>
                                                <i class="far fa-star fa-lg custom-button" @click="toggleImportant(reply)" v-else v-tooltip="trans('message.mark_important')"></i>
                                                {{reply.subject}}
                                            </h4>

                                            <div>
                                                <button class="btn btn-danger btn-sm pull-right" :key="reply.id" v-confirm="{ok: confirmDelete(reply)}"><i class="fas fa-trash"></i> </button>
                                                <p class="text-strong" v-if="reply">{{reply.user_from.email+' '+trans('message.to')+' '+reply.user_to.email}} </p>
                                            </div>
                                            <div v-html="reply.body"></div>
                                            <div v-if="reply.has_attachment">
                                                <ul style="list-style: none;padding: 0;" class="m-t-10">
                                                    <li v-for="attachment in reply_attachments[reply.id]">
                                                        <a :href="`/message/${reply.uuid}/attachment/${attachment.uuid}/download?token=${authToken}`"><i class="fas fa-paperclip"></i> {{attachment.user_filename}}</a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <hr v-if="!$last(reply, replies)" />
                                        </div>
                                    </div>

                                    <h4 class="card-title m-t-40">{{trans('message.reply')}}</h4>

                                    <form @submit.prevent="submit" @keydown="replyForm.errors.clear($event.target.name)">
                                        <div class="form-group">
                                            <html-editor name="body" :model.sync="replyForm.body" :isUpdate="false" @clearErrors="replyForm.errors.clear('body')"></html-editor>
                                            <show-error :form-name="replyForm" prop-name="body"></show-error>
                                        </div>
                                        <div class="form-group">
                                            <file-upload-input :button-text="trans('message.attachment')" :token="replyForm.upload_token" module="message" :clear-file="clear_message_attachment" module-id=""></file-upload-input>
                                        </div>
                                        <div class="form-group pull-right">
                                            <button type="submit" class="btn btn-success waves-effect waves-light"><i class="fas fa-paper-plane"></i> {{trans('message.send')}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import messageSidebar from './message-sidebar'
    import uuid from 'uuid/v4'
    import htmlEditor from '../../components/html-editor'
    import fileUploadInput from '../../components/file-upload-input'

    export default {
        components : { messageSidebar,htmlEditor,fileUploadInput },
        data() {
            return {
                uuid:this.$route.params.uuid,
                message: '',
                attachments: '',
                reply_attachments: '',
                auth_user_id: '',
                statistics: {
                    sent: 0,
                    inbox: 0,
                    draft: 0,
                    trash: 0
                },
                replyForm: new Form({
                    uuid: '',
                    body: '',
                    upload_token: ''
                }),
                clear_message_attachment: false,
                replies: ''
            };
        },
        mounted(){
            if(!helper.hasPermission('access-message')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('message')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            this.getStatistics();
            this.getAuthUserId();
            
            axios.get('/api/message/'+this.uuid)
                .then(response => response.data)
                .then(response => {
                    this.message = response.message;
                    this.attachments = response.attachments;
                    this.replyForm.uuid = response.message.uuid;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    this.$router.push('/message/inbox');
                });
            this.getReplies();
            this.replyForm.upload_token = uuid();
        },
        methods: {
            getStatistics(){
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
            },
            getReplies(){
                axios.get('/api/message/'+this.uuid+'/reply')
                    .then(response => response.data)
                    .then(response => {
                        this.replies = response.replies;
                        this.reply_attachments = response.reply_attachments;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            isImportant(message){
                if((message.to_user_id === this.auth_user_id && message.is_important_by_receiver) || (message.from_user_id === this.auth_user_id && message.is_important_by_sender))
                    return true;
                else
                    return false;
            },
            toggleImportant(message){
                axios.post('/api/message/'+message.uuid+'/important')
                    .then(response => response.data)
                    .then(response => {
                        if(this.auth_user_id == this.message.to_user_id)
                            message.is_important_by_receiver = !message.is_important_by_receiver;
                        else
                            message.is_important_by_sender = !message.is_important_by_sender;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            submit(){
                this.replyForm.post('/api/message/reply')
                    .then(response => {
                        toastr.success(response.message);
                        this.clear_message_attachment = true;
                        this.getReplies();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            confirmDelete(message){
                return dialog => this.deleteMessage(message);
            },
            deleteMessage(message){
                axios.post('/api/message/'+message.uuid+'/trash')
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        if(message.uuid === this.uuid)
                            this.$router.push('/message/inbox');
                        else
                            this.getReplies();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getAvatar(user){
                return helper.getAvatar(user);
            },
            getAuthUserId(){
                this.auth_user_id = helper.getAuthUser('id');
            }
        },
        filters: {
          momentDateTime(date) {
            return helper.formatDateTime(date);
          }
        },
        computed: {
            authToken(){
                return localStorage.getItem('auth_token');
            }
        }
    }
</script>
