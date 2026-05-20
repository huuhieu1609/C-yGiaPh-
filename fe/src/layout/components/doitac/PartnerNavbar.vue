<template>
  <nav class="admin-sidebar" :class="{ 'sidebar-collapsed-state': isCollapsed }">
    <!-- Header / Logo -->
    <div class="sidebar-header">
      <div class="brand-logo-container d-flex align-items-center gap-3">
        <div class="brand-icon-wrapper">
          <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-svg">
            <circle cx="20" cy="8" r="5" fill="url(#g1)" />
            <line x1="20" y1="13" x2="20" y2="20" stroke="url(#g1)" stroke-width="2"/>
            <line x1="20" y1="20" x2="12" y2="28" stroke="url(#g2)" stroke-width="2"/>
            <line x1="20" y1="20" x2="28" y2="28" stroke="url(#g2)" stroke-width="2"/>
            <circle cx="12" cy="30" r="4" fill="url(#g2)" />
            <circle cx="28" cy="30" r="4" fill="url(#g2)" />
            <defs>
              <linearGradient id="g1" x1="0" y1="0" x2="40" y2="40">
                <stop offset="0%" stop-color="#00f2fe"/>
                <stop offset="100%" stop-color="#4facfe"/>
              </linearGradient>
              <linearGradient id="g2" x1="0" y1="15" x2="40" y2="35">
                <stop offset="0%" stop-color="#38ef7d"/>
                <stop offset="100%" stop-color="#11998e"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div class="d-flex flex-column hide-on-collapse">
          <span class="fw-bold fs-5 logo-text text-nowrap">Gia Phả <span class="partner-badge">Đối Tác</span></span>
        </div>
      </div>
    </div>

    <!-- Floating Toggle Button (Sleek Mechanical Design) -->
    <button class="toggle-btn" @click="$emit('toggle-sidebar')" :title="isCollapsed ? 'Mở rộng' : 'Thu gọn'">
      <i class='bx' :class="isCollapsed ? 'bx-chevron-right' : 'bx-chevron-left'"></i>
    </button>

    <!-- User Greeting Profile (Premium Card) -->
    <div class="user-profile-card">
      <div class="avatar-wrapper position-relative">
        <div class="avatar-ring"></div>
        <div class="avatar-container d-flex align-items-center justify-content-center text-white fw-bold rounded-circle shadow-sm">
          {{ userName.charAt(0).toUpperCase() }}
        </div>
        <span class="status-indicator-dot"></span>
      </div>
      <div class="user-meta-info hide-on-collapse text-nowrap">
        <span class="greet-title text-white-50">Xin chào,</span>
        <strong class="user-fullname text-white" :title="userName">{{ userName }}</strong>
        <span class="badge premium-rank-badge mt-1">
          <i class="bx bxs-crown text-warning me-1"></i>Đối Tác Vàng
        </span>
      </div>
    </div>

    <!-- Main Navigation Menu -->
    <div class="sidebar-body overflow-auto flex-grow-1">
      <div class="nav-heading text-uppercase text-white-50 px-3 mb-2 hide-on-collapse">Tổng Quan</div>
      <ul class="nav nav-pills flex-column gap-1 px-2">
        <li class="nav-item">
          <router-link to="/doi-tac/dashboard" class="nav-link" active-class="active" title="Tổng Quan Dashboard">
            <i class="bx bx-grid-alt"></i>
            <span class="hide-on-collapse">Dashboard</span>
          </router-link>
        </li>
      </ul>

      <div class="nav-heading text-uppercase text-white-50 px-3 mt-4 mb-2 hide-on-collapse">Quản Lý Gia Hệ</div>
      <ul class="nav nav-pills flex-column gap-1 px-2">
        <li class="nav-item">
          <router-link to="/doi-tac/dong-ho" class="nav-link" active-class="active" title="Quản Lý Dòng Họ">
            <i class="bx bx-building-house"></i>
            <span class="hide-on-collapse">Quản Lý Dòng Họ</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/doi-tac/gia-pha" class="nav-link" active-class="active" title="Cây Gia Phả">
            <i class="bx bx-git-branch"></i>
            <span class="hide-on-collapse">Cây Gia Phả</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/doi-tac/thanh-vien" class="nav-link" active-class="active" title="Quản Lý Thành Viên">
            <i class="bx bx-group"></i>
            <span class="hide-on-collapse">Quản Lý Thành Viên</span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/doi-tac/tra-cuu" class="nav-link" active-class="active" title="Tra Cứu Gia Phả">
            <i class="bx bx-search-alt"></i>
            <span class="hide-on-collapse">Tra Cứu</span>
          </router-link>
        </li>
      </ul>
    </div>

    <!-- Footer Actions (Premium Buttons) -->
    <div class="sidebar-footer p-3 gap-2 d-flex flex-column">
      <button class="btn btn-home-redirect w-100 d-flex align-items-center justify-content-center gap-2 py-2.5 rounded-3" @click="goHome" title="Về Trang Chủ">
        <i class="bx bx-home-alt"></i> <span class="hide-on-collapse text-nowrap">Trang Chủ</span>
      </button>
      <button class="btn btn-logout-action w-100 d-flex align-items-center justify-content-center gap-2 py-2.5 rounded-3" @click="logout" title="Đăng Xuất">
        <i class="bx bx-power-off"></i> <span class="hide-on-collapse text-nowrap">Đăng Xuất</span>
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
  mounted() {
    this.checkLogin();
  },
  methods: {
    checkLogin() {
      const userData = JSON.parse(localStorage.getItem('user'));
      if (userData) {
        const user = userData.user || userData;
        this.userName = user.ho_ten || 'Đối Tác';
      }
    },
    logout() {
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
      this.$router.push('/login');
    },
    goHome() {
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>
/* Sidebar Main Container */
.admin-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  width: 280px;
  display: flex;
  flex-direction: column;
  z-index: 1040;
  background: linear-gradient(145deg, #0d0f1e 0%, #151930 60%, #0a0b16 100%);
  border-right: 1px solid rgba(255, 255, 255, 0.05);
  box-shadow: 10px 0 30px rgba(0, 0, 0, 0.35);
  transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
}

/* Sidebar Collapsed State overrides */
.admin-sidebar.sidebar-collapsed-state {
  width: 80px;
}

.admin-sidebar.sidebar-collapsed-state .hide-on-collapse {
  display: none !important;
  opacity: 0;
  width: 0;
}

/* Header & Brand */
.sidebar-header {
  padding: 24px 18px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.03);
}

.brand-icon-wrapper {
  width: 44px;
  height: 44px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.02);
  transition: all 0.3s ease;
}

