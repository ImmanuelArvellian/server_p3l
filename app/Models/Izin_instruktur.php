<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin_instruktur extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_izin';
    protected $keyType = 'string';
    protected $table = 'izin_instruktur';
    protected $fillable = [
        'id_izin',
        'id_instruktur',
        'id_instruktur_pengganti',
        'id_jadwal_harian',
        'status',
        'keterangan',
        'tgl_izin',
    ];

    public function Instruktur(){
        return $this->belongsTo(Instruktur::class, 'id_instruktur');
    }

    public function Jadwal_harian(){
        return $this->belongsTo(Jadwal_harian::class, 'id_jadwal_harian');
    }
}
