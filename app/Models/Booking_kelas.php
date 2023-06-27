<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking_kelas extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_booking_kelas';
    protected $keyType = 'string';
    protected $table = 'booking_kelas';
    protected $fillable = [
        'id_booking_kelas',
        'id_member',
        'id_jadwal_harian',
        'no_struk',
        'tgl_tujuan',
        'tgl_presensi',
        'tipe_pembayaran',
    ];

    public function Member(){
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function Jadwal_harian(){
        return $this->belongsTo(Jadwal_harian::class, 'id_jadwal_harian');
    }

    public function Kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
