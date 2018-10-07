<template>
    <section id="wrapper">
        <div class="login-register" style="background-image:url(/images/background.jpg);">
            <div class="login-box card">
            <div class="card-body p-4">
                <center class="m-t-30"> <img :src="getAuthUser('avatar')" class="img-circle" width="100" />
                    <h4 class="card-title m-t-10">{{getAuthUser('full_name')}}</h4>
                </center>
                <form class="form-horizontal form-material" id="lockScreenForm" @submit.prevent="submit" @keydown="lockScreenForm.errors.clear($event.target.name)">
                    <h3 class="box-title m-b-20 text-center">{{trans('auth.password')}}</h3>
                    <div class="form-group ">
                        <input type="password" name="password" class="form-control text-center" :placeholder="trans('auth.password')" v-model="lockScreenForm.password">
                        <show-error :form-name="lockScreenForm" prop-name="password"></show-error>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">{{trans('auth.confirm')}}</button>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p><a href="#" @click.prevent="logout"><i class="fas fa-power-off"></i> {{trans('auth.logout')}}</a></p>
                        </div>
                    </div>
                </form>
            </div>
            <guest-footer></guest-footer>
          </div>
        </div>

    </section>
</template>
<script>
    import guestFooter from '../../layouts/guest-footer.vue'

    export default {
        components: {guestFooter},
        data(){
            return {
                lockScreenForm: new Form({
                    password: '',
                })
            }
        },
        mounted(){
            if(!helper.getConfig('lock_screen') || !helper.isScreenLocked())
                this.$router.push('/home');

            if(!helper.getConfig('mode'))
                this.lockScreenForm.password = 'abcd1234';
        },
        methods: {
            logout(){
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login')
                })
            },
            submit(){
                this.lockScreenForm.post('/api/auth/lock')
                    .then(response => {
                        this.$store.dispatch('setLastActivity');
                        toastr.success(response.message);
                        this.$router.push('/home');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            }
        },
        computed: {
        }
    }
</script>
