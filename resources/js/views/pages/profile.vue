<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('user.profile')}} <span class="card-subtitle">{{getAuthUser('full_name')}} ({{getAuthUser('email')}})</span>
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/change-password')"><i class="fas fa-key"></i> <span class="d-none d-sm-inline">{{trans('auth.change_password')}}</span></button>
            </h3>
        </div>
        <div class="row">
            <div class="col-12 col-sm-8">
                <div class="card border-right">
                    <div class="card-body p-4">
                        <h4 class="card-title">{{trans('user.update_profile')}}</h4>
                        <form @submit.prevent="updateProfile" @keydown="profileForm.errors.clear($event.target.name)">
                            <div class="row">
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.first_name')}}</label>
                                        <input class="form-control" type="text" name="first_name" v-model="profileForm.first_name">
                                        <show-error :form-name="profileForm" prop-name="first_name"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.last_name')}}</label>
                                        <input class="form-control" type="text" name="last_name" v-model="profileForm.last_name">
                                        <show-error :form-name="profileForm" prop-name="last_name"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.date_of_birth')}}</label>
                                        <datepicker v-model="profileForm.date_of_birth" :bootstrapStyling="true" name="date_of_birth" @selected="profileForm.errors.clear('date_of_birth')"></datepicker>
                                        <show-error :form-name="profileForm" prop-name="date_of_birth"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.date_of_anniversary')}}</label>
                                        <datepicker v-model="profileForm.date_of_anniversary" :bootstrapStyling="true" name="date_of_anniversary" @selected="profileForm.errors.clear('date_of_anniversary')"></datepicker>
                                        <show-error :form-name="profileForm" prop-name="date_of_anniversary"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label for="">{{trans('user.gender')}}</label>
                                        <div class="radio radio-info">
                                            <div class="row">
                                                <div v-for="gender in genders" class="col-12 col-sm-4">
                                                    <input type="radio" :value="gender.id" :id="gender.id" v-model="profileForm.gender" :checked="profileForm.gender == gender.id" name="gender" @click="profileForm.errors.clear('gender')">
                                                    <label :for="gender.id"> {{trans('list.'+gender.id)}}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <show-error :form-name="profileForm" prop-name="gender"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">

                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.sm_profile',{name: 'Facebook'})}}</label>
                                        <input class="form-control" type="text" name="facebook_profile" v-model="profileForm.facebook_profile">
                                        <show-error :form-name="profileForm" prop-name="facebook_profile"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.sm_profile',{name: 'Twitter'})}}</label>
                                        <input class="form-control" type="text" name="twitter_profile" v-model="profileForm.twitter_profile">
                                        <show-error :form-name="profileForm" prop-name="twitter_profile"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.sm_profile',{name: 'Google Plus'})}}</label>
                                        <input class="form-control" type="text" name="google_plus_profile" v-model="profileForm.google_plus_profile">
                                        <show-error :form-name="profileForm" prop-name="google_plus_profile"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label for="">{{trans('user.sm_profile',{name: 'Linkedin'})}}</label>
                                        <input class="form-control" type="text" name="linkedin_profile" v-model="profileForm.linkedin_profile">
                                        <show-error :form-name="profileForm" prop-name="linkedin_profile"></show-error>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">{{trans('general.save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="card-title">{{trans('user.avatar')}}</h4>
                        <upload-image id="avatar" :upload-path="`/user/profile/avatar/${getAuthUser('id')}`" :remove-path="`/user/profile/avatar/remove/${getAuthUser('id')}`" :image-source="avatar.user" @uploaded="updateAvatar" @removed="updateAvatar"></upload-image>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import datepicker from 'vuejs-datepicker'
    import uploadImage from '../../components/upload-image'

    export default {
        components : { datepicker,uploadImage },
        data() {
            return {
                profileForm: new Form({
                    first_name : '',
                    last_name : '',
                    date_of_birth : '',
                    date_of_anniversary : '',
                    gender : '',
                    facebook_profile : '',
                    twitter_profile : '',
                    google_plus_profile : '',
                    linkedin_profile : ''
                }, false),
                avatar: {
                    user: ''
                },
                genders: [],
                user: {
                    profile: {}
                }
            };
        },
        mounted(){
            axios.get('/api/user/profile/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.user = response.user;
                    this.profileForm.first_name = response.user.profile.first_name;
                    this.profileForm.last_name = response.user.profile.last_name;
                    this.profileForm.date_of_birth = response.user.profile.date_of_birth;
                    this.profileForm.date_of_anniversary = response.user.profile.date_of_anniversary;
                    this.profileForm.gender = response.user.profile.gender;
                    this.profileForm.facebook_profile = response.user.profile.facebook_profile;
                    this.profileForm.twitter_profile = response.user.profile.twitter_profile;
                    this.profileForm.google_plus_profile = response.user.profile.google_plus_profile;
                    this.profileForm.linkedin_profile = response.user.profile.linkedin_profile;
                    this.avatar.user = response.user.profile.avatar;
                    this.genders = response.genders;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            updateProfile() {
                this.profileForm.date_of_birth = (this.profileForm.date_of_birth) ? moment(this.profileForm.date_of_birth).format('YYYY-MM-DD') : null;
                this.profileForm.date_of_anniversary = (this.profileForm.date_of_anniversary) ? moment(this.profileForm.date_of_anniversary).format('YYYY-MM-DD') : null;
                this.profileForm.post('/api/user/profile/update')
                    .then(response => {
                        toastr.success(response.message);
                        this.$store.dispatch('setAuthUserDetail',{
                            first_name: this.profileForm.first_name,
                            last_name: this.profileForm.last_name
                        });
                    }).catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            updateAvatar(val){
                this.$store.dispatch('setAuthUserDetail',{
                    avatar: val
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
