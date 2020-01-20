@extends('layouts.app')

@section('content')

<head>
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
    <title>Users</title>
</head>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('hr.index') }}">HR</a></li>
        <li class="breadcrumb-item">Users</li>
    </ol>
</nav>
<br><br>
<h3>Users</h3>
<hr><br>

<table id="userTable" class="table" style="width:100%">
    <thead class="thead-dark sort-table">
        <tr>
            <th style="width:20%" scope="col" onclick="sortTable(0)"><i class="fa fa-sort-alpha-asc"></i> Name</th>
            <th style="width:20%" scope="col" onclick="sortTable(1)"><i class="fa fa-sort-alpha-asc"></i> Leave Days
                Remaining</th>
            <th style="width:20%" scope="col" onclick="sortTable(2)"><i class="fa fa-sort-alpha-asc"></i> Leave Days
                Taken</th>
            <th style="width:20%" scope="col" onclick="sortTable(3)"><i class="fa fa-sort-alpha-asc"></i> Sick Days
                Remaining</th>
            <th style="width:10%" scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($users) > 0)
        @foreach($users as $user)
        <tr class='table-info'>
            <td scope='row'>{{ $user->name }}</th>
            <td scope='row'>{{ $user->entitled_days }} Days</th>
            <td scope='row'>{{ $user->days_taken }} Days</th>
            <td scope='row'>{{ $user->sick_days }} Days</th>
            <td>
                <h4>
                    <a href='{{ route('hr.users.edit', ['username' => $user->username]) }}'><span
                            class="badge badge-pill badge-secondary"><i class='fa fa-wrench'></i></span></a>
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
    $('#userTable').DataTable( {
        "order": [[ 0, "asc" ]]
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
