<template>
  <div class="partner-layout-container">
    <PartnerNavbar :is-collapsed="isCollapsed" @toggle-sidebar="isCollapsed = !isCollapsed" />
    
    <div class="partner-main-wrapper">
      <main class="page-content">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script>
import PartnerNavbar from '../../layout/components/doitac/PartnerNavbar.vue';

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

<style scoped>
/* ─── CONTAINER LAYOUT TỔNG & HỆ NỀN PREMIUM CHUYỂN SẮC ─── */
.partner-layout-container {
  display: flex !important; /* Xếp hàng ngang: Sidebar trái - Nội dung phải */
  width: 100vw;
  min-height: 100vh;
  overflow-x: hidden;

  /* 🌟 ĐÃ NÂNG CẤP: Hệ nền Aura Mesh Luxury Light hiển thị cực rõ nét 🌟 */
  background-color: #f2f5f9; /* Đẩy tông nền gốc xuống xám mịn để tăng tương phản */
  background-image: 
    radial-gradient(at 0% 0%, rgba(79, 70, 229, 0.18) 0px, transparent 55%),   /* Góc trên trái: Tím Indigo (Tổng Quan) */
    radial-gradient(at 100% 0%, rgba(219, 39, 119, 0.16) 0px, transparent 55%), /* Góc trên phải: Hồng Cánh Sen (Cây Gia Phả) */
    radial-gradient(at 100% 100%, rgba(20, 184, 166, 0.15) 0px, transparent 60%),/* Góc dưới phải: Xanh Ngọc Teal (Thành Viên) */
    radial-gradient(at 0% 100%, rgba(245, 158, 11, 0.14) 0px, transparent 55%);  /* Góc dưới trái: Vàng Hổ Phách (Tra Cứu) */
  
  background-attachment: fixed; /* Giữ cố định nền khi cuộn trang con */
  position: relative;
}

/* 🌟 LỚP PHỦ HẠT MỊN (NOISE OVERLAY): Tạo kết cấu giấy mỹ thuật hạng sang,
   giúp các dải màu gradient phía trên hòa quyện mượt mà vào nhau, không bị gãy sọc màu */
.partner-layout-container::before {
  content: "";
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  opacity: 0.025; /* Độ nhám mờ tinh tế chuẩn UI Apple và Microsoft Windows */
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
  pointer-events: none;
  z-index: 1;
}

/* Đảm bảo khung bọc nội dung luôn nổi lên trên lớp hạt mịn */
.partner-main-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  min-width: 0;
  background: transparent !important; /* Loại bỏ hoàn toàn nền đặc */
  position: relative;
  z-index: 2;
}

.page-content {
  flex: 1;
  width: 100%;
  padding: 24px !important; /* Khoảng cách đệm 24px tiêu chuẩn vàng giúp UI thoáng mắt */
  box-sizing: border-box;
  background: transparent !important; /* Xuyên thấu nhìn ra dải màu Aura phía sau */
}
</style>