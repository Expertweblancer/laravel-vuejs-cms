<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <div class="row">
                <div class="col-3">
                    <h3 class="text-themecolor">{{trans('todo.todo')}} 
                        <span class="card-subtitle" v-if="todos">{{trans('general.total_result_found',{'count' : todos.total})}}</span>
                        <span class="card-subtitle" v-else>{{trans('general.no_result_found')}}</span>
                    </h3>
                </div>    
                    <div class="col-3">
                        <div class="form-group">
                            <label for="">{{trans('todo.keyword')}}</label>
                            <input class="form-control" name="keyword" v-model="filterTodoForm.keyword">
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <date-range-picker :start-date.sync="filterTodoForm.start_date" :end-date.sync="filterTodoForm.end_date" :label="trans('general.date_between')"></date-range-picker>
                        </div>
                    </div>

                <div class="col-3">
                    <h3 class="text-themecolor">
                        <sort-by class="pull-right" :order-by-options="orderByOptions" :sort-by="filterTodoForm.sort_by" :order="filterTodoForm.order" @updateSortBy="value => {filterTodoForm.sort_by = value}"  @updateOrder="value => {filterTodoForm.order = value}"></sort-by>
                        <button class="btn btn-info btn-sm pull-right m-r-10" v-if="todos.total && !showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> <span class="d-none d-sm-inline">{{trans('todo.add_new_todo')}}</span></button>
                    </h3>
                </div>    
            </div>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <transition name="fade">
                        <div class="card border-bottom" v-if="showCreatePanel">
                            <div class="card-body p-4">
                                <h4 class="card-title">{{trans('todo.add_new_todo')}}</h4>
                                <todo-form @completed="getTodos" @cancel="showCreatePanel = !showCreatePanel"></todo-form>
                            </div>
                        </div>
                    </transition>  
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" v-if="todos.total">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('todo.title')}}</th>
                                            <th>{{trans('todo.date')}}</th>
                                            <th>{{trans('todo.status')}}</th>
                                            <th>{{trans('todo.completed_at')}}</th>
                                            <th>{{trans('todo.description')}}</th>
                                            <th class="table-option">{{trans('general.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="todo in todos.data">
                                            <td v-text="todo.title"></td>
                                            <td>{{todo.date | moment}}</td>
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
                            <module-info v-if="!todos.total" module="todo" title="module_info_title" description="module_info_description" icon="check-circle">
                                <div slot="btn">
                                    <button class="btn btn-info btn-md" v-if="!showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-plus"></i> {{trans('general.add_new')}}</button>
                                </div>
                            </module-info>
                            <pagination-record :page-length.sync="filterTodoForm.page_length" :records="todos" @updateRecords="getTodos"></pagination-record>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import todoForm from './form'
    import switches from 'vue-switches'
    import datepicker from 'vuejs-datepicker'
    import dateRangePicker from '../../components/date-range-picker'
    import sortBy from '../../components/sort-by'

    export default {
        components : { todoForm,switches,datepicker,dateRangePicker,sortBy },
        data() {
            return {
                todos: {
                    total: 0,
                    data: []
                },
                filterTodoForm: {
                    keyword: '',
                    status: false,
                    start_date: '',
                    end_date: '',
                    sort_by : 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
                orderByOptions: [
                    {
                        value: 'title',
                        translation: i18n.todo.title
                    },
                    {
                        value: 'description',
                        translation: i18n.todo.description
                    },
                    {
                        value: 'created_at',
                        translation: i18n.todo.date
                    },
                    {
                        value: 'status',
                        translation: i18n.todo.status
                    }
                ],
                showCreatePanel: false,
                showFilterPanel: false
            };
        },
        mounted(){
            if(!helper.hasPermission('access-todo')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('todo')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            this.getTodos();
        },
        methods: {
            hasPermission(permission){
                return helper.hasPermission(permission);
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
            getStatus(todo){
                if(todo.status == '1'){
                    return ('<span class="label label-success">'+i18n.todo.complete+'</span>')
                }else if(todo.status == '0'){
                    return ('<span class="label label-danger">'+i18n.todo.incomplete+'</span>')
                }else{
                    return ('<span class="label label-warning">In behandeling</span>')
                }
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
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(date) {
            return helper.formatDateTime(date);
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
