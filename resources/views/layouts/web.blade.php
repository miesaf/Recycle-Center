
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ env("APP_NAME") }}</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset("plugins/fontawesome-free/css/all.min.css") }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{ asset("plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css") }}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset("plugins/icheck-bootstrap/icheck-bootstrap.min.css") }}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset("plugins/jqvmap/jqvmap.min.css") }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset("dist/css/adminlte.min.css?v=3.2.0") }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset("plugins/overlayScrollbars/css/OverlayScrollbars.min.css") }}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset("plugins/daterangepicker/daterangepicker.css") }}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset("plugins/summernote/summernote-bs4.min.css") }}">

    <script data-cfasync="false" nonce="0a9167ba-c847-48a2-ac2b-d3a264e31442">try{(function(w,d){!function(a,b,c,d){if(a.zaraz)console.error("zaraz is loaded twice");else{a[c]=a[c]||{};a[c].executed=[];a.zaraz={deferred:[],listeners:[]};a.zaraz._v="5848";a.zaraz._n="0a9167ba-c847-48a2-ac2b-d3a264e31442";a.zaraz.q=[];a.zaraz._f=function(e){return async function(){var f=Array.prototype.slice.call(arguments);a.zaraz.q.push({m:e,a:f})}};for(const g of["track","set","debug"])a.zaraz[g]=a.zaraz._f(g);a.zaraz.init=()=>{var h=b.getElementsByTagName(d)[0],i=b.createElement(d),j=b.getElementsByTagName("title")[0];j&&(a[c].t=b.getElementsByTagName("title")[0].text);a[c].x=Math.random();a[c].w=a.screen.width;a[c].h=a.screen.height;a[c].j=a.innerHeight;a[c].e=a.innerWidth;a[c].l=a.location.href;a[c].r=b.referrer;a[c].k=a.screen.colorDepth;a[c].n=b.characterSet;a[c].o=(new Date).getTimezoneOffset();if(a.dataLayer)for(const k of Object.entries(Object.entries(dataLayer).reduce(((l,m)=>({...l[1],...m[1]})),{})))zaraz.set(k[0],k[1],{scope:"page"});a[c].q=[];for(;a.zaraz.q.length;){const n=a.zaraz.q.shift();a[c].q.push(n)}i.defer=!0;for(const o of[localStorage,sessionStorage])Object.keys(o||{}).filter((q=>q.startsWith("_zaraz_"))).forEach((p=>{try{a[c]["z_"+p.slice(7)]=JSON.parse(o.getItem(p))}catch{a[c]["z_"+p.slice(7)]=o.getItem(p)}}));i.referrerPolicy="origin";i.src="/cdn-cgi/zaraz/s.js?z="+btoa(encodeURIComponent(JSON.stringify(a[c])));h.parentNode.insertBefore(i,h)};["complete","interactive"].includes(b.readyState)?zaraz.init():a.addEventListener("DOMContentLoaded",zaraz.init)}}(w,d,"zarazData","script");window.zaraz._p=async bs=>new Promise((bt=>{if(bs){bs.e&&bs.e.forEach((bu=>{try{const bv=d.querySelector("script[nonce]"),bw=bv?.nonce||bv?.getAttribute("nonce"),bx=d.createElement("script");bw&&(bx.nonce=bw);bx.innerHTML=bu;bx.onload=()=>{d.head.removeChild(bx)};d.head.appendChild(bx)}catch(by){console.error(`Error executing script: ${bu}\n`,by)}}));Promise.allSettled((bs.f||[]).map((bz=>fetch(bz[0],bz[1]))))}bt()}));zaraz._p({"e":["(function(w,d){})(window,document)"]});})(window,document)}catch(e){throw fetch("/cdn-cgi/zaraz/t"),e;};</script>

    @yield('head')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

    <!-- Navbar -->
    <nav class="navbar navbar-expand navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route("welcome") }}" class="nav-link">Home</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
            </li>
        </ul>
    </nav>

    <!--
    <nav class="navbar navbar-expand bg-body-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route("welcome") }}">
                <img src="{{ asset('assets/img/georecycle-logo.jpg') }}" height="40px">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('welcome') ? 'active' : ''}}" aria-current="page" href="{{ route("welcome") }}">Home</a> </li>
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('map') ? 'active' : ''}}" href="{{ route("map") }}">GeoRecycle Map</a> </li>
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('services') ? 'active' : ''}}" href="{{ route("services") }}">Our Services</a> </li>
                    <li class="nav-item"> <a class="nav-link {{ request()->routeIs('info') ? 'active' : ''}}" href="{{ route("info") }}">Recycling Information</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route("login") }}">Login</a> </li>
                </ul>
            </div>
        </div>
    </nav> -->
    <!-- /.navbar -->

    @yield('body')

    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2024 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 3.2.0
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="{{ asset("plugins/jquery/jquery.min.js") }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset("plugins/jquery-ui/jquery-ui.min.js") }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
    $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset("plugins/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset("plugins/chart.js/Chart.min.js") }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset("plugins/sparklines/sparkline.js") }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset("plugins/jqvmap/jquery.vmap.min.js") }}"></script>
    <script src="{{ asset("plugins/jqvmap/maps/jquery.vmap.usa.js") }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset("plugins/jquery-knob/jquery.knob.min.js") }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset("plugins/moment/moment.min.js") }}"></script>
    <script src="{{ asset("plugins/daterangepicker/daterangepicker.js") }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset("plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js") }}"></script>
    <!-- Summernote -->
    <script src="{{ asset("plugins/summernote/summernote-bs4.min.js") }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset("plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js") }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset("dist/js/adminlte.js?v=3.2.0") }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset("dist/js/demo.js") }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset("dist/js/pages/dashboard.js") }}"></script>

    @yield('body-js')
</body>
</html>
