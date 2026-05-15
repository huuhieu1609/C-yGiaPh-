<template>
  <nav class="admin-sidebar shadow" :class="{ 'sidebar-collapsed-state': isCollapsed }" style="background: linear-gradient(135deg, #1a1c2e 0%, #252740 50%, #1e2035 100%);">
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
          <defs>
            <linearGradient id="g1" x1="0" y1="0" x2="40" y2="40">
              <stop offset="0%" stop-color="#4fc3f7"/>
              <stop offset="100%" stop-color="#0288d1"/>
            </linearGradient>
            <linearGradient id="g2" x1="0" y1="15" x2="40" y2="35">
              <stop offset="0%" stop-color="#29b6f6"/>
              <stop offset="100%" stop-color="#01579b"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      <span class="fw-bold fs-5 text-info logo-text text-nowrap hide-on-collapse">Gia Phả Đối Tác</span>
    </div>

    <!-- Floating Toggle Button -->
    <button class="toggle-btn shadow-sm" @click="$emit('toggle-sidebar')" title="Thu gọn/Mở rộng">
      <i class='bx' :class="isCollapsed ? 'bx-chevron-right' : 'bx-chevron-left'"></i>
    </button>

    <!-- User Greeting Profile -->
    <div class="user-greeting p-3 d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-25">
      <div class="avatar d-flex align-items-center justify-content-center bg-info text-white fw-bold rounded-circle shadow-sm flex-shrink-0" style="width: 44px; height: 44px; font-size: 18px;">
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
          <router-link to="/doi-tac/dashboard" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Tổng Quan">
            <i class="bx bx-home-circle fs-5"></i><span class="hide-on-collapse text-nowrap">Tổng Quan</span>
          </router-link>
        </li>
        
        <li class="nav-heading text-uppercase text-white-50 mt-4 mb-2 ps-3 hide-on-collapse text-nowrap" style="font-size: 11px; font-weight: 600; letter-spacing: 0.8px;">Quản Lý Gia Phả</li>
        
        <li class="nav-item">
          <router-link to="/doi-tac/gia-pha" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Cây Gia Phả">
            <i class="bx bx-git-branch fs-5"></i><span class="hide-on-collapse text-nowrap">Cây Gia Phả</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/doi-tac/thanh-vien" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Thành Viên">
            <i class="bx bx-group fs-5"></i><span class="hide-on-collapse text-nowrap">Thành Viên</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/doi-tac/tra-cuu" class="nav-link text-white-50 d-flex align-items-center gap-3 px-3 py-2" active-class="active" title="Tra Cứu">
            <i class="bx bx-search-alt fs-5"></i><span class="hide-on-collapse text-nowrap">Tra Cứu</span>
          </router-link>
        </li>
      </ul>
    </div>

    <!-- Footer Actions -->
    <div class="sidebar-footer p-3 border-top border-secondary border-opacity-25 mt-auto">
      <button class="btn btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 shadow-sm mb-2" @click="goHome" title="Về Trang Chủ">
        <i class="bx bx-home fs-5"></i> <span class="hide-on-collapse text-nowrap">Trang Chủ</span>
      </button>
      <button class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 shadow-sm" @click="logout" title="Đăng Xuất">
        <i class="bx bx-log-out-circle fs-5"></i> <span class="hide-on-collapse text-nowrap">Đăng Xuất</span>
      </button>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'PartnerNavbar',
  props: {
    isCollapsed: {
      type: Boolean,
      default: false
    }
  },
  emits: ['toggle-sidebar'],
  data() {
    return {
      userName: 'Đối Tác'
    }
  },
  methods: {
    logout() {
      // Clear token/session here if needed
      this.$router.push('/login');
    },
    goHome() {
      this.$router.push('/');
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
  width: 280px !important;
  display: flex;
  flex-direction: column;
  z-index: 1040;
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.admin-sidebar.sidebar-collapsed-state {
  width: 80px !important;
}

.admin-sidebar.sidebar-collapsed-state .hide-on-collapse {
  display: none !important;
}

.admin-sidebar.sidebar-collapsed-state .sidebar-header {
  padding: 15px 10px !important;
  justify-content: center !important;
}

.admin-sidebar.sidebar-collapsed-state .user-greeting {
  justify-content: center !important;
  padding: 15px 10px !important;
}

.admin-sidebar.sidebar-collapsed-state .nav-link {
  justify-content: center !important;
  padding: 10px !important;
}

.admin-sidebar.sidebar-collapsed-state .nav-link i {
  font-size: 24px !important;
}

.admin-sidebar.sidebar-collapsed-state .sidebar-footer {
  padding: 10px !important;
}

.admin-sidebar.sidebar-collapsed-state .btn {
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
  background: #03a9f4;
  transform: scale(1.1);
}

.toggle-btn i {
  font-size: 1.2rem;
}

/* Gradient Logo Text */
.logo-text {
  background: linear-gradient(135deg, #4fc3f7, #0288d1);
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
  background: linear-gradient(135deg, rgba(3,169,244,0.25), rgba(1,87,155,0.1));
  box-shadow: 0 4px 15px rgba(3,169,244,0.15);
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
  background: #03a9f4;
  border-radius: 0 4px 4px 0;
  box-shadow: 2px 0 8px rgba(3, 169, 244, 0.5);
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
