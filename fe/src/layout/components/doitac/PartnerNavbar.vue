<template>
  <nav class="admin-sidebar shadow" :class="{ 'sidebar-collapsed-state': isCollapsed }">
    <div class="sidebar-bg-layer" aria-hidden="true"></div>

    <div class="sidebar-header p-3 d-flex align-items-center gap-3 position-relative">
      <div class="brand-icon d-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
        <div class="brand-inner-circle"></div>
      </div>
      <div class="hide-on-collapse logo-wrap">
        <span class="logo-text fw-bold">Gia Phả</span>
        <span class="logo-sub">Đối Tác</span>
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
      <ul class="nav flex-column gap-2">
        
        <li class="nav-item item-home">
          <router-link to="/doi-tac/dashboard" class="nav-link" active-class="active" title="Tổng Quan">
            <span class="nav-icon"><i class="bx bx-home-circle"></i></span>
            <span class="hide-on-collapse nav-label">Tổng Quan</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="section-heading hide-on-collapse">Quản Lý Gia Phả</li>

        <li class="nav-item item-tree">
          <router-link to="/doi-tac/gia-pha" class="nav-link" active-class="active" title="Cây Gia Phả">
            <span class="nav-icon"><i class="bx bx-git-branch"></i></span>
            <span class="hide-on-collapse nav-label">Cây Gia Phả</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-members">
          <router-link to="/doi-tac/thanh-vien" class="nav-link" active-class="active" title="Thành Viên">
            <span class="nav-icon"><i class="bx bx-group"></i></span>
            <span class="hide-on-collapse nav-label">Thành Viên</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-search">
          <router-link to="/doi-tac/tra-cuu" class="nav-link" active-class="active" title="Tra Cứu">
            <span class="nav-icon"><i class="bx bx-search-alt"></i></span>
            <span class="hide-on-collapse nav-label">Tra Cứu</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-clan">
          <router-link to="/doi-tac/dong-ho" class="nav-link" active-class="active" title="Dòng Họ">
            <span class="nav-icon"><i class="bx bx-folder"></i></span>
            <span class="hide-on-collapse nav-label">Dòng Họ</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        
        <li class="section-heading hide-on-collapse">Tools & Hoạt Động</li>
        
        <li class="nav-item item-proposals">
          <router-link to="/doi-tac/de-xuat" class="nav-link" active-class="active" title="Kiểm Duyệt Đề Xuất">
            <span class="nav-icon"><i class="bx bx-git-pull-request"></i></span>
            <span class="hide-on-collapse nav-label">Kiểm Duyệt Đề Xuất</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-events">
          <router-link to="/doi-tac/su-kien" class="nav-link" active-class="active" title="Quản Lý Sự Kiện">
            <span class="nav-icon"><i class="bx bx-calendar-event"></i></span>
            <span class="hide-on-collapse nav-label">Quản Lý Sự Kiện</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-notifs">
          <router-link to="/doi-tac/thong-bao" class="nav-link" active-class="active" title="Quản Lý Thông Báo">
            <span class="nav-icon"><i class="bx bx-bell"></i></span>
            <span class="hide-on-collapse nav-label">Quản Lý Thông Báo</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-docs">
          <router-link to="/doi-tac/tai-lieu" class="nav-link" active-class="active" title="Thư Viện Tài Liệu">
            <span class="nav-icon"><i class="bx bx-book-open"></i></span>
            <span class="hide-on-collapse nav-label">Thư Viện Tài Liệu</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-logs">
          <router-link to="/doi-tac/nhat-ky" class="nav-link" active-class="active" title="Nhật Ký Thao Tác">
            <span class="nav-icon"><i class="bx bx-history"></i></span>
            <span class="hide-on-collapse nav-label">Nhật Ký Thao Tác</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
      </ul>
    </div>

    <div class="sidebar-footer d-flex flex-column gap-2">
      <button class="btn-home d-flex align-items-center gap-3" @click="goHome" title="Về Trang Chủ">
        <span class="nav-icon"><i class="bx bx-home"></i></span>
        <span class="hide-on-collapse text-nowrap font-medium">Trang Chủ</span>
      </button>
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
    const userStr = localStorage.getItem('user');
    if (userStr) {
      try {
        const user = JSON.parse(userStr);
        if (user && user.ho_ten) {
          this.userName = user.ho_ten;
        } else if (user && user.username) {
          this.userName = user.username;
        }
      } catch (e) {
        console.error("Lỗi giải mã dữ liệu người dùng tại Navbar:", e);
      }
    }
  },
  methods: {
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
/* ─── SYSTEM LIGHT CORE VARIABLES ─── */
.admin-sidebar {
  --neo-bg:         #ffffff;
  --neo-surface:    #f8f9fa;
  --neo-elevated:   #ffffff;
  --neo-border:     rgba(0, 0, 0, 0.06);
  
  /* Bảng màu Pastel Light cao cấp cho các mục danh mục */
  --color-home:      #4f46e5; /* Royal Indigo */
  --color-tree:      #db2777; /* Rose Pink */
  --color-members:   #0d9488; /* Dark Teal */
  --color-search:    #d97706; /* Dark Amber */
  --color-clan:      #059669; /* Emerald */
  --color-proposals: #8b5cf6; /* Modern Violet */
  --color-events:    #f97316; /* Bright Orange */
  --color-notifs:    #ef4444; /* Soft Red */
  --color-docs:      #06b6d4; /* Cyan Doc */
  --color-logs:      #64748b; /* Cool Slate */

  --text-main:      #1f2937;
  --text-sub:       #6b7280;
  --text-muted:     #9ca3af;

  --transition-smooth: 0.4s cubic-bezier(0.25, 1, 0.5, 1);
}

/* ─── CONTAINER LAYOUT SÁNG ─── */
.admin-sidebar {
  position: sticky;
  top: 15px; left: 15px;
  height: calc(100vh - 30px);
  width: 260px !important;
  min-width: 260px !important;
  background: var(--neo-bg) !important;
  display: flex;
  flex-direction: column;
  z-index: 1040;
  margin: 15px 0 15px 15px;
  border-radius: 24px;
  border: 1px solid var(--neo-border);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
  transition: width var(--transition-smooth), min-width var(--transition-smooth);
}

.admin-sidebar.sidebar-collapsed-state {
  width: 82px !important;
  min-width: 82px !important;
}

/* ─── DECORATION LAYER ─── */
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
  top: -100px; left: -100px;
  width: 300px; height: 300px;
  background: radial-gradient(circle, rgba(79, 70, 229, 0.03) 0%, transparent 70%);
}

