<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('general.home')}}
                <button class="btn btn-danger btn-sm pull-right" @click.prevent="logout"><i class="fas fa-power-off"></i> <span class="d-none d-sm-inline">{{trans('auth.logout')}}</span></button>
                <button class="btn btn-info btn-sm right-sidebar-toggle pull-right m-r-10" v-tooltip="trans('user.user_preference')"><i class="fas fa-cog"></i></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row" v-if="hasRole('admin')">
                <div class="col-12 col-sm-3">
                    <div class="card">
                        <div class="card-body px-3 pt-3">
                            <h4 class="card-title">{{trans('dashboard.period_registered_user',{period: trans('dashboard.total')})}}</h4>
                            <div class="text-right">
                                <h2 class="font-light m-b-0"><i class="fas fa-users fa-lg pull-right m-r-40"></i> <span class="pull-left">{{users}}</span></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <div class="card">
                        <div class="card-body px-3 pt-3">
                            <h4 class="card-title">{{trans('dashboard.period_registered_user',{period: trans('dashboard.today')})}}</h4>
                            <div class="text-right">
                                <h2 class="font-light m-b-0"><i class="fas fa-users fa-lg pull-right m-r-40"></i> <span class="pull-left">{{today_registered_users}}</span></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <div class="card">
                        <div class="card-body px-3 pt-3">
                            <h4 class="card-title">{{trans('dashboard.period_registered_user',{period: trans('dashboard.week')})}}</h4>
                            <div class="text-right">
                                <h2 class="font-light m-b-0"><i class="fas fa-users fa-lg pull-right m-r-40"></i> <span class="pull-left">{{weekly_registered_users}}</span></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <div class="card">
                        <div class="card-body px-3 pt-3">
                            <h4 class="card-title">{{trans('dashboard.period_registered_user',{period: trans('dashboard.month')})}}</h4>
                            <div class="text-right">
                                <h2 class="font-light m-b-0"><i class="fas fa-users fa-lg pull-right m-r-40"></i> <span class="pull-left">{{monthly_registered_users}}</span></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-0 border-top">
                <div class="col-12 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="px-3 pt-3">
                                <h4 class="card-title">{{trans('todo.todo')}}
                                    <span class="card-subtitle" v-if="!todos.total">{{trans('general.no_result_found')}}</span>
                                </h4>
                            </div>
                            <div class="table-responsive" v-if="todos.total">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('todo.title')}}</th>
                                            <th class="table-option">{{trans('todo.date')}}</th>
                                            <th>{{trans('todo.status')}}</th>
                                            <th>{{trans('todo.completed_at')}}</th>
                                            <th>{{trans('todo.description')}}</th>
                                            <th class="table-option">{{trans('general.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="todo in todos.data">
                                            <td v-text="todo.title"></td>                                            
                                            <td class="table-option">{{todo.date | moment}}</td>
                                            <td v-html="getStatus(todo)"></td>
                                            <td>{{todo.completed_at | momentDateTime}}</td>
                                            <td v-text="todo.description"></td>
                                            <td class="table-option">
                                                <div class="btn-group">
                                                    <button class="btn btn-secondary btn-sm" v-tooltip="todo.status >= 1 ? trans('todo.mark_as_incomplete') : trans('todo.mark_as_complete')" @click.prevent="toggleStatus(todo)">
                                                        <i :class="['fa', (todo.status >= 1 ?  'fa-times' : 'fa-check')]"></i>
                                                    </button>
                                                    <button class="btn btn-info btn-sm" v-tooltip="trans('todo.edit_todo')" @click.prevent="editTodo(todo)"><i class="fas fa-edit"></i></button>
                                                    <button class="btn btn-danger btn-sm" :key="todo.id" v-confirm="{ok: confirmDelete(todo)}" v-tooltip="trans('todo.delete_todo')"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="right-sidebar">
            <div class="slimscrollright">
                <div class="rpanel-title"> 
                    {{trans('user.user_preference')}} 
                    <button class="btn btn-danger btn-sm right-sidebar-toggle pull-right m-r-10"><i class="fas fa-times"></i></button>
                </div>
                <div class="r-panel-body">
                    <form @submit.prevent="updatePreference" @keydown="preferenceForm.errors.clear($event.target.name)">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">{{trans('configuration.color_theme')}}</label>
                                    <select v-model="preferenceForm.color_theme" class="custom-select col-12">
                                      <option v-for="option in color_themes" v-bind:value="option.value">
                                        {{ option.text }}
                                      </option>
                                    </select>
                                    <show-error :form-name="preferenceForm" prop-name="color_theme"></show-error>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">{{trans('configuration.direction')}}</label>
                                    <select v-model="preferenceForm.direction" class="custom-select col-12">
                                      <option v-for="option in directions" v-bind:value="option.value">
                                        {{ option.text }}
                                      </option>
                                    </select>
                                    <show-error :form-name="preferenceForm" prop-name="direction"></show-error>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">{{trans('configuration.sidebar')}}</label>
                                    <select v-model="preferenceForm.sidebar" class="custom-select col-12">
                                      <option v-for="option in sidebar" v-bind:value="option.value">
                                        {{ option.text }}
                                      </option>
                                    </select>
                                    <show-error :form-name="preferenceForm" prop-name="sidebar"></show-error>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label for="">{{trans('locale.locale')}}</label>
                                    <select v-model="preferenceForm.locale" class="custom-select col-12">
                                      <option v-for="option in locales" v-bind:value="option.value">
                                        {{ option.text }}
                                      </option>
                                    </select>
                                    <show-error :form-name="preferenceForm" prop-name="sidebar"></show-error>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">{{trans('general.save')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        components: {},
        mounted(){
            if(this.$route.query.reload)
                window.location = window.location.pathname;

            axios.get('/api/dashboard')
                .then(response => response.data)
                .then(response => {
                    this.users = response.users;
                    this.today_registered_users = response.today_registered_users;
                    this.weekly_registered_users = response.weekly_registered_users;
                    this.monthly_registered_users = response.monthly_registered_users;
                    this.activity_logs = response.activity_logs;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                })

            axios.get('/api/user/preference/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.color_themes = response.color_themes;
                    this.directions = response.directions;
                    this.sidebar = response.sidebar;
                    this.locales = response.locales;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                })

            this.getTodos();
        },
        methods: {
            getStatus(todo){
                if(todo.status == '1'){
                    return ('<span class="label label-success">'+i18n.todo.complete+'</span>')
                }else if(todo.status == '0'){
                    return ('<span class="label label-danger">'+i18n.todo.incomplete+'</span>')
                }else{
                    return ('<span class="label label-warning">In behandeling</span>')
                }
            },
            hasRole(role){
                return helper.hasRole(role);
            },
            logout(){
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login')
                })
            },
            editTodo(todo){
                this.$router.push('/todo/'+todo.id+'/edit');
            },
            confirmDelete(todo){
                return dialog => this.deleteTodo(todo);
            },
            deleteTodo(todo){
                axios.delete('/api/todo/'+todo.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getTodos();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            toggleStatus(todo){
                axios.post('/api/todo/'+todo.id+'/status')
                    .then(response => response.data)
                    .then(response => {
                        this.getTodos();
                        toastr.success(response.message);
                    })
                    .catch(error => {
                        helper.showDataErrorMsg();
                    });
            },
            getTodos(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterTodoForm);
                axios.get('/api/todo?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.todos = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            updatePreference(){
                this.preferenceForm.post('/api/user/preference')
                    .then(response => {
                        toastr.success(response.message);

                        $('#theme').attr('href','/css/colors/'+this.preferenceForm.color_theme+'.css');

                        if(this.user_preference.direction != this.preferenceForm.direction || this.user_preference.sidebar != this.preferenceForm.sidebar || this.user_preference.locale != this.preferenceForm.locale)
                            location.reload();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            }
        },
        data() {
            return {
                users: 0,
                today_registered_users: 0,
                weekly_registered_users: 0,
                monthly_registered_users: 0,
                activity_logs: {},
                todos: {
                    total: 0,
                    data: []
                },
                color_themes: [],
                directions: [],
                sidebar: [],
                locales: [],
                preferenceForm: new Form({
                    color_theme: helper.getConfig('user_color_theme') || helper.getConfig('color_theme'),
                    direction: helper.getConfig('user_direction') || helper.getConfig('direction'),
                    locale: helper.getConfig('user_locale') || helper.getConfig('locale'),
                    sidebar: helper.getConfig('user_sidebar') || helper.getConfig('sidebar')
                },false),
                user_preference: {
                    color_theme: helper.getConfig('user_color_theme') || helper.getConfig('color_theme'),
                    direction: helper.getConfig('user_direction') || helper.getConfig('direction'),
                    locale: helper.getConfig('user_locale') || helper.getConfig('locale'),
                    sidebar: helper.getConfig('user_sidebar') || helper.getConfig('sidebar')
                },                
                filterTodoForm: {
                    keyword: '',
                    status: false,
                    start_date: '',
                    end_date: '',
                    sort_by : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                }
            }
        },
        computed: {
        },
        filters: {
          momentDateTime(date) {
            return helper.formatDateTime(date);
          },
          moment(date) {
            return helper.formatDate(date);
          }
        },
        watch: {
            filterTodoForm: {
                handler(val){
                    setTimeout(() => {
                        this.getTodos();
                    }, 500)
                },
                deep: true
            }
        }
    }
</script>
<style>
    .shw-rside{
        width: 500px;
    }
</style>