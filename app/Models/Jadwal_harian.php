<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal_harian extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_jadwal_harian';
    protected $keyType = 'string';
    protected $table = 'jadwal_harian';
    protected $fillable = [
        'id_jadwal_harian',
        'id_kelas',
        'id_instruktur',
        'hari',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
    ];

    public function Kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function Instruktur(){
        return $this->belongsTo(Instruktur::class, 'id_instruktur');
    }

    public function Izin_instruktur(){
        return $this->hasMany(Jadwal_harian::class, 'id_jadwal_harian', 'id');
    }

    public function Presensi_instruktur(){
        return $this->hasMany(Jadwal_harian::class, 'id_jadwal_harian', 'id');
    }
}
