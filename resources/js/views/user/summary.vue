<template>
    <div class="col-12 col-sm-4" v-if="user">
        <div class="card border-left">
            <div class="card-body p-4">
                <center class="m-t-30"> <img :src="getAvatar(user)" class="img-circle" width="150" />
                    <h4 class="card-title m-t-10">{{user.profile.first_name+' '+user.profile.last_name}}
                        <span v-if="user.profile.gender === 'male'"><i class="fas fa-male fa-2x"></i></span>
                        <span v-if="user.profile.gender === 'female'"><i class="fas fa-female fa-2x"></i></span>
                    </h4>
                    <div><span v-for="status in getUserStatus(user)" :class="['label','label-'+status.color,'m-r-5']">{{status.label}}</span></div>

                    <div class="row m-t-10" v-if="user.id != getAuthUser('id')">
                        <div class="col-12" v-if="user.status == 'activated'">
                            <button type="button" class="btn btn-block btn-danger" @click="updateStatus('banned')"><i class="fas fa-ban"></i> {{trans('user.user_action_ban')}}</button>
                        </div>
                        <div class="col-12" v-if="user.status == 'disapproved'">
                            <button type="button" class="btn btn-block btn-success" @click="updateStatus('activated')"><i class="fas fa-check"></i> {{trans('user.user_action_approve')}}</button>
                        </div>
                        <div class="col-6" v-if="user.status == 'pending_activation' || user.status == 'pending_approval'">
                            <button type="button" class="btn btn-block btn-success" @click="updateStatus('activated')"><i class="fas fa-user-plus"></i> {{trans('user.user_action_approve')}}</button>
                        </div>
                        <div class="col-6" v-if="user.status == 'pending_activation' || user.status == 'pending_approval'">
                            <button type="button" class="btn btn-block btn-danger" @click="updateStatus('disapproved')"><i class="fas fa-user-times"></i> {{trans('user.user_action_disapprove')}}</button>
                        </div>
                        <div class="col-12" v-if="user.status == 'banned'">
                            <button type="button" class="btn btn-block btn-success" @click="updateStatus('activated')"><i class="fas fa-check"></i> {{trans('user.user_action_activate')}}</button>
                        </div>
                    </div>
                </center>
            </div>
            <div class="card-body border-top p-4">
                <small class="text-muted">{{trans('user.email')}}</small>
                <h6>{{user.email}}</h6>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">{{trans('user.date_of_birth')}}</small>
                        <h6>{{user.profile.date_of_birth | moment}}</h6>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">{{trans('user.date_of_anniversary')}}</small>
                        <h6>{{user.profile.date_of_anniversary | moment}}</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">{{trans('user.created_at')}}</small>
                        <h6>{{user.created_at | momentDateTime}}</h6>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">{{trans('user.updated_at')}}</small>
                        <h6>{{user.updated_at | momentDateTime}}</h6>
                    </div>
                </div>
                <small class="text-muted p-t-30 db" v-if="user.profile.phone">{{trans('user.phone')}}</small>
                <h6 v-if="user.profile.phone">{{user.profile.phone}}</h6>
                <small class="text-muted p-t-30 db">{{trans('user.social_profile')}}</small>
                <br/>
                <a v-if="user.profile.facebook_profile" :href="user.profile.facebook_profile" class="btn btn-circle btn-secondary"><i class="fab fa-facebook"></i></a>
                <a v-if="user.profile.twitter_profile" :href="user.profile.twitter_profile" class="btn btn-circle btn-secondary"><i class="fab fa-twitter"></i></a>
                <a v-if="user.profile.linkedin_profile" :href="user.profile.linkedin_profile" class="btn btn-circle btn-secondary"><i class="fab fa-linkedin"></i></a>
                <a v-if="user.profile.google_plus_profile" :href="user.profile.google_plus_profile" class="btn btn-circle btn-secondary"><i class="fab fa-google"></i></a>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {

            }
        },
        props: {
            user: {
                default: {
                    profile: {}
                },
                required: true
            }
        },
        methods: {
            getUserStatus(user){
                return helper.getUserStatus(user);
            },
            updateStatus(status){
                axios.post('/api/user/'+this.user.id+'/status',{
                    status: status
                })
                .then(response => response.data)
                .then(response => {
                    this.user.status = status;
                    toastr.success(response.message);
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
            },
            getAuthUser(name){
                return helper.getAuthUser(name);
            },
            getAvatar(user){
                return  helper.getAvatar(user);
            }
        },
        mounted(){
        },
        computed: {
        },
        watch: {
            user(newVal){
                this.user = newVal;
            }
        },
        filters: {
          moment(date) {
            return helper.formatDate(date);
          },
          momentDateTime(date) {
            return helper.formatDateTime(date);
          }
        }
    }
</script>
