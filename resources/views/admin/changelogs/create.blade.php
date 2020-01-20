@extends('layouts.app')

@section('content')
<head>
    <!-- Include Bootstrap Datepicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>

    <title>
        Create changelog
    </title>
</head>

<body>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('changelogs.index') }}">Changelogs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create New Changelog</li>
        </ol>
    </nav>
    <hr><br>

    <h3>Create a new changelog</h3><br>
    <form action="{{ route('changelogs.store') }}" style="border:2px solid #343a40; border-radius:15px; padding:20px" method="post" id="reqForm" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-sm-6">
                <label for="date">Date</label>
                <div class="date input-group mb-4">
                    <input class="form-control date" required type="text" placeholder="Date" id="date" name="date" >
                    <div class="input-group-append">
                        <span class="fa fa-calendar input-group-text" aria-hidden="true "></span>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="content">Changes Made</label>
                    <textarea class="form-control z-depth-1" id="content" rows="3" placeholder="Enter changes here..." name="content"></textarea>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-4">
                <button id="submit" name="submit" class="btn btn-success" type="submit"><i class="fa fa-check"></i> Submit</button>
            </div>
        </div>
    </form>
</body>

<script>
    $("#date").datepicker({
        format: 'dd MM yyyy',
        autoclose: true,
        todayHighlight: true
    });
</script>
@endsection



