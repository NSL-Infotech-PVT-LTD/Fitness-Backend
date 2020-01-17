<!DOCTYPE HTML>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="keywords" content="" />
        <title>{{ config('app.name', 'Laravel') }} | Admin</title>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
        <!-- Bootstrap Core CSS -->
        <link href="{{asset('adminMedia/css/bootstrap.css')}}" rel='stylesheet' type='text/css' />
        <!-- Custom CSS -->
        <link href="{{asset('adminMedia/css/style.css')}}" rel='stylesheet' type='text/css' />
        <!-- font CSS -->
        <!-- font-awesome icons -->
        <link href="{{asset('adminMedia/css/font-awesome.css')}}" rel="stylesheet">
        <!-- //font-awesome icons -->
        <!-- js-->
        <script src="{{asset('adminMedia/js/jquery-1.11.1.min.js')}}"></script>
        <script src="{{asset('adminMedia/js/modernizr.custom.js')}}"></script>
        <!--webfonts-->
        <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
        <!--datatable starts-->

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />-->
        <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>-->
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>-->
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>-->
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

        <!--datatable ends-->
        <!--//webfonts-->
        <!--animate-->
        <link href="{{asset('adminMedia/css/animate.css')}}" rel="stylesheet" type="text/css" media="all">
        <script src="{{asset('adminMedia/js/wow.min.js')}}"></script>
        <script>
new WOW().init();
        </script>
        <!--//end-animate-->
        <!-- Metis Menu -->
        <script src="{{asset('adminMedia/js/metisMenu.min.js')}}"></script>
        <script src="{{asset('adminMedia/js/custom.js')}}"></script>
        <link href="{{asset('adminMedia/css/custom.css')}}" rel="stylesheet">
        <!--//Metis Menu -->
    </head>
    <body class="cbp-spmenu-push">
        <div class="main-content">
            <!--left-fixed -navigation-->
            <div class=" sidebar" role="navigation">
                <div class="navbar-collapse">
                    <nav class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="cbp-spmenu-s1">
                        <ul class="nav" id="side-menu">
                            <li>
                                <a href="{{ url('/admin') }}"><i class="fa fa-home nav_icon"></i>Dashboard</a>
                            </li>
                            <li>
                                <a href="{{ url('admin/users/role/1') }}"><i class="fa fa-user nav_icon"></i>users<span class=""></span></a>
                                <ul class="nav nav-second-level collapse">
                                    <?php foreach (\App\Role::all() as $role): ?>
                                        <li>
                                            <a href="{{ url('admin/users/role/'.$role->id) }}">{{$role->name}}<span class=""></span></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <li>
                                <a href="{{ url('/admin/services') }}"><i class="fa fa-bars nav_icon"></i>Services</a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/events') }}"><i class="fa fa-bars nav_icon"></i>Events</a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/spaces') }}"><i class="fa fa-bars nav_icon"></i>Spaces</a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/session') }}"><i class="fa fa-bars nav_icon"></i>Session</a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/configuration') }}"><i class="fa fa-bars nav_icon"></i>Configuration</a>
                            </li>
                            <li>
                                <a href="{{ url('/admin/contact') }}"><i class="fa fa-bars nav_icon"></i>Contact Portal</a>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                        <!-- //sidebar-collapse -->
                    </nav>
                </div>
            </div>
            <!--left-fixed -navigation-->
            <!-- header-starts -->
            <div class="sticky-header header-section ">
                <div class="header-left">
                    <!--toggle button start-->
                    <button id="showLeftPush"><i class="fa fa-bars"></i></button>
                    <!--toggle button end-->
                    <!--logo -->
                    <div class="logo">
                        <a href="">
                            <h1>{{ config('app.name')}}</h1>
                            <span>AdminPanel</span>
                        </a>
                    </div>
                    <div class="clearfix"> </div>
                </div>
                <div class="header-right">
                    <div class="profile_details_left"><!--notifications of menu start -->
                        <ul class="nofitications-dropdown">
                            <li class="dropdown head-dpdn">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-bell"></i><span class="badge blue">3</span></a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div class="notification_header">
                                            <h3>You have 3 new notification</h3>
                                        </div>
                                    </li>
                                    <li><a href="#">
                                            <div class="user_img"><img src="images/2.png" alt=""></div>
                                            <div class="notification_desc">
                                                <p>Lorem ipsum dolor amet</p>
                                                <p><span>1 hour ago</span></p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </a></li>
                                    <li class="odd"><a href="#">
                                            <div class="user_img"><img src="images/1.png" alt=""></div>
                                            <div class="notification_desc">
                                                <p>Lorem ipsum dolor amet </p>
                                                <p><span>1 hour ago</span></p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </a></li>
                                    <li><a href="#">
                                            <div class="user_img"><img src="images/3.png" alt=""></div>
                                            <div class="notification_desc">
                                                <p>Lorem ipsum dolor amet </p>
                                                <p><span>1 hour ago</span></p>
                                            </div>
                                            <div class="clearfix"></div>
                                        </a></li>
                                    <li>
                                        <div class="notification_bottom">
                                            <a href="#">See all notifications</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <div class="clearfix"> </div>
                    </div>
                    <!--notification menu end -->
                    <div class="profile_details">
                        <ul>
                            <li class="dropdown profile_details_drop">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <div class="profile_img">
                                        <span class="prfil-img"><img src="images/a.png" alt=""> </span>
                                        <div class="user-name">
                                            <p>Admin</p>
                                            <span>Administrator</span>
                                        </div>
                                        <i class="fa fa-angle-down lnr"></i>
                                        <i class="fa fa-angle-up lnr"></i>
                                        <div class="clearfix"></div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu drp-mnu">
                                    <li> <a href="#"><i class="fa fa-cog"></i> Settings</a> </li>
                                    <li> <a href="#"><i class="fa fa-user"></i> Profile</a> </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ url('/logout') }}"
                                           onclick="event.preventDefault();
    document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i>
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="clearfix"> </div>
                </div>
                <div class="clearfix"> </div>
            </div>
            <!-- //header-ends -->
            <!-- main content start-->
            <div id="page-wrapper">
                <div class="main-page">
                    <h3 class="title1">{{ config('app.name')}}</h3>
                    <div class="blank-page widget-shadow scroll" id="style-2 div1">
                        @yield('content')
                    </div>
                </div>
            </div>
            <!--footer-->
            <div class="footer">
                <p>&copy; 2019 {{ config('app.name')}} Admin Panel. All Rights Reserved | Devloped by netscapelabs <a href="#">{{ config('app.name')}}</a></p>
            </div>
            <!--//footer-->
        </div>
        <!-- Classie -->
        <script src="{{asset('adminMedia/js/classie.js')}}"></script>
        <script>
var menuLeft = document.getElementById('cbp-spmenu-s1'),
        showLeftPush = document.getElementById('showLeftPush'),
        body = document.body;

showLeftPush.onclick = function () {
    classie.toggle(this, 'active');
    classie.toggle(body, 'cbp-spmenu-push-toright');
    classie.toggle(menuLeft, 'cbp-spmenu-open');
    disableOther('showLeftPush');
};

function disableOther(button) {
    if (button !== 'showLeftPush') {
        classie.toggle(showLeftPush, 'disabled');
    }
}
        </script>
        <!--scrolling js-->
        <script src="{{asset('adminMedia/js/jquery.nicescroll.js')}}"></script>
        <script src="{{asset('adminMedia/js/scripts.js')}}"></script>
        <!--//scrolling js-->
        <!-- Bootstrap Core JavaScript -->
        <script src="{{asset('adminMedia/js/bootstrap.js')}}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    </body>
</html>
