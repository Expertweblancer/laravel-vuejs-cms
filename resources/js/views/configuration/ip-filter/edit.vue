<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('ip_filter.edit_ip_filter')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/configuration/ip-filter')"><i class="fas fa-ellipsis-v"></i> <span class="d-none d-sm-inline">{{trans('ip_filter.ip_filter')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <ip-filter-form :id="id"></ip-filter-form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ipFilterForm from './form';

    export default {
        components : { ipFilterForm },
        data() {
            return {
                id:this.$route.params.id
            }
        },
        mounted(){
            if(!helper.hasPermission('access-configuration')){
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if(!helper.featureAvailable('ip_filter')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }
        }
    }
</script>
