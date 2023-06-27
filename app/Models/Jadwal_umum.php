<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal_umum extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_jadwal_umum';
    protected $keyType = 'string';
    protected $table = 'jadwal_umum';
    protected $fillable = [
        'id_jadwal_umum',
        'id_kelas',
        'id_instruktur',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function Kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function Instruktur(){
        return $this->belongsTo(Instruktur::class, 'id_instruktur');
    }
}
