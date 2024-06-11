<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AuthController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth')->except(['register', 'registerSave', 'login', 'loginAction', 'showLoginForm']);
    // }

    public function showLoginForm(Request $request)
    {
        $status = $request->session()->get('status');
        return view('auth.login', compact('status'));
    }

    public function register()
    {
        return view('auth.register');
    }

    public function registerSave(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed'
        ])->validate();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'level' => 'Admin'
        ]);

        try {
            $client = new Client();
            $response = $client->post('https://gisapis.manpits.xyz/api/register', [
                'form_params' => $data,
            ]);

            $responseBody = json_decode($response->getBody(), true);
            return redirect()->route('login')->with('status', 'Registration successful! Please login.');
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['registration' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    public function login(Request $request)
    {
        $data = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ])->validate();

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed')
            ]);
        }

        $request->session()->regenerate();

        try {
            $client = new Client();
            $response = $client->post('https://gisapis.manpits.xyz/api/login', [
                'json' => $data,
            ]);

            $body = $response->getBody();
            $content = $body->getContents();
            $responseData = json_decode($content, true);

            if (isset($responseData['meta']['token'])) {
                session(['token' => $responseData['meta']['token']]);
                return redirect()->route('dashboard')->with('status', 'Login successful! Welcome.');
            } else {
                return redirect()->back()->withErrors(['login' => 'Failed to get token from API response.']);
            }
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $content = $response->getBody()->getContents();
                return redirect()->back()->withErrors(['login' => 'Failed to login.']);
            } else {
                return redirect()->back()->withErrors(['login' => 'Failed to connect to the server.']);
            }
        }
    }

    public function logout(Request $request)
    {
        try {
            $client = new Client();
            $token = session('token'); // Ambil token dari sesi

            $response = $client->post('https://gisapis.manpits.xyz/api/logout', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            session()->forget('token'); // Hapus token dari sesi setelah logout berhasil

            return redirect('login')->with('status', 'Logged out successfully.');
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $content = $response->getBody()->getContents();
                return redirect()->back()->withErrors(['logout' => 'Logout failed, please try again.']);
            } else {
                return redirect()->back()->withErrors(['logout' => 'Failed to connect to the server.']);
            }
        }
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
