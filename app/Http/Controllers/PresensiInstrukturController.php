<?php

namespace App\Http\Controllers;

use App\Models\Presensi_instruktur;
use App\Models\Jadwal_harian;
use App\Models\Instruktur;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PresensiInstrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $presensi_instruktur = Presensi_instruktur::all();
        
        if(count($presensi_instruktur) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $presensi_instruktur
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
            // 'id_presensi_instruktur' => 'required',
            'id_jadwal_harian' => 'required',
            // 'tgl_presensi_instruktur' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $last = DB::table('Presensi_instruktur')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_presensi_instruktur, 2, 3)) + 1;
        }

        if($count < 10){
            $id = '00'.$count;
        }else if($count < 100){
            $id = '0'.$count;
        }

        $tgl_pi = Carbon::now();
        // $tglPG = $tgl_pi->toDateString();

        // $curdate = $tgl_pi;
        // $tgl = Str::substr($curdate, 8, 2);
        // $month = Str::substr($curdate, 5, 2); // 2023-01-02
        // $year = Str::substr($curdate, 2, 2);
        // Str::substr($year, -2);

        $jadwal_harian = Jadwal_harian::find($storeData['id_jadwal_harian']);
        $instruktur = Instruktur::find($jadwal_harian['id_instruktur']);

        // $updateJamMulai = Carbon::now()->format('H:i:s');
        $updateJamMulai = Carbon::parse('08:01:10');
        $jamMulaiKelas = Carbon::parse($jadwal_harian['jam_mulai']);
        $keterlambatan = $updateJamMulai->diff($jamMulaiKelas);

        $keterlambatanInstruktur = Carbon::parse($instruktur['jumlah_terlambat']);

        $hours = $keterlambatan->h;
        $minutes = $keterlambatan->i;
        $second = $keterlambatan->s;

        $totalKeterlambatan = $keterlambatanInstruktur->addHours($hours)->addMinutes($minutes)->addSeconds($second);
        $hasilKeterlambatan = $totalKeterlambatan->toTimeString();

        $storeData['jam_mulai'] = $updateJamMulai;
        $storeData['jumlah_terlambat'] = $hours.':'. $minutes.':'.$second;

        $jadwal_harian->jam_mulai = $storeData['jam_mulai'];
        $jadwal_harian->save();

        $instruktur->jumlah_terlambat = $hasilKeterlambatan;
        $instruktur->save();

        $presensi_instruktur = Presensi_instruktur::create([
            'id_presensi_instruktur' => 'PI'.$id,
            'id_jadwal_harian' => $request->id_jadwal_harian,
            'tgl_presensi_instruktur' => $tgl_pi,
        ]);

        $storeData = Presensi_instruktur::latest()->first();
        
        return response([
            'message' => 'Add Presensi_instruktur Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_presensi_instruktur)
    {
        $presensi_instruktur = Presensi_instruktur::find($id_presensi_instruktur);

        if(!is_null($presensi_instruktur)){
            return response([
                'message' => 'Retrieve Presensi_instruktur Success',
                'data' => $presensi_instruktur
            ], 200);
        }

        return response([
            'message' => 'Presensi_instruktur Not Found',
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
    public function update(Request $request, $id_presensi_instruktur)
    {
        $presensi_instruktur = Presensi_instruktur::find($id_presensi_instruktur);
        if(is_null($presensi_instruktur)){
            return response([
                'message' => 'Presensi_instruktur Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = FacadesValidator::make($updateData, [
            // 'id_booking_gym' => 'required',
            'id_member' => 'required',
            'id_sesi' => 'required',
            'tgl_tujuan' => 'required',
        ]);

        if($validate->fails())
            return response()->json($validate->errors(), 400);
        
        $presensi_instruktur->id_member = $updateData['id_member'];
        $presensi_instruktur->id_sesi = $updateData['id_sesi'];
        $presensi_instruktur->tgl_tujuan = $updateData['tgl_tujuan'];
        
        if ($presensi_instruktur->save()){
            return response([
                'message' =>'Update Presensi_instruktur Success',
                'data' => $presensi_instruktur
            ], 200);
        }

        return response([
            'message' =>'Update Presensi_instruktur Failed',
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
        $presensi_instruktur = Presensi_instruktur::find($id);

        if(is_null($presensi_instruktur)){
            return response([
                'message' => 'Presensi_instruktur Not Found',
                'data' => null
            ], 404);
        }

        if($presensi_instruktur->delete()){
            return response([
                'message' => 'Delete Presensi_instruktur Success',
                'data' => $presensi_instruktur
            ], 200);
        }

        return response([
            'message' => 'Delete Presensi_instruktur Failed',
            'data' => null
        ], 400);
    }
}
