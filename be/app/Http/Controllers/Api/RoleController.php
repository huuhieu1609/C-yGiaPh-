<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json(['status' => true, 'data' => Role::with('permissions')->get()]);
    }

    public function permissions()
    {
        return response()->json(['status' => true, 'data' => Permission::all()]);
    }
}
