<template>
    <section id="wrapper" class="error-page">
        <div class="error-box">
            <div class="error-body text-center">
                <h2>IP Restricted</h2>
                <p class="text-muted m-t-30 m-b-30">Unfortunately, This IP is not allowed to access this page!</p>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        mounted() {
            if(!localStorage.getItem('ip_restricted'))
                this.$router.push('/home');
        }
    }
</script>