.admin-sidebar.sidebar-collapsed-state .sidebar-header {
  padding: 20px 10px;
  display: flex;
  justify-content: center;
}

.admin-sidebar.sidebar-collapsed-state .brand-logo-container {
  justify-content: center;
}

.logo-svg {
  width: 26px;
  height: 26px;
  filter: drop-shadow(0 0 4px rgba(0, 242, 254, 0.3));
}

.logo-text {
  font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
  font-weight: 700 !important;
  letter-spacing: 0.5px;
  color: #ffffff;
  background: linear-gradient(135deg, #ffffff 30%, #a5b4fc 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.partner-badge {
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  padding: 2px 6px;
  border-radius: 6px;
  margin-left: 2px;
  vertical-align: middle;
  background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
  -webkit-text-fill-color: #0b0c15;
  color: #0b0c15;
  box-shadow: 0 2px 8px rgba(0, 242, 254, 0.4);
}

/* Floating Sidebar Toggle Button (Premium Mechanical Design) */
.toggle-btn {
  position: absolute;
  right: -13px;
  top: 30px;
  background: #171c35;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #a5b4fc !important;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
  z-index: 1050;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  cursor: pointer;
}

.toggle-btn:hover {
  background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
  color: #0b0c15 !important;
  transform: scale(1.15) rotate(180deg);
  box-shadow: 0 0 12px rgba(0, 242, 254, 0.6);
  border-color: transparent;
}

.toggle-btn i {
  font-size: 1.2rem;
}

/* User Profile Card */
.user-profile-card {
  margin: 18px;
  padding: 16px;
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.04);
  border-radius: 16px;
  display: flex;
  align-items: center;
  gap: 15px;
  transition: all 0.3s ease;
}

.admin-sidebar.sidebar-collapsed-state .user-profile-card {
  margin: 15px 5px;
  padding: 10px 5px;
  justify-content: center;
  background: transparent;
  border-color: transparent;
}

.avatar-wrapper {
  flex-shrink: 0;
}

.avatar-ring {
  position: absolute;
  top: -3px;
  left: -3px;
  width: 52px;
  height: 52px;
  border-radius: 50%;
  border: 2px dashed rgba(0, 242, 254, 0.4);
  animation: rotate-ring 12s linear infinite;
}

@keyframes rotate-ring {
  100% {
    transform: rotate(360deg);
  }
}

.avatar-container {
  width: 46px;
  height: 46px;
  font-size: 18px;
  background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
  color: #0b0c15 !important;
  border: 2px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 4px 12px rgba(0, 242, 254, 0.25);
  border-radius: 50%;
  z-index: 2;
  position: relative;
}

