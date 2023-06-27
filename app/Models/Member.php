<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_member';
    protected $keyType = 'string';
    protected $table = 'member';
    protected $fillable = [
        'id_member',
        'nama',
        'tgl_lahir',
        'email',
        'password',
        'gender',
        'no_telp',
        'alamat',
        'status_membership',
        'tgl_daftar',
        'tgl_exp_membership',
        'sisa_deposit',
    ];

    public function Aktivasi_tahunan(){
        return $this->hasMany(Member::class, 'id_member', 'id');
    }

    public function Deposit_umum(){
        return $this->hasMany(Member::class, 'id_member', 'id');
    }

    public function Deposit_kelas(){
        return $this->hasMany(Member::class, 'id_member', 'id');
    }

    public function Promo(){
        return $this->hasMany(Member::class, 'id_member', 'id');
    }

    public function Booking_gym(){
        return $this->hasMany(Member::class, 'id_member', 'id');
    }

    public function Booking_kelas(){
        return $this->hasMany(Member::class, 'id_member', 'id');
    }

    public function Member_deposit_kelas(){
        return $this->hasMany(Member::class, 'id_member', 'id');
    }
}
