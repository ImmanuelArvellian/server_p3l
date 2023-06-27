<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit_kelas extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_deposit_kelas';
    protected $keyType = 'string';
    protected $table = 'deposit_kelas';
    protected $fillable = [
        'id_deposit_kelas',
        'id_member',
        'id_pegawai',
        'id_kelas',
        'id_promo_kelas',
        'tgl_transaksi',
        'no_struk',
        'uang_deposit_kelas',
        'bonus_deposit_kelas',
        'sisa_deposit_kelas',
        'total_deposit_kelas',
        'masa_exp',
    ];

    public function Member(){
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function Pegawai(){
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function Kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function Promo_kelas(){
        return $this->belongsTo(Promo_kelas::class, 'id_promo_kelas');
    }
}
