<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('backup.backup')}}
                <span class="card-subtitle" v-if="backups">{{trans('general.total_result_found',{'count' : backups.total})}}</span>
                <span class="card-subtitle" v-else>{{trans('general.no_result_found')}}</span>
                <sort-by class="pull-right" :order-by-options="orderByOptions" :sort-by="filterBackupsForm.sort_by" :order="filterBackupsForm.order" @updateSortBy="value => {filterBackupsForm.sort_by = value}"  @updateOrder="value => {filterBackupsForm.order = value}"></sort-by>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card border-bottom">
                        <div class="card-body p-4">
                            <h4 class="card-title">{{trans('backup.generate_backup')}}</h4>
                            <show-tip module="backup" tip="tip_backup"></show-tip>
                            <div class="form-group">
                                <switches class="m-l-20" v-model="backupForm.delete_previous" theme="bootstrap" color="success"></switches> {{trans('backup.delete_previous_backup?')}} <show-tip module="backup" tip="tip_delete_previous_backup" type="field"></show-tip>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-info btn-sm" @click.prevent="createBackup">{{trans('backup.generate_backup')}}</button>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" v-if="backups.total">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('backup.name')}}</th>
                                            <th>{{trans('backup.generated_at')}}</th>
                                            <th class="table-option">{{trans('general.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="backup in backups.data">
                                            <td v-text="backup.file"></td>
                                            <td>{{backup.created_at | momentDateTime }}</td>
                                            <td class="table-option">
                                                <div class="btn-group">
                                                    <a :href="getDownloadLink(backup)" class="btn btn-success btn-sm" v-tooltip="'Download'"><i class="fas fa-download"></i></a>
                                                    <button class="btn btn-danger btn-sm" :key="backup.id" v-confirm="{ok: confirmDelete(backup)}" v-tooltip="'Delete Backup'"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <module-info v-if="!backups.total" module="backup" title="module_info_title" description="module_info_description" icon="database">
                            </module-info>
                            <pagination-record :page-length.sync="filterBackupsForm.page_length" :records="backups" @updateRecords="getBackups" @change.native="getBackups"></pagination-record>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import sortBy from '../../components/sort-by'
    import switches from 'vue-switches'

    export default {
        components : { switches,sortBy },
        data() {
            return {
                backupForm: new Form({
                    delete_previous: false
                }),
                backups: {
                    total: 0,
                    data: []
                },
                filterBackupsForm: {
                    page_length: helper.getConfig('page_length'),
                    sort_by: 'created_at',
                    order: 'desc'
                },
                orderByOptions: [
                    {
                        value: 'created_at',
                        translation: i18n.general.created_at
                    }
                ]
            };
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('backup')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            this.getBackups();
        },
        methods: {
            createBackup(){
                this.backupForm.post('/api/backup')
                    .then(response => {
                        toastr.success(response.message);
                        this.getBackups();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    })
            },
            getBackups(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterBackupsForm);
                axios.get('/api/backup?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.backups = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(backup){
                return dialog => this.deleteBackup(backup);
            },
            deleteBackup(backup){
                axios.delete('/api/backup/'+backup.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getBackups();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getDownloadLink(backup){
                return '/backup/'+backup.id+'/download/?token='+localStorage.getItem('auth_token');
            },
        },
        filters: {
          momentDateTime(date) {
            return helper.formatDateTime(date);
          }
        },
        watch: {
            filterBackupsForm: {
                handler(val){
                    this.getBackups();
                },
                deep: true
            }
        }
    }
</script>
