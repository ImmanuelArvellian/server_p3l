<?php

namespace App\Http\Controllers;

use App\Models\Izin_instruktur;
use App\Models\Instruktur;
use App\Http\Controllers\Controller;
use App\Models\Jadwal_harian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IzinInstrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $izin_instruktur = Izin_instruktur::with('instruktur')->latest()->get();
        // $instruktur = Instruktur::latest()->get();
        $izin_instruktur = DB::select('SELECT a.*, b.nama as instruktur_nama, c.nama as pengganti_nama FROM izin_instruktur a
        join instruktur b
        on a.id_instruktur = b.id_instruktur
        join instruktur c
        on a.id_instruktur_pengganti = c.id_instruktur');
        
        if(!is_null($izin_instruktur)){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $izin_instruktur
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
            // 'id_izin' => 'required',
            'id_instruktur' => 'required',
            'id_jadwal_harian' => 'required',
            // 'status' => 'required',
            'keterangan' => 'required',
            'id_instruktur_pengganti' => 'required',
            // 'tgl_izin' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $tgl_i = Carbon::now();
        $tglIzin = $tgl_i->toDateString();

        $last = DB::table('Izin_instruktur')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_izin, 2, 3)) + 1;
        }

        if($count < 10){
            $id = '00'.$count;
        }else if($count < 100){
            $id = '0'.$count;
        }

        $statuslama = 'Belum Dikonfirmasi';

        $izin_instruktur = Izin_instruktur::create([
            'id_izin' => 'IZ'.$id,
            'id_instruktur' => $request->id_instruktur,
            'id_jadwal_harian' => $request->id_jadwal_harian,
            'status' => $statuslama,
            'keterangan' => $request->keterangan,
            'id_instruktur_pengganti' => $request->id_instruktur_pengganti,
            'tgl_izin' => $tglIzin,
        ]);

        $storeData = Izin_instruktur::latest()->first();
        
        return response([
            'message' => 'Add Izin Instruktur Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_izin)
    {
        $izin_instruktur = Izin_instruktur::find($id_izin);

        if(!is_null($izin_instruktur)){
            return response([
                'message' => 'Retrieve Izin Instruktur Success',
                'data' => $izin_instruktur
            ], 200);
        }

        return response([
            'message' => 'Izin Instruktur Not Found',
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
    public function konfirmasi($id_izin)
    {
        $izin_instruktur = Izin_instruktur::find($id_izin);
        if(is_null($izin_instruktur)){
            return response([
                'message' => 'Izin Instruktur Not Found',
                'data' => null
            ], 404);
        }

        $instruktur = Instruktur::find($izin_instruktur->id_instruktur);
        $instrukturLama = $instruktur->nama;

        $idJadwalHarian = $izin_instruktur->id_jadwal_harian;
        $jadwal_harian = Jadwal_harian::find($idJadwalHarian);

        if($izin_instruktur->status == "Belum Dikonfirmasi"){
            $izin_instruktur->status = "Sudah Dikonfirmasi";
            $izin_instruktur->save();
            $jadwal_harian->id_instruktur = $izin_instruktur->id_instruktur_pengganti;
            $jadwal_harian->status = "Menggantikan ".$instrukturLama;
            $jadwal_harian->save();
        }
        
        if ($izin_instruktur->save()){
            return response([
                'message' =>'Update Izin Instruktur Success',
                'data' => $izin_instruktur
            ], 200);
        }

        return response([
            'message' =>'Update Izin Instruktur Failed',
            'data' => null
        ], 400);
    }

    public function getIzinByInstruktur($id_instruktur)
    {
        $izin_instruktur = Izin_instruktur::with('jadwal_harian.kelas', 'instruktur')
                                            ->where('id_instruktur', '=', $id_instruktur)
                                            ->get();

        if(!is_null($izin_instruktur)){
            return response([
                'message' => 'Retrieve Izin Instruktur Success',
                'data' => $izin_instruktur
            ], 200);
        }

        return response([
            'message' => 'Izin Instruktur Not Found',
            'data' => null
        ], 404);

    }
}
