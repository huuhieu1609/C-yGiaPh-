<template>
  <nav class="admin-sidebar shadow-sm" :class="{ 'sidebar-collapsed-state': isCollapsed }">
    <div class="sidebar-bg-layer" aria-hidden="true"></div>

    <div class="sidebar-header p-3 d-flex align-items-center gap-2 position-relative">
      <div class="brand-icon d-flex align-items-center justify-content-center rounded-circle flex-shrink-0">
        <div class="brand-inner-circle"></div>
      </div>
      <div class="hide-on-collapse logo-wrap">
        <span class="logo-text fw-bold">Gia Phả</span>
        <span class="logo-sub">Đối Tác</span>
      </div>
    </div>

    <button class="toggle-btn shadow-sm" @click.stop="$emit('toggle-sidebar')" :title="isCollapsed ? 'Mở rộng' : 'Thu gọn'">
      <i class='bx' :class="isCollapsed ? 'bx-chevron-right' : 'bx-chevron-left'"></i>
    </button>

    <div class="user-greeting d-flex align-items-center gap-3 mb-2" @click="goToProfile" style="cursor: pointer;" title="Xem hồ sơ & đổi mật khẩu">
      <div class="avatar flex-shrink-0">
        <span>{{ userName.charAt(0).toUpperCase() }}</span>
        <div class="avatar-ring"></div>
      </div>
      <div class="d-flex flex-column hide-on-collapse lh-1 text-nowrap">
        <span class="greeting-label">Xin chào,</span>
        <strong class="greeting-name">{{ userName }}</strong>
      </div>
    </div>

    <div class="theme-toggle-container px-3 mb-2">
      <button class="btn-theme-toggle d-flex align-items-center justify-content-between w-100" 
              @click="toggleTheme" 
              :title="isDarkMode ? 'Chuyển sang nền sáng' : 'Chuyển sang nền tối'">
        <div class="d-flex align-items-center gap-3">
          <span class="nav-icon toggle-icon-wrap">
            <i :class="isDarkMode ? 'bx bx-sun text-warning' : 'bx bx-moon text-secondary'"></i>
          </span>
          <span class="hide-on-collapse text-nowrap font-medium toggle-label">
            {{ isDarkMode ? 'Chế độ tối' : 'Chế độ sáng' }}
          </span>
        </div>
        <div class="theme-status-dot hide-on-collapse" :class="{ 'dark-active': isDarkMode }"></div>
      </button>
    </div>

    <div class="sidebar-body overflow-auto flex-grow-1">
      <ul class="nav flex-column gap-1">
        
        <li class="nav-item item-home">
          <router-link to="/doi-tac/dashboard" class="nav-link" active-class="active" title="Tổng Quan">
            <span class="nav-icon"><i class="bx bx-home-circle"></i></span>
            <span class="hide-on-collapse nav-label">Tổng Quan</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="section-heading hide-on-collapse" v-if="hasPermission('Cây Gia Phả') || hasPermission('Tra Cứu Xưng Hô') || hasPermission('Quản Lý Chi Nhánh') || hasPermission('Quản Lý Mộ Phần')">Quản Lý Gia Phả</li>

        <li class="nav-item item-tree" v-if="hasPermission('Cây Gia Phả')">
          <router-link to="/doi-tac/gia-pha" class="nav-link" active-class="active" title="Cây Gia Phả">
            <span class="nav-icon"><i class="bx bx-git-branch"></i></span>
            <span class="hide-on-collapse nav-label">Cây Gia Phả</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-members" v-if="hasPermission('Cây Gia Phả')">
          <router-link to="/doi-tac/thanh-vien" class="nav-link" active-class="active" title="Thành Viên">
            <span class="nav-icon"><i class="bx bx-group"></i></span>
            <span class="hide-on-collapse nav-label">Thành Viên</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-search" v-if="hasPermission('Tra Cứu Xưng Hô')">
          <router-link to="/doi-tac/tra-cuu" class="nav-link" active-class="active" title="Tra Cứu">
            <span class="nav-icon"><i class="bx bx-search-alt"></i></span>
            <span class="hide-on-collapse nav-label">Tra Cứu</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-clan" v-if="hasPermission('Quản Lý Chi Nhánh')">
          <router-link to="/doi-tac/dong-ho" class="nav-link" active-class="active" title="Phân Quyền">
            <span class="nav-icon"><i class="bx bx-shield-quarter"></i></span>
            <span class="hide-on-collapse nav-label">Phân Quyền</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-map" v-if="hasPermission('Quản Lý Mộ Phần')">
          <router-link to="/doi-tac/ban-do" class="nav-link" active-class="active" title="Bản Đồ Số">
            <span class="nav-icon"><i class="bx bx-map-alt"></i></span>
            <span class="hide-on-collapse nav-label">Bản Đồ Số</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        
        <li class="section-heading hide-on-collapse" v-if="hasPermission('Cây Gia Phả') || hasPermission('Quản Lý Sự Kiện') || hasPermission('Quản Lý Thông Báo') || hasPermission('Quản Lý Tài Liệu') || hasPermission('Nhật Ký Thao Tác')">Công Cụ & Hoạt Động</li>
        
        <li class="nav-item item-proposals" v-if="hasPermission('Cây Gia Phả')">
          <router-link to="/doi-tac/de-xuat" class="nav-link" active-class="active" title="Kiểm Duyệt Đề Xuất">
            <span class="nav-icon"><i class="bx bx-git-pull-request"></i></span>
            <span class="hide-on-collapse nav-label">Kiểm Duyệt Đề Xuất</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-events" v-if="hasPermission('Quản Lý Sự Kiện')">
          <router-link to="/doi-tac/su-kien" class="nav-link" active-class="active" title="Quản Lý Sự Kiện">
            <span class="nav-icon"><i class="bx bx-calendar-event"></i></span>
            <span class="hide-on-collapse nav-label">Quản Lý Sự Kiện</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-notifs" v-if="hasPermission('Quản Lý Thông Báo')">
          <router-link to="/doi-tac/thong-bao" class="nav-link" active-class="active" title="Quản Lý Thông Báo">
            <span class="nav-icon"><i class="bx bx-bell"></i></span>
            <span class="hide-on-collapse nav-label">Quản Lý Thông Báo</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-docs" v-if="hasPermission('Quản Lý Tài Liệu')">
          <router-link to="/doi-tac/tai-lieu" class="nav-link" active-class="active" title="Thư Viện Tài Liệu">
            <span class="nav-icon"><i class="bx bx-book-open"></i></span>
            <span class="hide-on-collapse nav-label">Thư Viện Tài Liệu</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>
        <li class="nav-item item-logs" v-if="hasPermission('Nhật Ký Thao Tác')">
          <router-link to="/doi-tac/nhat-ky" class="nav-link" active-class="active" title="Nhật Ký Thao Tác">
            <span class="nav-icon"><i class="bx bx-history"></i></span>
            <span class="hide-on-collapse nav-label">Nhật Ký Thao Tác</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="section-heading hide-on-collapse" v-if="isOwner()">Gói &amp; Thanh Toán</li>

        <li class="nav-item" v-if="isOwner()">
          <router-link to="/doi-tac/quan-ly-goi" class="nav-link" active-class="active" title="Quản Lý Gói">
            <span class="nav-icon"><i class="bx bx-package"></i></span>
            <span class="hide-on-collapse nav-label">Quản Lý Gói</span>
            <span class="nav-dot hide-on-collapse"></span>
          </router-link>
        </li>

        <li class="nav-item item-events" v-for="menu in comingSoonMenus" :key="'cs'+menu.id">
          <router-link :to="'/coming-soon?name=' + encodeURIComponent(menu.ten_chuc_nang)" class="nav-link" active-class="active" :title="menu.ten_chuc_nang">
            <span class="nav-icon"><i class="bx bx-crown text-warning"></i></span>
            <span class="hide-on-collapse nav-label text-gradient-gold-sidebar">{{ menu.ten_chuc_nang }}</span>
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
        <span class="logout-arrow hide-on-collapse"><i class="bx bx-chevron-right"></i></span>
      </button>
    </div>
  </nav>
