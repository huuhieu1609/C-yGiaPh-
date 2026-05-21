<template>
  <div class="admin-layout-container" :class="{ 'sidebar-collapsed-main': isCollapsed }">
    <AdminSidebar :is-collapsed="isCollapsed" @toggle-sidebar="isCollapsed = !isCollapsed" />
    
    <div class="admin-main-wrapper">
      <main class="admin-page-content">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script>
import AdminSidebar from '../../layout/components/admin/AdminNavbar.vue'; 

export default {
  name: 'AdminLayout',
  components: {
    AdminSidebar
  },
  // 🌟 ĐÃ SỬA: Khởi tạo biến theo dõi trạng thái đóng mở của thanh menu
  data() {
    return {
      isCollapsed: false
    }
  }
}
</script>

<style scoped>
/* ─── CONTAINER LAYOUT TỔNG ADMIN (Tone Tối Cyber) ─── */
.admin-layout-container {
  display: flex !important; 
  width: 100vw;
  min-height: 100vh;
  overflow-x: hidden;
  position: relative;
  background-color: #bbdeb9 !important;
  background-image: 
    radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.04) 0px, transparent 50%),  
    radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.03) 0px, transparent 60%); 
  background-attachment: fixed;
}

.admin-layout-container::before {
  content: "";
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  opacity: 0.015; 
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
  pointer-events: none;
  z-index: 1;
}

.admin-main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  min-width: 0;
  background: transparent !important;
  position: relative;
  z-index: 2;
}

.admin-page-content {
  flex: 1;
  width: 100%;
  padding: 24px !important; 
  box-sizing: border-box;
  background: transparent !important;
}
</style>