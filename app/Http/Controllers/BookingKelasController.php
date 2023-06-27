<?php

namespace App\Http\Controllers;

use App\Models\Booking_kelas;
use App\Models\Member;
use App\Models\Jadwal_harian;
use App\Models\Kelas;
use App\Models\Deposit_kelas;
use App\Models\Member_deposit_kelas;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class BookingKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $booking_kelas = Booking_kelas::with('member', 'jadwal_harian.kelas', 'jadwal_harian.instruktur')->get();
        $member = Member::latest()->get();
        $jadwal_harian = Jadwal_harian::latest()->get();
        $kelas = Kelas::latest()->get();
        $deposit_kelas = Deposit_Kelas::all();

        $i = 0;
        foreach($deposit_kelas as $data){
            foreach($booking_kelas as $data2){
                if($data2->id_member == $data->id_member){
                    $booking_kelas[$i]['masa_exp'] = $data->masa_exp;
                }
                $i++;
            }
        }
        
        if(count($booking_kelas) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $booking_kelas
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
            // 'id_booking_kelas' => 'required',
            'id_member' => 'required',
            'id_jadwal_harian' => 'required',
            // 'no_struk' => 'required',
            // 'tgl_tujuan' => 'required',
            // 'tgl_presensi' => 'required',
            // 'tipe_pembayaran' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $last = DB::table('Booking_kelas')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_booking_kelas, 2, 3)) + 1;
        }

        if($count < 10){
            $id = '00'.$count;
        }else if($count < 100){
            $id = '0'.$count;
        }

        $jadwal_harian = Jadwal_harian::find($request->id_jadwal_harian)->first();
        
        $tgl_b = Carbon::now();
        $tglBeli = $tgl_b->toDateString();
        
        $curdate = $tglBeli;
        $tgl = Str::substr($curdate, 8, 2);
        $month = Str::substr($curdate, 5, 2); // 2023-01-02
        $year = Str::substr($curdate, 2, 2);
        Str::substr($year, -2);

        $member = $request->id_member;
        $findMember = Member::find($member);
        

        // $findKelas = Kelas::find($jadwal_harian->id_kelas);

        $kelas = DB::table('jadwal_harian')
            ->join('jadwal_umum', 'jadwal_harian.id_kelas', '=', 'jadwal_umum.id_kelas')
            ->join('kelas', 'jadwal_umum.id_kelas', '=', 'kelas.id_kelas')
            ->where('jadwal_harian.id_jadwal_harian', '=', $request->id_jadwal_harian)
            ->select('kelas.kapasitas', 'kelas.id_kelas', 'kelas.harga', 'jadwal_harian.jam_selesai')
            ->first();

        $memberDepositKelas = Member_deposit_kelas::where('id_member', '=',$findMember->id_member)
                                            ->where('id_kelas', '=', $kelas->id_kelas)
                                            ->first();
        
        $findJadwalHarian = Jadwal_harian::find($jadwal_harian);
        
        if ($findMember->status_membership == 1)
        {
            if ($kelas->kapasitas > 0)
            {
                $kapasitasKelas = $kelas->kapasitas - 1;
                if(is_null($memberDepositKelas) || $memberDepositKelas->deposit_kelas < 1)
                {
                    if ($findMember->sisa_deposit >= $kelas->harga)
                    {
                        $depositUang = $findMember->sisa_deposit - $kelas->harga;

                        $booking_kelas = Booking_kelas::create([
                            'id_booking_kelas' => 'BK'.$id,
                            'id_member' => $request->id_member,
                            'id_jadwal_harian' => $request->id_jadwal_harian,
                            'no_struk' => $year.'.'.$month.'.'.$id,
                            // 'tgl_tujuan' => $request->tgl_tujuan,
                            'tgl_presensi' => $request->tgl_presensi,
                            'tipe_pembayaran' => 'Deposit Uang',
                        ]);
                        $hasil = (int)$kelas->kapasitas - 1;
                        DB::table('kelas')->where('id_kelas', $kelas->id_kelas)->update(['kapasitas' => $hasil]);
                        $findMember->sisa_deposit = $findMember->sisa_deposit - $kelas->harga;
                        $findMember->save();

                        // $findMember->sisa_deposit = $depositUang;
                        // $findMember->save();
                        // $kelas->kapasitas = $kapasitasKelas;
                        // $kelas->save();

                        $storeData = Booking_kelas::latest()->first();
                    }
                    else
                    {
                        $booking_kelas = Booking_kelas::latest()->first();
                        return response([
                            'message' => 'Member Tidak Memiliki Deposit Kelas dan Deposit Uang!',
                            'data' => null
                        ], 404);
                        
                    }
                }
                else if ($memberDepositKelas->deposit_kelas >= 1)
                {
                    $booking_kelas = Booking_kelas::create([
                        'id_booking_kelas' => 'BK'.$id,
                        'id_member' => $request->id_member,
                        'id_jadwal_harian' => $request->id_jadwal_harian,
                        'no_struk' => $year.'.'.$month.'.'.$id,
                        // 'tgl_tujuan' => $request->tgl_tujuan,
                        'tgl_presensi' => $request->tgl_presensi,
                        'tipe_pembayaran' => 'Deposit Kelas',
                    ]);
                    $hasilDepoK = $memberDepositKelas->deposit_kelas - 1;
                    DB::table('member_deposit_kelas')->where('id_kelas', '=', $kelas->id_kelas)
                                            ->where('id_member', '=', $findMember->id_member)
                                            ->update(['deposit_kelas' => $hasilDepoK]);
                    // $memberDepositKelas->deposit_kelas = $memberDepositKelas->deposit_kelas - 1;
                    // $memberDepositKelas->save();
                    DB::table('kelas')->where('id_kelas', $kelas->id_kelas)->update(['kapasitas' => $kapasitasKelas]);
            
                    $storeData = Booking_kelas::latest()->first();
                }
            }
            else
            {
                $booking_kelas = Booking_kelas::latest()->first();
                return response([
                    'message' => 'Kelas Full!',
                    'data' => null
                ], 404);
            }
        }
        else
        {
            $booking_kelas = Booking_kelas::latest()->first();
            return response([
                'message' => 'Member Not Active',
                'data' => null
            ], 404);
        }
        
        return response([
            'message' => 'Add Booking_kelas Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_booking_kelas)
    {
        $booking_kelas = Booking_kelas::find($id_booking_kelas);

        if(!is_null($booking_kelas)){
            return response([
                'message' => 'Retrieve Booking_kelas Success',
                'data' => $booking_kelas
            ], 200);
        }

        return response([
            'message' => 'Booking_kelas Not Found',
            'data' => null
        ], 404);

    }

    public function presensiKelas($id_booking_kelas)
    {
        $booking_kelas = Booking_kelas::find($id_booking_kelas);

        $tgl_presensi = Carbon::now()->toDateTimeString();

        $booking_kelas->tgl_presensi = $tgl_presensi;
        $booking_kelas->save();
        
        return response([
            'message' =>'Update Presensi Success',
            'data' => $booking_kelas
        ], 200);
    }

    public function showSudah()
    {
        $booking_kelas = Booking_kelas::with('member', 'jadwal_harian.kelas', 'jadwal_harian.instruktur')
            ->whereNotNull('tgl_presensi')->latest()->get();

            $deposit_kelas = Deposit_Kelas::all();
            $i = 0;
            foreach($deposit_kelas as $data){
                foreach($booking_kelas as $data2){
                    if($data2->id_member == $data->id_member){
                        $booking_kelas[$i]['masa_exp'] = $data->masa_exp;
                    }
                    $i++;
                }
            }
        
        return response([
            'message' => 'Member Sudah Presensi Sukses',
            'data' => $booking_kelas
        ], 200);
    }

    public function showBelum()
    {
        $booking_kelas = Booking_kelas::with('member', 'jadwal_harian.kelas')
            ->whereNull('tgl_presensi')->latest()->get();

        return response([
            'message' => 'Member Belum Presensi Sukses',
            'data' => $booking_kelas
        ], 200);
    }

    public function getBookingByMember($id_member)
    {
        $booking_kelas = Booking_kelas::with('jadwal_harian.kelas', 'jadwal_harian.instruktur')
                                            ->where('id_member', '=', $id_member)
                                            ->get();

        if(!is_null($booking_kelas)){
            return response([
                'message' => 'Retrieve Booking Kelas Success',
                'data' => $booking_kelas
            ], 200);
        }

        return response([
            'message' => 'Booking Kelas Not Found',
            'data' => null
        ], 404);

    }
}
