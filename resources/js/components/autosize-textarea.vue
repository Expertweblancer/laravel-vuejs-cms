<template>
    <textarea :rows="rows" class="form-control" :placeholder="placeholder" :value="value" ref="txtArea" @change="updateContent" :name="name"></textarea>
</template>

<script>
    export default {
        props: {
            name:{
                required: true
            },
            value : {
                default: ''
            },
            rows: {
                default: 1
            },
            placeholder: {
                default: ''
            }
        },
        mounted(){
            autosize($(this.$el));
        },
        methods: {
            updateContent(){
                this.$emit('input',this.$refs.txtArea.value);
            }
        }
    }
</script>
