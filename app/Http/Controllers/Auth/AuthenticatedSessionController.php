<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = User::find(Auth::user()->id);

        $request->session()->regenerate();

        $notification = [
            'message' => 'User '.$user->name.' Login Successfully',
            'alert-type' => 'info',
        ];

        $url = 'dashboard';
        if ($request->user()->role === 'admin') {
            $url = 'admin.dashboard';
        } elseif ($request->user()->role === 'agent') {
            $url = 'agent.dashboard';
        }

        return redirect()->intended(route($url, absolute: false))->with($notification);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
