<template>
    <form @submit.prevent="storeRole" @keydown="roleForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label for="">{{trans('role.name')}}</label>
            <input class="form-control" type="text" value="" v-model="roleForm.name" name="name" :placeholder="trans('role.name')">
            <show-error :form-name="roleForm" prop-name="name"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light pull-right">
            <span>{{trans('general.save')}}</span>
        </button>
        <button v-if="!id" type="button" class="btn btn-danger waves-effect waves-light pull-right m-r-10" @click="$emit('cancel')">{{trans('general.cancel')}}</button>
    </form>
</template>


<script>
    export default {
        data() {
            return {
                roleForm: new Form({
                    'name' : ''
                })
            };
        },
        methods: {
            storeRole(){
                this.roleForm.post('/api/role')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
