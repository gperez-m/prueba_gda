<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Communes;
use App\Models\Customers;
use App\Models\Regions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $customers = Customers::all();

        return response()->json([
            'status' => 'success',
            'customers' => $customers,
        ]);
    }

    public function store(RegisterRequest $request)
    {
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

        $customer = DB::transaction(function () use($request, $commun, $region){
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
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return $customer;
        });
        
        if($customer) {
            return response()->json([
                'status' => 'success',
                'customer' => $customer,
                'message' => 'User created successfully',
            ]);
        } else {
            Log::warning($customer);
            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un error al realizar la petición',
            ]);
        }

    }

    public function show(Request $request)
    {
        try {
            $user = Customers::where('dni', $request->findBy)->orWhere('email', $request->findBy)->with('regions', 'communes')->firstOrFail();

            return response()->json([
                'status' => 'success',
                'customer' => $user,
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => 'error',
                'message' => "No hay resultados para la busqueda solicitada"
            ]);
        }
        
    }

    public function destroy(Request $request)
    {
        try {
            $user = Customers::where('dni',$request->find)->orWhere('email', $request->find)->where(function ($query) {
                $query->where('status', 'A')->orWhere('status', 'I');
            })->update(['status' => Customers::$status['DELETED']]);

            Log::debug($user);
            if($user) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Usuario eliminado con éxito'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Registro no existe'
                ]);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un error en la petición'
            ]);
        }
    }
}