</template>

<script>
import axios from 'axios';

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
      userName: 'Đối Tác',
      isDarkMode: false,
      permissions: [],
      isMasterAdmin: false,
      comingSoonMenus: []
    }
  },
  mounted() {
    // Đọc trạng thái theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    this.isDarkMode = savedTheme === 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Đọc thông tin user
    const userStr = localStorage.getItem('user');
    if (userStr) {
      try {
        const user = JSON.parse(userStr);
        if (user && user.ho_ten) {
          this.userName = user.ho_ten;
        } else if (user && user.username) {
          this.userName = user.username;
        }
        // Master Admin luôn có toàn quyền
        this.isMasterAdmin = user?.vai_tro?.toLowerCase() === 'admin';
      } catch (e) {
        console.error("Lỗi parse thông tin user trong Sidebar:", e);
      }
    }

    // Đọc permissions từ localStorage (được lưu khi login)
    try {
      const permsStr = localStorage.getItem('permissions');
      this.permissions = permsStr ? JSON.parse(permsStr) : [];
    } catch (e) {
      this.permissions = [];
    }

    // Tải danh sách menu Coming Soon động
    this.loadComingSoonMenus();
  },
  methods: {
    loadComingSoonMenus() {
      axios.get('http://127.0.0.1:8000/api/chuc-nang/coming-soon-menus', {
        headers: { Authorization: 'Bearer ' + localStorage.getItem('access_token') }
      }).then(res => {
        if (res.data && res.data.status) {
          this.comingSoonMenus = res.data.data || [];
        }
      }).catch(() => {});
    },
    /**
     * Kiểm tra user có quyền chức năng không.
     * Master Admin (vai_tro='admin') hoặc Đối Tác không có chức vụ -> hiện tất cả.
     */
    hasPermission(chucNang) {
      if (this.isMasterAdmin) return true;

      // Đọc thông tin user từ localStorage để kiểm tra id_chuc_vu
      const userStr = localStorage.getItem('user');
      let idChucVu = null;
      if (userStr) {
        try {
          const user = JSON.parse(userStr);
          idChucVu = user?.id_chuc_vu;
        } catch (e) {}
      }

      // Nếu không có chức vụ (là Đối Tác chính / Chủ sở hữu dòng họ) -> có toàn quyền
      if (idChucVu === null || idChucVu === undefined) {
        return true;
      }

      // Nếu có chức vụ -> bắt buộc phải có tên quyền trong mảng permissions
      return this.permissions.includes(chucNang);
    },
    isOwner() {
      if (this.isMasterAdmin) return true;
      const userStr = localStorage.getItem('user');
      if (userStr) {
        try {
          const user = JSON.parse(userStr);
          return user?.id_chuc_vu === null || user?.id_chuc_vu === undefined;
        } catch (e) {}
      }
      return false;
    },
    toggleTheme() {
      this.isDarkMode = !this.isDarkMode;
      const themeStr = this.isDarkMode ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', themeStr);
      localStorage.setItem('theme', themeStr);
    },
    logout() {
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
      localStorage.removeItem('permissions');
      this.$router.push('/login');
    },
    goHome() {
      this.$router.push('/');
    },
    goToProfile() {
      this.$router.push('/profile');
    }
  }
}
</script>

