<template>
    <form @submit.prevent="proceed" @keydown="ipFilterForm.errors.clear($event.target.name)">
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label for="">{{trans('ip_filter.start_ip')}}</label>
                    <input class="form-control" type="text" value="" v-model="ipFilterForm.start_ip" name="start_ip" :placeholder="trans('ip_filter.start_ip')">
                    <show-error :form-name="ipFilterForm" prop-name="start_ip"></show-error>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label for="">{{trans('ip_filter.end_ip')}}</label>
                    <input class="form-control" type="text" value="" v-model="ipFilterForm.end_ip" name="end_ip" :placeholder="trans('ip_filter.end_ip')">
                    <show-error :form-name="ipFilterForm" prop-name="end_ip"></show-error>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label for="">{{trans('ip_filter.description')}}</label>
                    <textarea class="form-control" type="text" value="" v-model="ipFilterForm.description" rows="1" name="description" :placeholder="trans('ip_filter.description')"></textarea>
                    <show-error :form-name="ipFilterForm" prop-name="description"></show-error>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light pull-right">
            <span v-if="id">{{trans('general.update')}}</span>
            <span v-else>{{trans('general.save')}}</span>
        </button>
        <router-link to="/configuration/ip-filter" class="btn btn-danger waves-effect waves-light pull-right m-r-10" v-show="id">{{trans('general.cancel')}}</router-link>
        <button v-if="!id" type="button" class="btn btn-danger waves-effect waves-light pull-right m-r-10" @click="$emit('cancel')">{{trans('general.cancel')}}</button>
    </form>
</template>


<script>
    export default {
        components: {},
        data() {
            return {
                ipFilterForm: new Form({
                    start_ip: '',
                    end_ip: '',
                    description: ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if(this.id)
                this.getIpFilter();
        },
        methods: {
            proceed(){
                if(this.id)
                    this.updateIpFilter();
                else
                    this.storeIpFilter();
            },
            storeIpFilter(){
                this.ipFilterForm.post('/api/ip-filter')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getIpFilter(){
                axios.get('/api/ip-filter/'+this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.ipFilterForm.start_ip = response.start_ip;
                        this.ipFilterForm.end_ip = response.end_ip;
                        this.ipFilterForm.description = response.description;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/configuration/ip-filter');
                    });
            },
            updateIpFilter(){
                this.ipFilterForm.patch('/api/ip-filter/'+this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/configuration/ip-filter');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
