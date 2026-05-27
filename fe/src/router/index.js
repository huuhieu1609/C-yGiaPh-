import { createRouter, createWebHistory } from "vue-router";

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
        path: '/de-xuat',
        redirect: '/gia-pha'
    },
    {
        path: '/su-kien',
        name: 'client-su-kien',
        component: () => import('../components/ClientSuKien/index.vue'),
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
                path: 'doi-tac',
                name: 'admin-doi-tac',
                component: () => import('../components/Admin/DoiTac/index.vue')
            },
            {
                path: 'goi-dich-vu',
                name: 'admin-goi-dich-vu',
                component: () => import('../components/Admin/GoiDichVu/index.vue')
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
            },
            {
                path: 'ban-do',
                name: 'partner-ban-do',
                component: () => import('../components/DoiTac/QuanLyBanDo/index.vue')
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
router.beforeEach((to, from, next) => {

    const token = localStorage.getItem('access_token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    const requiresAuth = to.matched.some(
        record => record.meta.requiresAuth
    );

    // Chưa đăng nhập
    if (requiresAuth && !token) {
        return next({
            path: '/login',
            query: { redirect: to.fullPath }
        });
    }

    // Admin
    if (
        to.path.startsWith('/admin') &&
        to.path !== '/admin/login'
    ) {
        if (user?.vai_tro?.toLowerCase() !== 'admin') {
            alert('Bạn không có quyền truy cập!');
            return next('/');
        }
    }

    // Đối tác
    if (to.path.startsWith('/doi-tac')) {

        if (
            user?.is_doi_tac != 1 &&
            user?.vai_tro?.toLowerCase() !== 'admin'
        ) {
            alert('Bạn cần nâng cấp gói đối tác!');
            return next('/dich-vu-goi');
        }
    }

    // Đã login thì không vào login/register
    if (
        (to.path === '/login' || to.path === '/register')
        && token
    ) {

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
