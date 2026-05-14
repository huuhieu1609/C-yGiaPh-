<template>
  <nav class="admin-sidebar shadow" :class="{ 'collapsed': isCollapsed }" style="background: linear-gradient(135deg, #1a1c2e 0%, #252740 50%, #1e2035 100%);">
    <!-- Header / Logo -->
    <div class="sidebar-header p-3 d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-25 position-relative">
      <div class="brand-icon d-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-3 flex-shrink-0" style="width: 40px; height: 40px;">
        <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:24px; height:24px;">
          <circle cx="20" cy="8" r="5" fill="url(#g1)" />
          <line x1="20" y1="13" x2="20" y2="20" stroke="url(#g1)" stroke-width="2"/>
          <line x1="20" y1="20" x2="12" y2="28" stroke="url(#g2)" stroke-width="2"/>
          <line x1="20" y1="20" x2="28" y2="28" stroke="url(#g2)" stroke-width="2"/>
          <circle cx="12" cy="30" r="4" fill="url(#g2)" />
          <circle cx="28" cy="30" r="4" fill="url(#g2)" />
          <circle cx="4" cy="38" r="2.5" fill="url(#g3)" opacity="0.7"/>
          <circle cx="20" cy="38" r="2.5" fill="url(#g3)" opacity="0.7"/>
          <circle cx="36" cy="38" r="2.5" fill="url(#g3)" opacity="0.7"/>
          <line x1="12" y1="34" x2="4" y2="36" stroke="url(#g3)" stroke-width="1.5" opacity="0.7"/>
          <line x1="12" y1="34" x2="20" y2="36" stroke="url(#g3)" stroke-width="1.5" opacity="0.7"/>
          <line x1="28" y1="34" x2="36" y2="36" stroke="url(#g3)" stroke-width="1.5" opacity="0.7"/>
          <defs>
            <linearGradient id="g1" x1="0" y1="0" x2="40" y2="40">
              <stop offset="0%" stop-color="#66bb6a"/>
              <stop offset="100%" stop-color="#2e7d32"/>
            </linearGradient>
            <linearGradient id="g2" x1="0" y1="15" x2="40" y2="35">
              <stop offset="0%" stop-color="#43a047"/>
              <stop offset="100%" stop-color="#1b5e20"/>
            </linearGradient>
            <linearGradient id="g3" x1="0" y1="30" x2="40" y2="40">
              <stop offset="0%" stop-color="#81c784"/>
              <stop offset="100%" stop-color="#388e3c"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      <span class="fw-bold fs-5 text-success logo-text text-nowrap hide-on-collapse">Gia Phả Admin</span>
    </div>

    <!-- Floating Toggle Button -->
    <button class="toggle-btn shadow-sm" @click="$emit('toggle-sidebar')" title="Thu gọn/Mở rộng">
      <i class='bx' :class="isCollapsed ? 'bx-chevron-right' : 'bx-chevron-left'"></i>
    </button>

    <!-- User Greeting Profile -->
    <div class="user-greeting p-3 d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-25">
      <div class="avatar d-flex align-items-center justify-content-center bg-success text-white fw-bold rounded-circle shadow-sm flex-shrink-0" style="width: 44px; height: 44px; font-size: 18px;">
        {{ userName.charAt(0).toUpperCase() }}
      </div>
      <div class="d-flex flex-column lh-1 hide-on-collapse text-nowrap">
        <span class="text-white-50 mb-1" style="font-size: 13px;">Xin chào,</span>
        <strong class="text-white fs-6">{{ userName }}</strong>
      </div>
    </div>

    <!-- Main Navigation Menu -->
    <div class="sidebar-body p-3 overflow-auto flex-grow-1">
      <ul class="nav nav-pills flex-column mb-auto gap-1">
        <li class="nav-item">
          <router-link to="/admin/dashboard" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Tổng Quan">
            <i class="bx bx-home-circle fs-5"></i><span class="hide-on-collapse text-nowrap">Tổng Quan</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/gia-pha" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Gia Phả">
            <i class="bx bx-git-branch fs-5"></i><span class="hide-on-collapse text-nowrap">Gia Phả</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/tra-cuu" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Tra Cứu">
            <i class="bx bx-search-alt fs-5"></i><span class="hide-on-collapse text-nowrap">Tra Cứu</span>
          </router-link>
        </li>

        <!-- Dòng Họ -->
        <li class="nav-heading text-uppercase text-white-50 mt-4 mb-2 ps-3 hide-on-collapse text-nowrap" style="font-size: 11px; font-weight: 600; letter-spacing: 0.8px;">Quản Lý Dòng Họ</li>
        <li class="nav-item">
          <router-link to="/admin/chi-nhanh" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Chi Nhánh"><i class="bx bx-sitemap fs-5"></i> <span class="hide-on-collapse text-nowrap">Chi Nhánh</span></router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/doi-toc-ho" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Đời Tộc Họ"><i class="bx bx-list-ul fs-5"></i> <span class="hide-on-collapse text-nowrap">Đời Tộc Họ</span></router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/nha-tho-ho" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Nhà Thờ Họ"><i class="bx bx-building-house fs-5"></i> <span class="hide-on-collapse text-nowrap">Nhà Thờ Họ</span></router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/mo-phan" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Mộ Phần"><i class="bx bx-map-pin fs-5"></i> <span class="hide-on-collapse text-nowrap">Mộ Phần</span></router-link>
        </li>

        <!-- Sự Kiện & Quỹ -->
        <li class="nav-heading text-uppercase text-white-50 mt-4 mb-2 ps-3 hide-on-collapse text-nowrap" style="font-size: 11px; font-weight: 600; letter-spacing: 0.8px;">Sự Kiện & Quỹ</li>
        <li class="nav-item">
          <router-link to="/admin/su-kien" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Sự Kiện"><i class="bx bx-calendar-event fs-5"></i> <span class="hide-on-collapse text-nowrap">Sự Kiện</span></router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/tham-gia-su-kien" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Tham Gia"><i class="bx bx-user-check fs-5"></i> <span class="hide-on-collapse text-nowrap">Tham Gia</span></router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/dong-gop" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Đóng Góp"><i class="bx bx-wallet fs-5"></i> <span class="hide-on-collapse text-nowrap">Đóng Góp</span></router-link>
        </li>

        <!-- Hệ Thống -->
        <li class="nav-heading text-uppercase text-white-50 mt-4 mb-2 ps-3 hide-on-collapse text-nowrap" style="font-size: 11px; font-weight: 600; letter-spacing: 0.8px;">Hệ Thống</li>
        <li class="nav-item">
          <router-link to="/admin/nguoi-dung" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Người Dùng"><i class="bx bx-user fs-5"></i> <span class="hide-on-collapse text-nowrap">Người Dùng</span></router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/chuc-vu" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Chức Vụ"><i class="bx bx-shield fs-5"></i> <span class="hide-on-collapse text-nowrap">Chức Vụ</span></router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/chuc-nang" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Chức Năng"><i class="bx bx-slider fs-5"></i> <span class="hide-on-collapse text-nowrap">Chức Năng</span></router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/phan-quyen" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Phân Quyền"><i class="bx bx-shield-alt fs-5"></i> <span class="hide-on-collapse text-nowrap">Phân Quyền</span></router-link>
        </li>
      </ul>
    </div>

    <!-- Footer Actions -->
    <div class="sidebar-footer p-3 border-top border-secondary border-opacity-25 mt-auto">
      <button class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 shadow-sm" @click="logout" title="Đăng Xuất">
        <i class="bx bx-log-out-circle fs-5"></i> <span class="hide-on-collapse text-nowrap">Đăng Xuất</span>
      </button>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'AdminNavbar',
  props: {
    isCollapsed: {
      type: Boolean,
      default: false
    }
  },
  emits: ['toggle-sidebar'],
  data() {
    return {
      userName: 'Admin'
    }
  },
  methods: {
    logout() {
      this.$router.push('/admin/login');
    }
  }
}
</script>

