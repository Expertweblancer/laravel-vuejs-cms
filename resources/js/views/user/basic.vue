<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('user.user')}} 
                <span class="card-subtitle" v-if="user.profile">{{user.profile.first_name+' '+user.profile.last_name}} ({{user.email}})</span>

                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/user')"><i class="fas fa-list"></i> <span class="d-none d-sm-inline">{{trans('user.user_list')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12 col-sm-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <user-sidebar menu="basic" :id="id"></user-sidebar>
                                <div class="col-9 col-sm-9">
                                    <h4 class="card-title">{{trans('user.basic')}}</h4>
                                    <form @submit.prevent="submit" @keydown="userForm.errors.clear($event.target.name)">
                                        <div class="row">
                                            <div class="col-12 col-sm-12">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="">{{trans('user.first_name')}}</label>
                                                            <input class="form-control" type="text" value="" v-model="userForm.first_name" name="first_name" :placeholder="trans('user.first_name')">
                                                            <show-error :form-name="userForm" prop-name="first_name"></show-error>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="">{{trans('user.last_name')}}</label>
                                                            <input class="form-control" type="text" value="" v-model="userForm.last_name" name="last_name" :placeholder="trans('user.last_name')">
                                                            <show-error :form-name="userForm" prop-name="last_name"></show-error>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="">{{trans('user.email')}}</label>
                                                            <input class="form-control" type="text" value="" v-model="userForm.email" name="email" :placeholder="trans('user.email')" readonly>
                                                            <show-error :form-name="userForm" prop-name="email"></show-error>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="">{{trans('user.gender')}}</label>
                                                            <div class="radio radio-info" v-for="gender in genders">
                                                                <input type="radio" :value="gender.id" :id="gender.id" v-model="userForm.gender" :checked="userForm.gender == gender.id" name="gender">
                                                                <label :for="gender.id"> {{trans('list.'+gender.id)}} </label>
                                                            </div>
                                                            <show-error :form-name="userForm" prop-name="gender"></show-error>
                                                        </div>
                                                    </div>
                                                    <template v-if="!userHasRole(user,'admin')">
                                                        <div class="col-12 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="">{{trans('role.role')}}</label>
                                                                <v-select label="name" track-by="id" v-model="selected_roles" name="role_id" id="role_id" :options="roles" :placeholder="trans('role.select_role')" @select="onRoleSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onRoleRemove" :selected="selected_roles">
                                                                </v-select>
                                                                <show-error :form-name="userForm" prop-name="role_id"></show-error>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="">{{trans('user.date_of_birth')}}</label>
                                                            <datepicker v-model="userForm.date_of_birth" :bootstrapStyling="true" name="date_of_birth" @selected="userForm.errors.clear('date_of_birth')"></datepicker>
                                                            <show-error :form-name="userForm" prop-name="date_of_birth"></show-error>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="">{{trans('user.date_of_anniversary')}}</label>
                                                            <datepicker v-model="userForm.date_of_anniversary" :bootstrapStyling="true" name="date_of_anniversary" @selected="userForm.errors.clear('date_of_anniversary')"></datepicker>
                                                            <show-error :form-name="userForm" prop-name="date_of_anniversary"></show-error>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-info pull-right">{{trans('general.save')}}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <user-summary :user="user"></user-summary>
            </div>
        </div>
    </div>
</template>


<script>
    import userSidebar from './user-sidebar'
    import datepicker from 'vuejs-datepicker'
    import vSelect from 'vue-multiselect'
    import userSummary from './summary'

    export default {
        components : { userSidebar,datepicker,vSelect,userSummary },
        data() {
            return {
                id:this.$route.params.id,
                userForm: new Form({
                    first_name: '',
                    last_name: '',
                    email: '',
                    gender: '',
                    date_of_birth: '',
                    date_of_anniversary: '',
                    role_id: []
                },false),
                user: '',
                genders: [],
                roles: [],
                selected_roles: ''
            };
        },
        mounted(){
            if(!helper.hasPermission('edit-user')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            this.fetchUserData();
            this.fetchPreRequisites();
        },
        methods: {
            fetchUserData(){
                axios.get('/api/user/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.user = response.user;
                        this.userForm.first_name = response.user.profile.first_name;
                        this.userForm.last_name = response.user.profile.last_name;
                        this.userForm.email = response.user.email;
                        this.userForm.gender = response.user.profile.gender;
                        this.userForm.date_of_birth = response.user.profile.date_of_birth;
                        this.userForm.date_of_anniversary = response.user.profile.date_of_anniversary;
                        this.userForm.role_id = response.roles;
                        this.selected_roles = response.selected_roles;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/user');
                    })
            },
            fetchPreRequisites(){
                axios.get('/api/user/pre-requisite')
                    .then(response => response.data)
                    .then(response => {
                        this.countries = response.countries;
                        this.genders = response.genders;
                        this.roles = response.roles;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error)
                    });
            },
            onRoleSelect(selectedOption){
                this.userForm.errors.clear('role_id');
                this.userForm.role_id.push(selectedOption.id);
            },
            onRoleRemove(removedOption){
                this.userForm.role_id.splice(this.userForm.role_id.indexOf(removedOption.id), 1);
            },
            submit(){
                this.userForm.date_of_birth = (this.userForm.date_of_birth) ? moment(this.userForm.date_of_birth).format('YYYY-MM-DD') : null;
                this.userForm.date_of_anniversary = (this.userForm.date_of_anniversary) ? moment(this.userForm.date_of_anniversary).format('YYYY-MM-DD') : null;
                this.userForm.patch('/api/user/'+this.id)
                    .then(response => {
                        this.fetchUserData();
                        toastr.success(response.message);
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            userHasRole(user,role){
                return helper.userHasRole(user,role);
            }
        }
    }
</script>
