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
}
