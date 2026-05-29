<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeXuatChinhSua;
use App\Models\ThanhVien;
use App\Models\NguoiDung;
use App\Models\ChiNhanh;

class DeXuatChinhSuaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dọn dẹp dữ liệu cũ để tránh trùng lặp
        DeXuatChinhSua::truncate();

        // 2. Tìm người dùng Đối tác quản lý và người dùng thành viên
        $partnerUser = NguoiDung::where('email', 'doitac@master.com')->first();
        $memberUser = NguoiDung::where('email', 'minhvy@master.com')->first() 
            ?? NguoiDung::where('email', 'member1@master.com')->first();

        if (!$partnerUser) {
            return;
        }

        // Lấy chi nhánh của đối tác
        $chiNhanh = ChiNhanh::where('id_nguoi_dung', $partnerUser->id)->first();
        $cnId = $chiNhanh ? $chiNhanh->id : 1;

        // Tìm các thành viên tương ứng
        $ducAnh = ThanhVien::where('ho_ten', 'Nguyễn Đức Anh')->first();
        $minhVy = ThanhVien::where('ho_ten', 'Nguyễn Minh Vy')->first();
        $cuOng = ThanhVien::where('ho_ten', 'Nguyễn Đức Cường')->first();

        // 3. Seed các Đề xuất mẫu
        // Đề xuất 1: Nguyễn Minh Vy đề xuất cập nhật thông tin nghề nghiệp của mình (PENDING)
        if ($minhVy && $memberUser) {
            DeXuatChinhSua::create([
                'thanh_vien_id' => $minhVy->id,
                'proposed_by_user_id' => $memberUser->id,
                'type' => 'edit',
                'data' => [
                    'nghe_nghiep' => 'Senior UI/UX Designer tại Milan',
                    'ghi_chu' => 'Đã tốt nghiệp Thạc sĩ Mỹ thuật và ký hợp đồng làm việc chính thức tại Milan, Ý.',
                    'noi_sinh' => 'Hà Nội, Việt Nam'
                ],
                'status' => 'pending',
                'approved_by' => null,
                'note' => null,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ]);
        }

        // Đề xuất 2: Đối tác hoặc thành viên đề xuất cập nhật thông tin Đức Anh (APPROVED)
        if ($ducAnh && $memberUser) {
            DeXuatChinhSua::create([
                'thanh_vien_id' => $ducAnh->id,
                'proposed_by_user_id' => $memberUser->id,
                'type' => 'edit',
                'data' => [
                    'nghe_nghiep' => 'Giám đốc Công nghệ (CTO) kiêm Sáng lập',
                    'ghi_chu' => 'Được thăng chức và chính thức đảm nhiệm vai trò CTO điều hành dự án Gia Phả Số toàn quốc.'
                ],
                'status' => 'approved',
                'approved_by' => $partnerUser->id,
                'note' => 'Cập nhật chức danh mới của anh Đức Anh rất chính xác.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(5),
            ]);
        }

        // Đề xuất 3: Thêm con nuôi cho Cụ Cường (REJECTED)
        if ($cuOng && $memberUser) {
            DeXuatChinhSua::create([
                'thanh_vien_id' => $cuOng->id,
                'proposed_by_user_id' => $memberUser->id,
                'type' => 'add_child',
                'data' => [
                    'ho_ten' => 'Nguyễn Đức Tấn',
                    'ten_goi' => 'Bác Tấn',
                    'gioi_tinh' => 'Nam',
                    'ngay_sinh' => '1940-02-12',
                    'nghe_nghiep' => 'Thủy thủ đường dài',
                    'trang_thai' => 'Còn sống',
                    'chi_nhanh_id' => $cnId,
                    'ghi_chu' => 'Đề xuất thêm con nuôi'
                ],
                'status' => 'rejected',
                'approved_by' => $partnerUser->id,
                'note' => 'Không được phê duyệt. Hội đồng gia tộc kiểm chứng cụ Cường không có người con trai nuôi nào tên Tấn sinh năm 1940.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(2),
            ]);
        }
    }
}
