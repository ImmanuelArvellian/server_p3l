<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pegawai = Pegawai::all();
        
        if(count($pegawai) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate  = FacadesValidator::make($storeData, [
            'id_role' => 'required',
            'nama' => 'required',
            'email' => 'required',
            'password' => 'required',
            'gender' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }
        
        $last = DB::table('Pegawai')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_pegawai, 3, 3)) + 1;
        }

        if($count < 10){
            $id = '00'.$count;
        }else if($count < 100){
            $id = '0'.$count;
        }

        $pegawai = Pegawai::create([
            'id_pegawai' => 'PEG'.$id,
            'id_role' => $request->id_role,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => $request->password,
            'gender' => $request->gender,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
        ]);

        $storeData = Pegawai::latest()->first();

        return response([
            'message' => 'Add Pegawai Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_pegawai)
    {
        $pegawai = Pegawai::find($id_pegawai);

        if(!is_null($pegawai)){
            return response([
                'message' => 'Retrieve Pegawai Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Pegawai Not Found',
            'data' => null
        ], 404);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::find($id);
        if(is_null($pegawai)){
            return response([
                'message' => 'Pegawai Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = FacadesValidator::make($updateData, [
            'id_role' => 'required',
            'nama' => 'required',
            'email' => 'required',
            'password' => 'required',
            'gender' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
        ]);

        if($validate->fails())
            return response()->json($validate->errors(), 400);
        
        $pegawai->id_role = $updateData['id_role'];
        $pegawai->nama = $updateData['nama'];
        $pegawai->email = $updateData['email'];
        $pegawai->password = $updateData['password'];
        $pegawai->gender = $updateData['gender'];
        $pegawai->no_telp = $updateData['no_telp'];
        $pegawai->alamat = $updateData['alamat'];
        
        if ($pegawai->save()){
            return response([
                'message' =>'Update Pegawai Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' =>'Update Pegawai Failed',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::find($id);

        if(is_null($pegawai)){
            return response([
                'message' => 'Pegawai Not Found',
                'data' => null
            ], 404);
        }

        if($pegawai->delete()){
            return response([
                'message' => 'Delete Pegawai Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Delete Pegawai Failed',
            'data' => null
        ], 400);
    }
}
