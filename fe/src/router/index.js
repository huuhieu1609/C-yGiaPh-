import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import toastr from "toastr";
import "toastr/build/toastr.min.css";

// Cấu hình Toastr siêu mượt và nhanh, phản hồi tức thì
toastr.options = {
  "closeButton": true,
  "progressBar": true,
  "positionClass": "toast-top-right",
  "showDuration": "100",
  "hideDuration": "100",
  "timeOut": "2000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
};

const routes = [
    // =========================================================================
    // CLIENT ROUTES
    // =========================================================================
    {
        path: '/',
        name: 'trang-chu',
        component: () => import('../components/TrangChu/index.vue'),
        meta: { layout: 'client' }
    },
    {
        path: '/dich-vu-goi',
        name: 'dich-vu-goi',
        component: () => import('../components/DichVuGoi/index.vue'),
        meta: { layout: 'client' }
    },
    {
        path: '/profile',
        name: 'profile',
        component: () => import('../components/ClientProfile/index.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/gia-pha',
        name: 'gia-pha',
        component: () => import('../components/ClientGiaPha/index.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/ban-do',
        name: 'client-ban-do',
        component: () => import('../components/ClientBanDo/index.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/tuong-niem',
        name: 'client-tuong-niem',
        component: () => import('../components/ClientTuongNiem/index.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/tra-cuu',
        name: 'client-tra-cuu',
        component: () => import('../components/ClientTraCuu/index.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/de-xuat',
        name: 'client-de-xuat',
        component: () => import('../components/ClientDeXuat/index.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/su-kien',
        name: 'client-su-kien',
        component: () => import('../components/ClientSuKien/index.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/dich-vu-goi/chi-tiet',
        name: 'dich-vu-goi-detail',
        component: () => import('../components/DichVuGoi/Detail.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/thanh-toan',
        name: 'thanh-toan',
        component: () => import('../components/ClientThanhToan/MuaGoi.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/dong-gop-quy',
        name: 'dong-gop-quy',
        component: () => import('../components/ClientThanhToan/DongGopQuy.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/nguoi-dung',
        redirect: '/gia-pha'
    },

    // =========================================================================
    // AUTH ROUTES
    // =========================================================================
    {
        path: '/login',
        name: 'login',
        component: () => import('../components/Auth/Login.vue'),
        meta: { layout: 'blank' }
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../components/Auth/Register.vue'),
        meta: { layout: 'blank' }
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('../components/Auth/Forgot.vue'),
        meta: { layout: 'blank' }
    },
    {
        path: '/reset-password',
        name: 'reset-password',
        component: () => import('../components/Auth/Reset.vue'),
        meta: { layout: 'blank' }
    },
    {
        path: '/coming-soon',
        name: 'coming-soon',
        component: () => import('../components/ComingSoon/index.vue'),
        meta: { layout: 'blank' }
    },

    // =========================================================================
    // ADMIN LOGIN
    // =========================================================================
    {
        path: '/admin/login',
        name: 'admin-login',
        component: () => import('../components/Admin/Auth/Login.vue'),
        meta: { layout: 'blank' }
    },

    // =========================================================================
    // ADMIN ROUTES
    // =========================================================================
    {
        path: '/admin',
        component: () => import('../layout/wrapper/index.vue'),
        meta: { requiresAuth: true, layout: 'blank' },

        children: [
            {
                path: '',
                redirect: '/admin/dashboard'
            },
            {
                path: 'dashboard',
                name: 'admin-dashboard',
                component: () => import('../components/Admin/TongQuan/Dashboard.vue'),
                meta: { permission: 'Admin Dashboard' }
            },
            {
                path: 'gia-pha',
                name: 'admin-gia-pha',
                component: () => import('../components/Admin/GiaPha/index.vue'),
                meta: { permission: 'Cây Gia Phả' }
            },
            {
                path: 'nguoi-dung',
                name: 'admin-nguoi-dung',
                component: () => import('../components/Admin/NguoiDung/index.vue'),
                meta: { permission: 'Quản Lý Người Dùng' }
            },
            {
                path: 'doi-tac',
                name: 'admin-doi-tac',
                component: () => import('../components/Admin/DoiTac/index.vue'),
                meta: { permission: 'Quản Lý Đối Tác' }
            },
            {
                path: 'goi-dich-vu',
                name: 'admin-goi-dich-vu',
                component: () => import('../components/Admin/GoiDichVu/index.vue')
                // Gói dịch vụ không cần phân quyền riêng
            },
            {
                path: 'yeu-cau-mua-goi',
                name: 'admin-yeu-cau-mua-goi',
                component: () => import('../components/Admin/YeuCauMuaGoi/index.vue'),
                meta: { permission: 'Quản Lý Đối Tác' }
            },
            {
                path: 'quan-ly-tai-khoan',
                name: 'admin-quan-ly-tai-khoan',
                component: () => import('../components/Admin/QuanLyTaiKhoan/index.vue'),
                meta: { permission: 'Quản Lý Người Dùng' }
            },

            {
                path: 'chuc-vu',
                name: 'admin-chuc-vu',
                component: () => import('../components/Admin/ChucVu/index.vue')
            },
            {
                path: 'chuc-nang',
                name: 'admin-chuc-nang',
                component: () => import('../components/Admin/ChucNang/index.vue')
            },
            {
                path: 'nhat-ky-hoat-dong',
                name: 'admin-nhat-ky-hoat-dong',
                component: () => import('../components/Admin/NhatKyHoatDong/index.vue')
            },

            {
                path: 'phan-quyen',
                name: 'admin-phan-quyen',
                component: () => import('../components/Admin/PhanQuyen/index.vue')
            }
        ]
    },

    // =========================================================================
    // PARTNER ROUTES
    // =========================================================================
    {
        path: '/doi-tac',
        component: () => import('../layout/wrapper/partner.vue'),
        meta: { requiresAuth: true, layout: 'blank' },

        children: [
            {
                path: '',
                redirect: '/doi-tac/dashboard'
            },
            {
                path: 'dashboard',
                name: 'partner-dashboard',
                component: () => import('../components/DoiTac/TrangChu/index.vue')
            },
            {
                path: 'gia-pha',
                name: 'partner-gia-pha',
                component: () => import('../components/DoiTac/CayGiaPha/index.vue'),
                meta: { permission: 'Cây Gia Phả' }
            },
            {
                path: 'thanh-vien',
                name: 'partner-thanh-vien',
                component: () => import('../components/DoiTac/QuanLyThanhVien/index.vue'),
                meta: { permission: 'Cây Gia Phả' }
            },
            {
                path: 'tra-cuu',
                name: 'partner-tra-cuu',
                component: () => import('../components/DoiTac/TraCuu/index.vue'),
                meta: { permission: 'Tra Cứu Xưng Hô' }
            },
            {
                path: 'dong-ho',
                name: 'partner-dong-ho',
                component: () => import('../components/DoiTac/QuanLyDongHo/index.vue'),
                meta: { permission: 'Quản Lý Chi Nhánh' }
            },
            {
                path: 'thong-bao',
                name: 'partner-thong-bao',
                component: () => import('../components/DoiTac/QuanLyThongBao/index.vue'),
                meta: { permission: 'Quản Lý Thông Báo' }
            },
            {
                path: 'tai-lieu',
                name: 'partner-tai-lieu',
                component: () => import('../components/DoiTac/QuanLyTaiLieu/index.vue'),
                meta: { permission: 'Quản Lý Tài Liệu' }
            },
            {
                path: 'nhat-ky',
                name: 'partner-nhat-ky',
                component: () => import('../components/DoiTac/LichSuThaoTac/index.vue'),
                meta: { permission: 'Nhật Ký Thao Tác' }
            },
            {
                path: 'de-xuat',
                name: 'partner-de-xuat',
                component: () => import('../components/DoiTac/QuanLyDeXuat/index.vue'),
                meta: { permission: 'Cây Gia Phả' }
            },
            {
                path: 'su-kien',
                name: 'partner-su-kien',
                component: () => import('../components/DoiTac/QuanLySuKien/index.vue'),
                meta: { permission: 'Quản Lý Sự Kiện' }
            },
            {
                path: 'ban-do',
                name: 'partner-ban-do',
                component: () => import('../components/DoiTac/QuanLyBanDo/index.vue'),
                meta: { permission: 'Quản Lý Mộ Phần' }
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// =========================================================================
// NAVIGATION GUARD
// =========================================================================
router.beforeEach(async (to, from, next) => {

    const token = localStorage.getItem('access_token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    // Đọc permissions đã lưu khi login
    let permissions = [];
    try {
        permissions = JSON.parse(localStorage.getItem('permissions') || '[]');
    } catch (e) {
        permissions = [];
    }

    const isMasterAdmin = user?.vai_tro?.toLowerCase() === 'admin';
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

    // ── 1. Chưa đăng nhập → về trang login ──
    if (requiresAuth && !token) {
        return next({ path: '/login', query: { redirect: to.fullPath } });
    }

    // ── 2. Chặn truy cập trang admin (không phải login) ──
    if (to.path.startsWith('/admin') && to.path !== '/admin/login') {
        if (user?.vai_tro?.toLowerCase() !== 'admin') {
            alert('Bạn không có quyền truy cập!');
            return next('/');
        }
    }

    // ── 3. Chặn truy cập trang đối tác ──
    if (to.path.startsWith('/doi-tac')) {
        if (user?.is_doi_tac != 1 && user?.vai_tro?.toLowerCase() !== 'admin') {
            // localStorage cũ báo thiếu quyền -> Gọi đồng bộ API /me để xem Admin có vừa nâng cấp không
            let updatedUser = user;
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/me', {
                    headers: { Authorization: 'Bearer ' + token }
                });
                if (response.data && response.data.user) {
                    updatedUser = response.data.user;
                    localStorage.setItem('user', JSON.stringify(updatedUser));
                    if (response.data.permissions) {
                        localStorage.setItem('permissions', JSON.stringify(response.data.permissions));
                    }
                }
            } catch (error) {
                console.error('Realtime user check failed:', error);
            }

            // Kiểm tra lại sau khi đã sync
            if (updatedUser?.is_doi_tac != 1 && updatedUser?.vai_tro?.toLowerCase() !== 'admin') {
                alert('Bạn cần nâng cấp gói đối tác!');
                return next('/dich-vu-goi');
            }
        }
    }

    // ── 4. Kiểm tra quyền theo meta.permission của từng route ──
    // (Bỏ qua nếu là Master Admin)
    const requiredPermission = to.meta?.permission;
    if (requiredPermission && !isMasterAdmin) {
        const idChucVu = user?.id_chuc_vu;
        // Nếu user có chức vụ (idChucVu khác null/undefined) -> bắt buộc kiểm tra quyền
        if (idChucVu !== null && idChucVu !== undefined) {
            
            // Trường hợp 1: localStorage cũ ĐÃ CÓ quyền
            if (permissions.includes(requiredPermission)) {
                // Gửi ngầm request cập nhật quyền realtime để phòng trường hợp Admin vừa tắt quyền ở backend
                axios.get('http://127.0.0.1:8000/api/me', {
                    headers: { Authorization: 'Bearer ' + token }
                }).then(response => {
                    if (response.data && response.data.permissions) {
                        localStorage.setItem('permissions', JSON.stringify(response.data.permissions));
                        localStorage.setItem('user', JSON.stringify(response.data.user || user));
                    }
                }).catch(() => {});
                
                return next();
            }
            
            // Trường hợp 2: localStorage cũ THIẾU quyền -> gọi đồng bộ để check xem Admin có vừa bật lên không
            let updatedPermissions = [];
            try {
                const response = await axios.get('http://127.0.0.1:8000/api/me', {
                    headers: { Authorization: 'Bearer ' + token }
                });
                if (response.data && response.data.permissions) {
                    updatedPermissions = response.data.permissions;
                    localStorage.setItem('permissions', JSON.stringify(updatedPermissions));
                    localStorage.setItem('user', JSON.stringify(response.data.user || user));
                }
            } catch (error) {
                console.error('Realtime permissions fetch failed:', error);
                updatedPermissions = permissions;
            }

            // Kiểm tra lại với danh sách quyền mới nhất vừa cập nhật
            if (updatedPermissions.includes(requiredPermission)) {
                return next();
            }

            // Thực sự không có quyền -> Báo lỗi & Chặn
            const isThanhVien = String(user.id_chuc_vu) === '3' || (user.vai_tro && user.vai_tro.toLowerCase().includes('thành viên'));
            let errorMsg = "Bạn không có quyền với chức năng này";
            
            if (isThanhVien) {
                const getMemberFriendlyPermissionName = (name) => {
                    if (!name) return "chức năng này";
                    let friendlyName = name.replace(/Quản Lý/g, 'Xem').replace(/quản lý/g, 'xem');
                    if (name === 'Cây Gia Phả') return 'Xem Cây Gia Phả';
                    if (name === 'Tra Cứu Xưng Hô') return 'Xem Tra Cứu Xưng Hô';
                    if (name === 'Quỹ & Sự Kiện') return 'Xem Quỹ & Sự Kiện';
                    if (name === 'Nhật Ký Thao Tác') return 'Xem Nhật Ký Thao Tác';
                    return friendlyName;
                };
                errorMsg = `Bạn chưa được cấp quyền để xem ${getMemberFriendlyPermissionName(requiredPermission)}`;
            }

            try {
                toastr.error(errorMsg);
            } catch (e) {
                alert(errorMsg);
            }
            if (to.path.startsWith('/doi-tac')) {
                return next('/doi-tac/dashboard');
            }
            return next('/admin/dashboard');
        }
    }

    // ── 5. Đã login thì không vào login/register ──
    if ((to.path === '/login' || to.path === '/register') && token) {
        if (user?.vai_tro?.toLowerCase() === 'admin') {
            return next('/admin/dashboard');
        }
        if (user?.is_doi_tac == 1) {
            return next('/doi-tac/dashboard');
        }
        return next('/gia-pha');
    }

    next();
});

export default router;

