<!DOCTYPE html>
<html lang="en" @if((!cache('direction') && config('config.direction') == 'rtl') || cache('direction') == 'rtl') dir="rtl" @endif>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="ScriptMint">
        <title>{{env('APP_NAME')}}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="shortcut icon" href="/images/favicon.png">
        @if((!cache('direction') && config('config.direction') == 'rtl') || cache('direction') == 'rtl')
            <link href="{{ ('/css/style-rtl.css') }}" id="direction" rel="stylesheet">
        @else
            <link href="{{ ('/css/style.css') }}" id="direction" rel="stylesheet">
        @endif
        <link href="{{ ('/css/colors/'.(config('config.color_theme') ? : 'green').'.css') }}" id="theme" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="fix-header fix-sidebar">
        <div class="preloader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
        </div>
        <div id="root">
            <router-view></router-view>
        </div>
        <script src="/js/lang"></script>
        <script src="{{ mix('/js/plugin.js') }}"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
