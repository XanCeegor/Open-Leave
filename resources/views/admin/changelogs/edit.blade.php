@extends('layouts.app')

@section('content')

<head>
    <title>
        View Changelog
    </title>
</head>

<body>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('changelogs.index') }}">Changelogs</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Changelog</li>
        </ol>
    </nav>
    <hr><br>

    <h3>View Changelog</h3><br>
    <form action="{{ route('changelogs.update', ['id' => $changes->id]) }}"
        style="border:2px solid #343a40; border-radius:15px; padding:20px" method="post" id="changelogUpdateForm"
        autocomplete="off">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-sm-6">
                <label for="date">Date</label>
                <div class="date input-group mb-4">
                    <input class="form-control date" required type="text" placeholder="Date" id="date" name="date"
                        value="{{ $changes->date }}">
                    <div class="input-group-append">
                        <span class="fa fa-calendar input-group-text"></span>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="content">Changes Made</label>
                    <textarea class="form-control z-depth-1" id="content" rows="3" placeholder="Enter changes here..."
                        name="content">{{ $changes->content }}</textarea>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-4">
                <button id="submit" name="submit" class="btn btn-success" type="submit"><i class="fa fa-save"></i>
                    Save</button>
            </div>
        </div>
    </form>
</body>

<script>
    $("#date").datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });
</script>
@endsection
