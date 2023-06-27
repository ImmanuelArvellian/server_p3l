<?php

namespace App\Http\Controllers;

use App\Models\Aktivasi_tahunan;
use App\Models\Deposit_umum;
use App\Models\Deposit_kelas;
use App\Models\Kelas;
use App\Models\Instruktur;
use App\Models\Jadwal_harian;
use App\Models\Booking_kelas;
use App\Models\Booking_gym;
use App\Models\Presensi_instruktur;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function laporanPendapatan()
    {
        App::setLocale('id');

        $laporan = collect([]);
        for ($i = 1; $i < 13; $i++) {
            $bulan = Carbon::create(null, $i, 1)->translatedFormat('F');
            $storeData['bulan'] = $bulan;
            $aktivasi = Aktivasi_tahunan::whereMonth('tgl_transaksi', $i)->get();
            $deposit_uang = Deposit_umum::whereMonth('tgl_transaksi', $i)->get();

            $storeData['deposit_uang'] = 0;
            foreach ($deposit_uang as $item) {
                $storeData['deposit_uang'] = $storeData['deposit_uang'] + $item->total_deposit;
            }

            $storeData['deposit_kelas'] = 0;
            $deposit_kelas = Deposit_kelas::whereMonth('tgl_transaksi', $i)->get();
            foreach ($deposit_kelas as $item) {
                $storeData['deposit_kelas'] = $storeData['deposit_kelas'] + $item->uang_deposit_kelas;
            }
            $storeData['aktivasi'] = count($aktivasi) * 3000000;
            $storeData['totalDeposit'] = $storeData['deposit_uang'] + $storeData['deposit_kelas'];
            $storeData['total'] = $storeData['deposit_uang'] + $storeData['deposit_kelas'] + $storeData['aktivasi'];
            $laporan->add($storeData);
        }

        $storeData['totalSemua'] = 0;

        foreach ($laporan as $item) {
            $storeData['totalSemua'] = $item['total'] + $storeData['totalSemua'];
        }
        
        $now = Carbon::now()->format('Y-m-d');
        $tgl = Carbon::now()->translatedFormat('d F Y');
        $storeData['tahun'] = substr($now, 0, 4);
        $storeData['tanggal'] = $tgl;

        $laporan['total_semua'] = $storeData['totalSemua'];
        $th = substr($now, 0, 4);
        $tg = $tgl;

        if (!is_null($laporan)) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $laporan,
                'tahun' => $th,
                'tgl_cetak'=> $tg,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 400);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function laporanKelas()
    {
        App::setlocale('id');
        
        $laporan = collect([]);

        $now = Carbon::now()->format('Y-m-d');
        $bulanCetak = Carbon::create(null, substr($now, 5, 2), 1)->translatedFormat('F');
        $tgl = Carbon::now()->translatedFormat('d F Y');

        $b = $bulanCetak;
        $th = substr($now, 0, 4);
        $tg = $tgl;

        $bulan = substr($now, 5, 2);
        $kelas = kelas::orderBy('nama_kelas', 'asc')->get();
        $instruktur = instruktur::all();

        foreach($kelas as $item1) {
            foreach($instruktur as $item2) {
                $id_kelas = $item1->id_kelas;
                $id_instruktur = $item2->id_instruktur;

                $jadwalHarian = jadwal_harian::whereMonth('tanggal', $bulan)
                                            ->where('id_kelas', '=', $id_kelas)
                                            ->where('id_instruktur', '=', $id_instruktur)
                                            ->get();
                if(count($jadwalHarian)>0) {
                    $storeData['kelas'] = $item1->nama_kelas;
                    $storeData['instruktur'] = $item2->nama;

                    $jumlahLibur = jadwal_harian::whereMonth('tanggal', $bulan)
                                                ->where('id_kelas', '=', $item1->id_kelas)
                                                ->where('id_instruktur', '=', $item2->id_instruktur)
                                                ->where('status', '=', 'Libur')
                                                ->get();
                    $storeData['jumlah_libur'] = count($jumlahLibur);

                    // $jumlahP = jadwal_harian::whereMonth('tanggal', $bulan)
                    //                         ->where('id_kelas','=', $item1->id_kelas)
                    //                         ->where('id_instruktur','=',$item2->id_instruktur)
                    //                         ->where('status','=','Ada Kelas')
                    //                         ->get();

                    $storeData['jumlah_peserta'] = 0;
                    foreach($jadwalHarian as $item3) {
                        $jumlahPeserta = booking_kelas::where('id_jadwal_harian', '=', $item3->id_jadwal_harian)
                                                    ->whereNotNull('tgl_presensi')
                                                    ->get();
                        $storeData['jumlah_peserta'] = $storeData['jumlah_peserta'] + count($jumlahPeserta);
                    }
                    $storeData['id_kelas'] = $item1->id_kelas;
                    $laporan->add($storeData);
                }
            }
        }

        if(!is_null($laporan)) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $laporan,
                'bulan'=> $b,
                'tahun' => $th,
                'tgl_cetak'=> $tg,
            ], 200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function laporanGym()
    {
        App::setLocale('id');
        
        $laporanGym = collect([]);
        $now = Carbon::now()->format('Y-m-d');
        $bulan = Carbon::create(null, substr($now, 5, 2), 1)->translatedFormat('F');
        $tgl = Carbon::now()->translatedFormat('d F Y');

        $b = $bulan;
        $th = substr($now, 0, 4);
        $tg = $tgl;

        $akhir = Carbon::now();
        $akhir->endOfMonth();

        $akhir = substr($akhir, 8, 2);//0000-00-00
        $temp = (int)$akhir;

        for($i = 0;$i<$temp;$i++) {
            $tgl = Carbon::now();
            $tgl->startOfMonth();
            $tgl->addDays($i)->format('Y-F-d');
            $presensi = booking_gym::where('tgl_tujuan', '=', $tgl)
                                    ->whereNotNull('tgl_presensi')
                                    ->get();
            $count = count($presensi);
            $storeData['jumlah'] = $count;
            $storeData['tanggal'] = $tgl->translatedFormat('d F Y');
            $laporanGym->add($storeData);
        }


        if(count($laporanGym) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $laporanGym,
                'bulan'=> $b,
                'tahun' => $th,
                'tgl_cetak'=> $tg,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function laporanKinerjaInstruktur()
    {
        App::setLocale('id');
        $laporan = collect([]);

        $now = Carbon::now()->format('Y-m-d');
        $bulanCetak = Carbon::create(null, substr($now, 5, 2), 1)->translatedFormat('F');
        $tgl = Carbon::now()->translatedFormat('d F Y');

        $b = $bulanCetak;
        $th = substr($now, 0, 4);
        $tg = $tgl;

        $bulan = Carbon::now()->format('m');
        $instruktur = Instruktur::orderBy('nama', 'asc')->get();
        $data['Jumlah_Hadir'] = 0;
        $jadwalHarian[] = null;

        // return response([
        //     'message' => 'Retrieve All Success',
        //     'data' => $instruktur
        // ], 200);

        foreach($instruktur as $dataInstruktur) {
            $data['Nama'] = $dataInstruktur->nama;
            
            $jadwalHarian = Jadwal_harian::where('id_instruktur',$dataInstruktur['id_instruktur'])
                                        ->whereMonth('tanggal',$bulan)
                                        ->get();

            if(count($jadwalHarian) > 1) {
                foreach($jadwalHarian as $dataJadwalHarian) {
                    $jumlahHadir = Presensi_instruktur::where('id_jadwal_harian',$dataJadwalHarian->id_jadwal_harian)
                                                        ->whereMonth('tgl_presensi_instruktur',$bulan)
                                                        ->get();
                    $data['Jumlah_Hadir'] = $data['Jumlah_Hadir'] + count($jumlahHadir);
                }
            } else {
                $jumlahHadir = Presensi_instruktur::where('id_jadwal_harian',$jadwalHarian[0]->id_jadwal_harian)
                                                        ->whereMonth('tgl_presensi_instruktur',$bulan)
                                                        ->get();
                $data['Jumlah_Hadir'] = count($jumlahHadir);
            }

            $jumlahLibur = Jadwal_harian::where('id_instruktur',$dataInstruktur['id_instruktur'])
                                        ->where('status','Libur')
                                        ->whereMonth('tanggal',$bulan)
                                        ->get();

            $data['Jumlah_Libur'] = count($jumlahLibur);

            $keterlambatan = $dataInstruktur['jumlah_terlambat'];
            $detik = strtotime($keterlambatan) - strtotime('00:00:00');
            $data['Waktu_Terlambat'] = $detik;

            $laporan->add($data);
        }

        if (!is_null($laporan)) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $laporan,
                'bulan'=> $b,
                'tahun' => $th,
                'tgl_cetak'=> $tg,
            ], 200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }
}