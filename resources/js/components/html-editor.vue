<template>
    <textarea class="form-control"></textarea>
</template>

<<script>
    export default{
        props : {
            isUpdate: {
                default: false,
            },
            model: {
                required: true
            },
            height: {
                type: String,
                default: '150'
            }
        },
        data(){
            return {
                loadContent: false
            }
        },
        mounted() {
            let config = {
                height: this.height,
                fontNames: ['sans-serif'],
                fontNamesIgnoreCheck: ['sans-serif'],
                disableResizeEditor: true
            };
            let vm = this;
            config.callbacks = {
                onInit: function () {
                    $(vm.$el).summernote("code", vm.model);
                },
                onChange: function () {
                    vm.$emit('update:model', $(vm.$el).summernote('code'));
                    vm.$emit('clearErrors');
                },
                onBlur: function () {
                    vm.$emit('update:model', $(vm.$el).summernote('code'));
                },
                onImageUpload: function(files) {
                    vm.sendFile(files[0]);
                }
            };
            $(this.$el).summernote(config);
        },
        methods: {
            sendFile(file){
                var data = new FormData();
                data.append("file", file);
                axios.post('/api/upload/image',data)
                    .then(response => response.data)
                    .then(response => {
                        $(this.$el).summernote('insertImage', response.image_url);
                    })
                    .catch(error => {
                        if(error.response.status == 413 || error.response.status == 500)
                            toastr.error(i18n.general.file_too_large);
                        else if(error.response.status == 422)
                            toastr.error(error.response.data.errors.file[0]);
                        else
                            helper.showDataErrorMsg(error);
                    })
            }
        },
        watch: {
            model(val) {
                if (!this.loadContent && this.isUpdate) {
                    $(this.$el).summernote("code", this.model);
                    this.loadContent = true;
                }
                if(!this.model)
                    $(this.$el).summernote("code", '');
            },
            isUpdate(val) {
                this.loadContent = val;
            }
        },
    }
</script>
