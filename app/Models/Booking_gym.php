<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking_gym extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_booking_gym';
    protected $keyType = 'string';
    protected $table = 'booking_gym';
    protected $fillable = [
        'id_booking_gym',
        'id_member',
        'id_sesi',
        'no_struk',
        'tgl_tujuan',
        'tgl_presensi',
    ];

    public function Member(){
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function Sesi(){
        return $this->belongsTo(Sesi::class, 'id_sesi');
    }
}
