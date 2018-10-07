<template>
    <div class="progress" v-if="progress">
        <div class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" :style="{width: getProgress}">
            <span v-if="progress < 100 && showProgressText">
                {{getProgress}}
            </span>
        </div>
    </div>
</template>

<<script>
    export default {
        props: {
            progress: {
                default: 0,
                type: Number
            },
            showProgressText: {
                default: true,
                type: Boolean
            }
        },
        computed: {
            getProgress() {
                return Math.round(this.progress) + "%";
            }
        },
        watch: {
            progress(progress) {
                this.currentProgress = progress;
            }
        },
        data() {
            return {
                currentProgress: 0
            }
        }
    }
</script>
