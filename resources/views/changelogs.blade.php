<head>
    <!-- Scripts -->
    <script src="{{ URL::asset('/js/app.js') }}" defer></script>
    <script src="{{ URL::asset('/js/jquery-3.4.1.min.js') }}"></script>

    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- Bootstrap core CSS -->
    <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <span><a class="navbar-brand" href="{{ route('login') }}">Open Leave</a></span>
        <ul class="navbar-nav ml-auto ">
            @guest
            <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}"><i class="fa fa-key"></i> Login</a>
            </li>
            @else
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}"><i class="fa fa-home"></i> Home</a>
            </li>
            @endguest
        </ul>
    </div>
</nav>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h4>Changelogs</h4>
            @if(count($changes) > 0)
            <ul class="timeline">
                @foreach($changes as $change)
                <li>
                    <a href="#">{{ date('d F Y', strtotime($change->date)) }}</a>
                    <hr>
                    <pre>{{ $change->content }}</pre>
                </li>
                <br>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>

<style>
    pre {
        white-space: pre-wrap;
        font-family: sans-serif;
        font-size: 16px;
    }

    ul.timeline {
        list-style-type: none;
        position: relative;
    }

    ul.timeline:before {
        content: ' ';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 29px;
        width: 2px;
        height: 100%;
        z-index: 400;
    }

    ul.timeline>li {
        margin: 20px 0;
        padding-left: 20px;
    }

    ul.timeline>li:before {
        content: ' ';
        background: white;
        display: inline-block;
        position: absolute;
        border-radius: 50%;
        border: 3px solid #22c0e8;
        left: 20px;
        width: 20px;
        height: 20px;
        z-index: 400;
    }
</style>
