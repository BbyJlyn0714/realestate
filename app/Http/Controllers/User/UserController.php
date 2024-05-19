<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        return view('frontend.index');
    }

    public function profile() {
        $user = User::find(Auth::user()->id);

        return view('profile.edit',compact('user'));
    }

    public function update(Request $request) {
        $data = User::find(Auth::user()->id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address; 

        if ($request->file('photo')) {
            $file = $request->file('photo');
            @unlink(public_path('upload/images/user/'.$data->photo));
            $filename = date('YmdHi').$file->getClientOriginalName(); 
            $file->move(public_path('upload/images/user'),$filename);
            $data['photo'] = $filename;  
        }

        $data->save();

        $notification = array(
            'message' => 'User Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }

    public function logout(Request $request) {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $notification = array(
            'message' => 'User Logout Successfully',
            'alert-type' => 'success'
        );
        
        return redirect('/login')->with($notification);
    }

    public function changePassword() {
        return view('frontend.change_password');
    }

    public function updatePassword(Request $request) {
        // TODO: Validation Transfer it to Request Folder
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

        /// Update The New Password 
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
