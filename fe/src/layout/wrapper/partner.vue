<template>
  <div class="partner-master-layout">
    
    <!-- Sidebar Component (Left Sidebar Panel) -->
    <PartnerNavbar 
      :is-collapsed="isCollapsed" 
      @toggle-sidebar="isCollapsed = !isCollapsed"
      :class="{ 'mobile-sidebar-show': isMobileOpen }"
    />

    <!-- Mobile Drawer Backdrop Overlay -->
    <div 
      class="sidebar-mobile-backdrop" 
      v-if="isMobileOpen" 
      @click="isMobileOpen = false"
    ></div>

    <!-- Main Workspace Block (Right Content Panel) -->
    <div class="main-content-wrapper" :class="{ 'collapsed-sidebar-active': isCollapsed }">
      
      <!-- Top Workspace Glassmorphic Header -->
      <header class="admin-header shadow-sm">
        <div class="container-fluid h-100 d-flex align-items-center justify-content-between px-3 px-md-4">
          
          <!-- Page Info / Breadcrumbs (Left) -->
          <div class="d-flex align-items-center gap-3">
            <button class="mobile-toggle-btn d-lg-none" @click="isMobileOpen = !isMobileOpen" title="Mở menu">
              <i class='bx bx-menu fs-3'></i>
            </button>
            <div class="header-title-container hide-on-mobile">
              <h5 class="mb-0 fw-bold header-main-title">Hệ Thống Đối Tác</h5>
              <span class="text-muted font-11">Bảng quản lý cây gia phả & thành viên</span>
            </div>
          </div>

          <!-- Quick Actions & User Dropdowns (Right) -->
          <div class="d-flex align-items-center gap-3">
            
            <!-- Glow Search Box -->
            <div class="header-search d-none d-md-flex align-items-center">
              <i class='bx bx-search search-icon'></i>
              <input type="text" class="form-control border-0 bg-transparent search-input" placeholder="Tìm kiếm gia hệ, thành viên...">
            </div>

            <div class="divider-line d-none d-md-block"></div>

            <!-- Intelligent Notification Center -->
            <div class="dropdown dropdown-notifications">
              <button class="nav-icon-btn position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="Thông báo hoạt động">
                <i class='bx bx-bell bell-icon-glowing'></i>
                <span class="notification-badge-dot"></span>
              </button>
              <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu shadow-lg border-0 py-0 animated fadeIn">
                <div class="notification-header d-flex align-items-center justify-content-between p-3 border-bottom border-secondary border-opacity-10">
                  <h6 class="mb-0 fw-bold text-dark">Thông Báo Hoạt Động</h6>
                  <span class="badge notification-count-badge">3 Mới</span>
                </div>
                <div class="notification-body overflow-auto">
                  <a href="javascript:;" class="notification-item d-flex align-items-center gap-3 p-3 border-bottom border-secondary border-opacity-5 text-decoration-none">
                    <div class="notify-icon bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center">
                      <i class='bx bx-user-plus fs-5'></i>
                    </div>
                    <div class="notify-info flex-grow-1">
                      <p class="notify-title text-dark mb-1">Thành viên mới cập nhật</p>
                      <p class="notify-desc text-muted mb-0 font-12">Họ Trần vừa thêm cụ Nguyễn Thị A vào đời thứ 4.</p>
                      <span class="notify-time text-muted font-11"><i class='bx bx-time-five me-1'></i>5 phút trước</span>
                    </div>
                  </a>
                  <a href="javascript:;" class="notification-item d-flex align-items-center gap-3 p-3 border-bottom border-secondary border-opacity-5 text-decoration-none">
                    <div class="notify-icon bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center">
                      <i class='bx bx-calendar-event fs-5'></i>
                    </div>
                    <div class="notify-info flex-grow-1">
                      <p class="notify-title text-dark mb-1">Sắp tới ngày Giỗ Tổ</p>
                      <p class="notify-desc text-muted mb-0 font-12">Còn 3 ngày nữa là đến lễ cúng tế tổ tiên dòng họ.</p>
                      <span class="notify-time text-muted font-11"><i class='bx bx-time-five me-1'></i>2 giờ trước</span>
                    </div>
                  </a>
                  <a href="javascript:;" class="notification-item d-flex align-items-center gap-3 p-3 border-bottom border-secondary border-opacity-5 text-decoration-none">
                    <div class="notify-icon bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center">
                      <i class='bx bx-git-branch fs-5'></i>
                    </div>
                    <div class="notify-info flex-grow-1">
                      <p class="notify-title text-dark mb-1">Yêu cầu đồng bộ cây gia phả</p>
                      <p class="notify-desc text-muted mb-0 font-12">Chi nhánh Hải Phòng gửi yêu cầu cập nhật thông tin nhánh.</p>
                      <span class="notify-time text-muted font-11"><i class='bx bx-time-five me-1'></i>Hôm qua</span>
                    </div>
                  </a>
                </div>
                <div class="notification-footer p-2 text-center border-top border-secondary border-opacity-10">
                  <a href="javascript:;" class="text-primary-gradient fw-bold font-13 text-decoration-none">Đánh dấu tất cả đã đọc</a>
                </div>
              </div>
            </div>

            <!-- Profile Dropdown (Upper Right) -->
            <div class="dropdown">
              <button class="profile-trigger d-flex align-items-center gap-2 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm">
                  {{ userName.charAt(0).toUpperCase() }}
                </div>
                <div class="d-none d-sm-flex flex-column text-start profile-meta-info">
                  <span class="profile-username text-white fw-bold font-12">{{ userName }}</span>
                  <span class="profile-role text-white-50 font-10">Đối Tác Vàng</span>
                </div>
                <i class='bx bx-chevron-down text-white-50 font-11'></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end profile-dropdown-menu shadow-lg border-0 py-2 animated fadeIn">
                <li class="dropdown-header px-3 py-2 border-bottom mb-1">
                  <div class="fw-bold text-dark">{{ userName }}</div>
                  <div class="text-muted font-11">Thành viên Đối Tác</div>
                </li>
                <li>
                  <router-link class="dropdown-item py-2" to="/profile">
                    <i class="bx bx-user-circle me-2 font-16 text-primary"></i><span>Hồ sơ cá nhân</span>
                  </router-link>
                </li>
                <li>
                  <a class="dropdown-item py-2" href="javascript:;" @click="goHome">
                    <i class="bx bx-home-alt me-2 font-16 text-success"></i><span>Về Trang Chủ</span>
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item py-2 text-danger" href="javascript:;" @click="logout">
                    <i class='bx bx-power-off me-2 font-16'></i><span>Đăng xuất</span>
                  </a>
                </li>
              </ul>
            </div>

          </div>
        </div>
      </header>

      <!-- Main Nested Route Container -->
      <div class="admin-body-container">
        <router-view></router-view>
      </div>

    </div>
  </div>
