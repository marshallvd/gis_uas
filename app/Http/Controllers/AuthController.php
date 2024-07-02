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
    protected $apiUrl = 'https://gisapis.manpits.xyz/api';

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
        Log::info('Register attempt', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            Log::warning('Register validation failed', $validator->errors()->toArray());
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $response = Http::post($this->apiUrl . '/register', [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $responseData = $response->json();
            Log::info('API response', $responseData);

            if ($response->successful()) {
                if (isset($responseData['meta']['token'])) {
                    session(['token' => $responseData['meta']['token']]);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Please login.',
                    'redirect' => route('login')
                ]);
            } else {
                $errorMessage = $responseData['message'] ?? 'Unknown error occurred';
                throw new \Exception('API Registration failed: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8'
        ]);

        try {
            $response = Http::post($this->apiUrl . '/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['meta']['token'])) {
                session(['token' => $responseData['meta']['token']]);
                session(['user' => $responseData['data'] ?? null]); // Simpan data user jika ada
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful! Welcome.',
                    'redirect' => route('dashboard') // Gunakan route() helper
                ]);
            } else {
                throw new \Exception('Invalid credentials or failed to get token from API response.');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function logout(Request $request)
{
    Log::info('Logout attempt');
    try {
        $token = session('token');
        if (!$token) {
            throw new \Exception('No token found');
        }

        // Uncomment baris di bawah ini jika Anda benar-benar perlu memanggil API eksternal
        // $response = Http::withToken($token)->post($this->apiUrl . '/logout');

        // Hapus token dari session
        $request->session()->forget('token');
        $request->session()->forget('user');
        
        // Invalidate dan regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('Logout successful');
        return response()->json([
            'success' => true,
            'message' => 'Anda telah berhasil keluar'
        ]);
    } catch (\Exception $e) {
        Log::error('Logout error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat logout: ' . $e->getMessage()
        ], 500);
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