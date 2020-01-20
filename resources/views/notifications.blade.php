@extends('layouts.app')

@section('content')

<head>
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">

    <title>My Notifications</title>
</head>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Notifications</li>
    </ol>
</nav>
<br><br>
<h3>Unread Notifications</h3>
<hr><br>

<div class="row">
    <div class="col">
        <a href="{{ route('markallasread') }}"><button type="button" class="btn btn-dark">Mark All As Read</button></a>
    </div>
</div>
<br>
<br>
<table id="notificationTable" class="table" style="width:100%">
    <thead class="thead-dark sort-table">
        <tr>
            <th style="width:60%" scope="col" onclick="sortTable(0)"><i class="fa fa-sort-alpha-asc"></i> Notification
            </th>
            <th style="width:25%" scope="col" onclick="sortTable(1)"><i class="fa fa-sort-alpha-asc"></i> Date Created
            </th>
            <th style="width:15%" scope="col">Mark As Read</th>
        </tr>
    </thead>
    <tbody>
        @if(auth()->user()->unreadNotifications->count() > 0)
        @foreach(auth()->user()->unreadNotifications as $notification)
        <tr class='grey lighten-2'>
            @if($notification->data['status'] == "New")
            <td><b>{{ $notification->data['requested_by'] }}</b> submitted a new
                <b>{{ $notification->data['duration'] }}</b> day leave request. <a class="notification-link"
                    href="{{ route('markasread', ['id' => $notification->id, 'leavenr' => $notification->data['leavenr']]) }}"
                    style="color:blue" target="_blank">View request</a></td>
            <td scope='row'>{{ $notification->created_at }}</td>
            <td>
                <h4><a href='{{ route('markasread', ['id' => $notification->id, 'leavenr' => "null"]) }}'><span
                            class="badge badge-pill badge-success"><i class='fa fa-check'></i></span></a></h4>
            </td>
            @elseif($notification->data['status'] == 'Approved')
            <td scope='row'>Your leave request ({{ $notification->data['leavenr'] }}) for
                <b>{{ $notification->data['duration'] }}</b> days has been <b style="color:green">Approved</b>. <a
                    class="notification-link"
                    href="{{ route('markasread', ['id' => $notification->id, 'leavenr' => $notification->data['leavenr']]) }}"
                    style="color:blue" target="_blank">View request</a></td>
            <td scope='row'>{{ $notification->created_at }}</td>
            <td>
                <h4><a href='{{ route('markasread', ['id' => $notification->id, 'leavenr' => "null"]) }}'><span
                            class="badge badge-pill badge-success"><i class='fa fa-check'></i></span></a></h4>
            </td>
            @elseif($notification->data['status'] == 'Rejected')
            <td scope='row'>Your leave request ({{ $notification->data['leavenr'] }}) for
                <b>{{ $notification->data['duration'] }}</b> days has been <b style="color:red">Rejected</b>. <a
                    class="notification-link"
                    href="{{ route('markasread', ['id' => $notification->id, 'leavenr' => $notification->data['leavenr']]) }}"
                    style="color:blue" target="_blank">View request</a></td>
            <td scope='row'>{{ $notification->created_at }}</td>
            <td>
                <h4><a href='{{ route('markasread', ['id' => $notification->id, 'leavenr' => "null"]) }}'><span
                            class="badge badge-pill badge-success"><i class='fa fa-check'></i></span></a></h4>
            </td>
            @endif
            @endforeach
            @endif
        </tr>
    </tbody>
</table>
</div>
</body>

<footer class="page-footer ">
    <br><br>
    <hr>
    <div class="footer text-center py-3">
        Made with ❤
    </div>
</footer>

</html>

<script>
    $(document).ready(function() {
    $('#notificationTable').DataTable( {
        "order": [[ 1, "desc" ]]
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
