<!doctype html>
<html lang="en" data-layout="semibox" data-sidebar-visibility="show" data-topbar="light" data-sidebar="light"
    data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Live Chat Ui </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('Layouts.style')

    @yield('individual_style')

</head>

<body>

   
    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- removeNotificationModal -->
        <div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="NotificationModalbtn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                <h4>Are you sure ?</h4>
                                <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete
                                It!</button>
                        </div>
                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src={{ asset('assets/images/logo-sm.png') }} alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src={{ asset('assets/images/logo-sm.png') }} alt="" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src={{ asset('assets/images/logo-sm.png') }} alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src={{ asset('assets/images/logo-light.png') }} alt="" height="17">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar" data-simplebar="init" class="h-100">
                <div class="simplebar-wrapper" style="margin: 0px;">
                    <div class="simplebar-height-auto-observer-wrapper">
                        <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                            <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                                <div class="simplebar-content" style="padding: 0px;">
                                    <div class="container-fluid">

                                        <div id="two-column-menu">
                                        </div>
                                        <ul class="navbar-nav" id="navbar-nav">

                                            <li class="nav-item navboxicon">
                                                <a class="nav-link menu-link" href="#">
                                                    <i class="ri-account-circle-line iconbox" data-bs-toggle="tooltip"
                                                        data-bs-placement="right"
                                                        data-bs-original-title="Profile"></i>
                                                </a>
                                            </li>

                                            <li class="nav-item navboxicon">
                                                <a class="nav-link menu-link" href="#">
                                                    <i class="bx bx-bell fs-22 iconbox" data-bs-toggle="tooltip"
                                                        data-bs-placement="right"
                                                        data-bs-original-title="Notification"></i>
                                                </a>
                                            </li>

                                            <li class="nav-item navboxicon">
                                                <a class="nav-link menu-link" href="#">
                                                    <i class="las la-cog iconbox" data-bs-toggle="tooltip"
                                                        data-bs-placement="right"
                                                        data-bs-original-title="Settings"></i>
                                                </a>
                                            </li>

                                            <li class="nav-item navboxicon">
                                                <a class="nav-link menu-link" href="{{ route('logout') }}"
                                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    <i class="las la-sign-out-alt iconbox" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="Logout"></i>
                                                </a>
                                            </li>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>

                                        </ul>
                                    </div>
                                    <!-- Sidebar -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 827px;"></div>
                </div>
                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                </div>
                <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                    <div class="simplebar-scrollbar"
                        style="height: 178px; display: block; transform: translate3d(0px, 0px, 0px);"></div>
                </div>
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->

        <!-- Vertical Overlay-->
        <div class="vertical-overlay">

        </div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content me-0 pe-0">

            <div class="container-fluid pe-0">

                <div class="chat-wrapper d-lg-flex gap-1 mt-0 py-0">

                    <div class="chat-leftsidebar">

                        <div class="px-4 pt-4 mb-3">
                            <div class="d-flex align-items-start">
                                <button type="button"
                                    class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                                    id="topnav-hamburger-icon" style="margin-top: -23px;">
                                    <span class="hamburger-icon open">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </span>
                                </button>

                                <div class="flex-shrink-0">
                                    <h5 class="mb-4">Chats</h5>
                                </div>
                            </div>
                            <div class="search-box">
                                <input type="text" class="form-control bg-light border-light" id="searchInput"
                                    placeholder="Search here...">
                                <i class="ri-search-2-line search-icon"></i>
                            </div>
                        </div> <!-- .p-4 -->

                        <div class="tab-content text-muted">

                            <!-- official chat user list  -->

                            @yield('leftsidebar')

                        </div>

                        <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified" role="tablist"
                            style="margin-top: 100px;">

                            <li class="nav-item">
                                <a class="nav-link active" href="#">
                                    <i class="las la-mail-bulk" data-bs-toggle="tooltip" data-bs-placement="Top"
                                        data-bs-original-title="official  Chat" style="font-size: 25px;"></i>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="las la-window-maximize" data-bs-toggle="tooltip"
                                        data-bs-placement="Top" data-bs-original-title="Web Chat"
                                        style="font-size: 25px;"></i>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="lab la-whatsapp" data-bs-toggle="tooltip" data-bs-placement="Top"
                                        data-bs-original-title="WhatsApp Chat" style="font-size: 25px;"></i>
                                </a>
                            </li>

                        </ul>
                        <!-- end tab contact -->
                    </div>

                    <!-- Start User chat -->

                    <!-- official chat conversation  -->

                    <div id="user-chat-layout" class="user-chat w-100 overflow-hidden">                    

                        @yield('coversationLayout')

                    </div>
                    
                </div>
                <!-- end chat-wrapper -->

            </div>
            <!-- container-fluid -->

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!--end offcanvas-->


    <!-- web chat offcanvas  -->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>


    <!-- Theme Settings -->


    <!-- JAVASCRIPT -->
    @include('Layouts.script')

    @yield('individual_script')


</body>

</html>
