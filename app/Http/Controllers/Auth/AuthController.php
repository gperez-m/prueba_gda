<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAuthRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Communes;
use App\Models\Customers;
use App\Models\LogSessions;
use App\Models\Regions;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use function Psy\debug;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(LoginAuthRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales no válidas',
            ], 401);
        }

        $user = Auth::user();

        $user_exists = Customers::where('email', $user->email)->firstOrFail();

        LogSessions::create([
            'user_id' => $user->id,
            'ip'=> $request->ip()? $request->ip() : '0:0:0:0'
        ]);
        
        if(in_array($user_exists->status,[Customers::$status['DELETED'], Customers::$status['INACTIVE']])) {
            return response()->json([
                'status' => 'error',
                'message' => 'El registro no existe',
            ], 301);
        }
        
        $customer = Customers::where('email', $user->email)->with('regions','communes')->first();

        return response()->json([
                'status' => 'success',
                'user' => $customer,
                'access_token' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);

    }

    public function register(RegisterRequest $request){

        try {
            $region = Regions::where([['id_reg', $request->id_region],['status', 'A']])->firstOrFail();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'El identificador de region no existe'
            ]);
        }
        
        try {
            $commun = Communes::where('id_com', $request->id_commun)->firstOrFail();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'El identificador de compañia no existe'
            ]);
        }

        $result = DB::transaction(function () use($request, $commun, $region){
            $customer = Customers::create([
                'dni' => $request->email,
                'id_reg' => $region->id_reg,
                'id_com' => $commun->id_com,
                'email' => $request->email,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'data_reg' => \Carbon\Carbon::now(),
                'status' => Customers::$status['ACTIVE']
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return ['user' => $user, 'customer' => $customer];
        });
        
        if($result) {
            $token = Auth::login($result['user']);
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $result['customer'],
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
