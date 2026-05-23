import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: '/',
        name: 'trang-chu',
        component: () => import('../components/TrangChu/index.vue'),
        meta: { layout: 'client' }
    },
    {
<<<<<<< Updated upstream
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
=======
        path: '/dich-vu-goi',
        name: 'dich-vu-goi',
        component: () => import('../components/DichVuGoi/index.vue'),
        meta: { layout: 'client' }
>>>>>>> Stashed changes
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
        path: '/tra-cuu',
        name: 'client-tra-cuu',
        component: () => import('../components/ClientTraCuu/index.vue'),
        meta: { layout: 'client', requiresAuth: true }
    },
    {
        path: '/thanh-vien/detail/:id',
        name: 'public-member-detail',
        component: () => import('../components/ClientTraCuu/PublicDetail.vue'),
        meta: { layout: 'client' }
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
<<<<<<< Updated upstream
=======

    // =========================================================================
    // BLANK LAYOUT ROUTES (AUTH PAGES)
    // =========================================================================
>>>>>>> Stashed changes
    {
        path: '/login',
        name: 'login',
        component: () => import('../components/Auth/Login.vue'),
        meta: { layout: 'blank' }
    },
    {
<<<<<<< Updated upstream
        path: '/admin/dashboard',
        name: 'admin-dashboard',
        component: () => import('../components/Admin/Dashboard.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/gia-pha',
        name: 'admin-gia-pha',
        component: () => import('../components/Admin/GiaPha/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/nguoi-dung',
        name: 'admin-nguoi-dung',
        component: () => import('../components/Admin/NguoiDung/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/chi-nhanh',
        name: 'admin-chi-nhanh',
        component: () => import('../components/Admin/ChiNhanh/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/doi-toc-ho',
        name: 'admin-doi-toc-ho',
        component: () => import('../components/Admin/DoiTocHo/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/su-kien',
        name: 'admin-su-kien',
        component: () => import('../components/Admin/SuKien/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/tham-gia-su-kien',
        name: 'admin-tham-gia-su-kien',
        component: () => import('../components/Admin/ThamGiaSuKien/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/dong-gop',
        name: 'admin-dong-gop',
        component: () => import('../components/Admin/DongGop/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/nha-tho-ho',
        name: 'admin-nha-tho-ho',
        component: () => import('../components/Admin/NhaThoHo/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/mo-phan',
        name: 'admin-mo-phan',
        component: () => import('../components/Admin/MoPhan/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/chuc-vu',
        name: 'admin-chuc-vu',
        component: () => import('../components/Admin/ChucVu/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/chuc-nang',
        name: 'admin-chuc-nang',
        component: () => import('../components/Admin/ChucNang/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/nhat-ky-hoat-dong',
        name: 'admin-nhat-ky-hoat-dong',
        component: () => import('../components/Admin/NhatKyHoatDong/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/admin/tra-cuu',
        name: 'admin-tra-cuu',
        component: () => import('../components/Admin/TraCuu/index.vue'),
        meta: { layout: 'default' }
=======
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
        path: '/nguoi-dung',
        redirect: '/gia-pha'
    },

    // =========================================================================
    // ADMIN ROUTES (NESTED UNDER DEFAULT-LAYOUT COMPONENT)
    // =========================================================================
    {
        path: '/admin',
        component: () => import('../layout/wrapper/index.vue'), // Default Layout gốc làm cha
        meta: { requiresAuth: true, layout: 'blank' },
        children: [
            {
                path: '',
                redirect: '/admin/dashboard'
            },
            {
                path: 'dashboard',
                name: 'admin-dashboard',
                component: () => import('../components/Admin/TongQuan/Dashboard.vue')
            },
            {
                path: 'gia-pha',
                name: 'admin-gia-pha',
                component: () => import('../components/Admin/GiaPha/index.vue')
            },
            {
                path: 'nguoi-dung',
                name: 'admin-nguoi-dung',
                component: () => import('../components/Admin/NguoiDung/index.vue')
            },
            {
                path: 'chi-nhanh',
                name: 'admin-chi-nhanh',
                component: () => import('../components/Admin/ChiNhanh/index.vue')
            },
            {
                path: 'doi-toc-ho',
                name: 'admin-doi-toc-ho',
                component: () => import('../components/Admin/DoiTocHo/index.vue')
            },
            {
                path: 'su-kien',
                name: 'admin-su-kien',
                component: () => import('../components/Admin/SuKien/index.vue')
            },
            {
                path: 'tham-gia-su-kien',
                name: 'admin-tham-gia-su-kien',
                component: () => import('../components/Admin/ThamGiaSuKien/index.vue')
            },
            {
                path: 'dong-gop',
                name: 'admin-dong-gop',
                component: () => import('../components/Admin/DongGop/index.vue')
            },
            {
                path: 'nha-tho-ho',
                name: 'admin-nha-tho-ho',
                component: () => import('../components/Admin/NhaThoHo/index.vue')
            },
            {
                path: 'mo-phan',
                name: 'admin-mo-phan',
                component: () => import('../components/Admin/MoPhan/index.vue')
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
                path: 'tra-cuu',
                name: 'admin-tra-cuu',
                component: () => import('../components/Admin/TraCuu/index.vue')
            },
            {
                path: 'phan-quyen',
                name: 'admin-phan-quyen',
                component: () => import('../components/Admin/PhanQuyen/index.vue')
            }
        ]
>>>>>>> Stashed changes
    },

    // Partner Routes
    {
<<<<<<< Updated upstream
        path: '/doi-tac/dashboard',
        name: 'partner-dashboard',
        component: () => import('../components/DoiTac/TrangChu/index.vue'),
        meta: { layout: 'partner' }
    },
    {
        path: '/doi-tac/gia-pha',
        name: 'partner-gia-pha',
        component: () => import('../components/DoiTac/CayGiaPha/index.vue'),
        meta: { layout: 'partner' }
    },
    {
        path: '/doi-tac/thanh-vien',
        name: 'partner-thanh-vien',
        component: () => import('../components/DoiTac/QuanLyThanhVien/index.vue'),
        meta: { layout: 'partner' }
    },
    {
        path: '/doi-tac/tra-cuu',
        name: 'partner-tra-cuu',
        component: () => import('../components/DoiTac/TraCuu/index.vue'),
        meta: { layout: 'partner' }
    },
    {
        path: '/doi-tac/dong-ho',
        name: 'partner-dong-ho',
        component: () => import('../components/DoiTac/QuanLyDongHo/index.vue'),
        meta: { layout: 'partner', requiresAuth: true }
    },
    {
        path: '/admin/phan-quyen',
        name: 'admin-phan-quyen',
        component: () => import('../components/Admin/PhanQuyen/index.vue'),
        meta: { layout: 'default' }
    },
    {
        path: '/doi-tac/thong-bao',
        name: 'partner-thong-bao',
        component: () => import('../components/DoiTac/QuanLyThongBao/index.vue'),
        meta: { layout: 'partner', requiresAuth: true }
    },
    {
        path: '/doi-tac/tai-lieu',
        name: 'partner-tai-lieu',
        component: () => import('../components/DoiTac/QuanLyTaiLieu/index.vue'),
        meta: { layout: 'partner', requiresAuth: true }
    },
    {
        path: '/doi-tac/nhat-ky',
        name: 'partner-nhat-ky',
        component: () => import('../components/DoiTac/LichSuThaoTac/index.vue'),
        meta: { layout: 'partner', requiresAuth: true }
    },
    {
        path: '/doi-tac/de-xuat',
        name: 'partner-de-xuat',
        component: () => import('../components/DoiTac/QuanLyDeXuat/index.vue'),
        meta: { layout: 'partner', requiresAuth: true }
    },
    {
        path: '/doi-tac/su-kien',
        name: 'partner-su-kien',
        component: () => import('../components/DoiTac/QuanLySuKien/index.vue'),
        meta: { layout: 'partner', requiresAuth: true }
=======
        path: '/doi-tac',
        component: () => import('../layout/wrapper/partner.vue'), // Sử dụng Partner Layout làm gốc
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
                component: () => import('../components/DoiTac/CayGiaPha/index.vue')
            },
            {
                path: 'thanh-vien',
                name: 'partner-thanh-vien',
                component: () => import('../components/DoiTac/QuanLyThanhVien/index.vue')
            },
            {
                path: 'tra-cuu',
                name: 'partner-tra-cuu',
                component: () => import('../components/DoiTac/TraCuu/index.vue')
            },
            {
                path: 'dong-ho',
                name: 'partner-dong-ho',
                component: () => import('../components/DoiTac/QuanLyDongHo/index.vue')
            },
            {
                path: 'thong-bao',
                name: 'partner-thong-bao',
                component: () => import('../components/DoiTac/QuanLyThongBao/index.vue')
            },
            {
                path: 'tai-lieu',
                name: 'partner-tai-lieu',
                component: () => import('../components/DoiTac/QuanLyTaiLieu/index.vue')
            },
            {
                path: 'nhat-ky',
                name: 'partner-nhat-ky',
                component: () => import('../components/DoiTac/LichSuThaoTac/index.vue')
            },
            {
                path: 'de-xuat',
                name: 'partner-de-xuat',
                component: () => import('../components/DoiTac/QuanLyDeXuat/index.vue')
            },
            {
                path: 'su-kien',
                name: 'partner-su-kien',
                component: () => import('../components/DoiTac/QuanLySuKien/index.vue')
            }
        ]
>>>>>>> Stashed changes
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes: routes
})

<<<<<<< Updated upstream
// Navigation Guard for Authentication
=======
// =========================================================================
// NAVIGATION GUARD (AUTHENTICATION & AUTHORIZATION)
// =========================================================================
>>>>>>> Stashed changes
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('access_token');
    const userStr = localStorage.getItem('user');
    const user = userStr ? JSON.parse(userStr) : null;
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

<<<<<<< Updated upstream
    if (requiresAuth && !isAuthenticated) {
        next({ path: '/login', query: { redirect: to.fullPath } });
    } else {
        next();
    }
});

export default router
=======
    // 1. Nếu trang yêu cầu Auth mà chưa đăng nhập -> Đẩy về login
    if (requiresAuth && !token) {
        return next({ path: '/login', query: { redirect: to.fullPath } });
    }

    // 2. Bảo vệ cụm Route Admin
    if (to.path.startsWith('/admin') && to.path !== '/admin/login') {
        if (!user || user.vai_tro?.toLowerCase() !== 'admin') {
            alert('Bạn không có quyền truy cập trang quản trị!');
            return next('/');
        }
    }

    // 3. Bảo vệ cụm Route Đối Tác
    if (to.path.startsWith('/doi-tac')) {
        if (!user || (user.is_doi_tac != 1 && user.vai_tro?.toLowerCase() !== 'admin')) {
            alert('Vui lòng thanh toán gói dịch vụ để sử dụng chức năng quản lý gia phả!');
            return next('/dich-vu-goi');
        }
    }

    // 4. Nếu đã đăng nhập thành công mà quay lại login/register -> Tự động đưa về đúng Dashboard
    if ((to.path === '/login' || to.path === '/register') && token) {
        if (user?.vai_tro?.toLowerCase() === 'admin') return next('/admin/dashboard');
        if (user?.is_doi_tac == 1) return next('/doi-tac/dashboard');
        return next('/gia-pha');
    }

    // Tất cả điều kiện thỏa mãn -> Cho phép điều hướng
    next();
});

export default router;
>>>>>>> Stashed changes
