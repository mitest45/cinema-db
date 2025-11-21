<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // for auth
use Illuminate\Support\Facades\Hash;

// Models
use App\Models\User;

class AdminauthController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        return view(VIEW_PATH.'login.login');
    }

    public function login_check(Request $req){
        $req->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $req->email)
                    ->first();

        if (!$user || !Hash::check($req->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid login credentials.',
            ])->withInput($req->only('email'));
        }

        Auth::login($user);
        $req->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }


}
