<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show User Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Process User Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect based on role dynamically
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Logged in as Admin!');
            }

            return redirect()->route('products.index')->with('success', 'Logged in successfully!');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    // Show User Register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Process User Register
    public function register(Request $request)
    {
        
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // PASS PLAIN TEXT: The 'hashed' cast in User.php handles hashing automatically!
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('products.index')->with('success', 'User account created successfully!');
    }

    // Show Admin Login
    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    // Process Admin Login
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Attempt DB Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Check if user is an admin in DB
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Welcome to Admin Dashboard!');
            }

            // Reject non-admins
            Auth::logout();
            return back()->withErrors([
                'email' => 'Access denied. You do not have admin permissions.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Invalid admin credentials.',
        ])->onlyInput('email');
    }

    // Logout method
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out successfully!');
    }
}