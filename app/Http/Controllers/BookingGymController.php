<?php

namespace App\Http\Controllers;

use App\Models\Booking_gym;
use App\Models\Member;
use App\Models\Sesi;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BookingGymController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $booking_gym = Booking_gym::with('member', 'sesi')->get();
        $member = Member::latest()->get();
        $sesi = Sesi::latest()->get();
        
        if(count($booking_gym) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $booking_gym
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
            // 'id_booking_gym' => 'required',
            'id_member' => 'required',
            'id_sesi' => 'required',
            // 'no_struk' => 'required',
            'tgl_tujuan' => 'required',
            // 'tgl_presensi' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }

        $last = DB::table('Booking_gym')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_booking_gym, 2, 3)) + 1;
        }

        if($count < 10){
            $id = '00'.$count;
        }else if($count < 100){
            $id = '0'.$count;
        }

        $curdate = $request->tgl_tujuan;
        $tgl = Str::substr($curdate, 8, 2);
        $month = Str::substr($curdate, 5, 2); // 2023-01-02
        $year = Str::substr($curdate, 2, 2);
        Str::substr($year, -2);

        $member = $request->id_member;
        $findMember = Member::find($member);

        $kuotaGym = $request->id_sesi;
        $findKuota = Sesi::find($kuotaGym);

        $tglBook = $request->id_booking_gym;
        $findTgl = Booking_gym::find($tglBook);
        
        $bookingDate = Carbon::parse(($request->tgl_tujuan));

        $existingBooking = Booking_gym::where('id_member', $member)
            ->whereDate('tgl_tujuan', $bookingDate->toDateString())
            ->first(); 

        if ($findMember->status_membership == '1')
        {
            if ($findKuota->kuota > 0)
            {
                if (!$existingBooking)
                {
                    $kuotaGym = $findKuota->kuota - 1;
                    
                    $booking_gym = Booking_gym::create([
                        'id_booking_gym' => 'BG'.$id,
                        'id_member' => $request->id_member,
                        'id_sesi' => $request->id_sesi,
                        'no_struk' => $year.'.'.$month.'.'.$id,
                        'tgl_tujuan' => $request->tgl_tujuan,
                        'tgl_presensi' => $request->tgl_presensi,
                    ]);

                    $findKuota->kuota = $kuotaGym;
                    $findKuota->save();
            
                    $storeData = Booking_gym::latest()->first();
                }
                else
                {
                    $booking_gym = Booking_gym::latest()->first();
                    return response([
                        'message' => 'Member Sudah Booking untuk Tanggal Ini!',
                        'data' => null
                    ], 404);
                }
            }
            else
            {
                $booking_gym = Booking_gym::latest()->first();
                return response([
                    'message' => 'Kuota Full!',
                    'data' => null
                ], 404);
            }
        }
        else
        {
            $booking_gym = Booking_gym::latest()->first();
            return response([
                'message' => 'Member Not Active',
                'data' => null
            ], 404);
        }
        
        return response([
            'message' => 'Add Booking_gym Success',
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
        $booking_gym = Booking_gym::find($id_booking_kelas);

        if(!is_null($booking_gym)){
            return response([
                'message' => 'Retrieve Booking_gym Success',
                'data' => $booking_gym
            ], 200);
        }

        return response([
            'message' => 'Booking_gym Not Found',
            'data' => null
        ], 404);

    }

    public function cancelBooking($id_booking_gym)
    {
        $booking_gym = Booking_gym::find($id_booking_gym);
        
        $idSesi = $booking_gym->id_sesi;
        $sesi = Sesi::find($idSesi);

        // Pastikan booking ditemukan
        if ($booking_gym) {
            // Periksa apakah booking masih dapat dibatalkan (H-1)
            $h1Date = Carbon::parse($booking_gym->tgl_tujuan)->subDay();
            $today = Carbon::now();

            if ($h1Date->greaterThanOrEqualTo($today)) {
                // Batalkan booking
                $booking_gym->delete();
                $sesi->kuota = $sesi->kuota + 1;
                $sesi->save();

                return response()->json(['message' => 'Booking berhasil dibatalkan'], 200);
            } else {
                return response()->json(['message' => 'Maaf, booking tidak dapat dibatalkan'], 403);
            }
        } else {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }
    }

    public function presensiGym($id_booking_gym)
    {
        $booking_gym = Booking_gym::find($id_booking_gym);

        $tgl_presensi = Carbon::now()->toDateTimeString();

        $booking_gym->tgl_presensi = $tgl_presensi;
        $booking_gym->save();
        
        return response([
            'message' =>'Update Presensi Success',
            'data' => $booking_gym
        ], 200);
    }

    public function showSudah()
    {
        $booking_gym = Booking_gym::with('member', 'sesi')
            ->whereNotNull('tgl_presensi')->latest()->get();
        
        return response([
            'message' => 'Member Sudah Presensi Sukses',
            'data' => $booking_gym
        ], 200);
    }

    public function showBelum()
    {
        $booking_gym = Booking_gym::with('member', 'sesi')
            ->whereNull('tgl_presensi')->latest()->get();

        return response([
            'message' => 'Member Belum Presensi Sukses',
            'data' => $booking_gym
        ], 200);
    }
}