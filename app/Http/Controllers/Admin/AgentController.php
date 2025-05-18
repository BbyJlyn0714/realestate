<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AgentController extends Controller
{
    public function list() {
        $agents = User::where('role','agent')->get();

        return view('admin.agent.index', compact('agents'));
    }

    public function create() {
        return view('admin.agent.create');
    }

    public function store(Request $request) {
        User::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => 'agent',
            'status' => 'active', 
        ]);
    
    
        $notification = array(
            'message' => 'Agent Created Successfully',
            'alert-type' => 'success'
        );
    
        return redirect()->route('admin.agent.list')->with($notification); 
    }

    public function edit(User $user) {

        return view('admin.agent.edit',compact('user'));
    }

    public function update(Request $request) {
        $user_id = $request->id;

        User::findOrFail($user_id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address, 
        ]);


       $notification = array(
            'message' => 'Agent Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('admin.agent.list')->with($notification);  
    }

    public function delete(User $user) {
        $user->delete();

        $notification = array(
            'message' => 'Agent Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 
    }

    public function changeStatus(Request $request) {
        $user = User::find($request->user_id);
        $user->status = $request->status;
        $user->save();

        return response()->json(['success'=>'Status Change Successfully']);
    }
}
