@extends('layouts.app')

@section('content')

<head>
    <title>Dashboard</title>
</head>

{{-- handle initial loading animation --}}
@if(Session::get('login') == 1)

<body id="bod" class="animated zoomInDown">
    {{Session::put('login', 0)}}
    @else

    <body id="bod">
        @endif
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                </ol>
            </nav>
            <br>
            <h3>Dashboard</h3>
            <hr><br>
            <!-- Cards -->
            <div class="card-deck">
                <div class="card text-white bg-success mb-3 d-inline-block" style="max-width: 100%;">
                    <div class="row">
                        <div class="col">
                            <div class="card-body">
                                <h1 class="card-title"><strong>{{ $data['approved'] }}</strong></h1>
                                <p class="card-text text-white">Approved Leave Requests</p>
                            </div>
                        </div>
                        <div class="col">
                            <div style="text-align:center">
                                <h3><i class="fa fa-check fa-3x"></i></h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" style="text-align:center">
                        <a class="card-link text-white" href="{{ route('leave.showApproved') }}">More info <i
                                class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                </div>
                <div class="card text-white bg-warning mb-3 d-inline-block" style="max-width: 100%;">
                    <div class="row">
                        <div class="col">
                            <div class="card-body">
                                <h1 class="card-title"><strong>{{ $data['pending'] }}</strong></h1>
                                <p class="card-text text-white">Pending Leave Requests</p>
                            </div>
                        </div>
                        <div class="col">
                            <div style="text-align:center">
                                <h3><i class="fa fa-question fa-3x"></i></h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" style="text-align:center">
                        <a class="card-link text-white" href="{{ route('leave.showPending') }}">More info <i
                                class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                </div>
            </div>
            {{-- add some spacing between cards --}}
            <div class="d-none d-sm-block">
                <br>
            </div>
            <div class="card-deck">
                <div class="card text-white bg-danger mb-3 d-inline-block" style="max-width: 100%;">
                    <div class="row">
                        <div class="col">
                            <div class="card-body">
                                <h1 class="card-title"><strong>{{ $data['rejected'] }}</strong></h1>
                                <p class="card-text text-white">Rejected Leave Requests</p>
                            </div>
                        </div>
                        <div class="col">
                            <div style="text-align:center">
                                <h3><i class="fa fa-times fa-3x"></i></h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" style="text-align:center">
                        <a class="card-link text-white" href="{{ route('leave.showRejected') }}">More info <i
                                class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                </div>
                <div class="card text-white bg-dark mb-3 d-inline-block" style="max-width: 100%;">
                    <div class="row">
                        <div class="col">
                            <div class="card-body">
                                <h1 class="card-title"><strong>{{ $data['all'] }}</strong></h1>
                                <p class="card-text text-white">All My Leave Requests</p>
                            </div>
                        </div>
                        <div class="col">
                            <div style="text-align:center">
                                <h3><i class="fa fa-pencil-square-o fa-3x"></i></h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" style="text-align:center">
                        <a class="card-link text-white" href="{{ route('leave.index') }}">More info <i
                                class="fa fa-arrow-circle-o-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    @endsection
