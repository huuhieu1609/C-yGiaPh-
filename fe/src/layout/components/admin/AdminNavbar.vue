<template>
  <nav class="admin-sidebar shadow" :class="{ 'sidebar-collapsed-state': isCollapsed }">
    <div class="sidebar-bg-layer" aria-hidden="true"></div>

    <div class="sidebar-header p-3 d-flex align-items-center gap-3 position-relative">
      <div class="brand-icon d-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
        <div class="brand-inner-circle"></div>
      </div>
      <div class="hide-on-collapse logo-wrap">
        <span class="logo-text fw-bold">Gia Phả</span>
        <span class="logo-sub text-primary">Hệ Thống</span>
      </div>
    </div>

    <button class="toggle-btn" @click.stop="$emit('toggle-sidebar')" :title="isCollapsed ? 'Mở rộng' : 'Thu gọn'">
      <i class='bx' :class="isCollapsed ? 'bx-chevron-right' : 'bx-chevron-left'"></i>
    </button>

    <div class="user-greeting d-flex align-items-center gap-3">
      <div class="avatar flex-shrink-0">
        <span>A</span>
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
          <router-link to="/admin/dashboard" class="nav-link" active-class="active" title="Tổng Quan">
            <span class="nav-icon"><i class="bx bx-home-circle"></i></span>
            <span class="hide-on-collapse nav-label">Tổng Quan</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="section-heading hide-on-collapse">Hệ Thống Tổng</li>

        <li class="nav-item item-tree">
          <router-link to="/admin/gia-pha" class="nav-link" active-class="active" title="Quản Lý Gia Phả">
            <span class="nav-icon"><i class="bx bx-git-branch"></i></span>
            <span class="hide-on-collapse nav-label">Quản Lý Gia Phả</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="nav-item item-members">
          <router-link to="/admin/chi-nhanh" class="nav-link" active-class="active" title="Quản Lý Chi Nhánh">
            <span class="nav-icon"><i class="bx bx-buildings"></i></span>
            <span class="hide-on-collapse nav-label">Quản Lý Chi Nhánh</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="nav-item item-clan">
          <router-link to="/admin/doi-toc-ho" class="nav-link" active-class="active" title="Đời Tộc Họ">
            <span class="nav-icon"><i class="bx bx-layer"></i></span>
            <span class="hide-on-collapse nav-label">Đời Tộc Họ</span>
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
      userName: 'Quản Trị Viên'
    }
  },
  mounted() {
    const userStr = localStorage.getItem('user');
    if (userStr) {
      try {
        const user = JSON.parse(userStr);
        this.userName = user.ho_ten || user.username || 'Quản Trị Viên';
      } catch (e) {}
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
/* Đồng bộ biến CSS Premium Light của hệ thống */
.admin-sidebar {
  --neo-bg:         #ffffff;
  --neo-border:     rgba(0, 0, 0, 0.06);
  --color-home:      #4f46e5;
  --color-tree:      #db2777;
  --color-members:   #0d9488;
  --color-clan:      #059669;
  --text-sub:       #6b7280;
  --transition-smooth: 0.4s cubic-bezier(0.25, 1, 0.5, 1);
}

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
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  transition: width var(--transition-smooth), min-width var(--transition-smooth);
}

.admin-sidebar.sidebar-collapsed-state { width: 82px !important; min-width: 82px !important; }
.sidebar-bg-layer { position: absolute; inset: 0; pointer-events: none; overflow: hidden; border-radius: 24px; z-index: 0; }
.sidebar-bg-layer::before { content: ''; position: absolute; top: -100px; left: -100px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(59, 130, 246, 0.03) 0%, transparent 70%); }
.admin-sidebar > *:not(.sidebar-bg-layer) { position: relative; z-index: 1; }
.admin-sidebar.sidebar-collapsed-state .hide-on-collapse { display: none !important; }

