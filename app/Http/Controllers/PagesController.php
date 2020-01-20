<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Leave;
use App\Change;
use Auth;

class PagesController extends Controller
{
    private $rootLeave = '<li class="breadcrumb-item"><a href="/leave">My Leave Requests</a></li>';     //root breadcrumb
    private $rootHR = '<li class="breadcrumb-item"><a href="/hr/requests/">HR</a></li>';     //root breadcrumb

    //dashboard
    public function index()
    {
        if (auth()->user()->username == "cld") {
            return $this->showHRLeave();
        }

        $data = [
            'approved' => count(Leave::where('username', '=', Auth::user()->username)->where('status', '=', 'approved')->get()),
            'pending' => count(Leave::where('username', '=', Auth::user()->username)->where('status', '=', 'pending')->get()),
            'rejected' => count(Leave::where('username', '=', Auth::user()->username)->where('status', '=', 'rejected')->get()),
            'all' => count(Leave::where('username', '=', Auth::user()->username)->get()),
        ];

        return view('dashboard', compact('data'));
    }



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////                        USER LEAVE                               ////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function showLeave()
    {
        $rootLeave = '<li class="breadcrumb-item">My Leave Requests</li>';
        $leavereqs = Leave::where('username', Auth::user()->username)->get();
        $sort = '<a class="dropdown-item active" href="#">Any</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/leave/approved">Approved</a>
                <a class="dropdown-item" href="/leave/pending">Pending</a>
                <a class="dropdown-item" href="/leave/rejected">Rejected</a>
                <a class="dropdown-item" href="/leave/cancelled">Cancelled</a>';

        $data = [
            'leavereqs' => $leavereqs,
            'root' => $rootLeave,
            'sort' => $sort
        ];

        return view('leave.index', compact('data'));
    }

    public function showLeaveApproved()
    {
        $status = "Approved Leave";
        $leavereqs = Leave::where('status', 'Approved')->where('username', Auth::user()->username)->get();
        $sort = '<a class="dropdown-item" href="/leave">Any</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item active" href="#">Approved</a>
        <a class="dropdown-item" href="/leave/pending">Pending</a>
        <a class="dropdown-item" href="/leave/rejected">Rejected</a>
        <a class="dropdown-item" href="/leave/cancelled">Cancelled</a>';

        $data = [
            'leavereqs' => $leavereqs,
            'root' => $this->rootLeave,
            'status' => $status,
            'sort' => $sort
        ];

        return view('leave.index', compact('data'));
    }


    public function showLeavePending()
    {
        $status = "Pending Leave";
        $leavereqs = Leave::where('status', 'Pending')->where('username', Auth::user()->username)->get();
        $sort = '<a class="dropdown-item" href="/leave">Any</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/leave/approved">Approved</a>
                <a class="dropdown-item active" href="#">Pending</a>
                <a class="dropdown-item" href="/leave/rejected">Rejected</a>
                <a class="dropdown-item" href="/leave/cancelled">Cancelled</a>';

        $data = [
            'leavereqs' => $leavereqs,
            'root' => $this->rootLeave,
            'status' => $status,
            'sort' => $sort
        ];

        return view('leave.index', compact('data'));
    }


    public function showLeaveRejected()
    {
        $status = "Rejected Leave";
        $leavereqs = Leave::where('status', 'Rejected')->where('username', Auth::user()->username)->get();
        $sort = '<a class="dropdown-item" href="/leave">Any</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/leave/approved">Approved</a>
                <a class="dropdown-item" href="/leave/pending">Pending</a>
                <a class="dropdown-item active" href="#">Rejected</a>
                <a class="dropdown-item" href="/leave/cancelled">Cancelled</a>';

        $data = [
            'leavereqs' => $leavereqs,
            'root' => $this->rootLeave,
            'status' => $status,
            'sort' => $sort
        ];

        return view('leave.index', compact('data'));
    }

    public function showLeaveCancelled()
    {
        $status = "Cancelled Leave";
        $leavereqs = Leave::where('status', 'Cancelled')->where('username', Auth::user()->username)->get();
        $sort = '<a class="dropdown-item" href="/leave">Any</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/leave/approved">Approved</a>
                <a class="dropdown-item" href="/leave/pending">Pending</a>
                <a class="dropdown-item" href="/leave/rejected">Rejected</a>
                <a class="dropdown-item active" href="/leave/cancelled">Cancelled</a>';

        $data = [
            'leavereqs' => $leavereqs,
            'root' => $this->rootLeave,
            'status' => $status,
            'sort' => $sort
        ];

        return view('leave.index', compact('data'));
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////                        HR LEAVE                                 ////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function showHRLeave()
    {
        $rootHR = '<li class="breadcrumb-item">HR</li>';
        $leavereqs = Leave::all()->where('status', '!==', 'Cancelled'); //we don't show cancelled leave requests
        $sort = '<a class="dropdown-item active" href="#">Any</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/hr/requests/approved">Approved</a>
                <a class="dropdown-item" href="/hr/requests/pending">Pending</a>
                <a class="dropdown-item" href="/hr/requests/rejected">Rejected</a>';

        $data = [
            'leavereqs' => $leavereqs,
            'root' => $rootHR,
            'sort' => $sort
        ];

        return view('hr.index', compact('data'));
    }

    public function showHRApproved()
    {
        $status = "Approved User Leave";
        $leavereqs = Leave::where('status', 'Approved')->get();
        $sort = '<a class="dropdown-item" href="/hr/requests">Any</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item active" href="#">Approved</a>
        <a class="dropdown-item" href="/hr/requests/pending">Pending</a>
        <a class="dropdown-item" href="/hr/requests/rejected">Rejected</a>';

        $data = [
            'leavereqs' => $leavereqs,
            'root' => $this->rootHR,
            'status' => $status,
            'sort' => $sort
        ];

        return view('hr.index', compact('data'));
    }


    public function showHRPending()
    {
        $status = "Pending User Leave";
        $leavereqs = Leave::where('status', 'Pending')->get();
        $sort = '<a class="dropdown-item" href="/hr/requests">Any</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/hr/requests/approved">Approved</a>
                <a class="dropdown-item active" href="#">Pending</a>
                <a class="dropdown-item" href="/hr/requests/rejected">Rejected</a>';

        $data = [
            'leavereqs' => $leavereqs,
            'root' => $this->rootHR,
            'status' => $status,
            'sort' => $sort
        ];

        return view('hr.index', compact('data'));
    }


    public function showHRRejected()
    {
        $status = "Rejected User Leave";
        $leavereqs = Leave::where('status', 'Rejected')->get();
        $sort = '<a class="dropdown-item" href="/hr/requests">Any</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/hr/requests/approved">Approved</a>
                <a class="dropdown-item" href="/hr/requests/pending">Pending</a>
                <a class="dropdown-item active" href="#">Rejected</a>';

        $data = [
            'leavereqs' => $leavereqs,
            'root' => $this->rootHR,
            'status' => $status,
            'sort' => $sort
        ];

        return view('hr.index', compact('data'));
    }



    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////                        NOTIFICATIONS                            ////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function showUnreadNotifications()
    {
        $notifications = Auth()->user()->unreadNotifications->all();
        $root = '<li class="breadcrumb-item">Notifications</li>';

        $data = [
            'notifications' => $notifications,
            'root' => $root
        ];
        return view('notifications', compact('data'));
    }
}
