<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationFormRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    const JOB_SEEKER = 'seeker';
    const JOB_POSTER = 'employer';

    public function createSeeker()
    {
        return view('user.seeker-register');
    }

    public function createEmployer()
    {
        return view('user.employer-register');
    }

    public function storeSeeker(RegistrationFormRequest $request)
    {
        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'user_type' => self::JOB_SEEKER,
        ]);

        Auth::login($user);

        $user->sendEmailVerificationNotification();
        
        return response()->json('success');

        //return redirect()->route('verification.notice')->with('successMessage', 'Your account was created successfully');
    }

    public function storeEmployer(RegistrationFormRequest $request)
    {
        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
            'user_type' => self::JOB_POSTER,
            'user_trial' => now()->addWeek(),
        ]);

        Auth::login($user);

        $user->sendEmailVerificationNotification();

        return response()->json('success');

        //return redirect()->route('verification.notice')->with('successMessage', 'Your account was created successfully');
    }

    public function login()
    {
        return view('user.login');
    }

    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        
        if(Auth::attempt($credentials)) {
            return redirect()->intended('dashboard');
        }
        return 'Wrong email or password';
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}

