<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeXuatChinhSua;
use Illuminate\Http\Request;
use Exception;

class DeXuatController extends Controller
{
    public function getData(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            if ($user && $user->is_doi_tac == 1) {
                // Get branches owned by this partner
                $chiNhanhIds = \App\Models\ChiNhanh::where('id_nguoi_dung', $user->id)->pluck('id')->toArray();
                
                $proposals = DeXuatChinhSua::with(['thanhVien', 'proposedBy', 'approver'])
                    ->orderBy('id', 'desc')
                    ->get()
                    ->filter(function ($p) use ($chiNhanhIds) {
                        // Check if existing member belongs to partner's branches
                        if ($p->thanhVien && in_array($p->thanhVien->chi_nhanh_id, $chiNhanhIds)) {
                            return true;
                        }
                        // Or if new member, check branch in JSON data
                        $data = $p->data;
                        if (isset($data['chi_nhanh_id']) && in_array($data['chi_nhanh_id'], $chiNhanhIds)) {
                            return true;
                        }
                        return false;
                    })->values();
            } else {
                $proposals = DeXuatChinhSua::with(['thanhVien', 'proposedBy', 'approver'])
                    ->orderBy('id', 'desc')
                    ->get();
            }

            return response()->json([
                'status' => true,
                'message' => 'Lấy danh sách đề xuất thành công!',
                'data' => $proposals
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi lấy danh sách đề xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function myProposals(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $proposals = DeXuatChinhSua::where('proposed_by_user_id', $user->id)
                ->with(['thanhVien'])
                ->orderBy('id', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Lấy lịch sử đề xuất thành công!',
                'data' => $proposals
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi lấy lịch sử đề xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $type = $request->type;
            $data = $request->data;
            $thanhVienId = $request->thanh_vien_id;

            // Resolve branch ID for proposal
            $chiNhanhId = null;
            if ($thanhVienId) {
                $thanhVien = \App\Models\ThanhVien::find($thanhVienId);
                if ($thanhVien) {
                    $chiNhanhId = $thanhVien->chi_nhanh_id;
                }
            }
            if (!$chiNhanhId && isset($data['chi_nhanh_id'])) {
                $chiNhanhId = $data['chi_nhanh_id'];
            }

            // Check if the branch has auto-approve enabled
            $chiNhanh = \App\Models\ChiNhanh::find($chiNhanhId);
            $isAuto = $chiNhanh ? (bool)$chiNhanh->is_auto_approve : false;

            $proposal = DeXuatChinhSua::create([
                'thanh_vien_id' => $thanhVienId,
                'proposed_by_user_id' => $user->id,
                'type' => $type,
                'data' => $data,
                'status' => $isAuto ? 'approved' : 'pending',
                'approved_by' => $isAuto ? $user->id : null,
                'note' => $isAuto ? 'Tự động phê duyệt' : null,
            ]);

            if ($isAuto) {
                $this->applyProposal($proposal);
            }

            return response()->json([
                'status' => true,
                'message' => $isAuto ? 'Đề xuất đã tự động được phê duyệt!' : 'Gửi đề xuất thành công, đang chờ phê duyệt!',
                'data' => $proposal
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi gửi đề xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $proposal = DeXuatChinhSua::findOrFail($request->id);
            
            $proposal->status = 'approved';
            $proposal->approved_by = $user->id;
            $proposal->note = $request->note ?? 'Đã phê duyệt thủ công';
            $proposal->save();

            $this->applyProposal($proposal);

            return response()->json([
                'status' => true,
                'message' => 'Phê duyệt đề xuất thành công!',
                'data' => $proposal
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi phê duyệt: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $proposal = DeXuatChinhSua::findOrFail($request->id);

            $proposal->status = 'rejected';
            $proposal->approved_by = $user->id;
            $proposal->note = $request->note ?? 'Không được chấp thuận';
            $proposal->save();

            return response()->json([
                'status' => true,
                'message' => 'Đã từ chối đề xuất!',
                'data' => $proposal
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi từ chối đề xuất: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleAutoApprove(Request $request)
    {
        try {
            $chiNhanh = \App\Models\ChiNhanh::findOrFail($request->chi_nhanh_id);
            $chiNhanh->is_auto_approve = !$chiNhanh->is_auto_approve;
            $chiNhanh->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật cấu hình tự động duyệt thành công!',
                'is_auto_approve' => $chiNhanh->is_auto_approve
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi cấu hình tự động duyệt: ' . $e->getMessage()
            ], 500);
        }
    }

    private function applyProposal($proposal)
    {
        $data = $proposal->data;
        if ($proposal->type === 'edit') {
            $thanhVien = \App\Models\ThanhVien::find($proposal->thanh_vien_id);
            if ($thanhVien) {
                $thanhVien->update($data);
            }
        } elseif ($proposal->type === 'add_child') {
            $data['cha_id'] = $proposal->thanh_vien_id;
            $data['loai_quan_he'] = 'Chính';
            \App\Models\ThanhVien::create($data);
        } elseif ($proposal->type === 'add_spouse') {
            $data['spouse_of_id'] = $proposal->thanh_vien_id;
            $data['loai_quan_he'] = 'Vợ/Chồng';
            \App\Models\ThanhVien::create($data);
        } elseif ($proposal->type === 'delete') {
            $thanhVien = \App\Models\ThanhVien::find($proposal->thanh_vien_id);
            if ($thanhVien) {
                $thanhVien->delete();
            }
        }
    }
}
