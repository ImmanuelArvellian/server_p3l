<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo_kelas extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_promo_kelas';
    protected $keyType = 'string';
    protected $table = 'promo_kelas';
    protected $fillable = [
        'id_promo_kelas',
        'syarat',
        'bonus',
        'keterangan',
    ];

    public function Deposit_kelas(){
        return $this->hasMany(Promo_kelas::class, 'id_promo_kelas', 'id');
    }
}
