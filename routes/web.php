<?php

use App\Leave;
use App\Change;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    if (Auth::user()) {
        return redirect()->route('dashboard');
    }
    $changes = Change::all()->sortByDesc('date')->first();
    return view('auth.login', compact('changes'));
});
Route::post('login', 'Auth\LoginController@login')->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');


///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', 'PagesController@index')->name('dashboard');

    //Leave sort routes
    Route::get('/leave', 'PagesController@showLeave')->name('leave.index');
    Route::get('/leave/approved', 'PagesController@showLeaveApproved')->name('leave.showApproved');
    Route::get('/leave/pending', 'PagesController@showLeavePending')->name('leave.showPending');
    Route::get('/leave/rejected', 'PagesController@showLeaveRejected')->name('leave.showRejected');
    Route::get('/leave/cancelled', 'PagesController@showLeaveCancelled')->name('leave.showCancelled');

    //Leave CRUD routes
    Route::post('/leave', 'LeaveController@store')->name('leave.store');
    Route::get('/leave/create', 'LeaveController@create')->name('leave.create');
    Route::get('/leave/{leavenr}', 'LeaveController@show')->name('leave.show');
    Route::get('/leave/cancel/{leavenr}', 'LeaveController@cancel')->name('leave.cancel');
    Route::get('/checkHolidays', 'LeaveController@checkHolidays');
    Route::get('/getfile/{leavenr}', 'LeaveController@getFile')->name('getfile');


    //Handle notification mark as read
    Route::get('/markallasread', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    })->name('markallasread');

    Route::get('/markasread/{id}/{leavenr}', function ($id, $leavenr) {
        $notification = auth()->user()->unreadNotifications->where('id', $id)->first();
        if ($notification !== null) {
            $notification->markAsRead();
        }

        if ($leavenr !== "null") {
            if (Session::get('access') > 1) {
                return redirect()->route('hr.show', ['leavereqs' => Leave::where('leavenr', $leavenr)->first()]);
            }
            return redirect()->route('leave.show', ['leavereqs' => Leave::where('leavenr', $leavenr)->first()]);
        } else {
            return redirect()->back();
        }
    })->name('markasread');

    Route::get('/notifications', 'PagesController@showUnreadNotifications')->name('notifications');
});


///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////

Route::group(['middleware' => ['auth', 'admin']], function () {
    //HR sort routes
    Route::get('/hr/requests', 'PagesController@showHRLeave')->name('hr.index');
    Route::get('/hr/requests/approved', 'PagesController@showHRApproved')->name('hr.showHRApproved');
    Route::get('/hr/requests/pending', 'PagesController@showHRPending')->name('hr.showHRPending');
    Route::get('/hr/requests/rejected', 'PagesController@showHRRejected')->name('hr.showHRRejected');

    //HR CRUD Routes
    Route::get('/hr/create', 'HRController@create')->name('hr.create');
    Route::post('/hr', 'HRController@store')->name('hr.store');
    Route::get('/hr/requests/{leavenr}', 'HRController@show')->name('hr.show');
    Route::put('/hr/requests/{leavenr}', 'HRController@update')->name('hr.update');
    Route::delete('/hr/requests/{leavenr}', 'HRController@destory')->name('hr.destroy');
    Route::get('/hr/requests/edit/{leavenr}', 'HRController@edit')->name('hr.edit');
    Route::get('/hr/requests/approve/{leavenr}', 'HRController@approve')->name('hr.approve');
    Route::post('/hr/requests/reject/{leavenr}', 'HRController@reject')->name('hr.reject');

    //User CRUD Routes
    Route::get('/hr/users', 'UsersController@show')->name('hr.users.index');
    Route::get('/hr/users/edit/{username}', 'UsersController@edit')->name('hr.users.edit');
    Route::put('/hr/users/{username}', 'UsersController@update')->name('hr.users.update');

    //User search autocomplete route
    Route::get('autocomplete', 'HRController@UserSearchAutoComplete')->name('autocomplete');
    Route::get('/getcountry', 'HRController@getUserCountry')->name('getcountry');

    //Changelogs
    Route::get('/changelogs', 'ChangelogController@index')->name('changelogs.index');
    Route::get('/changelogs/create', 'ChangelogController@create')->name('changelogs.create');
    Route::post('/changelogs', 'ChangelogController@store')->name('changelogs.store');
    Route::get('/changelogs/{id}', 'ChangelogController@show')->name('changelogs.show');
    Route::get('/changelogs/edit/{id}', 'ChangelogController@edit')->name('changelogs.edit');
    Route::put('/changelogs/{id}', 'ChangelogController@update')->name('changelogs.update');
    Route::get('/changelogs/delete/{id}', 'ChangelogController@destroy')->name('changelogs.destroy');
});

Route::get('/changes', 'ChangelogController@changelog')->name('changelogs.view');
