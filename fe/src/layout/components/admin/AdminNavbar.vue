<template>
  <nav class="admin-sidebar shadow" :class="{ 'sidebar-collapsed-state': isCollapsed }">
    <div class="sidebar-bg-layer" aria-hidden="true"></div>

    <div class="sidebar-header p-3 d-flex align-items-center gap-3 position-relative">
      <div class="brand-icon d-flex align-items-center justify-content-center rounded-3 flex-shrink-0">
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
              <stop offset="0%" stop-color="#10b981"/>
              <stop offset="100%" stop-color="#059669"/>
            </linearGradient>
            <linearGradient id="g2" x1="0" y1="15" x2="40" y2="35">
              <stop offset="0%" stop-color="#34d399"/>
              <stop offset="100%" stop-color="#047857"/>
            </linearGradient>
            <linearGradient id="g3" x1="0" y1="30" x2="40" y2="40">
              <stop offset="0%" stop-color="#6ee7b7"/>
              <stop offset="100%" stop-color="#065f46"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      <div class="hide-on-collapse logo-wrap lh-1">
        <span class="logo-text fw-bold">Gia Phả</span>
        <span class="logo-sub">Admin</span>
      </div>
    </div>

    <button class="toggle-btn" @click.stop="$emit('toggle-sidebar')" :title="isCollapsed ? 'Mở rộng' : 'Thu gọn'">
      <i class='bx' :class="isCollapsed ? 'bx-chevron-right' : 'bx-chevron-left'"></i>
    </button>

    <div class="user-greeting d-flex align-items-center gap-3">
      <div class="avatar flex-shrink-0">
        <span>{{ userName.charAt(0).toUpperCase() }}</span>
        <div class="avatar-ring"></div>
      </div>
      <div class="d-flex flex-column hide-on-collapse lh-1 text-nowrap">
        <span class="greeting-label">Xin chào,</span>
        <strong class="greeting-name">{{ userName }}</strong>
      </div>
    </div>

    <div class="sidebar-body overflow-auto flex-grow-1">
      <ul class="nav flex-column gap-1">
        <li class="nav-item">
          <router-link to="/admin/dashboard" class="nav-link" active-class="active" title="Tổng Quan">
            <span class="nav-icon"><i class="bx bx-home-circle"></i></span>
            <span class="hide-on-collapse nav-label">Tổng Quan</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/gia-pha" class="nav-link" active-class="active" title="Gia Phả">
            <span class="nav-icon"><i class="bx bx-git-branch"></i></span>
            <span class="hide-on-collapse nav-label">Gia Phả</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/tra-cuu" class="nav-link" active-class="active" title="Tra Cứu">
            <span class="nav-icon"><i class="bx bx-search-alt"></i></span>
            <span class="hide-on-collapse nav-label">Tra Cứu</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="section-heading hide-on-collapse">Quản Lý Dòng Họ</li>
        <li class="nav-item">
          <router-link to="/admin/chi-nhanh" class="nav-link" active-class="active" title="Chi Nhánh">
            <span class="nav-icon"><i class="bx bx-sitemap"></i></span>
            <span class="hide-on-collapse nav-label">Chi Nhánh</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/doi-toc-ho" class="nav-link" active-class="active" title="Đời Tộc Họ">
            <span class="nav-icon"><i class="bx bx-list-ul"></i></span>
            <span class="hide-on-collapse nav-label">Đời Tộc Họ</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/nha-tho-ho" class="nav-link" active-class="active" title="Nhà Thờ Họ">
            <span class="nav-icon"><i class="bx bx-building-house"></i></span>
            <span class="hide-on-collapse nav-label">Nhà Thờ Họ</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/mo-phan" class="nav-link" active-class="active" title="Mộ Phần">
            <span class="nav-icon"><i class="bx bx-map-pin"></i></span>
            <span class="hide-on-collapse nav-label">Mộ Phần</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="section-heading hide-on-collapse">Sự Kiện & Quỹ</li>
        <li class="nav-item">
          <router-link to="/admin/su-kien" class="nav-link" active-class="active" title="Sự Kiện">
            <span class="nav-icon"><i class="bx bx-calendar-event"></i></span>
            <span class="hide-on-collapse nav-label">Sự Kiện</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/tham-gia-su-kien" class="nav-link" active-class="active" title="Tham Gia">
            <span class="nav-icon"><i class="bx bx-user-check"></i></span>
            <span class="hide-on-collapse nav-label">Tham Gia</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/dong-gop" class="nav-link" active-class="active" title="Đóng Góp">
            <span class="nav-icon"><i class="bx bx-wallet"></i></span>
            <span class="hide-on-collapse nav-label">Đóng Góp</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="section-heading hide-on-collapse">Hệ Thống</li>
        <li class="nav-item">
          <router-link to="/admin/nguoi-dung" class="nav-link" active-class="active" title="Người Dùng">
            <span class="nav-icon"><i class="bx bx-user"></i></span>
            <span class="hide-on-collapse nav-label">Người Dùng</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/chuc-vu" class="nav-link" active-class="active" title="Chức Vụ">
            <span class="nav-icon"><i class="bx bx-shield"></i></span>
            <span class="hide-on-collapse nav-label">Chức Vụ</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/chuc-nang" class="nav-link" active-class="active" title="Chức Năng">
            <span class="nav-icon"><i class="bx bx-slider"></i></span>
            <span class="hide-on-collapse nav-label">Chức Năng</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/admin/phan-quyen" class="nav-link" active-class="active" title="Phân Quyền">
            <span class="nav-icon"><i class="bx bx-shield-alt"></i></span>
            <span class="hide-on-collapse nav-label">Phân Quyền</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
      </ul>
    </div>

    <div class="sidebar-footer d-flex flex-column">
      <button class="btn-logout d-flex align-items-center justify-content-between" @click="logout" title="Đăng Xuất">
        <div class="d-flex align-items-center gap-3">
          <span class="nav-icon"><i class="bx bx-log-out-circle"></i></span>
          <span class="hide-on-collapse text-nowrap font-bold">Đăng Xuất</span>
        </div>
        <span class="logout-arrow hide-on-collapse"><i class="bx bx-up-arrow-alt"></i></span>
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
      userName: 'Chánh Admin'
    }
  },
  mounted() {
    const userStr = localStorage.getItem('user');
    if (userStr) {
      try {
        const user = JSON.parse(userStr);
        if (user && user.ho_ten) {
          this.userName = user.ho_ten;
        }
      } catch (e) {
        console.error("Lỗi parse thông tin user trong Sidebar:", e);
      }
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
<<<<<<< HEAD
      this.$router.push('/admin/login');
=======
      this.$router.push('/');
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
    }
  }
}
</script>

