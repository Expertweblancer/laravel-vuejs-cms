<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('permission.permission')}}
                <span class="card-subtitle" v-if="permissions">{{trans('general.total_result_found',{'count' : permissions.total})}}</span>
                <span class="card-subtitle" v-else>{{trans('general.no_result_found')}}</span>
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
                <button class="btn btn-info btn-sm pull-right m-r-10" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> <span class="d-none d-sm-inline">{{trans('permission.add_new_permission')}}</span></button>
                <button class="btn btn-info btn-sm pull-right m-r-10" @click="$router.push('/configuration/permission/assign')"><i class="fas fa-user-plus"></i> <span class="d-none d-sm-inline">{{trans('permission.assign_permission')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <transition name="fade">
                        <div class="card border-bottom" v-if="showCreatePanel">
                            <div class="card-body p-4">
                                <h4 class="card-title">{{trans('permission.add_new_permission')}}</h4>
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <permission-form @completed="getPermissions" @cancel="showCreatePanel = !showCreatePanel"></permission-form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" v-if="permissions.total">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('permission.name')}}</th>
                                            <th class="table-option">{{trans('general.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="permission in permissions.data">
                                            <td v-text="toWord(permission.name)"></td>
                                            <td class="table-option">
                                                <div class="btn-group">
                                                    <button class="btn btn-danger btn-sm" :key="permission.id" v-confirm="{ok: confirmDelete(permission)}" v-tooltip="trans('permission.delete_permission')"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <module-info v-if="!permissions.total" module="permission" title="module_info_title" description="module_info_description" icon="key"></module-info>
                            <pagination-record :page-length.sync="filterPermissionForm.page_length" :records="permissions" @updateRecords="getPermissions" @change.native="getPermissions"></pagination-record>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import permissionForm from './form'

    export default {
        components : { permissionForm },
        data() {
            return {
                permissions: {
                    total: 0,
                    data: []
                },
                filterPermissionForm: {
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
            this.getPermissions();
        },
        methods: {
            getPermissions(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterPermissionForm);
                axios.get('/api/permission?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.permissions = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(permission){
                return dialog => this.deletePermission(permission);
            },
            deletePermission(permission){
                axios.delete('/api/permission/'+permission.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getPermissions();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            toWord(str){
                return helper.toWord(str);
            }
        }
    }
</script>
