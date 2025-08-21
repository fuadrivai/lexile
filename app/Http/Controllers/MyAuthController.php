<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class MyAuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.login');
    }

    public function auth(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required'],
                'password' => ['required'],
            ]);

            $response = Http::post('https://mhis-hub.mhis.link/api/login', [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'device' => 'web-lexile',
                'device_id' => 'web-lexile'
            ]);
            if ($response->successful()) {
                $user = $response['user'];
                $token = $response['authorization']['token'];
                if ($token) {
                    Session::put('authorization', $token);
                    Session::put('username', $user['name']);
                    Session::put('userId', $user['id']);
                    Session::put('email', $user['email']);
                    return redirect()->intended('/admin');
                }
            }
            return back()->with('LoginError', $response->json()['message']);
        } catch (\Throwable $th) {
            return back()->with('LoginError', "failed to login, please try again or call support.");
        }
    }

    public function logout()
    {
        Session::forget('authorization');
        Session::forget('username');
        Session::forget('userId');
        Session::forget('email');
        return redirect()->route('login')->with('success', 'Logout successful !');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