<style scoped>
/* Sidebar Container Layout */
.admin-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  width: 280px;
  display: flex;
  flex-direction: column;
  z-index: 1040;
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.admin-sidebar.collapsed {
  width: 80px;
}

.admin-sidebar.collapsed .hide-on-collapse {
  display: none !important;
}

.admin-sidebar.collapsed .sidebar-header {
  padding: 15px 10px !important;
  justify-content: center !important;
}

.admin-sidebar.collapsed .user-greeting {
  justify-content: center !important;
  padding: 15px 10px !important;
}

.admin-sidebar.collapsed .nav-link {
  justify-content: center !important;
  padding: 10px !important;
}

.admin-sidebar.collapsed .nav-link i {
  font-size: 24px !important;
}

.admin-sidebar.collapsed .sidebar-footer {
  padding: 10px !important;
}

.admin-sidebar.collapsed .btn-danger {
  padding: 10px !important;
}

/* Floating Toggle Button */
.toggle-btn {
  position: absolute;
  right: -14px;
  top: 24px;
  background: #252740;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255,255,255,0.1);
  color: #fff !important;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
  z-index: 1050;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}

.toggle-btn:hover {
  background: #4caf50;
  transform: scale(1.1);
}

.toggle-btn i {
  font-size: 1.2rem;
}

/* Gradient Logo Text */
.logo-text {
  background: linear-gradient(135deg, #81c784, #43a047);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* User Greeting Custom Styles */
.user-greeting {
  background: rgba(0, 0, 0, 0.15); /* Slight dark overlay for contrast */
}

/* Navigation Links */
.nav-item {
  position: relative;
}

.nav-link {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  border-radius: 12px;
  font-weight: 500;
  font-size: 14.5px;
}

.nav-link:hover {
  color: #fff !important;
  background: rgba(255, 255, 255, 0.08);
  transform: translateX(4px);
}

.nav-link.active,
.nav-link.router-link-active {
  color: #fff !important;
  background: linear-gradient(135deg, rgba(76,175,80,0.25), rgba(56,142,60,0.1));
  box-shadow: 0 4px 15px rgba(76,175,80,0.15);
  font-weight: 600;
}

/* Active Indicator Line */
.nav-link.active::before,
.nav-link.router-link-active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 15%;
  height: 70%;
  width: 4px;
  background: #66bb6a;
  border-radius: 0 4px 4px 0;
  box-shadow: 2px 0 8px rgba(102, 187, 106, 0.5);
}

/* Custom Scrollbar for Sidebar */
.sidebar-body::-webkit-scrollbar {
  width: 5px;
}
.sidebar-body::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-body::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.15);
  border-radius: 10px;
}
.sidebar-body::-webkit-scrollbar-thumb:hover {
  background: rgba(255,255,255,0.25);
}
</style>
