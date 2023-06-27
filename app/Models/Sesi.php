<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_sesi';
    protected $keyType = 'string';
    protected $table = 'sesi';
    protected $fillable = [
        'id_sesi',
        'jam_mulais',
        'jam_selesai',
        'kuota',
    ];

    public function Booking_gym(){
        return $this->hasMany(Sesi::class, 'id_member', 'id');
    }
}
