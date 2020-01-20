<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Change;

class ChangelogController extends Controller
{
    public function index()
    {
        $changes = Change::all();
        return view('admin.changelogs.index', compact('changes'));
    }

    public function create()
    {
        return view('admin.changelogs.create');
    }

    public function store(Request $request)
    {
        try{
            Change::create($request->except(['submit', '_token']));
        }
        catch(\Exception $e){   //catch any exceptions
            return back()->withError("Operation failed! Error message: ".$e->getMessage());
        }
        return redirect()->route('changelogs.index')->with('success', "Changelog entry successfully created!");
    }

    public function show($id)
    {
        $changes = Change::findOrFail($id);
        return view('admin.changelogs.show', compact('changes'));
    }

    public function edit($id)
    {
        $changes = Change::findOrFail($id);
        return view('admin.changelogs.edit', compact('changes'));
    }

    public function update(Request $request, $id)
    {
        try{
            $change = Change::findOrFail($id);
            $change->update($request->except(['submit', '_token']));
        }
        catch(\Exception $e){
            return back()->withError("Operation failed! Error message: ".$e->getMessage());
        }
        return redirect()->route('changelogs.index')->with('success', "Changelog entry successfully updated!");
    }

    public function destroy($id)
    {
        try{
            $change = Change::find($id);
            $change->delete();
        }
        catch(\Exception $e){
            return back()->withError("Operation failed! Error message: ".$e->getMessage());
        }
        return back()->with('success', "Changelog entry successfully deleted!");
    }

    //public view of the changelogs
    public function changelog(){
        try{
            $changes = Change::all()->sortByDesc('date');
        }
        catch(\Exception $e){
            return back()->withError("Operation failed! Error message: ".$e->getMessage());
        }
        return view('changelogs', compact('changes'));
    }
}
