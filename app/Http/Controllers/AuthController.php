<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('web_user_id')) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = WebUser::where('username', $request->username)
            ->where('status', 1)
            ->first();

        if (!$user) {
            return back()->with('error', 'User not found or inactive');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid password');
        }
        // store session
        Session::put('web_user_id', $user->id);
        Session::put('web_username', $user->username);
        Session::put('web_full_name', $user->full_name);
        return redirect('/');
    }

    public function logout()
    {
        Session::flush();
        return redirect('/login');
    }
}