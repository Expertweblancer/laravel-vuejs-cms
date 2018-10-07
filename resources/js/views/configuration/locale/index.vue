<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('locale.locale')}}
                <span class="card-subtitle" v-if="locales">{{trans('general.total_result_found',{'count' : locales.total})}}</span>
                <span class="card-subtitle" v-else>{{trans('general.no_result_found')}}</span>

                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
                <button class="btn btn-info btn-sm pull-right m-r-10" v-if="locales.total && !showCreatePanel" @click="showCreatePanel = !showCreatePanel"><i class="fas fa-globe"></i> <span class="d-none d-sm-inline">{{trans('locale.add_new_locale')}}</span></button>
                <button class="btn btn-info btn-sm pull-right m-r-10" v-if="!showWordPanel" @click="showWordPanel = !showWordPanel"><i class="fas fa-plus"></i> <span class="d-none d-sm-inline">{{trans('locale.add_new_word')}}</span></button>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <transition name="fade">
                        <div class="card border-bottom" v-if="showCreatePanel">
                            <div class="card-body p-4">
                                <h4 class="card-title">{{trans('locale.add_new_locale')}}</h4>
                                <show-tip module="locale" tip="tip_locale"></show-tip>
                                <div class="row">
                                    <div class="col-12 col-sm-8">
                                        <locale-form @completed="getLocales" @cancel="showCreatePanel = !showCreatePanel"></locale-form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>

                    <transition name="fade">
                        <div class="card border-bottom" v-if="showWordPanel">
                            <div class="card-body p-4">
                                <h4 class="card-title m-t-20">{{trans('locale.add_new_word')}} <show-tip module="locale" tip="tip_add_word" type="field"></show-tip></h4>
                                <form @submit.prevent="addWord" @keydown="addWordForm.errors.clear($event.target.name)">
                                    <div class="row">
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="">{{trans('locale.word')}}</label>
                                                <input class="form-control" type="text" value="" v-model="addWordForm.word" name="word" :placeholder="trans('locale.word')">
                                                <show-error :form-name="addWordForm" prop-name="word"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="">{{trans('locale.module')}}</label>
                                                <select v-model="addWordForm.module" class="custom-select col-12" @change="addWordForm.errors.clear('module')">
                                                  <option value="">{{trans('general.select_one')}}</option>
                                                  <option v-for="module in modules" v-bind:value="module">
                                                    {{ toWord(module) }}
                                                  </option>
                                                </select>
                                                <show-error :form-name="addWordForm" prop-name="module"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label for="">{{trans('locale.translation')}}</label>
                                                <input class="form-control" type="text" value="" v-model="addWordForm.translation" name="translation" :placeholder="trans('locale.translation')">
                                                <show-error :form-name="addWordForm" prop-name="translation"></show-error>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-info waves-effect waves-light pull-right">{{trans('general.add')}}</button>
                                    <button type="button" class="btn btn-danger waves-effect waves-light pull-right m-r-10" @click="showWordPanel = !showWordPanel">{{trans('general.cancel')}}</button>
                                </form>
                            </div>
                        </div>
                    </transition>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive" v-if="locales.total">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{trans('general.name')}}</th>
                                            <th>{{trans('locale.locale')}}</th>
                                            <th>{{trans('general.created_at')}}</th>
                                            <th>{{trans('general.updated_at')}}</th>
                                            <th class="table-option">{{trans('general.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="locale in locales.data">
                                            <td v-text="locale.name"></td>
                                            <td v-text="locale.locale"></td>
                                            <td>{{locale.created_at | momentDateTime}}</td>
                                            <td>{{locale.updated_at | momentDateTime}}</td>
                                            <td class="table-option">
                                                <div class="btn-group">
                                                    <button class="btn btn-info btn-sm" v-tooltip="trans('locale.edit_locale')" @click.prevent="editLocale(locale)"><i class="fas fa-edit"></i></button>
                                                    <router-link :to="`/configuration/locale/${locale.locale}/auth`" class="btn btn-success btn-sm" v-tooltip="trans('locale.translation')"><i class="fas fa-arrow-circle-right"></i></router-link>
                                                    <button class="btn btn-danger btn-sm" :key="locale.id" v-if="locale.locale !== 'en'" v-confirm="{ok: confirmDelete(locale)}" v-tooltip="trans('locale.delete_locale')"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <module-info v-if="!locales.total" module="locale" title="module_info_title" description="module_info_description" icon="globe"></module-info>
                            <pagination-record :page-length.sync="filterLocaleForm.page_length" :records="locales" @updateRecords="getLocales" @change.native="getLocales"></pagination-record>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import localeForm from './form'

    export default {
        components : { localeForm },
        data() {
            return {
                locales: {
                    total: 0,
                    data: []
                },
                filterLocaleForm: {
                    page_length: helper.getConfig('page_length')
                },
                addWordForm: new Form({
                    word: '',
                    translation: '',
                    module: ''
                }),
                showCreatePanel: false,
                showWordPanel: false,
                modules: []
            };
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

            this.getLocales();
        },
        methods: {
            getLocales(page){
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterLocaleForm);
                axios.get('/api/locale?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.locales = response.locales;
                        this.modules = response.modules;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            editLocale(locale){
                this.$router.push('/configuration/locale/'+locale.id+'/edit');
            },
            confirmDelete(locale){
                return dialog => this.deleteLocale(locale);
            },
            deleteLocale(locale){
                axios.delete('/api/locale/'+locale.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getLocales();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            addWord(){
                this.addWordForm.post('/api/locale/add-word')
                    .then(response => {
                        toastr.success(response.message)
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            toWord(word){
                return helper.toWord(word);
            }
        },
        filters: {
          momentDateTime(date) {
            return helper.formatDateTime(date);
          }
        }
    }
</script>
