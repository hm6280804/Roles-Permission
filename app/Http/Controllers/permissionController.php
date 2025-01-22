<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class permissionController extends Controller //implements HasMiddleware
{
    // public static function middleware(): array
    // {
    //     return [
    //         new Middleware('permission:view permsission', only: ['index']),
    //         new Middleware('permission:edit permsission', only: ['edit']),
    //         new Middleware('permission:create permsission', only: ['create']),
    //         new Middleware('permission:delete permsission', only: ['destroy']),
    //     ];
    // }
    //This method will show permission page
    public function index()
    {
        $permission = Permission::orderBy('created_at', 'DESC')->paginate(20);
        return view('permissions.list',[
            'permissions' => $permission
        ]);
    }

    //This method will show create permissions page
    public function create()
    {
        return view('permissions.create');
    }

    //This method will insert permissions to DB
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|unique:permissions|min:3,'
        ]);

        if($validator->passes()){
            Permission::create(['name' => $request->name]);
            return redirect()->route('permissions.index')->with('success', 'Permission added Successfully');
        }
        else{
            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }

    //This method will show edit permission page
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', ['permission' => $permission]);
    }

    //This method will update the permissions in DB
    public function update($id, Request $request)
    {
        $permission = Permission::findOrFail($id);
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|min:3|unique:permissions,name,'.$id.',id'
        ]);

        if($validator->passes()){
            // Permission::create(['name' => $request->name]);
            $permission->name = $request->name;
            $permission->save();
            return redirect()->route('permissions.index')->with('success', 'Permission updated Successfully');
        }
        else{
            return redirect()->route('permissions.edit', $id)->withInput()->withErrors($validator);
        }
    }

    //This method will destroy the permissions in DB
    public function destroy(Request $request)
    {
        $id = $request->id;
        $permission = Permission::find($id);

        if($permission == null){
            Session()->flash('error', 'Permission Not Found');
            return response()->json(
                ['status' => false]
            );
        }

        $permission->delete();
        Session()->flash('success', 'Permission Deleted Successfully');
        return response()->json([
            'status' => true
        ]);
    }
}
