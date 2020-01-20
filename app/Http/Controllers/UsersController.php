<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UsersController extends Controller
{
    public function show()
    {
        $users = User::all();
        return view('hr.users.index', compact('users'));
    }

    public function edit($username)
    {
        $user = User::where('username', $username)->first();
        return view('hr.users.edit', compact('user'));
    }

    public function update(Request $request, $username)
    {
        $user = User::where('username', $username)->first();
        try {
            $user->update($request->except(['_method', '_token', 'submit']));
        } catch (\Exception $e) {
            return back()->withError("Operation failed! Error message:" . $e->getMessage());
        }
        return redirect()->back()->with('success', 'User successfully updated.');
    }
}
