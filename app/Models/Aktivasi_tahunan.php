<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivasi_tahunan extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_aktivasi';
    protected $keyType = 'string';
    protected $table = 'aktivasi_tahunan';
    protected $fillable = [
        'id_aktivasi',
        'id_member',
        'id_pegawai',
        'tgl_transaksi',
        'masa_aktif',
        'no_struk',
    ];

    public function Member(){
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function Pegawai(){
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
