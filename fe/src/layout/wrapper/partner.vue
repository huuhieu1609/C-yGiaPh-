<template>
  <div class="admin-layout" :class="{ 'sidebar-collapsed': isCollapsed }">
    <PartnerNavbar :is-collapsed="isCollapsed" @toggle-sidebar="isCollapsed = !isCollapsed" />
    
    <main class="admin-content">
      <div class="main-workspace-wrapper">
        <router-view></router-view>
      </div>
    </main>
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
      isCollapsed: false
    }
  }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
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

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  background-color: var(--app-bg) !important;
  color: var(--text-main) !important;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  transition: background-color 0.3s ease, color 0.3s ease;
}

/* Layout Container */
.admin-layout {
  display: flex;
  min-height: 100vh;
  width: 100%;
  overflow-x: hidden;
}

/* Khung bọc lớn bên phải */
.admin-content {
  flex: 1;
  display: flex !important;
  flex-direction: column !important;
  margin-left: 275px; 
  width: calc(100% - 275px);
  transition: all 0.35s cubic-bezier(0.25, 1, 0.5, 1);
  height: 100vh;
  box-sizing: border-box;
  position: relative;
}

/* Khi Sidebar co lại về 80px */
.sidebar-collapsed .admin-content {
  margin-left: 105px;
  width: calc(100% - 105px);
}

/* ─── 🌟 TỐI ƯU LẠI KHUNG NEO: ĐẨY LÊN TRÊN VÀ PHẲNG TUYỆT ĐỐI ─── */
.main-workspace-wrapper {
  width: 100% !important;
  height: 100% !important;
  /* Giảm padding-top từ 30px xuống 15px để nội dung dịch sát lên trên */
  padding: 19px 30px 20px 15px !important; 
  box-sizing: border-box;
  position: absolute !important;
  top: 0 !important;
  left: 0 !important;
  overflow-y: auto; /* Cuộn nội dung lọt lòng độc lập */
}

/* Triệt tiêu tận gốc mọi cấu trúc margin đè của Bootstrap ở phần tử đầu tiên */
.main-workspace-wrapper > div,
.main-workspace-wrapper > .row,
.main-workspace-wrapper > .card,
.main-workspace-wrapper > div > .row,
.main-workspace-wrapper > div > .card {
  margin-top: 0 !important;
  top: 0 !important;
  padding-top: 0 !important;
}

/* Loại bỏ lề âm của hàng đầu tiên tránh đẩy lệch khung */
.main-workspace-wrapper > .row:first-child,
.main-workspace-wrapper > div > .row:first-child {
  margin-top: 0 !important;
  padding-top: 0 !important;
}

/* ─── HỆ THỐNG BIẾN MÀU THEO CHẾ ĐỘ MÔI TRƯỜNG ─── */
:root, [data-theme="light"] {
  --app-bg:        #f0f2f5;
  --card-bg:       #ffffff;
  --text-main:     #1f2937;
  --text-sub:      #6b7280;
  --border-color:  rgba(0, 0, 0, 0.05);
  --input-bg:      rgba(0, 0, 0, 0.015);
}

[data-theme="dark"] {
  --app-bg:        #121320; 
  --card-bg:       #1a1c2e; 
  --text-main:     #f3f4f6;
  --text-sub:      #9ca3af;
  --border-color:  rgba(255, 255, 255, 0.08);
  --input-bg:      rgba(255, 255, 255, 0.03);
}

/* FORCE OVERRIDE: Chống tràn sáng cho Dark Mode */
[data-theme="dark"] .card, 
[data-theme="dark"] .card-header,
[data-theme="dark"] .bg-white,
[data-theme="dark"] .table-light {
  background-color: var(--card-bg) !important;
  color: var(--text-main) !important;
  border-color: var(--border-color) !important;
}

[data-theme="dark"] .table thead th {
  background-color: var(--app-bg) !important;
  color: var(--text-sub) !important;
  border-bottom: 1px solid var(--border-color) !important;
}

[data-theme="dark"] input, 
[data-theme="dark"] select, 
[data-theme="dark"] textarea {
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  border-color: var(--border-color) !important;
}

/* ========================================
   Bootstrap Overrides For Adaptive Theme
   ======================================== */
.card {
  border: none !important;
  border-radius: 14px;
  background-color: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01) !important;
  transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.2s ease;
}
.card:hover { box-shadow: 0 10px 25px var(--border-color) !important; }
.card-header { background: transparent !important; border-bottom: 1px solid var(--border-color) !important; padding: 18px 20px; }
.card-body { padding: 20px; }
.radius-10 { border-radius: 14px !important; }
.table { font-size: 14px; color: var(--text-main) !important; }

.table thead th {
  font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;
  color: var(--text-sub) !important; border-bottom: 1px solid var(--border-color) !important;
}

/* Thanh cuộn scroll mượt */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--text-sub); opacity: 0.3; border-radius: 6px; }
</style>