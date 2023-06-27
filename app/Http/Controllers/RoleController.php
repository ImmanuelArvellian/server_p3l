<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class RoleController extends Controller
{
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate  = FacadesValidator::make($storeData, [
            'id_role' => 'required',
            'nama_role' => 'required',
        ]);

        if($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $role = Role::create($storeData);
        $role = Role::latest()->first();
        
        return response([
            'message' => 'Add Role Success',
            'data' => $role
        ], 200);
    }
}