.status-indicator-dot {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 12px;
  height: 12px;
  background-color: #2ecc71;
  border: 2.5px solid #0d0f1e;
  border-radius: 50%;
  z-index: 3;
  box-shadow: 0 0 10px #2ecc71;
}

.user-meta-info {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  line-height: 1.35 !important; /* Spaced out beautifully for diacritics */
}

.greet-title {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  font-weight: 500;
  color: #6c7293 !important;
}

.user-fullname {
  font-size: 14px;
  font-weight: 600;
  overflow: hidden;
  text-overflow: ellipsis;
}

.premium-rank-badge {
  font-size: 9px;
  font-weight: 700;
  background: rgba(241, 196, 15, 0.1);
  color: #f1c40f !important;
  border: 1px solid rgba(241, 196, 15, 0.25);
  padding: 3px 6px;
  border-radius: 6px;
  width: fit-content;
  display: flex;
  align-items: center;
}

/* Sidebar Body & Navigation */
.sidebar-body {
  padding: 10px 0;
}

.nav-heading {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.2px;
  color: #4b5275 !important;
  margin-top: 20px;
}

.nav-item {
  width: 100%;
}

.nav-link {
  color: #a5b4fc !important;
  background: transparent;
  border-radius: 12px;
  font-weight: 500;
  font-size: 14px;
  padding: 11px 16px !important;
  display: flex;
  align-items: center;
  gap: 15px;
  transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
  border: 1px solid transparent;
  margin-bottom: 2px;
}

.nav-link i {
  font-size: 20px;
  transition: transform 0.25s ease;
  flex-shrink: 0;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.03);
  color: #ffffff !important;
  border-color: rgba(255, 255, 255, 0.05);
  transform: translateX(4px);
}

.nav-link:hover i {
  transform: scale(1.15);
  color: #00f2fe;
}

/* Router Link Active State (Cyan-Blue Cyber Glow) */
.nav-link.active,
.nav-link.router-link-active {
  color: #ffffff !important;
  background: linear-gradient(135deg, rgba(0, 242, 254, 0.15) 0%, rgba(79, 172, 254, 0.03) 100%);
  border: 1px solid rgba(0, 242, 254, 0.25);
  font-weight: 600;
  box-shadow: 0 4px 20px rgba(0, 242, 254, 0.08);
  position: relative;
}

/* 3D Glowing Active Line Indicator */
.nav-link.active::before,
.nav-link.router-link-active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 20%;
  height: 60%;
  width: 3px;
  background: linear-gradient(to bottom, #00f2fe, #4facfe);
  border-radius: 0 4px 4px 0;
  box-shadow: 0 0 10px rgba(0, 242, 254, 0.8);
}

.nav-link.active i,
.nav-link.router-link-active i {
  color: #00f2fe;
  filter: drop-shadow(0 0 5px rgba(0, 242, 254, 0.4));
}

.admin-sidebar.sidebar-collapsed-state .nav-link {
  justify-content: center;
  padding: 12px !important;
  width: 50px;
  margin: 0 auto 5px auto;
}

.admin-sidebar.sidebar-collapsed-state .nav-link i {
  font-size: 22px;
}

/* Sidebar Footer (Premium Buttons) */
.sidebar-footer {
  border-top: 1px solid rgba(255, 255, 255, 0.03);
}

.btn-home-redirect {
  background: rgba(255, 255, 255, 0.03);
  color: #a5b4fc !important;
  border: 1px solid rgba(255, 255, 255, 0.06);
  font-size: 13.5px;
  font-weight: 600;
  transition: all 0.25s ease;
}

.btn-home-redirect:hover {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff !important;
  border-color: rgba(255, 255, 255, 0.15);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.btn-logout-action {
  background: rgba(231, 76, 60, 0.1);
  color: #e74c3c !important;
  border: 1px solid rgba(231, 76, 60, 0.2);
  font-size: 13.5px;
  font-weight: 600;
  transition: all 0.25s ease;
}

.btn-logout-action:hover {
  background: #e74c3c;
  color: #ffffff !important;
  border-color: transparent;
  box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
}

.admin-sidebar.sidebar-collapsed-state .sidebar-footer {
  padding: 15px 10px;
}

.admin-sidebar.sidebar-collapsed-state .btn-home-redirect,
.admin-sidebar.sidebar-collapsed-state .btn-logout-action {
  padding: 10px !important;
  width: 44px;
  height: 44px;
  border-radius: 10px;
  margin: 0 auto;
}

/* Custom Scrollbar for Sidebar Body */
.sidebar-body::-webkit-scrollbar {
  width: 4px;
}
.sidebar-body::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-body::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
.sidebar-body::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.2);
}
</style>
