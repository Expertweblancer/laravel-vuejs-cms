<template>
    <div>
        <div class="page-titles p-3 border-bottom">
            <h3 class="text-themecolor">{{trans('locale.translation')}} ({{locale.name}}) 
                <button class="btn btn-info btn-sm pull-right" @click="$router.push('/home')"><i class="fas fa-home"></i> <span class="d-none d-sm-inline">{{trans('general.home')}}</span></button>
                <button class="btn btn-info btn-sm pull-right m-r-10" @click="$router.push('/configuration/locale')"><i class="fas fa-globe"></i> <span class="d-none d-sm-inline">{{trans('locale.locale')}}</span></button>
                <div class="dropdown pull-right m-r-10">
                  <button type="button" style="margin-top:-5px;" class="btn btn-info btn-sm" href="#" role="button" id="moduleLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-boxes"></i> <span class="d-none d-sm-inline">{{trans('locale.module')}}</span>
                  </button>
                  <div :class="['dropdown-menu',getConfig('direction') == 'ltr' ? 'dropdown-menu-right' : '']" aria-labelledby="moduleLink">
                    <button style="cursor:pointer;" class="dropdown-item" v-for="mod in modules" @click="$router.push('/configuration/locale/'+locale.locale+'/'+mod)">
                        {{toWord(mod)}} <span v-if="mod == module" class="pull-right"><i class="fas fa-check"></i></span> 
                    </button>
                  </div>
                </div>
            </h3>
        </div>
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <show-tip module="locale" tip="tip_translation"></show-tip>
                            <form>
                                <div v-if="getWordCount">
                                    <div class="row">
                                        <template v-for="(word,index) in words">
                                            <template v-if="typeof word === 'object'">
                                                <div class="col-12 col-sm-4" v-for="(wrd,i) in word">
                                                    <div class="form-group">
                                                        <label for="" style="color:red;">{{trans(module+'.'+index+'.'+i)}}</label>
                                                        <!-- <label for="">{{index}}_{{i}}</label> -->
                                                        <input class="form-control" type="text" value="" v-model="words[index][i]" :name="`${index}_${i}`">
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label for="" style="color:red;">{{trans(module+'.'+index)}}</label>
                                                        <!-- <label for="">{{index}}</label> -->
                                                        <input class="form-control" type="text" value="" v-model="words[index]" :name="index">
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-info btn-sm pull-right" @click="saveTranslation">Save</button>
                                    </div>
                                </div>
                                <div v-if="!getWordCount">
                                    <p class="alert alert-danger">No record found!</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        data() {
            return {
                modules: {},
                words: {},
                locale: {},
                module: (this.$route.params.module) ? this.$route.params.module : 'auth'
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

            this.fetchWords();
        },
        methods: {
            fetchWords(){
                axios.post('/api/locale/fetch',{
                    locale: this.$route.params.locale,
                    module: this.module
                    })
                    .then(response => response.data)
                    .then(response => {
                        this.modules = response.modules;
                        this.words = response.words;
                        this.locale = response.locale;
                    }).catch(error => {
                        this.$router.push('/configuration/locale');
                        helper.showDataErrorMsg(error);
                    });
            },
            getName(name){
                name = helper.ucword(name);
                return name.replace(/_/g, ' ');
            },
            getModuleLink(module){
                return '/configuration/locale/'+this.$route.params.locale+'/'+module
            },
            saveTranslation(){
                axios.post('/api/locale/translate',{
                    locale: this.$route.params.locale,
                    module: this.module,
                    words: this.words
                }).then(response => response.data)
                .then(response => {
                    toastr.success(response.message);
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
            },
            getConfig(config){
                return helper.getConfig(config);
            },
            toWord(word){
                return helper.toWord(word);
            }
        },
        watch: {
            '$route.params.module'(newModule, oldModule) {
                this.module = newModule;
                this.fetchWords();
            }
        },
        computed: {
            getWordCount(){
                return _size(this.words);
            }
        }
    }
</script>
<style>
    .list-group-item .active {color:#ffffff;}
</style>
