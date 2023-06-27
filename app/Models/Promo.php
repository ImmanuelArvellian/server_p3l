<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_promo';
    protected $keyType = 'string';
    protected $table = 'promo';
    protected $fillable = [
        'id_promo',
        'syarat',
        'bonus',
        'keterangan',
    ];

    public function Aktivasi_tahunan(){
        return $this->hasMany(Promo::class, 'id_promo', 'id');
    }

    public function Deposit_umum(){
        return $this->hasMany(Promo::class, 'id_promo', 'id');
    }

    public function Deposit_kelas(){
        return $this->hasMany(Promo::class, 'id_promo', 'id');
    }

    public function Promo(){
        return $this->hasMany(Promo::class, 'id_promo', 'id');
    }
}
