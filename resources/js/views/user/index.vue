<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <div class="row">
                <h3 class="text-themecolor">{{trans('user.user')}} 
                    <span class="card-subtitle" v-if="users">{{trans('general.total_result_found',{'count' : users.total})}}</span>
                    <span class="card-subtitle" v-else>{{trans('general.no_result_found')}}</span>
                </h3>
                
                <div class="col-2 col-sm-2 ">
                    <div class="form-group">
                        <label for="">{{trans('user.name')}}</label>
                        <input class="form-control" name="name" v-model="filterUserForm.name">
                    </div>
                </div>
                <div class="col-2 col-sm-2">
                    <div class="form-group">
                        <label for="">{{trans('user.email')}}</label>
                        <input class="form-control" name="email" v-model="filterUserForm.email">
                    </div>
                </div>
                <div class="col-2 col-sm-2 user_name">
                    <div class="form-group">
                        <label for="">{{trans('role.role')}}</label>
                        <select v-model="filterUserForm.role_id" class="custom-select col-12">
                          <option value="">{{trans('general.select_one')}}</option>
                          <option v-for="role in roles" v-bind:value="role.id">
                            {{ role.name }}
                          </option>
                        </select>
                    </div>
                </div>
                <div class="col-2 col-sm-2 user_name">
                    <div class="form-group">
                        <label for="">{{trans('user.status')}}</label>
                        <select v-model="filterUserForm.status" class="custom-select col-12">
                          <option value="">{{trans('general.select_one')}}</option>
                          <option value="activated">{{trans('user.status_activated')}}</option>
                          <option value="pending_activation">{{trans('user.status_pending_activation')}}</option>
                          <option value="pending_approval">{{trans('user.status_pending_approval')}}</option>
                          <option value="banned">{{trans('user.status_banned')}}</option>
                          <option value="disapproved">{{trans('user.status_disapproved')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-2 col-sm-2">
                    <div class="form-group">
                        <date-range-picker :start-date.sync="filterUserForm.created_at_start_date" d:end-date.sync="filterUserForm.created_at_end_date" :label="trans('user.created_at')"></date-range-picker>
                    </div>
                </div>
                        <h3 class="text-themecolor">
                            <sort-by class="pull-right" :order-by-options="orderByOptions" :sort-by="filterUserForm.sort_by" :order="filterUserForm.order" @updateSortBy="value => {filterUserForm.sort_by = value}"  @updateOrder="value => {filterUserForm.order = value}"></sort-by>
                            <button class="btn btn-info btn-sm pull-right m-r-10" v-if="users.total && !showCreatePanel && hasPermission('create-user')" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> <span class="d-none d-sm-inline">{{trans('user.create_user')}}</span></button>
                        </h3>   
            </div>    
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <transition name="fade">
                        <div class="card border-bottom" v-if="showFilterPanel">
                            <div class="card-body p-4">
                                <h4 class="card-title">{{trans('general.filter')}}</h4>
                                <div class="row">
                                    
                                </div>
                                <button class="btn btn-danger btn-sm pull-right" v-if="showFilterPanel" @click="showFilterPanel = !showFilterPanel">{{trans('general.cancel')}}</button>
                            </div>
                        </div>
                    </transition>
                    <transition name="fade" v-if="hasPermission('create-user')">
                        <div class="card border-bottom" v-if="showCreatePanel">
                            <div class="card-body p-4">
                                <h4 class="card-title">{{trans('user.add_new_user')}}</h4>
                                <form @submit.prevent="submit" @keydown="userForm.errors.clear($event.target.name)">
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
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
                                                        <input class="form-control" type="text" value="" v-model="userForm.email" name="email" :placeholder="trans('user.email')">
                                                        <show-error :form-name="userForm" prop-name="email"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('role.role')}}</label>
                                                        <v-select label="name" track-by="id" v-model="selected_roles" name="role_id" id="role_id" :options="roles" :placeholder="trans('role.select_role')" @select="onRoleSelect" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" @remove="onRoleRemove" :selected="selected_roles">
                                                        </v-select>
                                                        <show-error :form-name="userForm" prop-name="role_id"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('user.password')}}</label>
                                                        <input class="form-control" type="password" value="" v-model="userForm.password" name="password" :placeholder="trans('user.password')">
                                                        <show-error :form-name="userForm" prop-name="password"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('user.password_confirmation')}}</label>
                                                        <input class="form-control" type="password" value="" v-model="userForm.password_confirmation" name="password_confirmation" :placeholder="trans('user.password_confirmation')">
                                                        <show-error :form-name="userForm" prop-name="password_confirmation"></show-error>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('user.address_line_1')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="userForm.address_line_1" name="address_line_1" :placeholder="trans('user.address_line_1')">
                                                        <show-error :form-name="userForm" prop-name="address_line_1"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('user.address_line_2')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="userForm.address_line_2" name="address_line_2" :placeholder="trans('user.address_line_2')">
                                                        <show-error :form-name="userForm" prop-name="address_line_2"></show-error>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('user.city')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="userForm.city" name="city" :placeholder="trans('user.city')">
                                                        <show-error :form-name="userForm" prop-name="city"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('user.state')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="userForm.state" name="state" :placeholder="trans('user.state')">
                                                        <show-error :form-name="userForm" prop-name="state"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('user.zipcode')}}</label>
                                                        <input class="form-control" type="text" value="" v-model="userForm.zipcode" name="zipcode" :placeholder="trans('user.zipcode')">
                                                        <show-error :form-name="userForm" prop-name="zipcode"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="">{{trans('user.country')}}</label>
                                                        <select class="form-control" name="country_id" v-model="userForm.country_id">
                                                            <option value="">{{trans('user.country')}}</option>
                                                            <option v-for="country in countries" :value="country.value">{{country.text}}</option>
                                                        </select>
                                                        <show-error :form-name="userForm" prop-name="country_id"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <switches class="m-l-20" v-model="userForm.send_email" theme="bootstrap" color="success"></switches> {{trans('user.send_welcome_email')}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-info waves-effect waves-light pull-right">{{trans('general.add')}}</button>
                                    <button type="button" class="btn btn-danger waves-effect waves-light pull-right m-r-10" @click="showCreatePanel = !showCreatePanel">{{trans('general.cancel')}}</button>
                                </form>
                            </div>
                        </div>
                    </transition>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" v-if="hasPermission('list-user') && users.total">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{trans('user.name')}}</th>
                                            <th>{{trans('user.email')}}</th>
                                            <th>{{trans('role.role')}}</th>
                                            <th>{{trans('user.status')}}</th>
                                            <th>{{trans('user.created_at')}}</th>
                                            <th class="table-option">{{trans('general.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in users.data">
                                            <td v-text="user.profile.first_name+' '+user.profile.last_name"></td>
                                            <td v-text="user.email"></td>
                                            <td>
                                                <ul style="list-style: none; padding:0;">
                                                    <li v-for="role in user.roles">{{role.name}}</li>
                                                </ul>
                                            </td>
                                            <td>
                                                <span v-for="status in getUserStatus(user)" :class="['label','label-'+status.color,'m-r-5']">{{status.label}}</span>
                                            </td>
                                            <td>{{user.created_at | moment}}</td>
                                            <td class="table-option">
                                                <div class="btn-group">

                                                    <router-link :to="`/user/${user.id}`" class="btn btn-info btn-sm" v-tooltip="trans('user.view_user')"><i class="fas fa-arrow-circle-right"></i></router-link>
                                                    <button class="btn btn-danger btn-sm" :key="user.id" v-if="hasPermission('delete-user')" v-confirm="{ok: confirmDelete(user)}" v-tooltip="trans('user.delete_user')"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <module-info v-if="!users.total" module="user" title="module_info_title" description="module_info_description" icon="users">
                                <div slot="btn">
                                    <button class="btn btn-info btn-sm" v-if="hasPermission('create-user') && !showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                                </div>
                            </module-info>
                            <pagination-record :page-length.sync="filterUserForm.page_length" :records="users" @updateRecords="getUsers"></pagination-record>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import switches from 'vue-switches'
    import vSelect from 'vue-multiselect'
    import dateRangePicker from '../../components/date-range-picker'
    import sortBy from '../../components/sort-by'

    export default {
        components: {switches,vSelect,dateRangePicker,sortBy},
        data() {
            return {
                showCreatePanel: false,
                users: {
                    total: 0,
                    data: []
                },
                filterUserForm: {
                    name: '',
                    email: '',
                    role_id: '',
                    status: '',
                    created_at_start_date: '',
                    created_at_end_date: '',
                    sort_by : 'first_name',
                    order: 'asc',
                    page_length: helper.getConfig('page_length')
                },
                userForm: new Form({
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    role_id: [],
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zipcode: '',
                    country_id: '',
                    send_email: ''
                }),
                countries: [],
                roles: [],
                selected_roles: '',
                showFilterPanel: false,
                orderByOptions: [
                    {
                        value: 'first_name',
                        translation: i18n.user.first_name
                    },
                    {
                        value: 'last_name',
                        translation: i18n.user.last_name
                    },
                    {
                        value: 'email',
                        translation: i18n.user.email
                    },
                    {
                        value: 'status',
                        translation: i18n.user.status
                    },
                    {
                        value: 'created_at',
                        translation: i18n.user.created_at
                    }
                ],
            };
        },
        mounted(){
            if(!helper.hasPermission('list-user') && !helper.hasPermission('create-user')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            this.fetchPreRequisites();
            this.getUsers();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
            },
            fetchPreRequisites(){
                axios.get('/api/user/pre-requisite')
                    .then(response => response.data)
                    .then(response => {
                        this.countries = response.countries;
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
                this.userForm.post('/api/user')
                    .then(response => {
                        toastr.success(response.message);
                        this.selected_roles = null;
                        this.userForm.role_id = [];
                        this.getUsers();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getUsers(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterUserForm);
                axios.get('/api/user?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.users = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    })
            },
            confirmDelete(user){
                return dialog => this.deleteUser(user);
            },
            deleteUser(user){
                axios.delete('/api/user/'+user.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getUsers();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getUserStatus(user){
                return helper.getUserStatus(user);
            },
            getAvatar(user){
                return helper.getAvatar(user);
            }
        },
        watch: {
            filterUserForm: {
                handler(val){
                    this.getUsers();
                },
                deep: true
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          }
        }
    }
</script>
