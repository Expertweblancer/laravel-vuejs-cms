<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('message.edit_draft')}} 
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/message')"><i class="fas fa-envelope"></i> <span class="d-none d-sm-inline">{{trans('message.message')}}</span></button>
                <button class="btn btn-info btn-sm pull-right m-r-10" @click="$router.push('/message/draft')"><i class="fas fa-edit"></i> <span class="d-none d-sm-inline">{{trans('message.draft')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <message-sidebar menu="draft" :statistics="statistics"></message-sidebar>
                                <div class="col-10 col-sm-10">
                                    <message-form :uuid="uuid"></message-form>
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
                uuid:this.$route.params.uuid,
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
        }
    }
</script>
