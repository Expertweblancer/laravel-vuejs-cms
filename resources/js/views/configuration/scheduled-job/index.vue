<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('configuration.scheduled_job')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <show-tip module="configuration" tip="tip_scheduled_job"></show-tip>
                            <p>Add below cron command in your server:</p>
                            <blockquote>
                                php /path-to-artisan schedule:run >> /dev/null 2>&1
                            </blockquote>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-stripped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Frequency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Birthday/Anniversary wish to Staff/Customer</td>
                                            <td>Once per day at 09:00 AM</td>
                                        </tr>
                                        <tr>
                                            <td>Daily Backup</td>
                                            <td>Once per day at 01:00 AM</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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

            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
        },
        methods: {
        }
    }
</script>
