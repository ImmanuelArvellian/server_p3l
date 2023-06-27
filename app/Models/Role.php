<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
    * fillable
    *
    * @var array
    */
    protected $primaryKey = 'id_role';
    protected $keyType = 'string';
    protected $table = 'role';
    protected $fillable = [
        'id_role',
        'nama_role',
    ];
}
