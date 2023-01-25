<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function register(){
        return view('users.register');
    }

    public function store(Request $request){
        $formFields = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')], 
            'password' => ['required', 'min:8', 'confirmed']
        ]);
        
        // Hash Password
        $formFields['password'] = bcrypt($formFields['password']);

        // Create User
        $user = User::create($formFields);

        // Sign in User
        auth()->login($user);

        // Redirect to Dashboard
        return redirect('/')->with('message', 'Your account has been created.');

    }

    public function logout(Request $request){
        auth()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You have successfully logged out.');
    }
}
