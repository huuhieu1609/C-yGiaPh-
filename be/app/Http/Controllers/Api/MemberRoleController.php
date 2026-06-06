<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberRole;
use App\Models\Role;
use App\Models\NotificationCustom;
use App\Models\ThanhVien;
use Illuminate\Support\Facades\Auth;

class MemberRoleController extends Controller
{
    public function assign(Request $request, $memberId)
    {
        $roleId = $request->input('role_id');
        $chiNhanhId = $request->input('chi_nhanh_id');
        if (! $roleId) return response()->json(['status' => false, 'message' => 'role_id required'], 422);

        $mr = MemberRole::updateOrCreate(
            ['member_id' => $memberId, 'role_id' => $roleId, 'chi_nhanh_id' => $chiNhanhId],
            ['assigned_by' => Auth::id() ?: null]
        );

        // Fetch detailed role and permissions
        $role = Role::with('permissions')->find($roleId);
        $permsList = $role->permissions->pluck('display_name')->toArray();
        $permsText = count($permsList) > 0 ? implode(', ', $permsList) : 'Không có';
        
        $branchName = 'Toàn hệ';
        if ($chiNhanhId) {
            $branch = \App\Models\ChiNhanh::find($chiNhanhId);
            if ($branch) {
                $branchName = $branch->ten_chi;
            }
        }

        // Notify target member (if has user)
        NotificationCustom::create([
            'user_id' => null,
            'target_member_id' => $memberId,
            'title' => 'Bạn được cấp quyền mới',
            'body' => "Bạn đã được cấp vai trò: {$role->display_name} tại chi nhánh: {$branchName}. Quyền hạn được phép quản lý bao gồm: {$permsText}.",
            'meta' => ['role_id' => $roleId, 'chi_nhanh_id' => $chiNhanhId]
        ]);

        return response()->json(['status' => true, 'data' => $mr]);
    }

    public function revoke(Request $request, $memberId)
    {
        $roleId = $request->input('role_id');
        $chiNhanhId = $request->input('chi_nhanh_id');
        $deleted = MemberRole::where('member_id', $memberId)->where('role_id', $roleId)->where('chi_nhanh_id', $chiNhanhId)->delete();
        return response()->json(['status' => (bool)$deleted]);
    }

    public function list($memberId)
    {
        $roles = MemberRole::where('member_id', $memberId)->with('role')->get();
        return response()->json(['status' => true, 'data' => $roles]);
    }

    public function myRolesAndPermissions(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        // Nếu là Admin hoặc Đối tác
        if ($user->vai_tro === 'Admin' || $user->is_doi_tac == 1) {
            return response()->json([
                'status' => true,
                'is_admin_or_partner' => true,
                'roles' => ['admin_or_partner'],
                'permissions' => ['assign_roles', 'edit_members', 'view_members']
            ]);
        }

        // Tìm thành viên tương ứng với email
        $member = ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
        if (!$member) {
            return response()->json([
                'status' => true,
                'is_admin_or_partner' => false,
                'roles' => [],
                'permissions' => []
            ]);
        }

        // Lấy tất cả vai trò & quyền hạn từ member_role
        $memberRoles = MemberRole::where('member_id', $member->id)->with('role.permissions')->get();
        
        $roles = [];
        $permissions = [];
        foreach ($memberRoles as $mr) {
            if ($mr->role) {
                $roles[] = [
                    'name' => $mr->role->name,
                    'display_name' => $mr->role->display_name,
                    'chi_nhanh_id' => $mr->chi_nhanh_id
                ];
                foreach ($mr->role->permissions as $p) {
                    $permissions[] = $p->name;
                }
            }
        }

        return response()->json([
            'status' => true,
            'is_admin_or_partner' => false,
            'member_id' => $member->id,
            'roles' => array_values(array_unique($roles, SORT_REGULAR)),
            'permissions' => array_values(array_unique($permissions))
        ]);
    }

    public function getNotifications(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $member = ThanhVien::where('email', $user->email)->whereNotNull('email')->first();
        
        $query = NotificationCustom::query();
        
        $query->where(function($q) use ($user, $member) {
            $q->where('user_id', $user->id);
            if ($member) {
                $q->orWhere('target_member_id', $member->id);
            }
        });

        $notifications = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }

    public function readAllNotifications(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $member = ThanhVien::where('email', $user->email)->whereNotNull('email')->first();

        $query = NotificationCustom::whereNull('read_at');
        $query->where(function($q) use ($user, $member) {
            $q->where('user_id', $user->id);
            if ($member) {
                $q->orWhere('target_member_id', $member->id);
            }
        });

        $query->update(['read_at' => now()]);

        return response()->json([
            'status' => true,
            'message' => 'Đã đánh dấu đọc toàn bộ thông báo.'
        ]);
    }
}
