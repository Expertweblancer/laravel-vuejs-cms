<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('ip_filter.ip_filter')}}
                <span class="card-subtitle" v-if="ip_filters">{{trans('general.total_result_found',{'count' : ip_filters.total})}}</span>
                <span class="card-subtitle" v-else>{{trans('general.no_result_found')}}</span>
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
                <button class="btn btn-info btn-sm pull-right m-r-10" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> <span class="d-none d-sm-inline">{{trans('ip_filter.add_new_ip_filter')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <transition name="fade">
                        <div class="card border-bottom" v-if="showCreatePanel">
                            <div class="card-body p-4">
                                <h4 class="card-title">{{trans('ip_filter.add_new_ip_filter')}}</h4>
                                <div class="row">
                                    <div class="col-12">
                                        <ip-filter-form @completed="getIpFilters" @cancel="showCreatePanel = !showCreatePanel"></ip-filter-form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" v-if="ip_filters.total">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('ip_filter.start_ip')}}</th>
                                            <th>{{trans('ip_filter.end_ip')}}</th>
                                            <th>{{trans('ip_filter.description')}}</th>
                                            <th class="table-option">{{trans('general.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ip_filter in ip_filters.data">
                                            <td v-text="ip_filter.start_ip"></td>
                                            <td v-text="ip_filter.end_ip"></td>
                                            <td v-text="ip_filter.description"></td>
                                            <td class="table-option">
                                                <div class="btn-group">
                                                    <button class="btn btn-info btn-sm" v-tooltip="trans('ip_filter.edit_ip_filter')" @click.prevent="editIpFilter(ip_filter)"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-danger btn-sm" :key="ip_filter.id" v-confirm="{ok: confirmDelete(ip_filter)}" v-tooltip="trans('ip_filter.delete_ip_filter')"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <module-info v-if="!ip_filters.total" module="ip_filter" title="module_info_title" description="module_info_description" icon="ellipsis-v"></module-info>
                            <pagination-record :page-length.sync="filterIpFilterForm.page_length" :records="ip_filters" @updateRecords="getIpFilters" @change.native="getIpFilters"></pagination-record>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import ipFilterForm from './form'

    export default {
        components : { ipFilterForm },
        data() {
            return {
                ip_filters: {
                    total: 0,
                    data: []
                },
                filterIpFilterForm: {
                    page_length: helper.getConfig('page_length')
                },
                showCreatePanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('ip_filter')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            this.getIpFilters();
        },
        methods: {
            getIpFilters(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterIpFilterForm);
                axios.get('/api/ip-filter?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.ip_filters = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editIpFilter(ip_filter){
                this.$router.push('/configuration/ip-filter/'+ip_filter.id+'/edit');
            },
            confirmDelete(ip_filter){
                return dialog => this.deleteIpFilter(ip_filter);
            },
            deleteIpFilter(ip_filter){
                axios.delete('/api/ip-filter/'+ip_filter.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getIpFilters();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        }
    }
</script>
