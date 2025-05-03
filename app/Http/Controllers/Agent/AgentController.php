<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AgentController extends Controller
{
    // Agent Dashboard
    public function dashboard()
    {
        $id = Auth::user()->id;
        
        $data = User::find($id);

        return view('agent.index', compact('data'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'Agent Logout Successfully',
            'alert-type' => 'success'
        ); 

        return redirect('/agent/login')->with($notification);
    }

    public function login() {
        return view('agent.login');
    }

    public function register(Request $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'agent',
            'status' => 'inactive',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/agent/dashboard');
    }

    public function profile() {
        $id = Auth::user()->id;
        
        $data = User::find($id);

        return view('agent.profile',compact('data'));
    }

    public function profileUpdate(Request $request) {
        $id = Auth::user()->id;
        $data = User::find($id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address; 

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/agent/profile/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName(); 
            $file->move(public_path('upload/agent/profile'),$filename);
            $data['photo'] = $filename;  
        }

        $data->save();

        $notification = array(
            'message' => 'Agent Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function changePassword() {
        $id = Auth::user()->id;
        $data = User::find($id);
        return view('agent.password', compact('data'));
    }

    public function updatePassword(Request $request) {
        // Validation 
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'

        ]);

        /// Match The Old Password
 
        if (!Hash::check($request->old_password, auth::user()->password)) {
           
            $notification = array(
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            );
 
            return back()->with($notification);
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)

        ]);

        $notification = array(
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification); 
    }
}
