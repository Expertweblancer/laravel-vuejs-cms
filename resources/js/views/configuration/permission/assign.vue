<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('permission.assign_permission')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
                <button class="btn btn-info btn-sm pull-right m-r-10" @click="$router.push('/configuration/permission')"><i class="fas fa-key"></i> <span class="d-none d-sm-inline">{{trans('permission.permission')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive m-b-20">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{trans('permission.permission')}}</th>
                                            <th v-for="role in roles" class="text-center">{{toWord(role.name)}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="permission in permissions">
                                            <td>{{toWord(permission.name)}}</td>
                                            <td v-for="role in roles" class="text-center">
                                                <switches v-model="assignPermissionForm.data[role.id][permission.id]" theme="bootstrap" color="success"></switches>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-info waves-effect waves-light m-l-20" @click="savePermission">{{trans('general.save')}}</button>
                            <router-link to="/configuration/permission" class="btn btn-sm btn-danger">{{trans('general.back')}}</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import switches from 'vue-switches'

    export default {
        components: {switches},
        data() {
            return {
                roles: [],
                permissions: [],
                assignPermissionForm: new Form({
                    data: {}
                })
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            axios.get('/api/permission/assign/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.permissions = response.permissions;
                    this.roles = response.roles;
                    this.assignPermissionForm.data = response.data;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods:{
            savePermission(){
                axios.post('/api/permission/assign',{
                    data: this.assignPermissionForm.data
                })
                .then(response => response.data)
                .then(response => {
                    toastr.success(response.message);
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
            },
            toWord(str){
                return helper.toWord(str);
            }
        }
    }
</script>