<style scoped>
/* ─── SYSTEM LIGHT CORE VARIABLES ─── */
.admin-sidebar {
  --admin-bg:        #ffffff;
  --admin-surface:   #f8f9fa;
  --admin-elevated:  #ffffff;
  --admin-border:    rgba(0, 0, 0, 0.06);
  --admin-emerald:   #059669; /* Emerald lục bảo sáng quý tộc */

  --text-main:       #1f2937;
  --text-sub:        #6b7280;
  --transition-smooth: 0.4s cubic-bezier(0.25, 1, 0.5, 1);
}

/* ─── CONTAINER KÉN CHỮ NHẬT TRẮNG SÁNG NGỌC TRAI ─── */
.admin-sidebar {
  position: sticky;
  top: 15px;
  left: 15px;
  height: calc(100vh - 30px);
  width: 260px !important;
  min-width: 260px !important;
  background: var(--admin-bg) !important;
  display: flex;
  flex-direction: column;
  z-index: 1040;
  margin: 15px 0 15px 15px;
  border-radius: 24px;
  border: 1px solid var(--admin-border);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
  transition: width var(--transition-smooth), min-width var(--transition-smooth);
}

.admin-sidebar.sidebar-collapsed-state {
  width: 82px !important;
  min-width: 82px !important;
}

/* ─── DECORATION BG LAYER ─── */
.sidebar-bg-layer {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
  border-radius: 24px;
  z-index: 0;
}
.sidebar-bg-layer::before {
  content: '';
  position: absolute;
  top: -100px;
  left: -100px;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(5, 150, 105, 0.03) 0%, transparent 70%);
}

.admin-sidebar > *:not(.sidebar-bg-layer) {
  position: relative;
  z-index: 1;
}

.admin-sidebar.sidebar-collapsed-state .hide-on-collapse {
  display: none !important;
}

/* ─── HEADER LOGO TỘC HỆ ─── */
.sidebar-header {
  padding: 24px 20px 16px !important;
  border-bottom: 1px solid rgba(0,0,0,0.03) !important;
}
.admin-sidebar.sidebar-collapsed-state .sidebar-header {
  padding: 24px 10px 16px !important;
  justify-content: center !important;
}

.brand-icon {
  background: #f3f4f6;
  border: 1px solid rgba(0,0,0,0.05);
  padding: 6px;
  width: 40px;
  height: 40px;
}
.logo-wrap {
  display: flex;
  align-items: baseline;
  gap: 5px;
}
.logo-text {
  font-size: 19px;
  color: #111827;
  font-family: 'Playfair Display', serif;
}
.logo-sub {
  font-size: 11px;
  font-weight: 700;
  color: var(--admin-emerald);
  letter-spacing: 1px;
  text-transform: uppercase;
}

