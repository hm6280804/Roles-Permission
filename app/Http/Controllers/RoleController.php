<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller // implements HasMiddleware
{
    // public static function middleware(): array
    // {
    //     return [
    //         new Middleware('permission:view roles', only: ['index']),
    //         new Middleware('permission:edit roles', only: ['edit']),
    //         new Middleware('permission:create roles', only: ['create']),
    //         new Middleware('permission:delete roles', only: ['destroy']),
    //     ];
    // }

    public function index(){
        $roles = Role::orderBy('name', 'ASC')->paginate(10);
        return view('roles.list',[
            'roles' => $roles
        ]);
    }

    public function create(){
        $permissions = Permission::orderBy('name', 'ASC')->get();
        return view('roles.create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|unique:roles|min:3,'
        ]);

        if($validator->passes()){
            $role = Role::create(['name' => $request->name]);
            if(!empty($request->permissions)){
                foreach($request->permissions as $name){
                    $role->givePermissionTo($name);
                }
            }
            return redirect()->route('roles.index')->with('success', 'Roles Assigned Successfully');
        }
        else{
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }

    public function edit($id){
        $roles = Role::findOrFail($id);
        $haspermissions = $roles->permissions->pluck('name');
        $permissions = Permission::orderBy('name', 'ASC')->get();
        // dd($haspermissions);
        return view('roles.edit', [
            'haspermissions' => $haspermissions,
            'permissions' => $permissions,
            'role' => $roles
        ]);
    }

    public function update($id, Request $request){
        $role = Role::findOrFail($id);
        $validator = Validator::make($request->all(),
        [
            'name' => 'required|unique:roles,name, '. $id . ',id'
        ]);

        if($validator->passes()){
            $role->name = $request->name;
            $role->save();

            if(!empty($request->permissions)){
                $role->syncPermissions($request->permissions);
            } else {
                $role->syncPermissions([]);
            }
            return redirect()->route('roles.index')->with('success', 'Roles Updated Successfully');
        }
        else{
            return redirect()->route('roles.edit')->withInput()->withErrors($validator);
        }
    }

    public function destroy(Request $request){
        $id = $request->id;
        $role = Role::find($id);
        // dd($role);
        if($role == null){
            session()->flash('error', 'Role Not Found!');
            return response()->json([
                'status' => false
            ]);
        }
        session()->flash('success', 'Role Deleted Successfully');
        $role->delete();
    }


}
