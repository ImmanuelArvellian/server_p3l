<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member_deposit_kelas extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $table = 'member_deposit_kelas';
    protected $fillable = [
        'id_member',
        'id_kelas',
        'deposit_kelas',
        'tgl_exp',
    ];

    public function Member(){
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function Kelas(){
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
