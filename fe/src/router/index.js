import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: '/',
        name: 'trang-chu',
        component: () => import('../components/TrangChu/index.vue'),
        meta: { layout: 'client' }
    },
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
        path: '/profile',
        name: 'profile',
        component: () => import('../components/ClientProfile/index.vue'),
        meta: { layout: 'client' }
    },
    {
        path: '/gia-pha',
        name: 'gia-pha',
        component: () => import('../components/ClientGiaPha/index.vue'),
        meta: { layout: 'client' }
    },
    {
        path: '/tra-cuu',
        name: 'client-tra-cuu',
        component: () => import('../components/ClientTraCuu/index.vue'),
        meta: { layout: 'client' }
    },
    {
        path: '/thanh-toan',
        name: 'thanh-toan',
        component: () => import('../components/ClientThanhToan/index.vue'),
        meta: { layout: 'client' }
    },
    {
        path: '/admin/login',
        name: 'admin-login',
        component: () => import('../components/Admin/Auth/Login.vue'),
        meta: { layout: 'blank' }
    },
    
    // Admin Routes
    {
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
    },

    // Partner Routes
    {
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
        path: '/admin/phan-quyen',
        name: 'admin-phan-quyen',
        component: () => import('../components/Admin/PhanQuyen/index.vue'),
        meta: { layout: 'default' }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes: routes
})

export default router