/* BRAND LOGO */
.sidebar-header { padding: 24px 20px 16px; border-bottom: 1px solid rgba(0, 0, 0, 0.03); }
.brand-icon { width: 36px; height: 36px; background: linear-gradient(135deg, #3b82f6, #10b981, #f59e0b); border-radius: 50%; padding: 2px; }
.brand-inner-circle { width: 100%; height: 100%; background: var(--neo-bg); border-radius: 50%; }
.logo-wrap { display: flex; align-items: baseline; gap: 4px; }
.logo-text { font-size: 19px; color: #111827; font-family: 'Playfair Display', serif; }
.logo-sub { font-size: 11px; font-weight: 700; letter-spacing: 0.5px; }

/* TOGGLE BUTTON */
.toggle-btn { position: absolute; left: 100%; top: 32px; transform: translateX(-50%); width: 32px; height: 32px; border-radius: 50%; background: #ffffff; border: 1px solid var(--neo-border); color: var(--text-sub); display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 1055; box-shadow: 0 4px 10px rgba(0,0,0,0.06); transition: all 0.3s ease; }
.toggle-btn:hover { background: #3b82f6; border-color: transparent; color: #ffffff !important; transform: translateX(-50%) scale(1.12); }

/* GREETING & LINKS */
.user-greeting { padding: 12px 16px; margin: 12px 16px; background: #fdfdfd; border: 1px solid var(--neo-border); border-radius: 16px; }
.avatar { position: relative; width: 36px; height: 36px; }
.avatar span { position: relative; z-index: 2; width: 36px; height: 36px; border-radius: 50%; background: #f3f4f6; border: 1px solid var(--neo-border); color: #111827; font-weight: 700; display: flex; align-items: center; justify-content: center; }
.avatar-ring { position: absolute; inset: -2px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #10b981) border-box; -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0); -webkit-mask-composite: destination-out; mask-composite: exclude; }
.greeting-label { font-size: 11px; color: var(--text-sub); }
.greeting-name { font-size: 13.5px; color: #111827; }

.sidebar-body { padding: 12px 16px; }
.section-heading { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #a3a3a3; padding: 20px 12px 6px; }
.nav-link { display: flex; align-items: center; gap: 14px; padding: 11px 14px; border-radius: 14px; color: var(--text-sub) !important; font-size: 14px; font-weight: 500; transition: all 0.25s ease; text-decoration: none; }
.nav-dot { width: 5px; height: 5px; border-radius: 50%; opacity: 0; transform: scale(0.5); transition: all 0.2s ease; margin-left: auto; }
.nav-link:hover .nav-dot { opacity: 0.5; transform: scale(1); }
.nav-icon { display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; font-size: 18px; color: #9ca3af; }
.nav-link:hover { color: #111827 !important; background: #f9fafb; }

/* ACTIVE STATE */
.item-home .nav-link.active { --c-active: var(--color-home); }
.item-tree .nav-link.active { --c-active: var(--color-tree); }
.item-members .nav-link.active { --c-active: var(--color-members); }
.item-clan .nav-link.active { --c-active: var(--color-clan); }

.nav-link.active { color: #111827 !important; background: #ffffff !important; border: 1px solid rgba(0, 0, 0, 0.04) !important; font-weight: 600; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); }
.nav-link.active .nav-icon i { color: var(--c-active) !important; }
.nav-link.active .nav-dot { opacity: 1 !important; transform: scale(1.2); }

/* FOOTER */
.sidebar-footer { padding: 16px; border-top: 1px solid rgba(0, 0, 0, 0.03); margin-top: auto; }
.btn-home { width: 100%; background: transparent; border: 1px solid var(--neo-border); color: var(--text-sub); border-radius: 14px; padding: 10px 14px; font-size: 13.5px; cursor: pointer; transition: all 0.2s ease; }
.btn-logout { width: 100%; background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%); border: none; color: #ffffff; border-radius: 30px; padding: 11px 18px; font-size: 14px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2); }
.logout-arrow { display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; font-size: 13px; transform: rotate(45deg); }
</style>