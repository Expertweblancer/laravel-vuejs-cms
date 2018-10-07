<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('todo.edit_todo')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/todo')"><i class="fas fa-check-circle"></i> <span class="d-none d-sm-inline">{{trans('todo.todo')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <h4 class="card-title">{{trans('todo.edit_todo')}}</h4>
                            <todo-form :id="id"></todo-form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import todoForm from './form';

    export default {
        components : { todoForm },
        data() {
            return {
                id:this.$route.params.id
            }
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
        }
    }
</script>
