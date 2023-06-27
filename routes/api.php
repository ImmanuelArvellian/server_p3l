<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Login
Route::post('login', 'App\Http\Controllers\AuthController@login');

//Instruktur
Route::get('instruktur', 'App\Http\Controllers\InstrukturController@index');
Route::get('instruktur/{id_instruktur}', 'App\Http\Controllers\InstrukturController@show');
Route::post('instruktur', 'App\Http\Controllers\InstrukturController@store');
Route::put('instruktur/{id}', 'App\Http\Controllers\InstrukturController@update');
Route::delete('instruktur/{id}', 'App\Http\Controllers\InstrukturController@destroy');
Route::post('instruktur_reset/{id_instruktur}', 'App\Http\Controllers\InstrukturController@resetTerlambat');

//Member
Route::get('member', 'App\Http\Controllers\MemberController@index');
Route::get('member/{id_member}', 'App\Http\Controllers\MemberController@show');
Route::post('member', 'App\Http\Controllers\MemberController@store');
Route::put('member/{id}', 'App\Http\Controllers\MemberController@update');
Route::delete('member/{id}', 'App\Http\Controllers\MemberController@destroy');
Route::post('member_deak/{id_member}', 'App\Http\Controllers\MemberController@deaktivasi');
Route::get('member_exp/', 'App\Http\Controllers\MemberController@showExp');

//Pegawai
Route::get('pegawai', 'App\Http\Controllers\PegawaiController@index');
Route::get('pegawai/{id_pegawai}', 'App\Http\Controllers\PegawaiController@show');
Route::post('pegawai', 'App\Http\Controllers\PegawaiController@store');
Route::put('pegawai/{id}', 'App\Http\Controllers\PegawaiController@update');
Route::delete('pegawai/{id}', 'App\Http\Controllers\PegawaiController@destroy');

//Kelas
Route::get('kelas', 'App\Http\Controllers\KelasController@index');
Route::get('kelas/{id_kelas}', 'App\Http\Controllers\KelasController@show');
Route::post('kelas', 'App\Http\Controllers\KelasController@store');
Route::put('kelas/{id}', 'App\Http\Controllers\KelasController@update');
Route::delete('kelas/{id}', 'App\Http\Controllers\KelasController@destroy');

//Jadwal Umum
Route::get('jadwal_umum', 'App\Http\Controllers\JadwalUmumController@index');
Route::get('jadwal_umum/{id_jadwal_umum}', 'App\Http\Controllers\JadwalUmumController@show');
Route::post('jadwal_umum', 'App\Http\Controllers\JadwalUmumController@store');
Route::put('jadwal_umum/{id}', 'App\Http\Controllers\JadwalUmumController@update');
Route::delete('jadwal_umum/{id}', 'App\Http\Controllers\JadwalUmumController@destroy');

//Jadwal Harian
Route::get('jadwal_harian', 'App\Http\Controllers\JadwalHarianController@index');
Route::get('jadwal_harian/{id_jadwal_harian}', 'App\Http\Controllers\JadwalHarianController@show');
Route::post('jadwal_harian', 'App\Http\Controllers\JadwalHarianController@store');
Route::put('jadwal_harian/ubahStatus/{id}', 'App\Http\Controllers\JadwalHarianController@ubahStatus');

//Aktivasi Tahunan
Route::get('aktivasi_tahunan', 'App\Http\Controllers\AktivasiTahunanController@index');
Route::get('aktivasi_tahunan/{id_aktivasi}', 'App\Http\Controllers\AktivasiTahunanController@show');
Route::post('aktivasi_tahunan', 'App\Http\Controllers\AktivasiTahunanController@store');

//Deposit Umum
Route::get('deposit_umum', 'App\Http\Controllers\DepositUmumController@index');
Route::get('deposit_umum/{id_deposit_umum}', 'App\Http\Controllers\DepositUmumController@show');
Route::post('deposit_umum/{id_member}', 'App\Http\Controllers\DepositUmumController@store');

