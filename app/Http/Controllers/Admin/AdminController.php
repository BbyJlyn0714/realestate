<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use App\Models\User;

class AdminController extends Controller
{
    // Admin Dashboard
    public function dashboard() {
        return view('admin.index');
    }


    // Admin Logout
    public function logout(Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();
        
        $notification = array(
            'message' => 'Admin Logout Successfully',
            'alert-type' => 'success'
        ); 

        return redirect('/admin/login')->with($notification);
    }

    // Admin Login View
    public function login() {
        return view('admin.login');
    }

    public function profile() {
        $id = Auth::user()->id;

        $profileData = User::find($id);

        return view('admin.profile', compact('profileData'));
    }

    public function updateProfile(Request $request) {
        $data = User::find(Auth::user()->id);

        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/images/admin/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName(); 
            $file->move(public_path('upload/images/admin/'),$filename);
            $data['photo'] = $filename;  
        }

        $data->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function changePassword() {
        $id = Auth::user()->id;

        $profileData = User::find($id);

        return view('admin.change_password', compact('profileData'));
    }

    public function updatePassword(Request $request) {
         // TODO: Validation move to Request
         $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'

        ]);

        if (!Hash::check($request->old_password, auth::user()->password)) {
            $notification = array(
             'message' => 'Old Password Does not Match!',
             'alert-type' => 'error'
            );
 
            return back()->with($notification);
        }

        User::whereId(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);

        $notification = array(
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }
}
