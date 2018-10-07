<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('message.compose')}} 
                
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <message-sidebar menu="compose" :statistics="statistics"></message-sidebar>
                                <div class="col-10 col-sm-10">
                                    <h4 class="card-title">{{trans('message.compose')}}</h4>

                                    <message-form></message-form>
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
    import messageForm from './form';
    import messageSidebar from './message-sidebar'

    export default {
        components : { messageForm,messageSidebar },
        data() {
            return {
                statistics: {
                    sent: 0,
                    inbox: 0,
                    draft: 0,
                    trash: 0
                }
            };
        },
        mounted(){
            if(!helper.featureAvailable('message')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }
            
            if(!helper.hasPermission('access-message')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

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
        methods: {
        }
    }
</script>