//Deposit Kelas
Route::get('deposit_kelas', 'App\Http\Controllers\DepositKelasController@index');
Route::get('deposit_kelas/{id_deposit_kelas}', 'App\Http\Controllers\DepositKelasController@show');
Route::post('deposit_kelas', 'App\Http\Controllers\DepositKelasController@store');
Route::post('deposit_kelas_reset/{id_deposit_kelas}', 'App\Http\Controllers\DepositKelasController@resetDeposit');
Route::get('deposit_kelas_exp/', 'App\Http\Controllers\DepositKelasController@showExp');

Route::get('deposit_kelas/byMember/{id_member}', 'App\Http\Controllers\MemberDepositKelasController@getDepositByMember');

//Izin Instruktur
Route::get('izin_instruktur', 'App\Http\Controllers\IzinInstrukturController@index');
Route::get('izin_instruktur/{id_izin}', 'App\Http\Controllers\IzinInstrukturController@show');
Route::get('izin_instruktur/byInstruktur/{id_instruktur}', 'App\Http\Controllers\IzinInstrukturController@getIzinByInstruktur');
Route::post('izin_instruktur', 'App\Http\Controllers\IzinInstrukturController@store');
Route::post('izin_instruktur/konfirmasi/{id_izin}', 'App\Http\Controllers\IzinInstrukturController@konfirmasi');

//Booking Kelas
Route::get('booking_kelas', 'App\Http\Controllers\BookingKelasController@index');
Route::get('booking_kelas/{id_booking_kelas}', 'App\Http\Controllers\BookingKelasController@show');
Route::post('booking_kelas', 'App\Http\Controllers\BookingKelasController@store');
Route::post('booking_kelas_presensi/{id_booking_kelas}', 'App\Http\Controllers\BookingKelasController@presensiKelas');
Route::get('booking_kelas_sudah', 'App\Http\Controllers\BookingKelasController@showSudah');
Route::get('booking_kelas_belum', 'App\Http\Controllers\BookingKelasController@showBelum');

Route::get('booking_kelas/byMember/{id_member}', 'App\Http\Controllers\BookingKelasController@getBookingByMember');

//Booking Gym
Route::get('booking_gym', 'App\Http\Controllers\BookingGymController@index');
Route::get('booking_gym/{id_booking_gym}', 'App\Http\Controllers\BookingGymController@show');
Route::post('booking_gym', 'App\Http\Controllers\BookingGymController@store');
Route::post('booking_gym_cancel/{id_booking_gym}', 'App\Http\Controllers\BookingGymController@cancelBooking');
Route::post('booking_gym_presensi/{id_booking_gym}', 'App\Http\Controllers\BookingGymController@presensiGym');
Route::get('booking_gym_sudah', 'App\Http\Controllers\BookingGymController@showSudah');
Route::get('booking_gym_belum', 'App\Http\Controllers\BookingGymController@showBelum');

//Presensi Instruktur
Route::get('presensi_instruktur', 'App\Http\Controllers\PresensiInstrukturController@index');
Route::get('presensi_instruktur/{id_presensi_instruktur}', 'App\Http\Controllers\PresensiInstrukturController@show');
Route::post('presensi_instruktur', 'App\Http\Controllers\PresensiInstrukturController@store');
Route::put('presensi_instruktur/{id}', 'App\Http\Controllers\PresensiInstrukturController@update');
Route::delete('presensi_instruktur/{id}', 'App\Http\Controllers\PresensiInstrukturController@destroy');

//Laporan
Route::get('laporan_pendapatan', 'App\Http\Controllers\LaporanController@laporanPendapatan');
Route::get('laporan_kelas', 'App\Http\Controllers\LaporanController@laporanKelas');
Route::get('laporan_gym', 'App\Http\Controllers\LaporanController@laporanGym');
Route::get('laporan_kinerja_instruktur', 'App\Http\Controllers\LaporanController@laporanKinerjaInstruktur');