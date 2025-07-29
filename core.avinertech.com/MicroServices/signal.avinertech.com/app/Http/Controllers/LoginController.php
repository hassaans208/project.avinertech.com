<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request for super admin
     */
    public function login(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['email' => 'Invalid credentials'])
                ->withInput($request->only('email'));
        }

        // Check if user is active
        if (!$user->isActive()) {
            return redirect()->back()
                ->withErrors(['email' => 'Account is inactive'])
                ->withInput($request->only('email'));
        }

        // Check if user is super admin
        if (!$user->isSuperAdmin()) {
            return redirect()->back()
                ->withErrors(['email' => 'Only Super Admin users can access this panel'])
                ->withInput($request->only('email'));
        }

        // Generate API token for the session
        $token = $user->generateApiToken();

        // Redirect to tenants page with token
        return redirect('/tenants?access_token=' . $token)
            ->with('success', 'Successfully logged in');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): RedirectResponse
    {
        $token = $request->query('access_token');
        
        if ($token) {
            $user = User::where('api_token', $token)->first();
            if ($user) {
                $user->revokeApiToken();
            }
        }

        return redirect('/login')
            ->with('success', 'Successfully logged out');
    }
} 