<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi_instruktur extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_presensi_instruktur';
    protected $keyType = 'string';
    protected $table = 'presensi_instruktur';
    protected $fillable = [
        'id_presensi_instruktur',
        'id_jadwal_harian',
        'tgl_presensi_instruktur',
    ];

    public function Jadwal_harian(){
        return $this->belongsTo(Jadwal_harian::class, 'id_jadwal_harian');
    }

}
