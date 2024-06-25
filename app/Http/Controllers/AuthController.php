<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\AppHelper;


class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|confirmed',
        ]);

        try {

            $user = new User;
            $user->username = $request->input('username');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return AppHelper::response_json($user, 200, 'User successfully reated');

        } catch (\Exception $e) {
            //return error message
            return AppHelper::response_json(null, 200, 'Failed created user');
        }

    }

    public function getToken(Request $request)
    {
        if(!$request->header('x-username'))
        {
            return AppHelper::response_json(null, 201, 'Username atau Password Tidak Sesuai');
        }

        if(!$request->header('x-password'))
        {
            return AppHelper::response_json(null, 201, 'Username atau Password Tidak Sesuai');
        }




        //validate incoming request
        // $this->validate($request, [
        //     'username' => 'required|string',
        //     'password' => 'required|string',
        // ]);

        // $credentials = $request->only(['username', 'password']);
        $credentials = [
            'username' => $request->header('x-username'),
            'password' => $request->header('x-password'),
        ];
            
        if (! $token = Auth::setTTL(1440)->attempt($credentials)) {
        // if (! $token = Auth::setTTL(1)->attempt($credentials)) {
            return AppHelper::response_json(null, 201, 'Username atau Password Tidak sesuai');
        }

        $resp = [
            'token' => $token,
        ];
        return AppHelper::response_json($resp, 200, 'Ok');
    }
}
