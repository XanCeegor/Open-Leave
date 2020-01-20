@extends('layouts.app')

@section('content')

<head>
    <!-- Include Bootstrap Datepicker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js">
    </script>
    <title>View Leave Request</title>
</head>

<body>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
            <li class="breadcrumb-item"><a href="/hr/requests">HR</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Leave Request</li>
        </ol>
    </nav>
    <hr><br>

    <div class="row">
        <div class="col-sm-12 col-lg-6">
            <h3>View Leave Request</h3><br>
        </div>
        <div class="col-sm-12 col-lg-6 text-right">
            @if($leavereqs->status == "Approved")
            <h3>Status: <span class="badge badge-pill badge-success">{{ $leavereqs->status }}</h3></span>
            @elseif($leavereqs->status == "Pending")
            <h3>Status: <span class="badge badge-pill badge-warning">{{ $leavereqs->status }}</h3></span>
            @elseif($leavereqs->status == "Rejected")
            <h3>Status: <span class="badge badge-pill badge-danger">{{ $leavereqs->status }}</h3></span>
            @elseif($leavereqs->status == "Cancelled")
            <h3>Status: <span class="badge badge-pill badge-info">{{ $leavereqs->status }}</h3></span>
            @endif
        </div>
    </div>

    <form action="{{ route('hr.reject', $leavereqs->leavenr) }}"
        style="border:2px solid #343a40; border-radius:15px; padding:20px" method="post" id="reqForm"
        autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="name">Requested By</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $leavereqs->user->name }}"
                        readonly>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="name">Submitted On</label>
                    <input type="text" class="form-control" id="name" name="submitted_on"
                        value="{{ $leavereqs->created_at->format('Y-m-d') }}" readonly>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="form-group">
                    <label for="department">Department</label>
                    <input type="text" class="form-control" id="department" name="department"
                        value="{{ $leavereqs->user->department }}" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <label for="currency">Start Date</label>
                <div class="start_date input-group mb-4">
                    <input class="form-control start_date" type="text" readonly value="{{ $leavereqs->start_date }}"
                        id="startdate_datepicker" name="start_date">
                    <div class="input-group-append">
                        <span class="fa fa-calendar input-group-text start_date_calendar" aria-hidden="true "></span>
                    </div>
                </div>
                @if($leavereqs->start_date_radio)
                <div class="form-check-inline">
                    <h3><span class="badge badge-dark">{{ ucfirst($leavereqs->start_date_radio) }}</span></h3>
                </div>
                @endif
                @if($leavereqs->start_date_period)
                <div class="form-check-inline">
                    <h3><i class="fa fa-arrow-right"></i><span class="badge badge-dark"
                            style="margin-left: 14px">{{ ucfirst($leavereqs->start_date_period) }}</span></h3>
                </div>
                @endif
            </div>
            <div class="col-sm-12 col-lg-4">
                <label for="currency">End Date</label>
                <div class="end_date input-group mb-4">
                    <input class="form-control end_date" type="text" readonly value="{{ $leavereqs->end_date }}"
                        id="enddate_datepicker" name="end_date">
                    <div class="input-group-append">
                        <span class="fa fa-calendar input-group-text end_date_calendar" aria-hidden="true "></span>
                    </div>
                </div>
                @if($leavereqs->end_date_radio)
                <div class="form-check-inline">
                    <div class="form-check" style="padding-left:0">
                        <h3><span class="badge badge-dark">{{ ucfirst($leavereqs->end_date_radio) }}</span></h3>
                    </div>
                </div>
                @endif
                @if($leavereqs->end_date_period)
                <div class="form-check-inline">
                    <div class="form-check" style="padding-left:0">
                        <h3><i class="fa fa-arrow-right"></i><span class="badge badge-dark"
                                style="margin-left: 14px">{{ ucfirst($leavereqs->end_date_period) }}</span></h3>
                    </div>
                </div>
                @endif
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="form-group mb-4">
                    <label for="type" name="type_label">Type of Leave</label>
                    <select class="form-control" readonly id="type" name="type">
                        <option>{{ $leavereqs->type }}</option>
                    </select>
                </div>
                <div class="form-check-inline">
                    <div class="form-check" style="padding-left:0">
                        <h3>Duration (days): <span class="badge badge-dark">{{ $leavereqs->duration }}</span></h3>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="reason">Justification</label>
                    @if($leavereqs->reason !== null)
                    <textarea class="form-control z-depth-1" id="exampleFormControlTextarea6" readonly rows="3"
                        name="reason">{{ $leavereqs->reason }}</textarea>
                    @else
                    <textarea class="form-control z-depth-1" id="exampleFormControlTextarea6" readonly rows="3"
                        placeholder="No reason was given by the user." name="reason"></textarea>
                    @endif
                </div>
            </div>
        </div>
        @if($leavereqs->file_name !== null)
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>
                        <h4><span class="badge badge-dark">Supporting Documents</span></h4>
                    </label>
                    <br>
                    • <a href="{{ route('getfile', ['leavenr' => $leavereqs->leavenr]) }}"
                        target="_blank">{{ $leavereqs->file_name }}</a>
                </div>
            </div>
        </div>
        @endif
        @if($holidayList !== [] && $holidayList)
        <hr>
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="form-group">
                    <label for="reason">Falls over {{ $leavereqs->user->country }} Public Holidays</label><br>
                    @foreach($holidayList as $holiday)
                    <h3><span class="badge badge-dark">{{ $holiday }}</span></h3>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        <hr>
        <div class="row">
            <div class="col-sm-12 col-lg-3">
                Days Remaining:
            </div>
            <div class="col-sm-12 col-lg-3">
                Annual Leave <span class="badge badge-pill badge-dark">{{ $leavereqs->user->entitled_days }}</span>
            </div>
            <div class="col-sm-12 col-lg-3">
                Sick Leave <span class="badge badge-pill badge-dark">{{ $leavereqs->user->sick_days }}</span>
            </div>
            <div class="col-sm-12 col-lg-3">
                Family Responsibility Leave <span
                    class="badge badge-pill badge-dark">{{ $leavereqs->user->family_days }}</span>
            </div>
        </div>
        <hr>
        @if($leavereqs->status == "Pending")
        <div class="row">
            <div class="col-sm-12">
                <button type="button" id="approve" name="approve" class="btn btn-success" data-toggle="modal"
                    data-target="#approveModal"><i class="fa fa-check"></i> Approve</button>
                <button type="button" id="reject" name="reject" class="btn btn-danger" data-toggle="modal"
                    data-target="#rejectModal"><i class="fa fa-times"></i> Reject</button>
                <a href="{{ route('hr.edit', ['leavenr' => $leavereqs->leavenr])  }}"><button type="button" id="editBtn"
                        name="editBtn" class="btn btn-secondary"><i class="fa fa-pencil-square-o"></i> Edit</button></a>
                <button type="button" id="cancelBtn" name="cancelBtn" class="btn btn-warning" data-toggle="modal"
                    data-target="#cancelModal"><i class="fa fa-thumbs-down"></i> Cancel</button>
            </div>
        </div>
        @endif
        @if($leavereqs->comment !== null)
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="comment">Rejection Reason</label>
                    <textarea class="form-control z-depth-1" id="comment" readonly rows="3"
                        placeholder="{{ $leavereqs->comment }}" name="comment"></textarea>
                </div>
            </div>
        </div>
        @endif
        @if($leavereqs->status == "Approved")
        <div class="row">
            <div class="col-sm-12">
                <button type="button" id="cancelBtn" name="cancelBtn" class="btn btn-warning" data-toggle="modal"
                    data-target="#cancelModal"><i class="fa fa-thumbs-down"></i> Cancel</button>
            </div>
        </div>
        @endif
        <div id="data" hidden></div>
    </form>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Approve Leave Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to approve this leave request?
                </div>
                <div class="modal-footer">
                    <a href="{{ route('hr.approve', ['leavenr' => $leavereqs->leavenr])  }}"><button type="button"
                            class="btn btn-success">Yes</button></a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reject Leave Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="app-form" id="modal-form" action="{{ route('hr.reject', $leavereqs->leavenr) }}"
                        method="POST">
                        @csrf
                        Please provide a reason for denying the leave request:
                        <hr>
                        <div class="md-form">
                            <textarea type="text" id="reject_reason" name="reject_reason"
                                class="form-control md-textarea" rows="3"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript: formSubmit()" class="btn btn-success">OK</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Leave Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel this leave request?
                </div>
                <div class="modal-footer">
                    <a href="{{ route('leave.cancel', ['leavenr' => $leavereqs->leavenr])  }}"
                        class="btn btn-success">OK</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</body>


<script>
    function formSubmit(){
        $("#data").append($('#reject_reason'));
        document.getElementById("reqForm").submit();
    }
</script>

@endsection