</template>

<script>
import PartnerNavbar from '../components/doitac/PartnerNavbar.vue';

export default {
  name: 'PartnerLayout',
  components: {
    PartnerNavbar
  },
  data() {
    return {
      isCollapsed: false,
      isMobileOpen: false,
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

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap');
@import url('https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css');
@import "../../assets/css/bootstrap.min.css";
@import "../../assets/css/bootstrap-extended.css";

*,
*::before,
*::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Master Layout Framework */
body {
  font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
  background-color: #f6f8fb;
  color: #2b3040;
  -webkit-font-smoothing: antialiased;
}

.partner-master-layout {
  min-height: 100vh;
  display: flex;
  position: relative;
}

/* Main content wrapper stretching and margins */
.main-content-wrapper {
  flex: 1;
  margin-left: 280px;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background-color: #f7fafc;
  background-image: radial-gradient(#cbd5e1 0.7px, transparent 0.7px);
  background-size: 16px 16px;
  transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.main-content-wrapper.collapsed-sidebar-active {
  margin-left: 80px;
}

/* Glassmorphism Header Bar */
.admin-header {
  position: sticky;
  top: 0;
  height: 76px;
  background: rgba(255, 255, 255, 0.8) !important;
  backdrop-filter: blur(16px) !important;
  -webkit-backdrop-filter: blur(16px) !important;
  border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
  z-index: 1000;
}

.header-main-title {
  color: #0f172a;
  letter-spacing: -0.2px;
}

.mobile-toggle-btn {
  background: none;
  border: none;
  color: #334155;
  cursor: pointer;
  padding: 4px;
}

/* Glow Search Input box */
.header-search {
  background: #f1f5f9;
  border: 1px solid rgba(226, 232, 240, 0.8);
  border-radius: 12px;
  padding: 0 16px;
  width: 280px;
  height: 40px;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.header-search:focus-within {
  background: #ffffff;
  border-color: rgba(0, 242, 254, 0.5);
  box-shadow: 0 0 12px rgba(0, 242, 254, 0.15);
  width: 320px;
}

.search-input {
  font-size: 13.5px;
  font-weight: 500;
  color: #1e293b;
}

.search-input::placeholder {
  color: #94a3b8;
}

.search-icon {
  color: #94a3b8;
  transition: color 0.25s ease;
  margin-right: 8px;
}

.header-search:focus-within .search-icon {
  color: #00f2fe;
}

.divider-line {
  width: 1px;
  height: 24px;
  background-color: #e2e8f0;
}

/* Nav icons buttons */
.nav-icon-btn {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  border: 1px solid rgba(226, 232, 240, 0.8);
  background: #ffffff;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
}

.nav-icon-btn:hover {
  background: #f8fafc;
  color: #0f172a;
  transform: translateY(-1.5px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
}

.bell-icon-glowing {
  font-size: 20px;
}

.notification-badge-dot {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 9px;
  height: 9px;
  background-color: #ef4444;
  border: 2px solid #ffffff;
  border-radius: 50%;
  animation: pulse-bell 2s infinite;
}

@keyframes pulse-bell {
  0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6); }
  70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
  100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

/* Profile Dropdown Header block */
.profile-trigger {
  padding: 4px 6px;
  border-radius: 12px;
  transition: all 0.25s ease;
  background: #0f1123 !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.profile-trigger:hover {
  background: #151833 !important;
}

.avatar-sm {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
  color: #0b0c15 !important;
  border: 1.5px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 2px 8px rgba(0, 242, 254, 0.25);
  font-weight: 700;
  font-size: 13.5px;
}

.profile-meta-info {
  line-height: 1.35 !important;
}

.profile-username {
  font-size: 12.5px !important;
  font-weight: 700 !important;
  color: #ffffff !important;
  letter-spacing: -0.1px;
}

.profile-role {
  font-size: 9.5px !important;
  font-weight: 500 !important;
  color: rgba(255, 255, 255, 0.55) !important;
}

/* Intelligent Popups */
.animated {
  animation-duration: 0.25s;
  animation-fill-mode: both;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.fadeIn {
  animation-name: fadeIn;
}

.notification-dropdown-menu {
  width: 340px;
  max-width: 90vw;
  border-radius: 14px !important;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
  background: #ffffff;
  margin-top: 10px !important;
}

.notification-count-badge {
  background: rgba(0, 242, 254, 0.15) !important;
  color: #0d8299 !important;
  font-size: 11px !important;
  font-weight: 700 !important;
  padding: 4px 8px !important;
}

.notification-body {
  max-height: 280px;
}

.notification-item {
  transition: background-color 0.2s ease;
}

.notification-item:hover {
  background-color: #f8fafc;
}

.notify-icon {
  width: 38px;
  height: 38px;
  flex-shrink: 0;
}

.notify-title {
  font-size: 13px;
  font-weight: 600;
}

.text-primary-gradient {
  background: linear-gradient(135deg, #00f2fe, #4facfe);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  display: inline-block;
}

.profile-dropdown-menu {
  border-radius: 14px !important;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
  width: 180px;
  margin-top: 10px !important;
}

.profile-dropdown-menu .dropdown-item {
  font-size: 13px;
  font-weight: 500;
  color: #475569;
  display: flex;
  align-items: center;
  transition: all 0.2s ease;
}

.profile-dropdown-menu .dropdown-item:hover {
  background-color: #f1f5f9;
  color: #0f172a;
}

.profile-dropdown-menu .dropdown-item.text-danger:hover {
  background-color: #fef2f2;
  color: #dc2626;
}

/* Nest Child View padding */
.admin-body-container {
  padding: 24px;
  flex: 1;
}

/* Sidebar Drawer Overlay for Mobile viewports */
.sidebar-mobile-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(15, 17, 36, 0.6);
  backdrop-filter: blur(4px);
  z-index: 1030;
  animation: backdrop-fade 0.3s ease;
}

@keyframes backdrop-fade {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* ==============================================================
   Highly Polished Cards, Tables & Responsive Grids (Vanilla CSS)
   ============================================================== */
.card {
  border: 1px solid rgba(226, 232, 240, 0.7) !important;
  border-radius: 20px !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.015) !important;
  background: #ffffff !important;
  overflow: hidden;
  transition: all 0.3s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
}

.card:hover {
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
  transform: translateY(-2px);
  border-color: rgba(203, 213, 225, 0.8) !important;
}

.card-header {
  background: transparent !important;
  border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
  padding: 20px 24px !important;
}

.card-body {
  padding: 24px !important;
}

.radius-10 {
  border-radius: 20px !important;
}

.table-responsive {
  border-radius: 12px;
  border: 1px solid rgba(226, 232, 240, 0.6);
  background: #ffffff;
}

.table {
  font-size: 14px !important;
  margin-bottom: 0 !important;
}

.table thead th {
  font-weight: 700 !important;
  font-size: 11px !important;
  text-transform: uppercase !important;
  letter-spacing: 0.8px !important;
  color: #64748b !important;
  background-color: #f8fafc !important;
  padding: 14px 18px !important;
  border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
}

.table tbody td {
  padding: 16px 18px !important;
  border-bottom: 1px solid rgba(241, 245, 249, 0.8) !important;
  color: #334155;
}

.table tbody tr:last-child td {
  border-bottom: none !important;
}

.table tbody tr {
  transition: background-color 0.15s ease;
}

.table tbody tr:hover {
  background-color: rgba(0, 242, 254, 0.015);
}

/* Premium Badges & Gradients */
.badge {
  font-weight: 600 !important;
  border-radius: 6px !important;
}

.bg-gradient-quepal {
  background: linear-gradient(135deg, #2ecc71, #1abc9c) !important;
  box-shadow: 0 4px 10px rgba(46, 204, 113, 0.2);
}
.bg-gradient-bloody {
  background: linear-gradient(135deg, #ff416c, #ff4b2b) !important;
  box-shadow: 0 4px 10px rgba(255, 65, 108, 0.2);
}
.bg-gradient-ohhappiness {
  background: linear-gradient(135deg, #00b09b, #96c93d) !important;
  box-shadow: 0 4px 10px rgba(0, 176, 155, 0.2);
}
.bg-gradient-blooker {
  background: linear-gradient(135deg, #f39c12, #e67e22) !important;
  box-shadow: 0 4px 10px rgba(243, 156, 18, 0.2);
}
.bg-gradient-scooter {
  background: linear-gradient(135deg, #00f2fe, #4facfe) !important;
  box-shadow: 0 4px 12px rgba(0, 242, 254, 0.25);
}

.widgets-icons-2 {
  width: 48px;
  height: 48px;
  border-radius: 14px !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

/* Custom Scrollbars */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* ==============================================
   Mobile & Responsive Adaptations (Media Queries)
   ============================================== */
@media (max-width: 991.98px) {
  .main-content-wrapper,
  .main-content-wrapper.collapsed-sidebar-active {
    margin-left: 0 !important;
  }
  
  .admin-sidebar {
    transform: translateX(-100%);
    box-shadow: none;
  }
  
  .admin-sidebar.mobile-sidebar-show {
    transform: translateX(0);
    box-shadow: 15px 0 40px rgba(15, 17, 36, 0.5);
  }
  
  .toggle-btn {
    display: none !important;
  }
  
  .admin-body-container {
    padding: 16px;
  }
}

@media (max-width: 575.98px) {
  .hide-on-mobile {
    display: none !important;
  }
  
  .admin-header {
    height: 64px;
  }
  
  .avatar-sm {
    width: 28px;
    height: 28px;
    font-size: 12px;
  }
}
</style>
