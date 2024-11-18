<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomersController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:50',
            'email'    => 'required|email|unique:customers,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
            'address1' => 'nullable|string',
            'address2' => 'nullable|string',
            'address3' => 'nullable|string',
        ]);

        $customers = Customers::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'address3' => $request->address3,
        ]);

        // Buat token untuk API
        $token = $customers->createToken('auth_token')->plainTextToken;

        return redirect()->route('welcome')->with('success', 'Registration successful');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('customers')->attempt($credentials)) {
            return redirect()->route('welcome')->with('success', 'Login successful');
        }

        return redirect()->back()->withErrors(['message' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        Auth::guard('customers')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}