/* ─── NÚT TOGGLE THU GỌN - ĐẨY RA RÌA VÁCH NGĂN SÁNG TRẮNG ─── */
.toggle-btn {
  position: absolute;
  left: 100%; 
  top: 32px;
  transform: translateX(-50%); 
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #ffffff;
  border: 1px solid var(--admin-border);
  color: var(--text-sub);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1055;
  padding: 0;
  font-size: 16px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
}
.toggle-btn:hover {
  background: var(--admin-emerald);
  border-color: transparent;
  color: #ffffff !important;
  transform: translateX(-50%) scale(1.12); 
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}
.toggle-btn:active {
  transform: translateX(-50%) scale(0.95);
}

/* ─── USER RECTANGLE BLOCK SÁNG MƯỢT ─── */
.user-greeting {
  padding: 12px 16px;
  margin: 14px 16px;
  background: #fdfdfd;
  border: 1px solid var(--admin-border);
  border-radius: 16px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.01);
}
.admin-sidebar.sidebar-collapsed-state .user-greeting {
  justify-content: center !important;
  margin: 12px 8px !important;
  padding: 12px 6px !important;
}

.avatar {
  position: relative;
  width: 36px;
  height: 36px;
}
.avatar span {
  position: relative;
  z-index: 2;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #f3f4f6;
  border: 1px solid var(--admin-border);
  color: #111827;
  font-weight: 700;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.avatar-ring {
  position: absolute;
  inset: -2px;
  border-radius: 50%;
  border: 1px solid transparent;
  background: linear-gradient(135deg, var(--admin-emerald), #3b82f6) border-box;
  -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: destination-out;
  mask-composite: exclude;
}

.greeting-label { font-size: 11px; color: var(--text-sub); }
.greeting-name { font-size: 13.5px; color: #111827; }

/* ─── SIDEBAR NAVIGATION MENU ─── */
.sidebar-body {
  padding: 10px 16px;
}
.sidebar-body::-webkit-scrollbar { width: 3px; }
.sidebar-body::-webkit-scrollbar-track { background: transparent; }
.sidebar-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 99px; }

.section-heading {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #a3a3a3;
  padding: 20px 12px 6px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 10px 14px;
  border-radius: 14px;
  color: var(--text-sub) !important;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.25s ease;
  text-decoration: none;
  border: 1px solid transparent;
}
.admin-sidebar.sidebar-collapsed-state .nav-link {
  justify-content: center;
  padding: 12px 0;
}

.nav-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--admin-emerald);
  opacity: 0;
  transform: scale(0.5);
  transition: all 0.2s ease;
  margin-left: auto;
}
.nav-link:hover .nav-dot {
  opacity: 0.5;
  transform: scale(1);
}

.nav-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  font-size: 18px;
  color: #9ca3af;
  transition: color 0.2s ease;
}

.nav-link:hover {
  color: #111827 !important;
  background: #f9fafb;
}
.nav-link:hover .nav-icon {
  color: #4b5563;
}

/* TRẠNG THÁI ACTIVE HẠT KÉN TRẮNG NỔI KHỐI MỊN EMERALD */
.nav-link.active {
  color: #111827 !important;
  background: #ffffff !important;
  border: 1px solid rgba(0, 0, 0, 0.04) !important;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.01);
}
.nav-link.active .nav-icon i {
  color: var(--admin-emerald) !important;
}
.nav-link.active .nav-dot {
  opacity: 1 !important;
  transform: scale(1.2);
}

/* ─── SIDEBAR FOOTER - VIÊN THUỐC ĐĂNG XUẤT ─── */
.sidebar-footer {
  padding: 16px;
  border-top: 1px solid rgba(0, 0, 0, 0.03);
  margin-top: auto;
}

.btn-logout {
  width: 100%;
  background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
  border: none;
  color: #ffffff;
  border-radius: 30px; 
  padding: 11px 18px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
  transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
}
.btn-logout .nav-icon i { color: #ffffff !important; }

.logout-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  font-size: 13px;
  transform: rotate(45deg); 
  transition: transform 0.3s ease;
}

.btn-logout:hover {
  transform: translateY(-1.5px);
  box-shadow: 0 8px 20px rgba(5, 150, 105, 0.35);
  filter: brightness(1.02);
}
.btn-logout:hover .logout-arrow {
  transform: rotate(45deg) translateX(1px) translateY(-1px);
}

/* Thu gọn chân trang */
.admin-sidebar.sidebar-collapsed-state .btn-logout {
  padding: 12px 0;
  justify-content: center;
  border-radius: 14px;
  background: #ffffff;
  border: 1px solid rgba(5, 150, 105, 0.2);
}
.admin-sidebar.sidebar-collapsed-state .btn-logout .nav-icon i {
  color: var(--admin-emerald) !important;
}
</style>