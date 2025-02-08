@include('layouts.admin.header')
    <body class="sb-nav-fixed">
    @include('layouts.admin.navbar')
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                @include('layouts.admin.sidebar')
            </div>
            <div id="layoutSidenav_content">
                {{--@include('layouts.admin.body')--}}
                @yield('content')
                @include('layouts.admin.footer')
            </div>
        </div>
    </body>
</html>
