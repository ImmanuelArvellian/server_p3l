<?php

namespace App\Http\Controllers;

use App\Models\Jadwal_umum;
use App\Models\Kelas;
use App\Models\Instruktur;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class JadwalUmumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jadwal_umum = Jadwal_umum::with('kelas', 'instruktur')->get();
        $kelas = Kelas::latest()->get();
        $instruktur = Instruktur::latest()->get();
        
        if(count($jadwal_umum) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $jadwal_umum
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
            'id_kelas' => 'required',
            'id_instruktur' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $checkJadwal = Jadwal_umum::where('hari', $storeData['hari'])
                                    ->where('jam_mulai', $storeData['jam_mulai'])
                                    ->where('id_instruktur', $storeData['id_instruktur'])
                                    ->get();
        
        if(count($checkJadwal) != 0){
            return response(['message' => 'Instruktur Sudah Memiliki Jadwal'], 400);
        }else{
            $last = DB::table('Jadwal_umum')->latest()->first();
            if($last == null){
                $count = 1;
            }else{
                $count = ((int)Str::substr($last->id_jadwal_umum, 2, 3)) + 1;
            }

            if($count < 10){
                $id = '00'.$count;
            }else if($count < 100){
                $id = '0'.$count;
            }
        
            $jadwal_umum = Jadwal_umum::create([
                'id_jadwal_umum' => 'JU'.$id,
                'id_kelas' => $request->id_kelas,
                'id_instruktur' => $request->id_instruktur,
                'hari' => $request->hari,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ]);

            $storeData = Jadwal_umum::latest()->first();
        }

        return response([
            'message' => 'Add Jadwal Umum Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_jadwal_umum)
    {
        $jadwal_umum = Jadwal_umum::find($id_jadwal_umum);
        // $kelas = Kelas::all();
        // $instruktur = Instruktur::all();

        if(!is_null($jadwal_umum)){
            return response([
                'message' => 'Retrieve Jadwal Umum Success',
                'data' => $jadwal_umum
            ], 200);
        }

        return response([
            'message' => 'Jadwal Umum Not Found',
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
        $jadwal_umum = Jadwal_umum::find($id);
        if(is_null($jadwal_umum)){
            return response([
                'message' => 'Jadwal Umum Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = FacadesValidator::make($updateData, [
            'id_kelas' => 'required',
            'id_instruktur' => 'required',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $checkJadwal = Jadwal_umum::where('hari', $updateData['hari'])
                                    ->where('jam_mulai', $updateData['jam_mulai'])
                                    ->where('id_instruktur', $updateData['id_instruktur'])
                                    ->get();

        if(count($checkJadwal) != 0){
            return response(['message' => 'Instruktur Sudah Memiliki Jadwal'], 400);
        }else{
            // $jadwal_umum->id_jadwal_umum = $updateData['id_jadwal_umum'];
            $jadwal_umum->id_kelas = $updateData['id_kelas'];
            $jadwal_umum->id_instruktur = $updateData['id_instruktur'];
            $jadwal_umum->hari = $updateData['hari'];
            $jadwal_umum->jam_mulai = $updateData['jam_mulai'];
            $jadwal_umum->jam_selesai = $updateData['jam_selesai'];
            
            if ($jadwal_umum->save()){
                return response([
                    'message' =>'Update Jadwal Umum Success',
                    'data' => $jadwal_umum
                ], 200);
            }

            return response([
                'message' =>'Update Jadwal Umum Failed',
                'data' => null
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jadwal_umum = Jadwal_umum::find($id);

        if(is_null($jadwal_umum)){
            return response([
                'message' => 'Jadwal Umum Not Found',
                'data' => null
            ], 404);
        }

        if($jadwal_umum->delete()){
            return response([
                'message' => 'Delete Jadwal Umum Success',
                'data' => $jadwal_umum
            ], 200);
        }

        return response([
            'message' => 'Delete Jadwal Umum Failed',
            'data' => null
        ], 400);
    }
}
