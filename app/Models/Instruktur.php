<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruktur extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_instruktur';
    protected $keyType = 'string';
    protected $table = 'instruktur';
    protected $fillable = [
        'id_instruktur',
        'nama',
        'email',
        'password',
        'gender',
        'no_telp',
        'alamat',
        'jumlah_terlambat'
    ];

    public function Jadwal_umum(){
        return $this->hasMany(Instruktur::class, 'id_instruktur', 'id');
    }

    public function Jadwal_harian(){
        return $this->hasMany(Instruktur::class, 'id_instruktur', 'id');
    }

    public function Izin_instruktur(){
        return $this->hasMany(Instruktur::class, 'id_instruktur', 'id');
    }
}
