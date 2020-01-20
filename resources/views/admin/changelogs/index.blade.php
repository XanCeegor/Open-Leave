@extends('layouts.app')

@section('content')

<head>
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
    <title>Changelogs</title>
</head>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Changelogs</li>
    </ol>
</nav>
<br><br>
<h3>Changelogs</h3>
<hr>
<br>

<div class="row">
    <div class="col text-right">
        <a href="{{ route('changelogs.create') }}"><button type="button" class="btn btn-dark">Create New</button></a>
    </div>
</div>
<br><br>

<table id="changeTable" class="table" style="width:100%">
    <thead class="thead-dark sort-table">
        <tr>
            <th style="width:10%" scope="col" onclick="sortTable(0)"><i class="fa fa-sort-alpha-asc"></i> Date</th>
            <th style="width:75%" scope="col" onclick="sortTable(1)"><i class="fa fa-sort-alpha-asc"></i> Content</th>
            <th style="width:15%" scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(count($changes) > 0)
        @foreach($changes as $change)
        <tr class='table-info'>
            <td scope='row'>{{ $change->date }}</th>
            <td scope='row'>{{ str_limit($change->content, 100) }}</th>
            <td>
                <h4>
                    <a href='{{ route('changelogs.show', ['id' => $change->id]) }}'><span
                            class="badge badge-pill badge-info"><i class='fa fa-eye'></i></span></a>
                    <a href='{{ route('changelogs.edit', ['id' => $change->id]) }}'><span
                            class="badge badge-pill badge-secondary"><i class='fa fa-wrench'></i></span></a>
                    <a href='{{ route('changelogs.destroy', ['id' => $change->id]) }}'><span
                            class="badge badge-pill badge-danger"><i class='fa fa-times'></i></span></a>
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
    <style>
        .page-footer {
            background-color: #343a40;
            color: white;
        }
    </style>
    <div class="footer text-center py-3">
        Made with ❤
    </div>
</footer>

</html>

<script>
    $(document).ready(function() {
    $('#changeTable').DataTable( {
        "order": [[ 0, "desc" ]]
    });
});
</script>
@endsection
