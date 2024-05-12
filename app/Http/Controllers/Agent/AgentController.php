<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgentController extends Controller
{
     // Agent Dashboard
     public function dashboard() {
        return view('agent.agent_dashboard');
    }
}