.admin-sidebar > *:not(.sidebar-bg-layer) {
  position: relative;
  z-index: 1;
}

.admin-sidebar.sidebar-collapsed-state .hide-on-collapse {
  display: none !important;
}

/* ─── HEADER LOGO BRAND ─── */
.sidebar-header {
  padding: 24px 20px 16px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.03);
}
.admin-sidebar.sidebar-collapsed-state .sidebar-header {
  padding: 24px 10px 16px !important;
  justify-content: center !important;
}

.brand-icon {
  width: 36px; height: 36px;
  background: linear-gradient(135deg, #db2777, #f59e0b, #3b82f6);
  border-radius: 50%;
  padding: 2px;
  animation: logo-spin 8s linear infinite;
}
.brand-inner-circle {
  width: 100%; height: 100%;
  background: var(--neo-bg);
  border-radius: 50%;
}
@keyframes logo-spin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

.logo-wrap { display: flex; align-items: baseline; gap: 4px; }
.logo-text { font-size: 19px; color: #111827; font-family: 'Playfair Display', serif; }
.logo-sub { font-size: 11px; font-weight: 700; color: #db2777; letter-spacing: 0.5px; }

/* ─── TOGGLE BUTTON CƠ KHÍ ─── */
.toggle-btn {
  position: absolute;
  left: 100%; top: 32px;
  transform: translateX(-50%); 
  width: 32px; height: 32px;
  border-radius: 50%;
  background: #ffffff;
  border: 1px solid var(--neo-border);
  color: var(--text-sub);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 1055;
  padding: 0;
  font-size: 16px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.06);
  transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
}
.toggle-btn:hover {
  background: #db2777;
  border-color: transparent;
  color: #ffffff !important;
  transform: translateX(-50%) scale(1.12);
  box-shadow: 0 4px 12px rgba(219, 39, 119, 0.3);
}

/* ─── USER GREETING BLOCK ─── */
.user-greeting {
  padding: 12px 16px; margin: 12px 16px;
  background: #fdfdfd;
  border: 1px solid var(--neo-border);
  border-radius: 16px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.01);
}
.admin-sidebar.sidebar-collapsed-state .user-greeting {
  justify-content: center !important;
  margin: 12px 8px !important;
  padding: 12px 6px !important;
}

.avatar { position: relative; width: 36px; height: 36px; }
.avatar span {
  position: relative; z-index: 2;
  width: 36px; height: 36px;
  border-radius: 50%;
  background: #f3f4f6;
  border: 1px solid var(--neo-border);
  color: #111827;
  font-weight: 700; font-size: 14px;
  display: flex; align-items: center; justify-content: center;
}
.avatar-ring {
  position: absolute; inset: -2px;
  border-radius: 50%;
  border: 1px solid transparent;
  background: linear-gradient(135deg, #db2777, #4f46e5) border-box;
  -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
  -webkit-mask-composite: destination-out;
  mask-composite: exclude;
}

.greeting-label { font-size: 11px; color: var(--text-sub); }
.greeting-name { font-size: 13.5px; color: #111827; overflow: hidden; text-overflow: ellipsis; }

/* ─── SIDEBAR NAVIGATION LINKS ─── */
.sidebar-body { padding: 12px 16px; }
.sidebar-body::-webkit-scrollbar { width: 3px; }
.sidebar-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 99px; }

.section-heading {
  font-size: 10px; font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #a3a3a3;
  padding: 20px 12px 6px;
}

.nav-link {
  display: flex; align-items: center;
  gap: 14px; padding: 11px 14px;
  border-radius: 14px;
  color: var(--text-sub) !important;
  font-size: 14px; font-weight: 500;
  transition: all 0.25s ease;
  text-decoration: none;
  border: 1px solid transparent;
}
.admin-sidebar.sidebar-collapsed-state .nav-link { justify-content: center; padding: 12px 0; }

/* Ánh xạ màu sắc điểm phát quang nav-dot */
.item-home .nav-dot { background: var(--color-home); }
.item-tree .nav-dot { background: var(--color-tree); }
.item-members .nav-dot { background: var(--color-members); }
.item-search .nav-dot { background: var(--color-search); }
.item-clan .nav-dot { background: var(--color-clan); }
.item-proposals .nav-dot { background: var(--color-proposals); }
.item-events .nav-dot { background: var(--color-events); }
.item-notifs .nav-dot { background: var(--color-notifs); }
.item-docs .nav-dot { background: var(--color-docs); }
.item-logs .nav-dot { background: var(--color-logs); }

.nav-dot {
  width: 5px; height: 5px;
  border-radius: 50%;
  opacity: 0;
  transform: scale(0.5);
  transition: all 0.2s ease;
  margin-left: auto;
}
.nav-link:hover .nav-dot { opacity: 0.5; transform: scale(1); }
.nav-icon { display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; font-size: 18px; color: #9ca3af; transition: color 0.2s ease; }
.nav-link:hover { color: #111827 !important; background: #f9fafb; }
.nav-link:hover .nav-icon { color: #4b5563; }

/* ACTIVE STATE CHUẨN KÉN TRẮNG NỔI 3D KHỐI */
.item-home .nav-link.active { --c-active: var(--color-home); }
.item-tree .nav-link.active { --c-active: var(--color-tree); }
.item-members .nav-link.active { --c-active: var(--color-members); }
.item-search .nav-link.active { --c-active: var(--color-search); }
.item-clan .nav-link.active { --c-active: var(--color-clan); }
.item-proposals .nav-link.active { --c-active: var(--color-proposals); }
.item-events .nav-link.active { --c-active: var(--color-events); }
.item-notifs .nav-link.active { --c-active: var(--color-notifs); }
.item-docs .nav-link.active { --c-active: var(--color-docs); }
.item-logs .nav-link.active { --c-active: var(--color-logs); }

.nav-link.active {
  color: #111827 !important;
  background: #ffffff !important;
  border: 1px solid rgba(0, 0, 0, 0.04) !important;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.02);
}
.nav-link.active .nav-icon i { color: var(--c-active) !important; }
.nav-link.active .nav-dot { opacity: 1 !important; transform: scale(1.2); }

/* ─── SIDEBAR FOOTER ACTION ─── */
.sidebar-footer { padding: 16px; border-top: 1px solid rgba(0, 0, 0, 0.03); margin-top: auto; }
.btn-home {
  width: 100%; background: transparent; border: 1px solid var(--neo-border); color: var(--text-sub);
  border-radius: 14px; padding: 10px 14px; font-size: 13.5px; cursor: pointer; transition: all 0.2s ease;
}
.btn-home:hover { background: #f9fafb; color: #111827; }

.btn-logout {
  width: 100%; background: linear-gradient(135deg, #db2777 0%, #f97316 50%, #f59e0b 100%);
  border: none; color: #ffffff; border-radius: 30px; 
  padding: 11px 18px; font-size: 14px; font-weight: 600; cursor: pointer;
  box-shadow: 0 4px 12px rgba(219, 39, 119, 0.2);
  transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
}
.btn-logout .nav-icon i { color: #ffffff !important; }
.logout-arrow {
  display: flex; align-items: center; justify-content: center; width: 20px; height: 20px;
  background: rgba(255, 255, 255, 0.2); border-radius: 50%; font-size: 13px; transform: rotate(45deg); transition: transform 0.3s ease;
}
.btn-logout:hover { transform: translateY(-1.5px); box-shadow: 0 8px 20px rgba(219, 39, 119, 0.35); filter: brightness(1.03); }
.btn-logout:hover .logout-arrow { transform: rotate(45deg) translateX(1px) translateY(-1px); }

/* Thu gọn chân trang */
.admin-sidebar.sidebar-collapsed-state .btn-home,
.admin-sidebar.sidebar-collapsed-state .btn-logout { padding: 12px 0; justify-content: center; border-radius: 14px; }
.admin-sidebar.sidebar-collapsed-state .btn-logout { background: #ffffff; border: 1px solid rgba(219, 39, 119, 0.2); }
.admin-sidebar.sidebar-collapsed-state .btn-logout .nav-icon i { color: #db2777 !important; }
</style>