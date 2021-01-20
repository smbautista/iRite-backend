<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;


class UserController extends Controller
{
    public function index_register()
    {
        return view('registration-form');
    }

    public function register(Request $request)
    {
        DB::beginTransaction();

        try{
            $validate = $request->validate([
                'username' =>'required',
                'dateOfBirth' =>'required',
                'email'=>'required|email',
                'password'=>'required|min:8|max:12|confirmed',
                
            ]);
            $user = 
                User::create([
                    'username'=>$request->username,
                    'date-of-birth'=>$request->dateOfBirth,
                    'email'=>$request->email,
                    'password'=>Hash::make($request->password),
                ]);
            DB::commit();
            return response()->json($user);

            /* TO BE USE */
            if(!is_null($user)) {
                return redirect()->route('home')->with("success", "Success! Registration completed");
            }

            else {
                return back()->withErrors($user);
            }
        
            
        }catch(Exception $e){
            DB::rollBack();
            return $e;
        }
    }

    public function index_login()
    {
       return view('login-form');
    }
    public function login(Request $request)
    {
            $request->validate([
                'email'=>'required',
                'password'=>'required'
            ]);
            $credentials = $request->only('email','password');

            if (Auth::attempt($credentials)) {
                Auth::user()->createToken('access_token')->plainTextToken;
                //TO BE USE
                // return redirect()->intended('home');
                return response()->json('successful login');
            }
            // TO BE USE
            // return back()->withErrors([
            //     'errors' => 'The provided credentials do not match our records.'
            // ]);
            return response()->json('failed to log in');
                    
    }
}
