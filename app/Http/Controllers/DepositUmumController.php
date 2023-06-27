<?php

namespace App\Http\Controllers;

use App\Models\Deposit_umum;
use App\Models\Member;
use App\Models\Pegawai;
use App\Models\Promo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepositUmumController extends Controller
{
    /**
    * index
    *
    * @return void
    */
    public function index()
    {
        $deposit_umum = Deposit_umum::with('member', 'pegawai', 'promo')->latest()->get();
        $member = Member::latest()->get();
        $pegawai = Pegawai::latest()->get();
        $promo = Promo::latest()->get();

        $du = $deposit_umum->map(function($item) {
            $item->tgl_transaksi = date('d M Y', strtotime($item->tgl_transaksi)); // Mengubah format tanggal menjadi tanggal Indonesia
            return $item;
        });
        
        if(count($deposit_umum) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $deposit_umum
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
    public function store(Request $request, $id_member)
    {
        $storeData = $request->all();
        $member = Member::find($id_member);
        $validate  = FacadesValidator::make($storeData, [
            'id_member' => 'required',
            'id_pegawai' => 'required',
            'deposit_uang' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }
        
        $last = DB::table('Deposit_umum')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_deposit_umum, 4, 3)) + 1;
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

        $member = Member::find($id_member);
        $promoReguler = Promo::where('id_promo', '2')->first();

        $syaratPromo = $promoReguler->syarat;
        $bonusPromo = $promoReguler->bonus;

        if($request->deposit_uang >= $syaratPromo){
            $total = $member->sisa_deposit + $request->deposit_uang + $bonusPromo;
            $deposit_umum = Deposit_umum::create([
                'id_deposit_umum' => 'DEPU'.$id,
                'id_member' => $request->id_member,
                'id_pegawai' => $request->id_pegawai,
                'id_promo' => 2,
                'tgl_transaksi' => $tglBeli,
                'no_struk' => $year.'.'.$month.'.'.$id,
                'deposit_uang' => $request->deposit_uang,
                'bonus_deposit' => 300000,
                'sisa_deposit' => $member->sisa_deposit,
                'total_deposit' => $total,
            ]);
        }else if($request->deposit_uang < $syaratPromo && $request->deposit_uang >= 500000){
            $total = $member->sisa_deposit + $request->deposit_uang;
            $deposit_umum = Deposit_umum::create([
                'id_deposit_umum' => 'DEPU'.$id,
                'id_member' => $request->id_member,
                'id_pegawai' => $request->id_pegawai,
                'id_promo' => 1,
                'tgl_transaksi' => $tglBeli,
                'no_struk' => $year.'.'.$month.'.'.$id,
                'deposit_uang' => $request->deposit_uang,
                'bonus_deposit' => 0,
                'sisa_deposit' => $member->sisa_deposit,
                'total_deposit' => $total,
            ]);
        }else if($request->deposit_uang < 500000){
            return response([
                'message' => 'TopUp Minimal Rp500.000,-',
                'data' => null,
            ], 400);
        }

        $member->sisa_deposit = $deposit_umum->total_deposit;
        $member->save();
    
        $storeData = Deposit_umum::latest()->first();

        return response([
            'message' => 'Add Deposit Umum Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_deposit_umum)
    {
        $deposit_umum = Deposit_umum::find($id_deposit_umum);
        // $kelas = Kelas::all();
        // $instruktur = Instruktur::all();

        if(!is_null($id_deposit_umum)){
            return response([
                'message' => 'Retrieve Deposit Umum Success',
                'data' => $id_deposit_umum
            ], 200);
        }

        return response([
            'message' => 'Deposit Umum Not Found',
            'data' => null
        ], 404);

    }
}
