<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $member = Member::all();

        $m = $member->map(function($item) {
            $item->tgl_lahir = date('d M Y', strtotime($item->tgl_lahir));
            $item->tgl_daftar = date('d M Y', strtotime($item->tgl_daftar));
            // $item->tgl_exp_membership = date('d M Y', strtotime($item->tgl_exp_membership)); 
            // Mengubah format tanggal menjadi tanggal Indonesia
            return $item;
        });
        
        if(count($member) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate  = FacadesValidator::make($storeData, [
            'nama' => 'required',
            'tgl_lahir' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
            // 'status_membership' => 'required',
            // 'tgl_daftar' => 'required',
            // 'tgl_exp_membership' => 'required',
            // 'sisa_deposit' => 'required',
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors(), 400);
        }
        
        $last = DB::table('Member')->latest()->first();
        if($last == null){
            $count = 1;
        }else{
            $count = ((int)Str::substr($last->id_member, 6, 3)) + 1;
        }

        if($count < 10){
            $id = '00'.$count;
        }else if($count < 100){
            $id = '0'.$count;
        }

        $tgl_d = Carbon::now();
        $tglDaftar = $tgl_d->toDateString();

        $curdate = $tglDaftar;
        $tgl = Str::substr($curdate, 8, 2);
        $monthDaftar = Str::substr($curdate, 5, 2); // 2023-01-02
        $yearDaftar = Str::substr($curdate, 2, 2);
        Str::substr($yearDaftar, -2);

        $tglLahir = $request->tgl_lahir;
        $tgllahir = Str::substr($tglLahir, 8, 2);
        $monthLahir = Str::substr($tglLahir, 5, 2); // 2023-01-02
        $yearLahir = Str::substr($tglLahir, 2, 2);
        Str::substr($yearLahir, -2);

        $member = Member::create([
            'id_member' => $yearDaftar.'.'.$monthDaftar.'.'.$id,
            'nama' => $request->nama,
            'tgl_lahir' => $request->tgl_lahir,
            'email' => $request->email,
            'password' => ($tgllahir.$monthLahir.$yearLahir),
            'gender' => $request->gender,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'status_membership' => 0,
            'tgl_daftar' => $tglDaftar,
            'tgl_exp_membership' => null,
            'sisa_deposit' => 0,
        ]);

        $storeData = Member::latest()->first();

        return response([
            'message' => 'Add Member Success',
            'data' => $storeData
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id_member)
    {
        $member = Member::find($id_member);
        // $member = Member::where('tgl_exp_membership', '>=', Carbon::today())->get();

        $member->tgl_lahir = Carbon::create($member->tgl_lahir)->translatedFormat('d M Y');
        $member->tgl_exp_membership = Carbon::create($member->tgl_exp_membership)->format('d M Y');

        if(!is_null($member)){
            return response([
                'message' => 'Retrieve Member Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Member Not Found',
            'data' => null
        ], 404);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_member)
    {
        $member = Member::find($id_member);
        if(is_null($member)){
            return response([
                'message' => 'Member Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = FacadesValidator::make($updateData, [
            'nama' => 'required',
            'tgl_lahir' => 'required',
            'email' => 'required',
            'gender' => 'required',
            'no_telp' => 'required',
            'alamat' => 'required',
            // 'status_membership' => 'required',
            // 'tgl_exp_membership' => 'required',
            'sisa_deposit' => 'required',
        ]);

        if($validate->fails())
            return response()->json($validate->errors(), 400);
        
        $member->nama = $updateData['nama'];
        $member->tgl_lahir = $updateData['tgl_lahir'];
        $member->email = $updateData['email'];
        $member->gender = $updateData['gender'];
        $member->no_telp = $updateData['no_telp'];
        $member->alamat = $updateData['alamat'];
        $member->status_membership = $updateData['status_membership'];
        // $member->tgl_exp_membership = $updateData['tgl_exp_membership'];
        $member->sisa_deposit = $updateData['sisa_deposit'];
        
        if ($member->save()){
            return response([
                'message' =>'Update Member Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' =>'Update Member Failed',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $member = Member::find($id);

        if(is_null($member)){
            return response([
                'message' => 'Member Not Found',
                'data' => null
            ], 404);
        }

        if($member->delete()){
            return response([
                'message' => 'Delete Member Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Delete Member Failed',
            'data' => null
        ], 400);
    }

    public function deaktivasi($id_member)
    {
        $member = Member::find($id_member);

        if($member->tgl_exp_membership <= Carbon::today()){
            $member->status_membership = 0;
            $member->save();
        }   
        
        return response([
            'message' => 'Retrieve Member Success Banget',
            'data' => $member
        ], 200);
    }

    public function showExp()
    {
        $member = Member::where('tgl_exp_membership', '<=', Carbon::today())->get();

        if(!is_null($member)){
            return response([
                'message' => 'Retrieve Member Success',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Member not found',
            'data' => null
        ], 400);
    }
}
