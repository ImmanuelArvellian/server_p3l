<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Pegawai;
use App\Models\Member;
use App\Models\Instruktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class AuthController extends Controller
{
    public function login (Request $request)
    {
        $loginData = $request->all();
        $validate = FacadesValidator::make($loginData, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validate->fails())
        return response([
                'status' => 400,
                'message' => $validate->errors()
        ], 400);

        if($pegawai = Pegawai::where('email',$request->email)->first())
        {
            $loginPegawai = Pegawai::where('email','=',$request['email'])->first();

            if($request['password'] == $loginPegawai['password']){

                $role = Role::find($pegawai['id_role']);

                return response([
                    'message' => 'Successfully Login as pegawai',
                    'id' => $pegawai['id_pegawai'],
                    'data' => $pegawai,
                    'role' => $role,
                ]);
            }
            else{
                return response([
                    'status' => 401,
                    'message' => 'Email atau Password Salah',
                ], 401);
            }
        }
        else if($member = Member::where('email',$request->email)->first())
        {
            $loginMember = Member::where('email','=',$request['email'])->first();

            if($request['password'] == $loginMember['password']){
                $member = Member::where('email', '=',$request['email'])->first();
            }else{
                return response(['message' => 'Invalid Password or Email'], 404);
            }

            return response([
                'message' => 'Successfully Login as member',
                'id' => $member['id_member'],
            ], 200);
        }
        else if($instruktur = Instruktur::where('email',$request->email)->first())
        {
            $loginInstruktur = Instruktur::where('email','=',$request['email'])->first();

            if($request['password'] == $loginInstruktur['password']){
                $instruktur = Instruktur::where('email', '=',$request['email'])->first();
            }else{
                return response(['message' => 'Invalid Password or Email'], 404);
            }

            return response([
                'message' => 'Successfully Login as instruktur',
                'id' => $instruktur['id_instruktur'],
            ], 200);
        }
        else
        {
            return response([
                'status' => 404,
                'message' => 'User Not Found',
                'data' => null
            ], 404);
        }
    }
}
