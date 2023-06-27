<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kelas = Kelas::all();
        
        if(count($kelas) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $kelas
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
            'nama_kelas' => 'required',
            'harga' => 'required',
            'kapasitas' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }
        
        $last = DB::table('Kelas')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_kelas, 1, 3)) + 1;
        }

        if($count < 10){
            $id = '00'.$count;
        }else if($count < 100){
            $id = '0'.$count;
        }

        $kelas = Kelas::create([
            'id_kelas' => 'K'.$id,
            'nama_kelas' => $request->nama_kelas,
            'harga' => $request->harga,
            'kapasitas' => $request->kapasitas,
        ]);

        $storeData = Kelas::latest()->first();

        return response([
            'message' => 'Add Kelas Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_kelas)
    {
        $kelas = Kelas::find($id_kelas);

        if(!is_null($kelas)){
            return response([
                'message' => 'Retrieve Kelas Success',
                'data' => $kelas
            ], 200);
        }

        return response([
            'message' => 'Kelas Not Found',
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
        $kelas = Kelas::find($id);
        if(is_null($kelas)){
            return response([
                'message' => 'Kelas Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = FacadesValidator::make($updateData, [
            'nama_kelas' => 'required',
            'harga' => 'required',
            'kapasitas' => 'required',
        ]);

        if($validate->fails())
            return response()->json($validate->errors(), 400);
        
        $kelas->nama_kelas = $updateData['nama_kelas'];
        $kelas->harga = $updateData['harga'];
        $kelas->kapasitas = $updateData['kapasitas'];
        
        if ($kelas->save()){
            return response([
                'message' =>'Update Kelas Success',
                'data' => $kelas
            ], 200);
        }

        return response([
            'message' =>'Update Kelas Failed',
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
        $kelas = Kelas::find($id);

        if(is_null($kelas)){
            return response([
                'message' => 'Kelas Not Found',
                'data' => null
            ], 404);
        }

        if($kelas->delete()){
            return response([
                'message' => 'Delete Kelas Success',
                'data' => $kelas
            ], 200);
        }

        return response([
            'message' => 'Delete Kelas Failed',
            'data' => null
        ], 400);
    }
}
