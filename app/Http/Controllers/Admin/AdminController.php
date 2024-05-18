<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        
        return redirect('/admin/login');
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
}
