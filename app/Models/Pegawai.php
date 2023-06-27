<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_pegawai';
    protected $keyType = 'string';
    protected $table = 'pegawai';
    protected $fillable = [
        'id_pegawai',
        'id_role',
        'nama',
        'email',
        'password',
        'gender',
        'no_telp',
        'alamat',
    ];

    public function Aktivasi_tahunan(){
        return $this->hasMany(Pegawai::class, 'id_pegawai', 'id');
    }

    public function Deposit_umum(){
        return $this->hasMany(Pegawai::class, 'id_pegawai', 'id');
    }

    public function Deposit_kelas(){
        return $this->hasMany(Pegawai::class, 'id_pegawai', 'id');
    }

    public function Promo(){
        return $this->hasMany(Pegawai::class, 'id_pegawai', 'id');
    }
}
