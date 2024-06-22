<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\User;

class AuthController extends Controller
{
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
        // Validate incoming request data
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);
    
        try {
            // Create a new User record using the validated data
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'level' => 'Admin' // Example: Assigning a default level
            ]);
    
            // If user creation is successful, redirect to login page with success message
            return redirect()->route('login')->with('status', 'Registration successful! Please login.');
        } catch (\Exception $e) {
            // If any exception occurs during user creation, redirect back with error message
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
        $token = session('token');
    
        if ($token) {
            try {
                $client = new Client();
                $response = $client->post('https://gisapis.manpits.xyz/api/logout', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                    ],
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse() && $e->getResponse()->getStatusCode() == 402) {
                    Log::error('API Subscription Issue: ' . $e->getMessage());
                } else {
                    Log::error('Error during logout: ' . $e->getMessage());
                }
            }
        }
    
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('token');
    
        return redirect()->route('dashboard')->with('status', 'Logged out successfully. Note: There might be an issue with our external services.');
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
