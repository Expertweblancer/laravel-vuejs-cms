<template>
    <div id="main-wrapper">
        <app-header></app-header>
        <app-sidebar></app-sidebar>

        <div class="page-wrapper">
            <div v-html="message" v-if="!getConfig('mode')"></div>
            <router-view></router-view>
        	<app-footer></app-footer>
        </div>
    </div>
</template>


<script>
    import AppHeader from './header.vue'
    import AppSidebar from './sidebar.vue'
    import AppFooter from './footer.vue'

    export default {
        data(){
            return {
                message: '',
                sidebar: helper.getConfig('user_sidebar') || helper.getConfig('sidebar')
            }
        },
        components: {
            AppHeader, AppSidebar, AppFooter
        },
        mounted() {
            helper.notification();

            $('.scroll-sidebar').slimScroll({
                position: 'left', 
                size: "5px", 
                height: '100%', 
                color: '#dcdcdc'
            });
            $('.message-scroll').slimScroll({
                position: 'right', 
                size: "5px", 
                height: '570', 
                color: '#dcdcdc'
            });
            $('.slimscrollright').slimScroll({
                position: 'left', 
                size: "5px", 
                height: '100%', 
                color: '#dcdcdc'
            });

            $("body").addClass("fix-header fix-sidebar");

            if (this.sidebar == 'mini')
                this.miniSidebar();

            $('#theme').attr('href','/css/colors/'+(helper.getConfig('user_color_theme') || helper.getConfig('color_theme'))+'.css');

            var set = function () {
                    var width = (window.innerWidth > 0) ? window.innerWidth : this.screen.width;
                    var topOffset = 70;
                    if (width < 1170) {
                        $("body").addClass("mini-sidebar");
                        $('.navbar-brand span').hide();
                        $(".scroll-sidebar, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible");
                        $(".sidebartoggler i").addClass("fa-arrow-circle-right");
                        $(".sidebartoggler i").removeClass("fa-arrow-circle-left");
                    }

                    var height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
                    height = height - topOffset;
                    if (height < 1) height = 1;
                    if (height > topOffset) {
                        $(".page-wrapper").css("min-height", (height) + "px");
                    }

            };
            $(window).ready(set);
            $(window).on("resize", set);

            $(document).on('click',".right-sidebar-toggle", function () {
                $(".right-sidebar").slideDown(50);
                $(".right-sidebar").toggleClass("shw-rside");
            });

            $(document).on('click','.sidebartoggler', function () {
                if ($("body").hasClass("mini-sidebar")) {
                    $("body").trigger("resize");
                    $(".scroll-sidebar, .slimScrollDiv").css("overflow", "hidden").parent().css("overflow", "visible");
                    $("body").removeClass("mini-sidebar");
                    $('.navbar-brand span').show();
                    $(".sidebartoggler i").removeClass("fa-arrow-circle-right");
                    $(".sidebartoggler i").addClass("fa-arrow-circle-left");
                }
                else {
                    $("body").trigger("resize");
                    $(".scroll-sidebar, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible");
                    $("body").addClass("mini-sidebar");
                    $('.navbar-brand span').hide();
                    $(".sidebartoggler i").removeClass("fa-arrow-circle-left");
                    $(".sidebartoggler i").addClass("fa-arrow-circle-right");
                }
            });

            $(".fix-header .topbar").stick_in_parent();

            $(document).on('click',".nav-toggler",function () {
                $("body").toggleClass("show-sidebar");
                $(".nav-toggler i").toggleClass("fa-bars");
                $(".nav-toggler i").toggleClass("fa-times");
            });
            $(".sidebartoggler").on('click', function () {
            });

            $('#sidebarnav').metisMenu();
            
            axios.post('/api/demo/message')
                .then(response => response.data)
                .then(response => {
                    this.message  = response;
                })
                .catch(error => {
                });
        },
        methods: {
            miniSidebar(){
                $('body').addClass("mini-sidebar");
                $("body").trigger("resize");
                $('.navbar-brand span').hide();
                $(".scroll-sidebar, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible");
                $(".sidebartoggler i").removeClass("fa-arrow-circle-left");
                $(".sidebartoggler i").addClass("fa-arrow-circle-right");
            },
            normalSidebar(){
                $("body").trigger("resize");
                $('.navbar-brand span').show();
                $(".scroll-sidebar, .slimScrollDiv").css("overflow", "hidden").parent().css("overflow", "visible");
                $('body').removeClass("mini-sidebar");
                $(".sidebartoggler i").removeClass("fa-arrow-circle-right");
                $(".sidebartoggler i").addClass("fa-arrow-circle-left");
            },
            getConfig(config){
                return helper.getConfig(config);
            }
        },
        watch: {
            sidebar(val) {
                if (val == 'mini')
                    this.miniSidebar();
                else
                    this.normalSidebar();

            }
        }
    }
</script>
