<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('locale.edit_locale')}}
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/configuration/locale')"><i class="fas fa-globe"></i> <span class="d-none d-sm-inline">{{trans('locale.locale')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-12 col-sm-8">
                                    <locale-form :id="id"></locale-form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import localeForm from './form';

    export default {
        components : { localeForm },
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

            if(!helper.featureAvailable('multilingual')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }
        }
    }
</script>
