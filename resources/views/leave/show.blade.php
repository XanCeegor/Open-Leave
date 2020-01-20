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
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('leave.index') }}">My Leave Requests</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Leave Request</li>
        </ol>
    </nav>
    <hr><br>

    <div class="row">
        <div class="col">
            <h3>View Leave Request</h3><br>
        </div>
        <div class="col text-right">
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

    <form style="border:2px solid #343a40; border-radius:15px; padding:20px" method="post" id="leaveForm"
        autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-4 mb-2">
                <label for="start_date">Start Date</label>
                <div class="start_date input-group mb-4">
                    <input class="form-control start_date" type="text" disabled value="{{ $leavereqs->start_date }}"
                        id="startdate_datepicker" name="start_date">
                    <div class="input-group-append">
                        <span class="fa fa-calendar input-group-text" aria-hidden="true "></span>
                    </div>
                </div>
                <div class="form-check-inline">
                    @if($leavereqs->start_date_radio)
                    <h3><span class="badge badge-dark">{{ ucfirst($leavereqs->start_date_radio) }}</span></h3>
                    @endif
                </div>
                @if($leavereqs->start_date_period)
                <div class="form-check-inline">
                    <h3><i class="fa fa-arrow-right"></i><span class="badge badge-dark"
                            style="margin-left: 14px">{{ ucfirst($leavereqs->start_date_period) }}</span></h3>
                </div>
                @endif
            </div>
            <div class="col-sm-12 col-lg-4 mb-2">
                <label for="end_date">End Date</label>
                <div class="end_date input-group mb-4">
                    <input class="form-control end_date" type="text" disabled value="{{ $leavereqs->end_date }}"
                        id="enddate_datepicker" name="end_date">
                    <div class="input-group-append">
                        <span class="fa fa-calendar input-group-text" aria-hidden="true"></span>
                    </div>
                </div>
                <div class="form-check-inline">
                    <div class="form-check" style="padding-left:0">
                        @if($leavereqs->end_date_radio)
                        <h3><span class="badge badge-dark">{{ ucfirst($leavereqs->end_date_radio) }}</span></h3>
                        @endif
                    </div>
                </div>
                @if($leavereqs->end_date_period)
                <div class="form-check-inline">
                    <h3><i class="fa fa-arrow-right"></i><span class="badge badge-dark"
                            style="margin-left: 14px">{{ ucfirst($leavereqs->end_date_period) }}</span></h3>
                </div>
                @endif
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="form-group mb-4">
                    <label for="type" name="type_label">Type of Leave</label>
                    <select class="form-control" disabled id="type" name="type">
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
            <div class="col-md-12">
                <div class="form-group">
                    <label for="justification">Justification</label>
                    @if($leavereqs->reason !== null)
                    <textarea class="form-control z-depth-1" id="justification" disabled rows="3"
                        placeholder="{{ $leavereqs->reason }}" name="justification"></textarea>
                    @else
                    <textarea class="form-control z-depth-1" id="justification" disabled rows="3"
                        placeholder="No reason was given by the user." name="justification"></textarea>
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
            <div class="col-md-6">
                <div class="form-group">
                    <label>Falls over {{ $leavereqs->user->country }} Public Holidays</label><br>
                    @foreach($holidayList as $holiday)
                    <h3><span class="badge badge-dark">{{ $holiday }}</span></h3>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @if($leavereqs->status == "Pending")
        <hr>
        <div class="row">
            <div class="col-sm-12 col-lg-6">
                <button type="button" id="cancelBtn" name="cancelBtn" class="btn btn-warning" data-toggle="modal"
                    data-target="#cancelModal" @if($leavereqs->seen) disabled @endif><i class="fa fa-thumbs-down"></i>
                    Cancel Request</button>
                @if($leavereqs->seen)
                <small class="col-sm-12 col-lg-6"><a href="/#" data-toggle="modal" data-target="#whyModal">Why can't I
                        cancel my leave request?</a></small>
                @endif
            </div>
        </div>
        @endif
        @if($leavereqs->comment !== null)
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="comment">Rejection Reason</label>
                    <textarea class="form-control z-depth-1" id="comment" disabled rows="3"
                        placeholder="{{ $leavereqs->comment }}" name="comment"></textarea>
                </div>
            </div>
        </div>
        @endif
    </form>

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
                    <a href="{{ route('leave.cancel', ['leavenr' => $leavereqs->leavenr])  }}"><button type="button"
                            class="btn btn-success">Yes</button></a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="whyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Why can't I cancel my leave request?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    You can only cancel your leave request if the leave department has not yet viewed it.
                    If you cannot cancel your request, it means it has already been viewed and the request has begun
                    processing.
                    Please contact and ask the leave department to cancel this request for you.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
@endsection
