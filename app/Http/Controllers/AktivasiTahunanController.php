<?php

namespace App\Http\Controllers;

use App\Models\Aktivasi_tahunan;
use App\Models\Member;
use App\Models\Pegawai;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AktivasiTahunanController extends Controller
{
    /**
    * index
    *
    * @return void
    */
    public function index()
    {
        $aktivasi_tahunan = Aktivasi_tahunan::with('member', 'pegawai')->latest()->get();
        $member = Member::latest()->get();
        $pegawai = Pegawai::latest()->get();

        $at = $aktivasi_tahunan->map(function($item) {
            $item->tgl_transaksi = date('d M Y', strtotime($item->tgl_transaksi));
            $item->masa_aktif = date('d M Y', strtotime($item->masa_aktif)); // Mengubah format tanggal menjadi tanggal Indonesia
            return $item;
        });
        
        if(count($aktivasi_tahunan) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $aktivasi_tahunan
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
            'id_member' => 'required',
            'id_pegawai' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }
        
        $last = DB::table('Aktivasi_tahunan')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_aktivasi, 3, 3)) + 1;
        }

        if($count < 10){
            $id = '00'.$count;
        }else if($count < 100){
            $id = '0'.$count;
        }

        $tgl_t = Carbon::now();
        $tglBeli = $tgl_t->toDateString();

        $curdate = $tglBeli;
        $tgl = Str::substr($curdate, 8, 2);
        $month = Str::substr($curdate, 5, 2); // 2023-01-02
        $year = Str::substr($curdate, 2, 2);
        Str::substr($year, -2);

        $beli = Carbon::parse($tglBeli);
        $exp = $beli->addYear();
    
        $aktivasi_tahunan = Aktivasi_tahunan::create([
            'id_aktivasi' => 'ACT'.$id,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'tgl_transaksi' => $tglBeli,
            'masa_aktif' => $exp,
            'no_struk' => $year.'.'.$month.'.'.$id,
        ]);

        $memberId = $request->id_member;
        $member = Member::find($memberId);
        $member->tgl_exp_membership = $exp;
        $member->status_membership = 1;
        $member->save();

        $storeData = Aktivasi_tahunan::latest()->first();

        return response([
            'message' => 'Add Aktivasi Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_aktivasi)
    {
        $aktivasi_tahunan = Aktivasi_tahunan::find($id_aktivasi);
        // $kelas = Kelas::all();
        // $instruktur = Instruktur::all();

        if(!is_null($id_aktivasi)){
            return response([
                'message' => 'Retrieve Aktivasi Success',
                'data' => $id_aktivasi
            ], 200);
        }

        return response([
            'message' => 'Aktivasi Not Found',
            'data' => null
        ], 404);

    }
}
