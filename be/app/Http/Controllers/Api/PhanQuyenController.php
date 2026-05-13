<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChiTietPhanQuyen;
use App\Models\ChucNang;
use Illuminate\Http\Request;

class PhanQuyenController extends Controller
{
    public function getChucNang(Request $request)
    {
        $chuc_vu_id = $request->chuc_vu_id;
        
        $list_chuc_nang = ChucNang::where('trang_thai', 'Hoạt động')->get();
        $list_da_chon = ChiTietPhanQuyen::where('chuc_vu_id', $chuc_vu_id)->pluck('chuc_nang_id')->toArray();
        
        return response()->json([
            'status' => true,
            'data' => $list_chuc_nang,
            'da_chon' => $list_da_chon
        ]);
    }

    public function updatePhanQuyen(Request $request)
    {
        $chuc_vu_id = $request->chuc_vu_id;
        $list_chuc_nang = $request->list_chuc_nang; // Array of IDs

        // Clear existing
        ChiTietPhanQuyen::where('chuc_vu_id', $chuc_vu_id)->delete();

        // Add new
        foreach ($list_chuc_nang as $id_chuc_nang) {
            ChiTietPhanQuyen::create([
                'chuc_vu_id' => $chuc_vu_id,
                'chuc_nang_id' => $id_chuc_nang
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật phân quyền thành công!'
        ]);
    }
}
