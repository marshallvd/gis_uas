<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Jangan tambahkan middleware 'auth' di sini
    // public function __construct()
    // {
    //     $this->middleware('auth')->except(['register', 'registerSave', 'login', 'loginAction']);
    // }

    public function register()
    {
        return view('auth.register');
    }

    public function registerSave(Request $request)
    {
        
        $data = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ])->validate();
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'level' => 'Admin'
        ]);
        try {
            $response = Http::post('https://gisapis.manpits.xyz/api/register', $data);
            if ($response->successful()) {
                $responseData = $response->json();
                if ($responseData['meta']['code'] == 200) {

                    return redirect()->route('login')->with('success', $responseData['meta']['message']);
                } else {
                    return back()->withErrors(['message' => 'Registration failed. Please try again.']);
                }
            } else {
                return back()->withErrors(['message' => 'Registration failed. Please try again.']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'An error occurred: ' . $e->getMessage()]);
        }

        return redirect()->route('login');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function loginAction(Request $request)
    {
        $data = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ])->validate();
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed')
            ]);
        }

        $request->session()->regenerate();
        try {
            $response = Http::post('https://gisapis.manpits.xyz/api/login', $data);
            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['meta']['token'])) {
                    session(['token' => $responseData['meta']['token']]);

                    return redirect()->route('dashboard')->with('success', $responseData['meta']['message']);
                } else {
                    return back()->withErrors(['message' => 'Login failed. Please try again.']);
                }
            } else {
                return back()->withErrors(['message' => 'Login failed. Please try again.']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'An error occurred: ' . $e->getMessage()]);
        }
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        return redirect('/login');
    }

    public function profile()
    {
        return view('profile');
    }

    public function index()
    {
        return view('frontend.home');
    }
}

