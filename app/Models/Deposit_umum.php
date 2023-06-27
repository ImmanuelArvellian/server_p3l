<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit_umum extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_deposit_umum';
    protected $keyType = 'string';
    protected $table = 'deposit_umum';
    protected $fillable = [
        'id_deposit_umum',
        'id_member',
        'id_pegawai',
        'id_promo',
        'tgl_transaksi',
        'no_struk',
        'deposit_uang',
        'bonus_deposit',
        'sisa_deposit',
        'total_deposit',
    ];

    public function Member(){
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function Pegawai(){
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }

    public function Promo(){
        return $this->belongsTo(Promo::class, 'id_promo');
    }
}
