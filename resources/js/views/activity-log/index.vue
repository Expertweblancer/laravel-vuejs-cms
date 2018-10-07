<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('activity.activity_log')}}
                <span class="card-subtitle" v-if="activity_logs">{{trans('general.total_result_found',{'count' : activity_logs.total})}}</span>
                <span class="card-subtitle" v-else>{{trans('general.no_result_found')}}</span>

                <sort-by class="pull-right" :order-by-options="orderByOptions" :sort-by="filterActivityLogForm.sort_by" :order="filterActivityLogForm.order" @updateSortBy="value => {filterActivityLogForm.sort_by = value}"  @updateOrder="value => {filterActivityLogForm.order = value}"></sort-by>
                <button class="btn btn-info btn-sm pull-right m-r-10" v-if="!showFilterPanel" @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i> <span class="d-none d-sm-inline">{{trans('general.filter')}}</span></button>
            </h3>
        </div>
        <div class="row">
            <div class="col-12">
                <transition name="fade">
                    <div class="card border-bottom" v-if="showFilterPanel">
                        <div class="card-body p-4">
                            <h4 class="card-title">{{trans('general.filter')}}</h4>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group" v-show="users">
                                        <label for="">{{trans('user.user')}}</label>
                                        <select v-model="filterActivityLogForm.user_id" class="custom-select col-12">
                                          <option value="">{{trans('general.select_one')}}</option>
                                          <option v-for="user in users" v-bind:value="user.id">
                                            {{ user.profile.first_name+' '+user.profile.last_name }}
                                          </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <date-range-picker :start-date.sync="filterActivityLogForm.created_at_start_date" :end-date.sync="filterActivityLogForm.created_at_end_date"></date-range-picker>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-danger btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.cancel')}}</button>
                        </div>
                    </div>
                </transition>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive" v-if="activity_logs.total">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{trans('activity.user')}}</th>
                                        <th>{{trans('activity.ip')}}</th>
                                        <th>{{trans('activity.user_agent')}}</th>
                                        <th>{{trans('activity.activity')}}</th>
                                        <th>{{trans('activity.date_time')}}</th>
                                        <th class="pull-right">{{trans('general.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="activity_log in activity_logs.data">
                                        <td v-text="activity_log.user.profile.first_name+' '+activity_log.user.profile.last_name"></td>
                                        <td v-text="activity_log.ip"></td>
                                        <td v-text="activity_log.user_agent"></td>
                                        <td>{{trans('activity.'+activity_log.activity,{activity: trans('activity.'+activity_log.module)})}}</td>
                                        <td>{{activity_log.created_at | moment }}</td>
                                        <td class="pull-right">
                                            <div class="btn-group">
                                                <button class="btn btn-danger btn-sm" :key="activity_log.id" v-confirm="{ok: confirmDelete(activity_log)}" v-tooltip="trans('general.delete')"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <module-info v-if="!activity_logs.total" module="activity" title="module_info_title" description="module_info_description" icon="list-alt"></module-info>
                        <pagination-record :page-length.sync="filterActivityLogForm.page_length" :records="activity_logs" @updateRecords="getActivityLogs"></pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import dateRangePicker from '../../components/date-range-picker'
    import sortBy from '../../components/sort-by'

    export default {
        components: {dateRangePicker,sortBy},
        data(){
            return {
                activity_logs: {
                    total: 0,
                    data: []
                },
                filterActivityLogForm: {
                    page_length: helper.getConfig('page_length'),
                    user_id: '',
                    created_at_start_date: '',
                    created_at_end_date: '',
                    sort_by: 'created_at',
                    order: 'desc'
                },
                users: [],
                showFilterPanel: false,
                orderByOptions: [
                    {
                        value: 'created_at',
                        translation: i18n.general.created_at
                    }
                ]
            };
        },
        mounted(){
            if(!helper.featureAvailable('activity_log')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            this.getActivityLogs();
        },
        methods: {
            getActivityLogs(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterActivityLogForm);
                axios.get('/api/activity-log?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.users = response.users;
                        this.activity_logs = response.activity_logs;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(activity_log){
                return dialog => this.deleteActivityLog(activity_log);
            },
            deleteActivityLog(activity_log){
                axios.delete('/api/activity-log/'+activity_log.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getActivityLogs();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        filters: {
          moment(date) {
            return helper.formatDateTime(date);
          }
        },
        watch: {
            filterActivityLogForm: {
                handler(val){
                    this.getActivityLogs();
                },
                deep: true
            }
        }
    }
</script>
