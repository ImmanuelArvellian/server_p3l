<?php

namespace App\Http\Controllers;

use App\Models\Deposit_kelas;
use App\Models\Member_deposit_kelas;
use Illuminate\Http\Request;

class MemberDepositKelasController extends Controller
{
    public function getDepositByMember($id_member)
    {
        $member_deposit_kelas = Member_deposit_kelas::with('kelas', 'member')
                                                        ->where('id_member', '=', $id_member)
                                                        ->get();

        $mdk = $member_deposit_kelas->map(function($item) {
            $item->tgl_exp = date('d M Y', strtotime($item->tgl_exp)); // Mengubah format tanggal menjadi tanggal Indonesia
            return $item;
        });

        if(!is_null($member_deposit_kelas)){
            return response([
                'message' => 'Retrieve Deposit Kelas Success',
                'data' => $member_deposit_kelas
            ], 200);
        }

        return response([
            'message' => 'Deposit Kelas Not Found',
            'data' => null
        ], 404);

    }
}
