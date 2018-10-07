<template>
    <form @submit.prevent="proceed" @keydown="todoForm.errors.clear($event.target.name)">
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label for="">{{trans('todo.title')}}</label>
                    <input class="form-control" type="text" value="" v-model="todoForm.title" name="title" :placeholder="trans('todo.title')">
                    <show-error :form-name="todoForm" prop-name="title"></show-error>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label for="">{{trans('todo.date')}}</label>
                    <datepicker v-model="todoForm.date" :bootstrapStyling="true" @selected="todoForm.errors.clear('date')" :placeholder="trans('todo.date')"></datepicker>
                    <show-error :form-name="todoForm" prop-name="date"></show-error>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label for="">{{trans('todo.description')}}</label>
                    <autosize-textarea v-model="todoForm.description" rows="1" name="description" :placeholder="trans('todo.description')"></autosize-textarea>
                    <show-error :form-name="todoForm" prop-name="description"></show-error>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light pull-right">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/todo" class="btn btn-danger waves-effect waves-light pull-right m-r-10" v-show="id">{{trans('general.cancel')}}</router-link>
        <button v-if="!id" type="button" class="btn btn-danger waves-effect waves-light pull-right m-r-10" @click="$emit('cancel')">{{trans('general.cancel')}}</button>
    </form>
</template>


<script>
    import datepicker from 'vuejs-datepicker'
    import autosizeTextarea from '../../components/autosize-textarea'

    export default {
        components: {datepicker,autosizeTextarea},
        data() {
            return {
                todoForm: new Form({
                    title : '',
                    description : '',
                    date: ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if(this.id)
                this.getTodo();
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateTodo();
                else
                    this.storeTodo();
            },
            storeTodo(){
                this.todoForm.date = moment(this.todoForm.date).format('YYYY-MM-DD');
                this.todoForm.post('/api/todo')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getTodo(){
                axios.get('/api/todo/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.todoForm.title = response.title;
                        this.todoForm.description = response.description;
                        this.todoForm.date = response.date;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/todo');
                    });
            },
            updateTodo(){
                this.todoForm.date = moment(this.todoForm.date).format('YYYY-MM-DD');
                this.todoForm.patch('/api/todo/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/todo');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
