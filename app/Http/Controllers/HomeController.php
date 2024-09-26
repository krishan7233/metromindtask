<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 


class HomeController extends Controller
{
    //


    public function index(){
        return view('login');
    }

    public function register(){
        return view('register');
    }


    public function registerUser(Request $request)
    {

     
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', 
            'password' => 'required|string|min:6', 
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create the user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role'=>2,
            'password' => Hash::make($request->password), // Hash the password
        ]);

       
        return redirect()->back()->with('success', 'User registered successfully!');
    }



    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
    
          // If validation fails
          if ($validator->fails()) {
            // Redirect back with validation errors and old input
            return redirect()->back()->withErrors($validator) ->withInput();
        }

        $credentials = $request->only('email', 'password');

        // Attempt to log the user in
        if (Auth::attempt($credentials)) {
            // Authentication passed, redirect to intended page
            return redirect()->route('dashboard');
        }

        // If login fails, redirect back with an error
        return redirect()->back()->withErrors('Login failed, please check your credentials.');
    }





}
