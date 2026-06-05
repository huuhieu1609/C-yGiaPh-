<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Gói Khởi Tạo
        DB::table('goi_dich_vus')
            ->where('ten_goi', 'Gói Khởi Tạo')
            ->update([
                'features' => 'tao_cay_gia_pha,them_thanh_vien,sua_xoa_thanh_vien,quan_ly_vo_chong,quan_ly_con_nuoi,quan_ly_tai_lieu,upload_hinh_anh'
            ]);

        // Gói Hưng Thịnh
        DB::table('goi_dich_vus')
            ->where('ten_goi', 'Gói Hưng Thịnh')
            ->update([
                'features' => 'tao_cay_gia_pha,them_thanh_vien,sua_xoa_thanh_vien,quan_ly_vo_chong,quan_ly_con_nuoi,quan_ly_tai_lieu,upload_hinh_anh,xuat_pdf,phe_duyet_de_xuat,quan_ly_chi_nhanh,nhat_ky_hoat_dong,quan_ly_su_kien,dang_ky_su_kien,tuong_niem,nhac_gio_tu,album_gia_dinh,ban_do_mo_phan,ban_do_nha_tho,tra_cuu_ban_do,quan_ly_dong_gop,bao_cao_tai_chinh,tra_cuu_quan_he'
            ]);

        // Gói Trường Tồn
        DB::table('goi_dich_vus')
            ->where('ten_goi', 'Gói Trường Tồn')
            ->update([
                'features' => 'tao_cay_gia_pha,them_thanh_vien,sua_xoa_thanh_vien,quan_ly_vo_chong,quan_ly_con_nuoi,xuat_pdf,phe_duyet_de_xuat,tu_dong_phe_duyet,quan_ly_chi_nhanh,phan_quyen_thanh_vien,nhat_ky_hoat_dong,quan_ly_su_kien,dang_ky_su_kien,tuong_niem,nhac_gio_tu,quan_ly_tai_lieu,upload_hinh_anh,album_gia_dinh,ban_do_mo_phan,ban_do_nha_tho,tra_cuu_ban_do,quan_ly_dong_gop,bao_cao_tai_chinh,tra_cuu_quan_he,xuat_csv,thong_ke_nang_cao,api_tich_hop'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
