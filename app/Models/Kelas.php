<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_kelas';
    protected $keyType = 'string';
    protected $table = 'kelas';
    protected $fillable = [
        'id_kelas',
        'nama_kelas',
        'harga',
        'kapasitas',
    ];

    public function Jadwal_umum(){
        return $this->hasMany(Kelas::class, 'id_kelas', 'id');
    }

    public function Jadwal_harian(){
        return $this->hasMany(Kelas::class, 'id_kelas', 'id');
    }

    public function Booking_kelas(){
        return $this->hasMany(Kelas::class, 'id_kelas', 'id');
    }

    public function Deposit_kelas(){
        return $this->hasMany(Kelas::class, 'id_kelas', 'id');
    }

    public function Member_deposit_kelas(){
        return $this->hasMany(Kelas::class, 'id_kelas', 'id');
    }
}
