@extends('layouts.app')

@section('content')

<head>
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
    <title>User Leave Requests</title>
</head>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        {!! $data['root'] !!}
        @isset($data['status'])
        <li class="breadcrumb-item active">{{ $data['status'] }}</li>
        @endisset
    </ol>
</nav>
<br><br>

@isset($data['status'])
<h3>{{$data['status']}}</h3>
@endisset
@empty($data['status'])
<h3>User Leave Requests</h3>
@endempty
<hr>
<br>

<div class="row">
    <div class="col" id="colsort">
        <div class="btn-group dropright">
            <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                Sort by Status
            </button>
            <div class="dropdown-menu" x-placement="right-start"
                style="position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;">
                <!-- Select active button based on what it's sorted by -->
                {!! $data['sort'] !!}
            </div>
        </div>
    </div>
    <div class="col text-right">
        <a href="/hr/create"><button type="button" class="btn btn-dark">Create New</button></a>
    </div>
</div>
<br>
<br>
<table id="leaveTable" class="table" style="width:100%">
    <thead class="thead-dark sort-table">
        <tr>
            <th style="width:14%" scope="col" onclick="sortTable(0)"><i class="fa fa-sort-alpha-asc"></i> Requested By
            </th>
            <th style="width:12%" scope="col" onclick="sortTable(1)"><i class="fa fa-sort-alpha-asc"></i> From</th>
            <th style="width:12%" scope="col" onclick="sortTable(2)"><i class="fa fa-sort-alpha-asc"></i> Until</th>
            <th style="width:11%" scope="col" onclick="sortTable(3)"><i class="fa fa-sort-alpha-asc"></i> Duration</th>
            <th style="width:12%" scope="col" onclick="sortTable(4)"><i class="fa fa-sort-alpha-asc"></i> Type</th>
            <th style="width:11%" scope="col" onclick="sortTable(5)"><i class="fa fa-sort-alpha-asc"></i> Status</th>
            <th style="width:12%" scope="col" onclick="sortTable(6)"><i class="fa fa-sort-alpha-asc"></i> Submitted</th>
            <th style="width:12%" scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($data['leavereqs']) > 0)
        @foreach($data['leavereqs'] as $leave)
        @if($leave->status == "Pending")
        <tr class='table-warning'>
            @elseif($leave->status == "Approved")
        <tr class='table-success'>
            @elseif($leave->status == "Rejected")
        <tr class='table-danger'>
            @endif
            <td scope='row'>{{ $leave->requested_by }}</td>
            <td scope='row'>{{ $leave->start_date }}</td>
            <td scope='row'>{{ $leave->end_date }}</td>
            <td scope='row'>{{ $leave->duration }} Days</td>
            <td scope='row'>{{ $leave->type }}</td>
            <td scope='row'>{{ $leave->status }}</td>
            <td scope='row'>{{ $leave->created_at->format('Y-m-d') }}</td>
            <td>
                <h4>
                    <a href='{{ route('hr.show', ['leavenr' => $leave->leavenr]) }}'><span
                            class="badge badge-pill badge-primary"><i class='fa fa-eye'></i></span></a>
                    @if($leave->status == "Pending")
                    <a href='{{ route('hr.edit', ['leavenr' => $leave->leavenr]) }}'><span
                            class="badge badge-pill badge-secondary"><i class='fa fa-wrench'></i></span></a>
                    @endif
                </h4>
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
</div>
</body>
<br><br>
<hr>
<footer class="page-footer ">
    <div class="footer text-center py-3">
        Made with ❤
    </div>
</footer>

</html>

<script>
    $(document).ready(function() {
    $('#leaveTable').DataTable( {
        "order": [[ 6, "desc" ]]
    });
});
</script>

<style>
    .page-footer {
        background-color: #343a40;
        color: white;
    }
</style>

@endsection
