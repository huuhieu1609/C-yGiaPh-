<template>
  <div class="admin-layout" :class="{ 'sidebar-collapsed': isCollapsed }">
    <AdminNavbar :is-collapsed="isCollapsed" @toggle-sidebar="isCollapsed = !isCollapsed" />
    <main class="admin-content">
      <router-view></router-view>
    </main>
  </div>
</template>

<script>
import AdminNavbar from '../components/admin/AdminNavbar.vue';

export default {
  name: 'DefaultLayout',
  components: {
    AdminNavbar
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
  background: #f0f2f5;
  color: #333;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Layout */
.admin-layout {
  display: flex;
  min-height: 100vh;
}

.admin-content {
  flex: 1;
  padding: 24px;
  margin-left: 280px; /* Offset for vertical sidebar */
  width: calc(100% - 280px);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar-collapsed .admin-content {
  margin-left: 80px;
  width: calc(100% - 80px);
}

/* ========================================
   Bootstrap Overrides for Child Pages
   ======================================== */
.card {
  border: none;
  border-radius: 14px;
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.04);
  transition: box-shadow 0.2s ease;
  background: #fff;
}

.card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
}

.card-header {
  background: transparent;
  border-bottom: 1px solid #f0f0f0;
  padding: 18px 20px;
}

.card-body {
  padding: 20px;
}

.radius-10 {
  border-radius: 14px !important;
}

.table {
  font-size: 14px;
}

.table thead th {
  font-weight: 600;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #666;
  border-bottom-width: 1px;
}

/* Badge gradient overrides */
.bg-gradient-quepal {
  background: linear-gradient(135deg, #43cea2, #185a9d) !important;
}
.bg-gradient-bloody {
  background: linear-gradient(135deg, #f5515f, #a1051d) !important;
}
.bg-gradient-ohhappiness {
  background: linear-gradient(135deg, #00b09b, #96c93d) !important;
}
.bg-gradient-blooker {
  background: linear-gradient(135deg, #e65100, #ff6d00) !important;
}
.bg-gradient-scooter {
  background: linear-gradient(135deg, #36d1dc, #5b86e5) !important;
}

/* Widget icons */
.widgets-icons-2 {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  border-radius: 12px;
  flex-shrink: 0;
}

.bg-light-primary { background: rgba(66, 133, 244, 0.1) !important; }
.bg-light-success { background: rgba(52, 168, 83, 0.1) !important; }
.bg-light-warning { background: rgba(251, 188, 4, 0.1) !important; }
.bg-light-danger  { background: rgba(234, 67, 53, 0.1) !important; }
.bg-light-info    { background: rgba(66, 133, 244, 0.1) !important; }

/* Order actions */
.order-actions a {
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: #f0f2f5;
  color: #666;
  transition: all 0.2s;
}

.order-actions a:hover {
  background: #e0e2e5;
  color: #333;
}

/* Scrollbar */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: #d0d0d0;
  border-radius: 6px;
}

::-webkit-scrollbar-thumb:hover {
  background: #b0b0b0;
}

/* ========================================
   Responsive
   ======================================== */
@media (max-width: 767.98px) {
  .admin-content {
    padding: 16px;
  }
}
</style>