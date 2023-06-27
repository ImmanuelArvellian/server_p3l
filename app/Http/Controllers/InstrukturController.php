<?php

namespace App\Http\Controllers;

use App\Models\Instruktur;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instruktur = Instruktur::all();

        if(count($instruktur) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $instruktur
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
            'nama' => 'required',
            'email' => 'required',
            'password' => 'required',
            'gender' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required'
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $last = DB::table('Instruktur')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_instruktur, 3, 3)) + 1;
        }

        if($count < 10){
            $id = '00'.$count;
        }else if($count < 100){
            $id = '0'.$count;
        }

        $instruktur = Instruktur::create([
            'id_instruktur' => 'INS'.$id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => hash::make($request->password),
            'gender' => $request->gender,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat
        ]);

        $storeData = Instruktur::latest()->first();
        
        return response([
            'message' => 'Add Instruktur Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_instruktur)
    {
        $instruktur = Instruktur::find($id_instruktur);

        if(!is_null($instruktur)){
            return response([
                'message' => 'Retrieve Instruktur Success',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' => 'Instruktur Not Found',
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
    public function update(Request $request, $id_instruktur)
    {
        $instruktur = Instruktur::find($id_instruktur);
        if(is_null($instruktur)){
            return response([
                'message' => 'Instruktur Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = FacadesValidator::make($updateData, [
            'nama' => 'required',
            'email' => 'required',
            'password' => 'required',
            'gender' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required'
        ]);

        if($validate->fails())
            return response()->json($validate->errors(), 400);
        
        $instruktur->nama = $updateData['nama'];
        $instruktur->email = $updateData['email'];
        $instruktur->password = $updateData['password'];
        $instruktur->gender = $updateData['gender'];
        $instruktur->no_telp = $updateData['no_telp'];
        $instruktur->alamat = $updateData['alamat'];
        
        if ($instruktur->save()){
            return response([
                'message' =>'Update Instruktur Success',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' =>'Update Instruktur Failed',
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
        $instruktur = Instruktur::find($id);

        if(is_null($instruktur)){
            return response([
                'message' => 'Instruktur Not Found',
                'data' => null
            ], 404);
        }

        if($instruktur->delete()){
            return response([
                'message' => 'Delete Instruktur Success',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' => 'Delete Instruktur Failed',
            'data' => null
        ], 400);
    }

    function resetTerlambat($id_instruktur)
    {
        $instruktur = Instruktur::find($id_instruktur);
        
        foreach ($instruktur as $absen){
            // $today = Carbon::now()->toDateString();
            $today = '2023-05-01';
            if ($today == Carbon::now()->startOfMonth()->toDateString()){
                $absen->jumlah_terlambat = '00:00:00';
                $absen->save();
            }
        }

        return response([
            'message' => 'Delete Instruktur Success',
            'data' => $instruktur
        ], 200);
    }
}
