<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Scripts -->
    <script src="{{ URL::asset('/js/app.js') }}" defer></script>
    <script src="{{ URL::asset('/js/jquery-3.4.1.min.js') }}"></script>
    <!-- Font Awesome -->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body id="bod">
    @guest
    <main class="py-4">
        @yield('content')
    </main>
    @else
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <div class="dropdown">
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <span><a class="navbar-brand" href="#">Welcome back, {{ Auth::user()->name }}!</a></span>
                <div class="d-none d-lg-block">
                    <div class="dropdown-content">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                Leave Days Available
                            </div>
                        </div>
                        <hr style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                        <div class="row">
                            <div class="col-md-3 text-center" style="font-size: 15px;">
                                Annual: <span
                                    class="badge badge-pill badge-dark">{{ Auth::user()->entitled_days }}</span>
                            </div>
                            <div class="col-md-4 text-center" style="font-size: 15px;">
                                Sick: <span class="badge badge-pill badge-dark">{{ Auth::user()->sick_days }}</span>
                            </div>
                            <div class="col-md-5 text-center" style="font-size: 15px;">
                                Family Responsibility: <span
                                    class="badge badge-pill badge-dark">{{ Auth::user()->family_days }}</span>
                            </div>
                        </div>
                        <hr style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
                        <div class="row">
                            <div class="col-md-12">
                                <small><a href="#" data-toggle="modal" data-target="#leaveCalcModal">How is my leave
                                        calculated?</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Navbar Collapse for responsiveness -->
            <div class="navbar-collapse collapse">
                @if(Session::get('access') > 1)
                <div class="text-white d-none d-lg-block"> | </div>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown">
                            <i class="fa fa-user fa-1x"></i> HR Control Panel
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="/hr/requests/pending">Leave Requests</a>
                            <a class="dropdown-item" href="{{ route('hr.users.index') }}">User List</a>
                        </div>
                    </li>
                </ul>
                @endif
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ml-auto ">
                        <li class="nav-item dropdown d-none d-lg-block">
                            <!-- Notification Tab -->
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-toggle="dropdown">
                                <i class="fa fa-bell fa-1x"></i>
                                <!-- Show number of unread messages next to notification icon -->
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="badge badge-danger"> {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                                @endif
                                </span>
                            </a>
                            <div class="dropdown-menu"
                                style="width: 350px; left: 50%; transform: translateX(-50%); padding: 0">
                                <div class="card text-black d-inline-block" style="width: 350px;">
                                    <div class="card-header" style="background-color:#f0f0f0; color:black; height:45px">
                                        <div class="row">
                                            @if(auth()->user()->unreadNotifications->count() > 0)
                                            <div class="col-md-6">Notifications
                                                (<b>{{ auth()->user()->unreadNotifications->count() }}</b>)</div>
                                            <div class="col-md-6 text-right"><small><a class="notification-link"
                                                        href="{{ route('markallasread') }}">Mark all as read</a></small>
                                            </div>
                                            @else
                                            <div class="col-md-12 text-center">No unread notifications</div>
                                            @endif
                                        </div>
                                    </div>
                                    <ul class="list-group">
                                        @if(auth()->user()->notifications->count() > 0)
                                        @foreach(auth()->user()->unreadNotifications->take(3) as $notification)
                                        @if($notification->data['status'] == "New")
                                        <li class="list-group-item" style="padding: 0rem;">
                                            <div class="card-body" style="padding-top:0">
                                                <div class="row">
                                                    <div class="col-sm-2 align-self-center"
                                                        style="background-color:#ffbb33; border:1px #343a40; border-radius:5px; padding-top:5px; padding-bottom:5px; color:white">
                                                        <i class="fa fa-question-circle-o fa-2x text-center"></i>
                                                    </div>
                                                    <div class="col-sm-10">
                                                        <div
                                                            style="font-size: 90%; padding-bottom:10px; padding-top:10px;">
                                                            <b>{{ $notification->data['requested_by'] }}</b> submitted a
                                                            new <b>{{ $notification->data['duration'] }}</b> day leave
                                                            request. <a class="notification-link"
                                                                href="{{ route('markasread', ['id' => $notification->id, 'leavenr' => $notification->data['leavenr']]) }}"
                                                                style="color:red">View request</a>
                                                        </div>
                                                        <div style="position:absolute; font-size: 12px;">
                                                            <a class="notification-link"
                                                                href="{{ route('markasread', ['id' => $notification->id, 'leavenr' => "null"]) }}"><i
                                                                    class="fa fa-check"> Mark as read</i></a>
                                                        </div>
                                                        <div class="text-right" style="font-size: 12px;">
                                                            <a class="notification-link" href="#"><i
                                                                    class="fa fa-clock-o"> Created:
                                                                    {{ Carbon\Carbon::parse($notification->created_at)->format('Y/m/d') }}</i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @else
                                        <li class="list-group-item" style="padding: 0rem;">
                                            <div class="card-body" style="padding-top:0">
                                                <div class="row">
                                                    @if($notification->data['status'] == "Approved")
                                                    <div class="col-sm-2 align-self-center"
                                                        style="background-color:green; border:1px #343a40; border-radius:5px; padding-top:5px; padding-bottom:5px; color:white">
                                                        <i class="fa fa-check-circle-o fa-2x text-center"></i>
                                                    </div>
                                                    @else
                                                    <div class="col-sm-2 align-self-center"
                                                        style="background-color:red; border:1px #343a40; border-radius:5px; padding-top:5px; padding-bottom:5px; color:white">
                                                        <i class="fa fa-times-circle-o fa-2x text-center"></i>
                                                    </div>
                                                    @endif
                                                    <div class="col-sm-10">
                                                        <div
                                                            style="font-size: 90%; padding-bottom:10px; padding-top:10px;">
                                                            Your leave request for
                                                            <b>{{ $notification->data['duration'] }}</b> days has been
                                                            <b>{{ $notification->data['status'] }}</b>. <a
                                                                class="notification-link"
                                                                href="{{ route('markasread', ['id' => $notification->id, 'leavenr' => $notification->data['leavenr']]) }}"
                                                                style="color:red">View request</a>
                                                        </div>
                                                        <div style="position:absolute; font-size: 12px;">
                                                            <a class="notification-link"
                                                                href="{{ route('markasread', ['leavenr' => "null", 'id' => $notification->id]) }}"><i
                                                                    class="fa fa-check"> Mark as read</i></a>
                                                        </div>
                                                        <div class="text-right" style="font-size: 12px;">
                                                            <a class="notification-link" href="#"><i
                                                                    class="fa fa-clock-o">
                                                                    {{ $notification->data['status'] }}:
                                                                    {{ Carbon\Carbon::parse($notification->updated_at)->format('Y/m/d') }}</i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endif
                                        @endforeach
                                        @if(auth()->user()->unreadNotifications->count() != 0)
                                        <a class="notification-link" href="{{ route('notifications') }}">
                                            <div class="text-center"
                                                style="background-color:#f0f0f0; height:30px; padding-top:5px">View all
                                                ({{ auth()->user()->unreadNotifications->count() }}) unread
                                                notifications</div>
                                            @endif
                                            @endif
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}"><i class="fa fa-home "></i> Home</a>
                        </li>
                        @if(Session::get('access') == 3)
                        <div class="navbar-collapse collapse">
                            <ul class="navbar-nav">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="" id="navbarDropdown" role="button"
                                        data-toggle="dropdown">
                                        <i class="fa fa-cogs "></i> Admin
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href={{ route('changelogs.index') }}>Changelogs</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        @endif
                        {{-- don't show the create option to the leave department  --}}
                        @if(Auth()->user()->username !== env('LEAVE_DEPARTMENT_LDAP_USERNAME'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('leave.create') }}"><i class="fa fa-rocket "></i> Create
                                Request</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" id="logout" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); logout(); document.getElementById('logout-form').submit();"><i
                                    class="fa fa-power-off fa-1x"></i> Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page container -->
    <div class="container">
        <br>
        @if(count($errors) > 0)
        @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{$error}}
        </div>
        @endforeach
        @endif
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{session('success')}}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{session('error')}}
        </div>
        @endif

        <main class="py-4">
            @yield('content')
        </main>
        @endguest

        <!-- Modal -->
        <div class="modal fade" id="leaveCalcModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">How is my leave calculated?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span>
                            -- Annual Leave --<br>
                            Annual leave is accrued - meaning that the number of days to which you are entitled starts
                            at zero and increases with the passage of time as the leave cycle progresses.<br>
                            Therefore, at the start of the leave cycle you would have zero days leave due to you.<br>

                            Each month you acrue 1.25 days to your entitled leave.<br>
                            For example: After 6 months your accrued leave would be 7.5 days.<br><br>

                            -- Sick Leave --<br>
                            As stated by the Department of Labour, workers may take the number of days they would
                            normally work in a 6-week period for sick leave on full pay in a 3 year period.<br>
                            This means you are entitled to 30 days sick leave during your 3 year sick leave cycle. If
                            you are unsure as to what your sick leave cycle is, please contact the Leave
                            Department.<br><br>

                            -- Family Responsibility Leave --<br>
                            Workers are allowed fully paid family responsibility leave of 3 days during each annual
                            leave cycle (12 months).
                        </span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

</body>

</html>

<script>
    // zoom out animation on logout
    function logout() {
        $('#bod').addClass('animated zoomOut');
    }
</script>

<style>
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 500px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        padding: 15px 15px;
        z-index: 1;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }
</style>
