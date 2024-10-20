<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
    
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $hasher = app()->make('hash');

        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => 'Email required!',
                'token'   => "",
                'data'    => (object)[]
            ]);
        }

        if (empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password required!',
                'token'   => "",
                'data'    => (object)[]
            ]);
        }


        $ressServer1 = Http::post(env("SUMUR_SERVER_URL")."api/auth/login", [
            'email' => $email,
            'password' => $password,
        ]);

        if($ressServer1->status() == 200)
        {
            if($ressServer1->json()['status'] == 'success')
            {
                return response()->json([
                    'status'    => "success",
                    'id_user'   => $ressServer1->json()["data"]["id"],
                    'token'     =>  $ressServer1->json()["data"]["token"],
                    'base_url'  => env("SUMUR_SERVER_URL")
                ],200);
            }
        } 

        $ressServer2 = Http::post(env("RUMAH_SERVER_URL")."api/auth/login", [
            'email' => $email,
            'password' => $password,
        ]);

        if($ressServer2->status() == 200)
        {
            if($ressServer2->json()['status'] == 'success')
            {
                return response()->json([
                    'status'    => "success",
                    'id_user'   => $ressServer2->json()["data"]["id"],
                    'token'     =>  $ressServer2->json()["data"]["token"],
                    'base_url'  => env("RUMAH_SERVER_URL")
                ],200);
            }
        } 


        return response()->json([
            'status'    => "failed",
            'id_user'   => null,
            'token'     => "",
            'base_url'  => ""
        ],404);
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $database = $request->input('database');

        if(empty($name)){
            return response()->json([
                'success' => false,
                'api_key' => '',
                'message' => 'Name required!',
                'data'    => (object)[]
            ]);
        }

        if(empty($email)){
            return response()->json([
                'success' => false,
                'api_key' => '',
                'message' => 'Email required!',
                'data'    => (object)[]
            ]);
        }

        if(empty($password)){
            return response()->json([
                'success' => false,
                'api_key' => '',
                'message' => 'Password required!',
                'data'    => (object)[]
            ]);
        }

        if(empty($database)){
            return response()->json([
                'success' => false,
                'api_key' => '',
                'message' => 'Database required!',
                'data'    => (object)[]
            ]);
        }


        if($database == 'sumur')
        {
            $checkUser = Http::get(env("RUMAH_SERVER_URL")."api/auth/check-user", [
                'email' => $email,
            ]);

            if($checkUser->status() == 200)
            {
                if($checkUser->json()['status'] == 'success')
                {
                    return response()->json([
                        'status'   => "failed",
                        'id_user'  => null,
                        'token'    => "",
                        'base_url' => ""
                    ]);
                }
           
    
                $ressServer1 = Http::post(env("SUMUR_SERVER_URL")."api/auth/register", [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ]);
        
                if($ressServer1->status() == 200)
                {
                    if($ressServer1->json()['status'] == 'success')
                    {
                        return response()->json([
                            'status'    => "success",
                            'id_user'   => $ressServer1->json()["data"]["id"],
                            'token'     =>  $ressServer1->json()["data"]["token"],
                            'base_url'  => env("SUMUR_SERVER_URL")
                        ],200);
                    }
                } 

            }
        } 
        else if
        ($database == 'rumah'){
            $checkUser = Http::get(env("SUMUR_SERVER_URL")."api/auth/check-user", [
                'email' => $email,
            ]);

            if($checkUser->status() == 200)
            {
                if($checkUser->json()['status'] == 'success')
                {
                    return response()->json([
                        'status'   => "failed",
                        'id_user'  => null,
                        'token'    => "",
                        'base_url' => ""
                    ]);
                }

                $ressServer2 = Http::post(env("RUMAH_SERVER_URL")."api/auth/register", [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                ]);
        
                if($ressServer2->status() == 200)
                {
                    if($ressServer2->json()['status'] == 'success')
                    {
                        return response()->json([
                            'status'    => "success",
                            'id_user'   => $ressServer2->json()["data"]["id"],
                            'token'     =>  $ressServer2->json()["data"]["token"],
                            'base_url'  => env("RUMAH_SERVER_URL")
                        ],200);
                    }
                } 
            }
        }
        

        return response()->json([
            'status'   => "failed",
            'id_user'  => null,
            'token'    => "",
            'base_url' => ""
        ]);
    }

    public function get_profile(Request $request)
    {
        $ressServer1 = Http::withHeaders([
            'Authorization' => 'Bearer '.$request->bearerToken(),
            'Accept' => '*/*',
            'Content-Type' => 'application/json',
        ])->get(env("SUMUR_SERVER_URL")."api/auth/get-profile");

        if($ressServer1->status() == 200)
        {
            if($ressServer1->json()['status'] == 'success')
            {
                return response()->json([
                    'status'    => "success",
                    'data'   => $ressServer1->json()["data"],
                    'base_url'  => env("SUMUR_SERVER_URL")
                ],200);
            }
        } 

        $ressServer2 = Http::withHeaders([
            'Authorization' => 'Bearer '.$request->bearerToken(),
            'Accept' => '*/*',
            'Content-Type' => 'application/json',
        ])->get(env("RUMAH_SERVER_URL")."api/auth/get-profile");

        if($ressServer2->status() == 200)
        {
            if($ressServer2->json()['status'] == 'success')
            {
                return response()->json([
                    'status'    => "success",
                    'data'   => $ressServer2->json()["data"],
                    'base_url'  => env("RUMAH_SERVER_URL")
                ],200);
            }
        } 

        return response()->json([
            'status'   => "failed",
            'data'     => (object)[],
            "base_url" => ""
        ]);
    }

}
