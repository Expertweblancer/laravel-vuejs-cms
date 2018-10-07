<template>
    <section id="wrapper">
        <div class="login-register">
            <div class="login-box card">
                <div class="card-body">
                    <h3 class="box-title m-b-20">{{trans('auth.tnc')}}</h3>
                    <p>Here goes terms and conditions!</p>
                     <div class="text-center m-t-20">
                        <div class="col-xs-12">
                            <router-link class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" to="/">{{trans('general.back')}}</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
    export default {
        mounted(){

            if(!helper.featureAvailable('terms_and_conditions')){
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }
        }
    }
</script>
