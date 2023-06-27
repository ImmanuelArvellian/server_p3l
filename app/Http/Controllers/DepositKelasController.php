<?php

namespace App\Http\Controllers;

use App\Models\Deposit_kelas;
use App\Models\Member;
use App\Models\Pegawai;
use App\Models\Kelas;
use App\Models\Promo_kelas;
use App\Http\Controllers\Controller;
use App\Models\Member_deposit_kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use function PHPUnit\Framework\isNull;

class DepositKelasController extends Controller
{
    /**
    * index
    *
    * @return void
    */
    public function index()
    {
        $deposit_kelas = Deposit_kelas::with('member', 'pegawai', 'kelas', 'promo_kelas')->latest()->get();
        $member = Member::latest()->get();
        $pegawai = Pegawai::latest()->get();
        $kelas = Kelas::latest()->get();
        $promo_kelas = Promo_kelas::latest()->get();

        $dk = $deposit_kelas->map(function($item) {
            $item->masa_exp = date('d M Y', strtotime($item->masa_exp));
            $item->tgl_transaksi = date('d M Y', strtotime($item->tgl_transaksi)); // Mengubah format tanggal menjadi tanggal Indonesia
            return $item;
        });
        
        if(count($deposit_kelas) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $deposit_kelas
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
            'id_kelas' => 'required',
            'sisa_deposit_kelas' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }
        
        $last = DB::table('deposit_kelas')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_deposit_kelas, 4, 3)) + 1;
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

        $kelas = Kelas::find($request->id_kelas);

        $deposit_kelas_input = $request->sisa_deposit_kelas;

        $promo_kelas1 = Promo_kelas::where('id_promo_kelas', '1')->first();
        $bonus_kelas1 = $promo_kelas1->bonus;
        $syarat_bonus1 = $promo_kelas1->syarat;
        $exp1 = $beli->addMonth()->toDateString();

        $promo_kelas2 = Promo_kelas::where('id_promo_kelas', '2')->first();
        $bonus_kelas2 = $promo_kelas2->bonus;
        $syarat_bonus2 = $promo_kelas2->syarat;
        $exp2 = $beli->addMonth(2)->toDateString();

        $deposit_kelas = Member_deposit_kelas::where('id_member', $request->id_member)
                                        ->where('id_kelas', $request->id_kelas)
                                        ->first();

        if(is_null($deposit_kelas))
        {
            if($deposit_kelas_input >= $syarat_bonus1 && $deposit_kelas_input < $syarat_bonus2){
                $totalDepositKelas = $deposit_kelas_input + $bonus_kelas1;
                $totalHarga = $kelas->harga * $deposit_kelas_input;
    
                $deposit_kelas = Deposit_kelas::create([
                    'id_deposit_kelas' => 'DEPK'.$id,
                    'id_member' => $request->id_member,
                    'id_pegawai' => $request->id_pegawai,
                    'id_kelas' => $request->id_kelas,
                    'id_promo_kelas' => 1,
                    'tgl_transaksi' => $tglBeli,
                    'no_struk' => $year.'.'.$month.'.'.$id,
                    'uang_deposit_kelas' => $totalHarga,
                    'bonus_deposit_kelas' => $bonus_kelas1,
                    'sisa_deposit_kelas' => $deposit_kelas_input,
                    'total_deposit_kelas' => $totalDepositKelas,
                    'masa_exp' => $exp1,
                ]);
                $storeData = Deposit_kelas::latest()->first();
        
            }else if($deposit_kelas_input >= $syarat_bonus2){
                $totalDepositKelas = $deposit_kelas_input + $bonus_kelas2;
                $totalHarga = $kelas->harga * $deposit_kelas_input;
    
                $deposit_kelas = Deposit_kelas::create([
                    'id_deposit_kelas' => 'DEPK'.$id,
                    'id_member' => $request->id_member,
                    'id_pegawai' => $request->id_pegawai,
                    'id_kelas' => $request->id_kelas,
                    'id_promo_kelas' => 2,
                    'tgl_transaksi' => $tglBeli,
                    'no_struk' => $year.'.'.$month.'.'.$id,
                    'uang_deposit_kelas' => $totalHarga,
                    'bonus_deposit_kelas' => $bonus_kelas2,
                    'sisa_deposit_kelas' => $deposit_kelas_input,
                    'total_deposit_kelas' => $totalDepositKelas,
                    'masa_exp' => $exp2,
                ]);
                $storeData = Deposit_kelas::latest()->first();
            }else{
                $totalDepositKelas = $deposit_kelas_input;
                $totalHarga = $kelas->harga * $deposit_kelas_input;
    
                $deposit_kelas = Deposit_kelas::create([
                    'id_deposit_kelas' => 'DEPK'.$id,
                    'id_member' => $request->id_member,
                    'id_pegawai' => $request->id_pegawai,
                    'id_kelas' => $request->id_kelas,
                    'id_promo_kelas' => 0,
                    'tgl_transaksi' => $tglBeli,
                    'no_struk' => $year.'.'.$month.'.'.$id,
                    'uang_deposit_kelas' => $totalHarga,
                    'bonus_deposit_kelas' => 0,
                    'sisa_deposit_kelas' => $deposit_kelas_input,
                    'total_deposit_kelas' => $totalDepositKelas,
                    'masa_exp' => $exp1,
                ]);
                $storeData = Deposit_kelas::latest()->first();
            }
        }
        else if($deposit_kelas->deposit_kelas < 1 || $deposit_kelas->tgl_exp < $tglBeli)
        {
            if($deposit_kelas_input >= $syarat_bonus1 && $deposit_kelas_input < $syarat_bonus2){
                $totalDepositKelas = $deposit_kelas_input + $bonus_kelas1;
                $totalHarga = $kelas->harga * $deposit_kelas_input;
    
                $deposit_kelas = Deposit_kelas::create([
                    'id_deposit_kelas' => 'DEPK'.$id,
                    'id_member' => $request->id_member,
                    'id_pegawai' => $request->id_pegawai,
                    'id_kelas' => $request->id_kelas,
                    'id_promo_kelas' => 1,
                    'tgl_transaksi' => $tglBeli,
                    'no_struk' => $year.'.'.$month.'.'.$id,
                    'uang_deposit_kelas' => $totalHarga,
                    'bonus_deposit_kelas' => $bonus_kelas1,
                    'sisa_deposit_kelas' => $deposit_kelas_input,
                    'total_deposit_kelas' => $totalDepositKelas,
                    'masa_exp' => $exp1,
                ]);
                $storeData = Deposit_kelas::latest()->first();

            }else if($deposit_kelas_input >= $syarat_bonus2){
                $totalDepositKelas = $deposit_kelas_input + $bonus_kelas2;
                $totalHarga = $kelas->harga * $deposit_kelas_input;
    
                $deposit_kelas = Deposit_kelas::create([
                    'id_deposit_kelas' => 'DEPK'.$id,
                    'id_member' => $request->id_member,
                    'id_pegawai' => $request->id_pegawai,
                    'id_kelas' => $request->id_kelas,
                    'id_promo_kelas' => 2,
                    'tgl_transaksi' => $tglBeli,
                    'no_struk' => $year.'.'.$month.'.'.$id,
                    'uang_deposit_kelas' => $totalHarga,
                    'bonus_deposit_kelas' => $bonus_kelas2,
                    'sisa_deposit_kelas' => $deposit_kelas_input,
                    'total_deposit_kelas' => $totalDepositKelas,
                    'masa_exp' => $exp2,
                ]);
                $storeData = Deposit_kelas::latest()->first();
        
            }else{
                $totalDepositKelas = $deposit_kelas_input;
                $totalHarga = $kelas->harga * $deposit_kelas_input;
    
                $deposit_kelas = Deposit_kelas::create([
                    'id_deposit_kelas' => 'DEPK'.$id,
                    'id_member' => $request->id_member,
                    'id_pegawai' => $request->id_pegawai,
                    'id_kelas' => $request->id_kelas,
                    'id_promo_kelas' => 0,
                    'tgl_transaksi' => $tglBeli,
                    'no_struk' => $year.'.'.$month.'.'.$id,
                    'uang_deposit_kelas' => $totalHarga,
                    'bonus_deposit_kelas' => 0,
                    'sisa_deposit_kelas' => $deposit_kelas_input,
                    'total_deposit_kelas' => $totalDepositKelas,
                    'masa_exp' => $exp1,
                ]);
                $storeData = Deposit_kelas::latest()->first();
            }
        }
        else
        {
            return response([
                'message' => 'Deposit Kelas Masih Ada atau Sudah Kadaluarsa',
                'data' => null
            ], 400);
        }

        $data = [
            'id_member' => $deposit_kelas->id_member,
            'id_kelas' => $deposit_kelas->id_kelas,
            'deposit_kelas' => $deposit_kelas->total_deposit_kelas,
            'tgl_exp' => $deposit_kelas->masa_exp,
            'created_at' => $deposit_kelas->created_at,
            'updated_at' => $deposit_kelas->updated_at
        ];
        DB::table('member_deposit_kelas')->insert($data);

        $storeData = Deposit_kelas::latest()->first();

        return response([
            'message' => 'Add Deposit Kelas Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_deposit_kelas)
    {
        $deposit_kelas = Deposit_kelas::find($id_deposit_kelas);
        $member = Member::all();
        $pegawai = Pegawai::all();
        $kelas = Kelas::all();
        $promo_kelas = Promo_kelas::all();

        if(!is_null($id_deposit_kelas)){
            return response([
                'message' => 'Retrieve Deposit Kelas Success',
                'data' => $id_deposit_kelas
            ], 200);
        }

        return response([
            'message' => 'Deposit Kelas Not Found',
            'data' => null
        ], 404);

    }

    public function resetDeposit($id_deposit_kelas){
        $deposit_kelas = Deposit_kelas::find($id_deposit_kelas);
        $idMember = $deposit_kelas->id_member;
        $member = Member::find($idMember);

        if($deposit_kelas->masa_exp <= Carbon::now()->toDatestring()){
            $deposit_kelas->total_deposit_kelas = 0;
            $deposit_kelas->masa_exp = null;
            $deposit_kelas->save();
        }   

        return response([
            'message' => 'Retrieve Customer Success',
            'data' => $member
        ], 200);
    }

    public function showExp()
    {
        $deposit_kelas = Deposit_kelas::where('masa_exp', '<=', Carbon::today())->get();

        if(!is_null($deposit_kelas)){
            return response([
                'message' => 'Retrieve Member Success',
                'data' => $deposit_kelas
            ], 200);
        }

        return response([
            'message' => 'Member not found',
            'data' => null
        ], 400);
    }
}
