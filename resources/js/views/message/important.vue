<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('message.important')}} 
                <span class="card-subtitle" v-if="messages">{{trans('general.total_result_found',{'count' : messages.total})}}</span>
                <span class="card-subtitle" v-else>{{trans('general.no_result_found')}}</span>
                
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/message')"><i class="fas fa-envelope"></i> <span class="d-none d-sm-inline">{{trans('message.message')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <message-sidebar menu="important" :statistics="statistics"></message-sidebar>
                                <div class="col-10 col-sm-10">
                                    <div class="table-responsive">
                                        <table class="table" v-if="messages.total">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>{{trans('message.sender')}}</th>
                                                    <th>{{trans('message.recipient')}}</th>
                                                    <th>{{trans('message.subject')}}</th>
                                                    <th></th>
                                                    <th>{{trans('message.received_at')}}</th>
                                                    <th class="table-option">{{trans('general.action')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="important in messages.data">
                                                    <td>
                                                        <i class="fas fa-star fa-lg starred custom-button" @click="toggleImportant(important)" v-tooltip="trans('message.important')"></i>
                                                    </td>
                                                    <td>
                                                        <span>
                                                            {{important.user_from.email}}
                                                        </span>
                                                    </td>
                                                    <td v-text="important.user_to.email"></td>
                                                    <td v-text="important.subject"></td>
                                                    <td><i class="fas fa-paperclip" v-if="important.has_attachment"></i></td>
                                                    <td>{{ important.created_at | momentDateTime }}</td>
                                                    <td class="table-option">
                                                        <div class="btn-group">
                                                            <router-link :to="`/message/${important.uuid}`" class="btn btn-info btn-sm" v-tooltip="trans('message.view')"><i class="fas fa-arrow-circle-right"></i></router-link>
                                                            <button class="btn btn-danger btn-sm" :key="important.id" v-confirm="{ok: confirmDelete(important)}" v-tooltip="trans('message.delete',{type: trans('message.important')})"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <module-info v-if="!messages.total" module="message" title="module_info_title_important" description="module_info_description_important" icon="star"></module-info>
                                    <pagination-record :page-length.sync="filterMessageForm.page_length" :records="messages" @updateRecords="getMessages" @change.native="getMessages"></pagination-record>
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

    export default {
        components : { messageSidebar },
        data() {
            return {
                messages: {
                    total: 0,
                    data: []
                },
                filterMessageForm: {
                    page_length: helper.getConfig('page_length')
                },
                statistics: {
                    sent: 0,
                    inbox: 0,
                    draft: 0,
                    trash: 0
                }
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

            this.getMessages();
            this.getStatistics();
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
            getMessages(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterMessageForm);
                axios.get('/api/message/important?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.messages = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(important){
                return dialog => this.deleteMessage(important);
            },
            deleteMessage(important){
                axios.post('/api/message/'+important.uuid+'/trash')
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getStatistics();
                        this.getMessages();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            toggleImportant(important){
                axios.post('/api/message/'+important.uuid+'/important')
                    .then(response => response.data)
                    .then(response => {
                        this.getMessages();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        },
        filters: {
          momentDateTime(date) {
            return helper.formatDateTime(date);
          }
        }
    }
</script>
