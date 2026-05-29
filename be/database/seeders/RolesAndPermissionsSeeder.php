<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Basic roles
        $leader = Role::firstOrCreate(['name' => 'branch_leader'], ['display_name' => 'Trưởng Nhánh', 'level' => 100]);
        $manager = Role::firstOrCreate(['name' => 'manager'], ['display_name' => 'Quản lý', 'level' => 50]);
        $editor = Role::firstOrCreate(['name' => 'editor'], ['display_name' => 'Chỉnh sửa', 'level' => 20]);
        $member = Role::firstOrCreate(['name' => 'member'], ['display_name' => 'Thành viên dòng họ', 'level' => 10]);
        $viewer = Role::firstOrCreate(['name' => 'viewer'], ['display_name' => 'Xem', 'level' => 0]);

        // Permissions
        $p_assign = Permission::firstOrCreate(['name' => 'assign_roles'], ['display_name' => 'Gán quyền']);
        $p_edit = Permission::firstOrCreate(['name' => 'edit_members'], ['display_name' => 'Chỉnh sửa thành viên']);
        $p_view = Permission::firstOrCreate(['name' => 'view_members'], ['display_name' => 'Xem thành viên']);

        // map permissions
        $leader->permissions()->syncWithoutDetaching([$p_assign->id, $p_edit->id, $p_view->id]);
        $manager->permissions()->syncWithoutDetaching([$p_edit->id, $p_view->id]);
        $editor->permissions()->syncWithoutDetaching([$p_edit->id]);
        $member->permissions()->syncWithoutDetaching([$p_view->id]);
        $viewer->permissions()->syncWithoutDetaching([$p_view->id]);
    }
}
