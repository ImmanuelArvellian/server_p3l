<?php

namespace App\Http\Controllers;

use App\Models\Jadwal_harian;
use App\Models\Kelas;
use App\Models\Instruktur;
use App\Models\Jadwal_umum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalHarianController extends Controller
{
    /**
    * index
    *
    * @return void
    */
    public function index()
    {
        $jadwal_harian = Jadwal_harian::with('kelas', 'instruktur')->get();
        $kelas = Kelas::latest()->get();
        $instruktur = Instruktur::latest()->get();

        $jh = $jadwal_harian->map(function($item) {
            $item->tanggal = date('d M Y', strtotime($item->tanggal)); // Mengubah format tanggal menjadi tanggal Indonesia
            return $item;
        });
        
        if(count($jadwal_harian) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $jadwal_harian
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
    public function store()
    {
        $jadwal_harian = Jadwal_harian::all();

        $now = Carbon::now();
        $startDate = Carbon::parse($now)->startOfWeek();
        $endDate = Carbon::parse($now)->endOfWeek();

        $existingJadwal = Jadwal_harian::whereBetween('tanggal', [$startDate, $endDate])->exists();

        if($existingJadwal){
            return response([
                'message' => 'Jadwal Minggu Ini Sudah Digenerate',
                'data' => $jadwal_harian
            ], 400);
        }else{
            $jadwal_umum = Jadwal_umum::all();

            foreach($jadwal_umum as $item){
                $storeData['id_instruktur'] = $item->id_instruktur;
                $storeData['id_kelas'] = $item->id_kelas;
                $storeData['hari'] = $item->hari;
                $storeData['jam_mulai'] = $item->jam_mulai;
                $storeData['jam_selesai'] = $item->jam_selesai;
                $storeData['status'] = 'Tidak Libur';

                $last = DB::table('Jadwal_harian')->latest('id_jadwal_harian')->first();
                if ($last == null) {
                    $increment = 1;
                } else {
                    $lastId = (int)substr($last->id_jadwal_harian, 2);
                    $increment = $lastId + 1;
                }

                $id_jadwal_harian = 'JH' . str_pad($increment, 3, '0', STR_PAD_LEFT);
                $storeData['id_jadwal_harian'] = $id_jadwal_harian;
                
                $tanggalMingguan = $startDate->copy();

                if ($item->hari == 'Senin') {

                } elseif ($item->hari == 'Selasa') {
                    $tanggalMingguan->addDay();
                } elseif ($item->hari == 'Rabu') {
                    $tanggalMingguan->addDays(2);
                } elseif ($item->hari == 'Kamis') {
                    $tanggalMingguan->addDays(3);
                } elseif ($item->hari == 'Jumat') {
                    $tanggalMingguan->addDays(4);
                } elseif ($item->hari == 'Sabtu') {
                    $tanggalMingguan->addDays(5);
                } elseif ($item->hari == 'Minggu') {
                    $tanggalMingguan->addDays(6);
                }

                $storeData['tanggal'] = $tanggalMingguan;
                $jadwal_harian = Jadwal_Harian::create($storeData);  
            }

            $jadwal_harian = Jadwal_Harian::all();

            return response([
                'message' => 'Generate Jadwal Harian Success',
                'data' => $jadwal_harian
            ], 200);    
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_jadwal_harian)
    {
        $jadwal_harian = Jadwal_harian::find($id_jadwal_harian);
        // $kelas = Kelas::all();
        // $instruktur = Instruktur::all();

        if(!is_null($jadwal_harian)){
            return response([
                'message' => 'Retrieve Jadwal Harian Success',
                'data' => $jadwal_harian
            ], 200);
        }

        return response([
            'message' => 'Jadwal Harian Not Found',
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
    public function ubahStatus(Request $request, $id_jadwal_harian)
    {
        $jadwal_harian = Jadwal_harian::find($id_jadwal_harian);
        if(is_null($jadwal_harian)){
            return response([
                'message' => 'Jadwal Harian Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();

        $jadwal_harian->update([
            'status' => $updateData['status'],
        ]);
        
        if ($jadwal_harian->save()){
            return response([
                'message' =>'Update Jadwal Harian Success',
                'data' => $jadwal_harian
            ], 200);
        }

        return response([
            'message' =>'Update Jadwal Harian Failed',
            'data' => null
        ], 400);
    }
}
