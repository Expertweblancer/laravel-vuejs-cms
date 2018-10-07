<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('role.role')}}
                <span class="card-subtitle" v-if="roles">{{trans('general.total_result_found',{'count' : roles.total})}}</span>
                <span class="card-subtitle" v-else>{{trans('general.no_result_found')}}</span>
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
                <button class="btn btn-info btn-sm pull-right m-r-10" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> <span class="d-none d-sm-inline">{{trans('role.add_new_role')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <transition name="fade">
                        <div class="card border-bottom" v-if="showCreatePanel">
                            <div class="card-body p-4">
                                <h4 class="card-title">{{trans('role.add_new_role')}}</h4>
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <role-form @completed="getRoles" @cancel="showCreatePanel = !showCreatePanel"></role-form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" v-if="roles.total">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('role.name')}}</th>
                                            <th>{{trans('general.created_at')}}</th>
                                            <th class="table-option">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="role in roles.data">
                                            <td v-text="toWord(role.name)"></td>
                                            <td>{{role.created_at | momentDateTime}}</td>
                                            <td class="table-option">
                                                <div class="btn-group">
                                                    <button class="btn btn-danger btn-sm" :key="role.id" v-confirm="{ok: confirmDelete(role)}" v-tooltip="trans('role.delete_role')"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <module-info v-if="!roles.total" module="role" title="module_info_title" description="module_info_description" icon="key"></module-info>
                            <pagination-record :page-length.sync="filterRoleForm.page_length" :records="roles" @updateRecords="getRoles" @change.native="getRoles"></pagination-record>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import roleForm from './form'

    export default {
        components : { roleForm },
        data() {
            return {
                roles: {
                    total: 0,
                    data: []
                },
                filterRoleForm: {
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
            this.getRoles();
        },
        methods: {
            getRoles(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterRoleForm);
                axios.get('/api/role?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.roles = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(role){
                return dialog => this.deleteRole(role);
            },
            deleteRole(role){
                axios.delete('/api/role/'+role.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getRoles();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            toWord(word){
                return helper.toWord(word);
            }
        },
        filters: {
          momentDatetime(date) {
            return helper.formatDateTime(date);
          }
        }
    }
</script>