<style scoped>
.admin-sidebar {
  --neo-bg:         var(--card-bg, #ffffff);
  --neo-border:     var(--border-color, rgba(0, 0, 0, 0.05));
  
  --color-home:      #f97316;
  --color-tree:      #e11d48;
  --color-members:   #ff7a00;
  --color-search:    #d97706;
  --color-clan:      #ea580c;

  --transition-smooth: 0.35s cubic-bezier(0.25, 1, 0.5, 1);
}

.admin-sidebar {
  position: fixed !important;
  top: 15px; left: 15px;
  height: calc(100vh - 30px);
  width: 250px !important;
  min-width: 250px !important;
  display: flex;
  flex-direction: column;
  z-index: 1040;
  background: var(--neo-bg) !important;
  border: 1px solid var(--neo-border);
  border-radius: 24px !important;
  box-shadow: 0 10px 35px rgba(0, 0, 0, 0.03) !important;
  transition: width var(--transition-smooth), min-width var(--transition-smooth), background-color 0.3s, border-color 0.3s;
}

.admin-sidebar.sidebar-collapsed-state {
  width: 80px !important;
  min-width: 80px !important;
}

.sidebar-bg-layer {
  position: absolute;
  inset: 0; pointer-events: none; overflow: hidden; border-radius: 24px; z-index: 0;
}
.sidebar-bg-layer::before {
  content: ''; position: absolute; top: -100px; left: -100px; width: 300px; height: 300px;
  background: radial-gradient(circle, rgba(249, 115, 22, 0.02) 0%, transparent 70%);
}

.admin-sidebar > *:not(.sidebar-bg-layer) { position: relative; z-index: 1; }
.admin-sidebar.sidebar-collapsed-state .hide-on-collapse { display: none !important; }

/* BRAND LOGO */
.sidebar-header { padding: 24px 20px 16px; border-bottom: 1px solid var(--neo-border); }
.admin-sidebar.sidebar-collapsed-state .sidebar-header { padding: 24px 10px 16px !important; justify-content: center !important; }

.brand-icon {
  width: 36px; height: 36px;
  background: linear-gradient(135deg, #f97316, #db2777, #f59e0b);
  border-radius: 50%; padding: 2px;
}
.brand-inner-circle { width: 100%; height: 100%; background: var(--neo-bg); border-radius: 50%; transition: background-color 0.3s; }
.logo-wrap { display: flex; align-items: baseline; gap: 4px; }
.logo-text { font-size: 19px; color: var(--text-main); font-family: 'Playfair Display', serif; }
.logo-sub { font-size: 11px; font-weight: 700; color: #db2777; letter-spacing: 0.5px; }

/* NÚT TOGGLE */
.toggle-btn {
  position: absolute; left: 100%; top: 36px; transform: translateX(-50%); 
  width: 34px !important; height: 34px !important; border-radius: 50% !important;
  background: var(--neo-bg) !important; border: 1px solid var(--neo-border) !important;
  color: var(--text-sub) !important; display: flex !important; align-items: center; justify-content: center;
  cursor: pointer; z-index: 1060; padding: 0; font-size: 18px !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important; transition: all 0.25s ease;
}
.toggle-btn:hover { background: #f97316 !important; border-color: transparent !important; color: #ffffff !important; transform: translateX(-50%) scale(1.08); }

/* USER CARD */
.user-greeting { padding: 14px 16px; margin: 12px 16px; background: var(--input-bg); border: 1px solid var(--neo-border); border-radius: 18px !important; }
.admin-sidebar.sidebar-collapsed-state .user-greeting { margin: 12px 6px !important; padding: 12px 4px !important; justify-content: center !important; }
.avatar { position: relative; width: 36px; height: 36px; }
.avatar span {
  position: relative; z-index: 2; width: 36px; height: 36px; border-radius: 50%;
  background: var(--app-bg); border: 1px solid var(--neo-border); color: var(--text-main);
  font-weight: 700; font-size: 14px; display: flex; align-items: center; justify-content: center;
}
.avatar-ring {
  position: absolute; inset: -2px; border-radius: 50%; border: 1px solid transparent;
  background: linear-gradient(135deg, #f97316, #db2777) border-box;
  -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0); -webkit-mask-composite: destination-out; mask-composite: exclude;
}
.greeting-label { font-size: 11px; color: var(--text-sub); }
.greeting-name { font-size: 13.5px; color: var(--text-main); }

/* NÚT THỂ HIỆN CHẾ ĐỘ CHUYỂN THEME */
.btn-theme-toggle {
  background: var(--input-bg) !important; border: 1px solid var(--neo-border) !important;
  color: var(--text-sub) !important; border-radius: 14px; padding: 10px 14px; font-size: 13.5px; cursor: pointer; transition: all 0.25s ease;
}
.btn-theme-toggle:hover { background: rgba(249, 115, 22, 0.08) !important; color: #f97316 !important; }
.theme-status-dot { width: 8px; height: 8px; background: #cbd5e1; border-radius: 50%; transition: all 0.3s ease; }
.theme-status-dot.dark-active { background: #f59e0b; box-shadow: 0 0 8px #f59e0b; }
.admin-sidebar.sidebar-collapsed-state .theme-toggle-container { padding: 0 8px !important; }
.admin-sidebar.sidebar-collapsed-state .btn-theme-toggle { justify-content: center !important; padding: 12px 0; }

/* THÂN DANH MỤC */
.sidebar-body { padding: 12px 16px; }
.sidebar-body::-webkit-scrollbar { width: 3px; }
.sidebar-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.04); border-radius: 99px; }
.section-heading { font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: var(--text-sub); opacity: 0.6; padding: 20px 12px 6px; }

.nav-link {
  display: flex; align-items: center; gap: 14px; padding: 11px 14px; border-radius: 14px !important;
  color: var(--text-sub) !important; font-size: 14px; font-weight: 500; transition: all 0.25s ease; text-decoration: none;
}
.admin-sidebar.sidebar-collapsed-state .nav-link { justify-content: center; padding: 12px 0; }
.nav-dot { width: 5px; height: 5px; border-radius: 50%; opacity: 0; transform: scale(0.5); transition: all 0.2s ease; margin-left: auto; }
.nav-link:hover .nav-dot { opacity: 0.5; transform: scale(1); }
.nav-icon { display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; font-size: 18px; color: var(--text-sub); }
.nav-link:hover { color: var(--text-main) !important; background: var(--input-bg); transform: translateX(4px); }

/* Màu Active tương ứng từng mục */
.item-home .nav-dot, .item-home .nav-link.active { --c-active: var(--color-home); }
.item-tree .nav-dot, .item-tree .nav-link.active { --c-active: var(--color-tree); }
.item-members .nav-dot, .item-members .nav-link.active { --c-active: var(--color-members); }
.item-search .nav-dot, .item-search .nav-link.active { --c-active: var(--color-search); }
.item-clan .nav-dot, .item-clan .nav-link.active { --c-active: var(--color-clan); }

.nav-link.active {
  color: var(--text-main) !important; background: var(--neo-bg) !important;
  border: 1px solid var(--neo-border) !important; font-weight: 600;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.01);
}
.nav-link.active .nav-icon i { color: var(--c-active) !important; }
.nav-link.active .nav-dot { opacity: 1 !important; transform: scale(1.2); background: var(--c-active); }

/* FOOTER BUTTON */
.sidebar-footer { padding: 16px; border-top: 1px solid var(--neo-border); margin-top: auto; }
.btn-home {
  width: 100%; background: transparent; border: 1px solid var(--neo-border); color: var(--text-sub);
  border-radius: 14px !important; padding: 10px 14px; font-size: 13.5px; cursor: pointer; transition: all 0.2s ease;
}
.btn-home:hover { background: var(--input-bg); color: var(--text-main); }

.btn-logout {
  width: 100%; background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
  border: none; color: #ffffff; border-radius: 30px !important; padding: 11px 18px; font-size: 14px; font-weight: 600; cursor: pointer;
  box-shadow: 0 4px 14px rgba(244, 63, 94, 0.25); transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
}
.btn-logout:hover { transform: translateY(-1.5px); box-shadow: 0 6px 18px rgba(244, 63, 94, 0.35); }
.logout-arrow { font-size: 14px; transition: transform 0.3s ease; }
.btn-logout:hover .logout-arrow { transform: translateX(2px); }

.admin-sidebar.sidebar-collapsed-state .btn-home,
.admin-sidebar.sidebar-collapsed-state .btn-logout { padding: 12px 0; justify-content: center; border-radius: 14px !important; }
.admin-sidebar.sidebar-collapsed-state .btn-logout { background: var(--neo-bg) !important; border: 1px solid rgba(244, 63, 94, 0.2); color: #f43f5e; }
.text-gradient-gold-sidebar {
  background: linear-gradient(135deg, #b8860b 0%, #ffd700 50%, #e5a93b 100%) !important;
  -webkit-background-clip: text !important;
  -webkit-text-fill-color: transparent !important;
  font-weight: 600 !important;
}
</style>