<template>
  <div class="package-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>
    <div class="glow-blob blob-gold"></div>

    <div class="container container-custom content-relative pt-5 pb-5">
      <!-- Section Header -->
      <div class="text-center mb-5 mt-2">
        <span class="badge badge-gold-outline mb-2">Bảng giá dịch vụ</span>
        <h1 class="text-gradient fw-extrabold mb-3">Các Gói Dịch Vụ Gia Phả Số</h1>
        <p class="text-white-50 max-width-600 mx-auto">
          Chọn giải pháp tối ưu nhất để số hóa, kết nối con cháu và vinh danh truyền thống văn hóa dòng tộc qua muôn thế hệ.
        </p>
      </div>

      <!-- Package Grid -->
      <div class="row g-4 align-items-stretch justify-content-center">
        <!-- Package 1: Khởi Tạo (Basic) -->
        <div class="col-lg-4 col-md-6 d-flex">
          <div class="glass-card package-card d-flex flex-column w-100">
            <div class="card-header-custom p-4 border-bottom border-white/5">
              <span class="badge bg-slate-700 text-white-50 mb-2">Cơ bản</span>
              <h3 class="package-title text-white">Gói Khởi Tạo</h3>
              <p class="package-desc text-white-50">Phù hợp cho chi ngành nhỏ hoặc dòng tộc bắt đầu số hóa phả hệ.</p>
              <div class="price-container mt-4">
                <span class="price-num">100,000đ</span>
                <span class="price-duration text-white-50">/ Năm</span>
              </div>
            </div>
            
            <div class="card-body-custom p-4 flex-grow-1">
              <ul class="features-list list-unstyled mb-0">
                <li><i class="bx bx-check-circle text-success me-2"></i>Tối đa <strong>5 đời</strong> (thế hệ)</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Tối đa <strong>100 thành viên</strong></li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Sơ đồ cây phả hệ 2D động</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Công cụ tìm kiếm cơ bản</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Bảo mật dữ liệu đám mây</li>
                <li class="disabled"><i class="bx bx-x-circle text-danger me-2"></i>Nhật ký hoạt động bảo mật</li>
                <li class="disabled"><i class="bx bx-x-circle text-danger me-2"></i>Tủ sách tài liệu & Sự kiện</li>
              </ul>
            </div>
            
            <div class="card-footer-custom p-4 border-top border-white/5 text-center">
              <button @click="dangKyGoi(100000, 'Gói Khởi Tạo')" class="btn btn-package-outline w-100 mb-3">
                Đăng Ký Ngay
              </button>
              <router-link to="/dich-vu-goi/chi-tiet?goi=khoi-tao" class="btn-detail-link text-decoration-none">
                Xem chi tiết gói <i class="bx bx-right-arrow-alt align-middle"></i>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Package 2: Hưng Thịnh (Standard - Featured) -->
        <div class="col-lg-4 col-md-6 d-flex">
          <div class="glass-card package-card featured-card d-flex flex-column w-100">
            <div class="featured-ribbon">PHỔ BIẾN NHẤT</div>
            <div class="card-header-custom p-4 border-bottom border-white/5">
              <span class="badge bg-gold text-dark mb-2">Đặc quyền</span>
              <h3 class="package-title text-white">Gói Hưng Thịnh</h3>
              <p class="package-desc text-white-50">Giải pháp toàn diện cho các dòng tộc quy mô trung bình.</p>
              <div class="price-container mt-4">
                <span class="price-num text-gold">250,000đ</span>
                <span class="price-duration text-white-50">/ Năm</span>
              </div>
            </div>
            
            <div class="card-body-custom p-4 flex-grow-1">
              <ul class="features-list list-unstyled mb-0">
                <li><i class="bx bx-check-circle text-success me-2"></i>Tối đa <strong>10 đời</strong> (thế hệ)</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Tối đa <strong>500 thành viên</strong></li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Sơ đồ cây tương tác nâng cao</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Nhật ký hoạt động & Thao tác</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Tủ sách tài liệu dòng họ</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Phê duyệt đề xuất của con cháu</li>
                <li class="disabled"><i class="bx bx-x-circle text-danger me-2"></i>Thông báo tự động & VIP hỗ trợ</li>
              </ul>
            </div>
            
            <div class="card-footer-custom p-4 border-top border-white/5 text-center">
              <button @click="dangKyGoi(250000, 'Gói Hưng Thịnh')" class="btn btn-package-gold w-100 mb-3">
                Đăng Ký Ngay
              </button>
              <router-link to="/dich-vu-goi/chi-tiet?goi=hung-thinh" class="btn-detail-link text-decoration-none text-gold">
                Xem chi tiết gói <i class="bx bx-right-arrow-alt align-middle text-gold"></i>
              </router-link>
            </div>
          </div>
        </div>

        <!-- Package 3: Trường Tồn (Premium) -->
        <div class="col-lg-4 col-md-6 d-flex">
          <div class="glass-card package-card premium-card d-flex flex-column w-100">
            <div class="card-header-custom p-4 border-bottom border-white/5">
              <span class="badge bg-purple-600 text-white mb-2">Vĩnh cửu</span>
              <h3 class="package-title text-white">Gói Trường Tồn</h3>
              <p class="package-desc text-white-50">Không giới hạn đặc quyền dành cho đại gia tộc lớn nhiều chi nhánh.</p>
              <div class="price-container mt-4">
                <span class="price-num text-purple-400">500,000đ</span>
                <span class="price-duration text-white-50">/ Năm</span>
              </div>
            </div>
            
            <div class="card-body-custom p-4 flex-grow-1">
              <ul class="features-list list-unstyled mb-0">
                <li><i class="bx bx-check-circle text-success me-2"></i><strong>Không giới hạn</strong> số đời</li>
                <li><i class="bx bx-check-circle text-success me-2"></i><strong>Không giới hạn</strong> thành viên</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Tất cả đặc quyền Quản trị cao cấp</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Quản lý sự kiện, lễ giỗ Tổ họ</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Hệ thống tự động gửi thông báo</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Xuất cây phả hệ PDF in khổ lớn</li>
                <li><i class="bx bx-check-circle text-success me-2"></i>Hỗ trợ kỹ thuật VIP 24/7</li>
              </ul>
            </div>
            
            <div class="card-footer-custom p-4 border-top border-white/5 text-center">
              <button @click="dangKyGoi(500000, 'Gói Trường Tồn')" class="btn btn-package-outline w-100 mb-3">
                Đăng Ký Ngay
              </button>
              <router-link to="/dich-vu-goi/chi-tiet?goi=truong-ton" class="btn-detail-link text-decoration-none">
                Xem chi tiết gói <i class="bx bx-right-arrow-alt align-middle"></i>
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- Donation Callout Section -->
      <div class="glass-card donation-callout mt-5 p-4 p-md-5 rounded-3xl border border-white/10 text-center">
        <div class="row align-items-center justify-content-center">
          <div class="col-lg-8 text-lg-start mb-4 mb-lg-0">
            <h3 class="text-gold fw-bold mb-2"><i class="bx bx-heart me-2 animate-pulse"></i>Đóng Góp Công Đức Tùy Tâm</h3>
            <p class="text-white-50 mb-0">
              Nếu bạn chỉ muốn đóng góp tài chính chung tay phát triển quỹ dòng tộc, bảo tồn di sản cội nguồn mà không cần mua các gói quản trị nâng cao.
            </p>
          </div>
          <div class="col-lg-4 text-lg-end">
            <router-link to="/dong-gop-quy" class="btn btn-donation px-4 py-3">
              <i class="bx bx-heart me-1"></i> ĐÓNG GÓP HẢO TÂM
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DichVuGoi',
  methods: {
    dangKyGoi(so_tien, ten_goi) {
      const isAuthenticated = localStorage.getItem('access_token');
      if (!isAuthenticated) {
        this.$router.push({
          path: '/login',
          query: { redirect: `/thanh-toan?so_tien=${so_tien}&ten_goi=${encodeURIComponent(ten_goi)}` }
        });
        return;
      }
      this.$router.push({
        path: '/thanh-toan',
        query: { 
          so_tien: so_tien,
          ten_goi: ten_goi
        }
      });
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

.package-page-wrapper {
  background-color: #0b1120;
  color: #f8fafc;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
  position: relative;
  overflow: hidden;
  padding-top: 80px;
}

.hero-background {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background-image: linear-gradient(to bottom, rgba(11, 17, 32, 0.4) 0%, rgba(11, 17, 32, 0.95) 100%),
      url('@/assets/images/bg_product.webp');
  background-size: cover;
  background-position: center;
  z-index: 0;
  opacity: 0.6;
}

.container-custom {
  max-width: 1200px;
  margin: 0 auto;
  position: relative;
  z-index: 2;
}

.glow-blob {
  position: absolute;
  width: 450px; height: 450px;
  border-radius: 50%;
  filter: blur(140px);
  z-index: 1;
  opacity: 0.35;
}

.blob-purple { background: #581c87; top: 5%; left: -10%; }
.blob-blue { background: #0369a1; top: 35%; right: -10%; }
.blob-gold { background: #78350f; bottom: 10%; left: 30%; }

.text-gradient {
  background: linear-gradient(90deg, #ffd700, #ff8c00);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.text-gold { color: #ffd700; }
.text-purple-400 { color: #c084fc; }

.badge-gold-outline {
  border: 1px solid rgba(212, 175, 55, 0.4);
  color: #ffd700;
  background: rgba(212, 175, 55, 0.05);
  padding: 6px 12px;
  font-size: 0.75rem;
  font-weight: 600;
  text-uppercase: uppercase;
  letter-spacing: 1px;
  border-radius: 99px;
}

.max-width-600 {
  max-width: 600px;
}

/* Glass Card */
.glass-card {
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.package-card {
  position: relative;
  overflow: hidden;
}

.package-card:hover {
  transform: translateY(-8px);
  border-color: rgba(255, 255, 255, 0.15);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.featured-card {
  border-color: rgba(212, 175, 55, 0.35);
  background: rgba(20, 25, 45, 0.8);
  box-shadow: 0 10px 30px rgba(212, 175, 55, 0.08);
}

.featured-card:hover {
  border-color: #d4af37;
  box-shadow: 0 20px 50px rgba(212, 175, 55, 0.18);
}

.featured-ribbon {
  position: absolute;
  top: 20px;
  right: -35px;
  background: #d4af37;
  color: #0f172a;
  font-size: 0.65rem;
  font-weight: 800;
  padding: 6px 35px;
  transform: rotate(45deg);
  letter-spacing: 0.5px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.bg-slate-700 {
  background-color: rgba(71, 85, 105, 0.2) !important;
  border: 1px solid rgba(255,255,255,0.05);
}

.bg-gold {
  background-color: #d4af37 !important;
  color: #0f172a !important;
}

.package-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-top: 5px;
}

.package-desc {
  font-size: 0.85rem;
  line-height: 1.4;
  min-height: 42px;
}

.price-container {
  display: flex;
  align-items: baseline;
}

.price-num {
  font-size: 2.2rem;
  font-weight: 800;
  letter-spacing: -1px;
}

.price-duration {
  font-size: 0.9rem;
  margin-left: 5px;
}

.features-list li {
  font-size: 0.9rem;
  color: #cbd5e1;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
}

.features-list li.disabled {
  color: #64748b;
  text-decoration: line-through;
  opacity: 0.65;
}

.features-list li i {
  font-size: 1.1rem;
}

/* Package Buttons */
.btn-package-outline {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.15);
  color: #f1f5f9;
  padding: 12px;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-package-outline:hover {
  background: #ffffff;
  color: #0f172a;
  border-color: #ffffff;
  transform: translateY(-1px);
}

.btn-package-gold {
  background: #d4af37;
  border: 1px solid #d4af37;
  color: #0f172a;
  padding: 12px;
  border-radius: 12px;
  font-weight: 700;
  transition: all 0.3s;
  box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
}

.btn-package-gold:hover {
  background: #f3d060;
  border-color: #f3d060;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
}

/* Donation Callout */
.donation-callout {
  border: 1px solid rgba(212, 175, 55, 0.2);
  background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 41, 59, 0.4) 100%);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.btn-donation {
  background: linear-gradient(135deg, #0a3c88, #6610f2);
  color: white;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  letter-spacing: 0.5px;
  box-shadow: 0 4px 15px rgba(102, 16, 242, 0.3);
  transition: all 0.3s;
  display: inline-block;
}

.btn-donation:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 16, 242, 0.5);
  opacity: 0.95;
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: .75; transform: scale(1.05); }
}

@media (max-width: 991px) {
  .featured-ribbon {
    top: 15px;
    right: -30px;
    font-size: 0.6rem;
    padding: 4px 30px;
  }
}

.btn-detail-link {
  font-size: 0.85rem;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.7);
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.btn-detail-link:hover {
  color: #d4af37 !important;
  transform: translateX(4px);
}

.text-gold {
  color: #d4af37 !important;
}
</style>