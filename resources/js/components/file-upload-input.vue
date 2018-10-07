<template>
  <div>
    <div class="input-group">
        <button type="button" class="btn btn-info" @click="launchFilePicker" :disabled="isUploadDisabled"><i class='fas fa-upload'></i> {{buttonText}}</button>
    </div>

    <input type="file" style="display:none" ref="file" v-uploader />

    <file-upload-progress :progress="progress" style="margin-top: 10px;"></file-upload-progress>
    <button type="button" class="btn btn-danger btn-sm m-t-10" @click="cancelUpload" v-tooltip="trans('upload.cancel_upload')" v-if="progress || isFileSelected"><i class="fas fa-trash"></i></button>

    <ul class="upload-file-list m-t-20" v-if="uploaded_files">
        <li v-for="uploaded_file in uploaded_files">
            <i class="fas fa-file"></i> {{uploaded_file.user_filename}}
            <span class="btn btn-danger btn-xs" :key="uploaded_file.id" v-confirm="{ok: confirmDelete(uploaded_file)}" v-tooltip="trans('general.delete_upload')"><i class="fas fa-trash"></i></span>
        </li>
    </ul>
  </div>
</template>

<script>
    import fileUploadProgress from './file-upload-progress.vue'

    export default {
        components : { fileUploadProgress },
        props: {
            buttonText: {
                default: 'Choose File',
            },
            token: {
                required: true
            },
            module: {
                required: true
            },
            moduleId: {
                default: ''
            },
            clearFile: {
                default: false
            }
        },
        directives: {
            uploader: {
              bind(el, binding, vnode) {
                el.addEventListener('change', e => {
                  vnode.context.file = e.target.files[0];
                });
              }
            },
        },
        watch: {
            file(file){
                let fileExtension = file.name.substr((file.name.lastIndexOf('.') + 1));

                if(this.allowed_file_extensions.indexOf(fileExtension) == -1){
                    toastr.error(i18n.general.file_not_allowed);
                    this.isUploadDisabled = false;
                } else if(file.size > helper.getConfig('post_max_size')){
                    toastr.error(i18n.general.file_too_large);
                    this.isUploadDisabled = false;
                } else {
                    let formData = new FormData();
                    formData.append('file', file);
                    formData.append('token', this.token);
                    formData.append('module', this.module);
                    formData.append('module_id', this.moduleId || '');
                    axios.post('/api/upload',formData, {
                        onUploadProgress: progressEvent => {
                            this.progress = Math.round( (progressEvent.loaded * 100) / progressEvent.total );
                        }
                    }).then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.uploaded_files.push(response.upload);
                    }).catch(error => {
                        if(error.response.status == 413)
                            toastr.error(i18n.general.file_too_large);
                        else
                            helper.showDataErrorMsg(error);
                    }).then(() => {
                        this.progress = 0;
                        this.isUploadDisabled = false;
                    });
                    this.$refs.file.value = '';
                }
            },
            clearFile(val){
                this.uploaded_files = [];
            },
            moduleId(val){
                if(val)
                    this.fetchUploads();
            }
        },
        mounted(){
            if(this.moduleId)
                this.fetchUploads();

            axios.post('/api/upload/extension',{module: this.module})
                .then(response => response.data)
                .then(response => {
                    this.allowed_file_extensions = response;
                })
                .catch(error => {

                });
        },
        computed: {
            authToken(){
                return localStorage.getItem('auth_token');
            },
            isFileSelected(){
                return this.isUploadDisabled ? true : false;
            }
        },
        methods: {
            launchFilePicker() {
                this.isUploadDisabled = true;
                this.$refs.file.click();
            },
            cancelUpload(){
                if (this.request) {
                    this.request.abort();
                }
                this.isUploadDisabled = false;
            },
            confirmDelete(uploaded_file){
                return dialog => this.deleteUpload(uploaded_file);
            },
            deleteUpload(uploaded_file){
                axios.post('/api/upload/'+uploaded_file.id,{
                    token: uploaded_file.upload_token,
                    module_id: this.moduleId || ''
                }).then(response => response.data)
                .then(response => {
                    this.uploaded_files = this.uploaded_files.filter(function (item) {
                        return uploaded_file.id != item.id;
                    });
                    toastr.success(response.message);
                }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
            },
            fetchUploads(){
                this.uploaded_files = [];
                axios.post('/api/upload/fetch',{
                    module: this.module,
                    module_id: this.moduleId
                })
                .then(response => response.data)
                .then(response => {
                    this.uploaded_files = response;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
            }
        },
        data() {
            return {
              file: '',
              isUploadDisabled: false,
              progress: 0,
              uploaded_files: [],
              allowed_file_extensions: []
            }
        }
    }
</script>

<style>
    .upload-file-list{
        list-style: none;
        padding:0px;
    }
</style>
