<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    // Agent Dashboard
    public function dashboard()
    {
        return view('agent.agent_dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
