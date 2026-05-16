<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="container-custom content-relative pt-5">
      <!-- Hero Section -->
      <section class="hero-section text-center">
        <span class="subtitle-gradient mb-3 d-block text-uppercase tracking-widest">HÀNH TRÌNH TÌM LẠI CỘI NGUỒN</span>
        <h1 class="hero-title">
          Kết nối với <span class="text-gradient">Tổ tiên</span> qua từng nén nhang
        </h1>
        <p class="hero-subtitle mx-auto">
          Dòng họ không chỉ là tên gọi, đó là quá khứ, là tâm hồn và là khởi nguồn của mỗi con người.
          Hãy cùng Heritage Archivist gìn giữ những giá trị thiêng liêng ngay hôm nay.
        </p>

        <div class="hero-actions justify-content-center mt-5">
          <button class="btn btn-gradient-home" @click="$router.push('/gia-pha')">KHỞI TẠO GIA PHẢ NGAY</button>
          <button class="btn btn-outline-home" @click="$router.push('/dich-vu-goi')">KHÁM PHÁ DỊCH VỤ</button>
        </div>

        <!-- Quick Stats Glass Card -->
        <div class="hero-stats-card mt-5 mx-auto">
          <div class="stat-item">
            <h3 class="text-gold">1,200+</h3>
            <p>Dòng họ đã tham gia</p>
          </div>
          <div class="stat-item">
            <h3 class="text-gold">50,000+</h3>
            <p>Thành viên số hóa</p>
          </div>
          <div class="stat-item border-0">
            <h3 class="text-gold">24/7</h3>
            <p>Truy cập toàn cầu</p>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section class="features-section mt-5">
        <div class="section-heading text-center mb-5">
          <h2 class="text-white fw-bold">Tính năng nổi bật</h2>
          <p class="text-white-50">Mang công nghệ hiện đại vào việc bảo tồn di sản gia đình</p>
        </div>

        <div class="grid-3-cols">
          <div class="glass-feature-card">
            <div class="feature-icon-wrapper">
              <i class="bx bx-data text-gold"></i>
            </div>
            <h3 class="text-white mt-4">Số hóa gia phả</h3>
            <p class="text-white-50 mt-3">Chuyển đổi những cuốn sổ cũ kỹ thành cây gia phả số hóa sống động, dễ dàng truy cập và bảo mật vĩnh viễn.</p>
          </div>

          <div class="glass-feature-card">
            <div class="feature-icon-wrapper">
              <i class="bx bx-book text-gold"></i>
            </div>
            <h3 class="text-white mt-4">Từ điển xưng hô</h3>
            <p class="text-white-50 mt-3">Hướng dẫn chi tiết cách gọi tên, xưng danh vị trí chính xác của từng cá nhân trong dòng họ nội ngoại.</p>
          </div>

          <div class="glass-feature-card">
            <div class="feature-icon-wrapper">
              <i class="bx bx-archive text-gold"></i>
            </div>
            <h3 class="text-white mt-4">Kho lưu trữ ký ức</h3>
            <p class="text-white-50 mt-3">Nơi lưu giữ những câu chuyện, video và hình ảnh chưa từng kể về những người đã khuất.</p>
          </div>
        </div>
      </section>

      <!-- About Preview -->
      <section class="about-preview mt-5 pb-5">
        <div class="glass-card p-5 rounded-3xl border border-white/10 overflow-hidden">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <img :src="aboutPhoto" alt="Heritage" class="img-fluid rounded-2xl shadow-glow-light">
            </div>
            <div class="col-lg-6">
              <div class="ps-lg-5 mt-4 mt-lg-0">
                <h2 class="text-white fw-bold mb-4">Gìn giữ giá trị thiêng liêng</h2>
                <p class="text-white-50 mb-4 lh-lg">Khoảnh khắc thắp nén nhang cho tổ tiên là lúc sợi dây truyền thống rạng lên mạnh mẽ nhất. Heritage Archivist giúp bạn thấu hiểu hơn về cội nguồn, để mỗi nén nhang thắp lên là một sự kết nối tâm hồn trọn vẹn.</p>
                <div class="d-flex align-items-center gap-3 mb-3">
                  <i class="bx bx-check-double text-gold fs-4"></i>
                  <span class="text-white">Xác định niên trình lịch sử</span>
                </div>
                <div class="d-flex align-items-center gap-3 mb-3">
                  <i class="bx bx-check-double text-gold fs-4"></i>
                  <span class="text-white">Kết nối mối quan hệ phả hệ</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                  <i class="bx bx-check-double text-gold fs-4"></i>
                  <span class="text-white">Chuẩn mực xưng hô truyền thống</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Process Section -->
      <section class="process-section mt-5 pt-5 pb-5">
        <div class="section-heading text-center mb-5">
          <h2 class="text-white fw-bold">Hành trình số hóa di sản</h2>
          <p class="text-white-50">Bắt đầu lưu giữ ký ức chỉ với 3 bước đơn giản</p>
        </div>
        
        <div class="row g-4 justify-content-center">
          <div class="col-md-4">
            <div class="process-step-card text-center p-5">
              <div class="step-number">01</div>
              <div class="step-icon mt-4"><i class="bx bx-user-plus text-gold"></i></div>
              <h4 class="text-white mt-4 fw-bold">Đăng ký tài khoản</h4>
              <p class="text-white-50 mt-3">Tạo tài khoản cá nhân và thiết lập thông tin cơ bản về dòng họ của bạn.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="process-step-card text-center p-5">
              <div class="step-number">02</div>
              <div class="step-icon mt-4"><i class="bx bx-network-chart text-gold"></i></div>
              <h4 class="text-white mt-4 fw-bold">Xây dựng phả hệ</h4>
              <p class="text-white-50 mt-3">Thêm thông tin, hình ảnh và câu kể về các thành viên qua nhiều thế hệ.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="process-step-card text-center p-5">
              <div class="step-number">03</div>
              <div class="step-icon mt-4"><i class="bx bx-share-alt text-gold"></i></div>
              <h4 class="text-white mt-4 fw-bold">Chia sẻ kết nối</h4>
              <p class="text-white-50 mt-3">Mời người thân cùng tham gia đóng góp và chiêm ngưỡng cây gia phả trực tuyến.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Testimonials Section -->
      <section class="testimonials-section mt-5 pt-5 pb-5">
        <div class="section-heading text-center mb-5">
          <h2 class="text-white fw-bold">Cảm nhận từ khách hàng</h2>
          <p class="text-white-50">Hàng ngàn gia đình đã tin tưởng sử dụng Heritage Archivist</p>
        </div>

        <div class="grid-3-cols">
          <div class="glass-testimonial-card p-4 border border-white/10 rounded-3xl">
            <div class="quote-icon mb-3"><i class="bx bxs-quote-left text-gold fs-1 opacity-25"></i></div>
            <p class="text-white-50 fst-italic mb-4">"Ứng dụng này đã giúp dòng họ chúng tôi kết nối lại với nhau sau nhiều năm thất lạc thông tin. Cảm ơn đội ngũ phát triển vì một sản phẩm quá ý nghĩa!"</p>
            <div class="author-meta d-flex align-items-center">
              <div class="author-avatar-small me-3">MT</div>
              <div>
                <h6 class="text-white mb-0 fw-bold">Lê Minh Tuấn</h6>
                <small class="text-gold">Kiến trúc sư - Hà Nội</small>
              </div>
            </div>
          </div>

          <div class="glass-testimonial-card p-4 border border-white/10 rounded-3xl">
            <div class="quote-icon mb-3"><i class="bx bxs-quote-left text-gold fs-1 opacity-25"></i></div>
            <p class="text-white-50 fst-italic mb-4">"Giao diện hiện đại nhưng vẫn giữ được nét truyền thống. Việc tra cứu và quản lý thành viên trở nên vô cùng đơn giản so với ghi chép sổ tay."</p>
            <div class="author-meta d-flex align-items-center">
              <div class="author-avatar-small me-3">PH</div>
              <div>
                <h6 class="text-white mb-0 fw-bold">Phạm Hoàng Nam</h6>
                <small class="text-gold">Nhà báo - TP.HCM</small>
              </div>
            </div>
          </div>

          <div class="glass-testimonial-card p-4 border border-white/10 rounded-3xl">
            <div class="quote-icon mb-3"><i class="bx bxs-quote-left text-gold fs-1 opacity-25"></i></div>
            <p class="text-white-50 fst-italic mb-4">"Tính năng lưu trữ hình ảnh và video là thứ tôi thích nhất. Những thước phim về ông bà giờ đây được lưu giữ vĩnh viễn cho con cháu đời sau."</p>
            <div class="author-meta d-flex align-items-center">
              <div class="author-avatar-small me-3">TH</div>
              <div>
                <h6 class="text-white mb-0 fw-bold">Nguyễn Thu Hà</h6>
                <small class="text-gold">Giáo viên - Đà Nẵng</small>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Footer Section -->
      <section class="footer-cta text-center mt-5 pb-5">
        <div class="glass-info p-5 rounded-3xl border border-white/10">
          <h2 class="text-white fw-bold mb-3">Sẵn sàng viết tiếp câu chuyện của dòng họ?</h2>
          <p class="text-white-50 mb-5">Bắt đầu miễn phí ngay hôm nay và bảo tồn di sản cho thế hệ mai sau.</p>
          <button class="btn btn-gradient-home px-5 py-3" @click="$router.push('/register')">ĐĂNG KÝ TÀI KHOẢN MIỄN PHÍ</button>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import aboutPhoto from '@/assets/images/about_photo.png';

export default {
    name: 'TrangChu',
    data() {
        return {
            aboutPhoto
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

.landing-page-wrapper {
    background-color: #0b1120;
    color: #f8fafc;
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
    position: relative;
    overflow: hidden;
}

.hero-background {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: linear-gradient(to bottom, rgba(11, 17, 32, 0.4) 0%, rgba(11, 17, 32, 0.9) 100%),
        url('@/assets/images/hero_bg.png');
    background-size: cover;
    background-position: center;
    z-index: 0;
}

.container-custom {
    max-width: 1100px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.glow-blob {
    position: absolute;
    width: 400px; height: 400px;
    border-radius: 50%;
    filter: blur(120px);
    z-index: 1;
    opacity: 0.4;
}

.blob-purple { background: #120a1f; top: 10%; left: -10%; }
.blob-blue { background: #031948; top: 40%; right: -10%; }

.subtitle-gradient {
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    color: #d4af37;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: #ffffff;
}

.text-gradient {
    background: linear-gradient(90deg, #fbff00, #ff8c00);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.hero-subtitle {
    font-size: 1.2rem;
    color: #94a3b8;
    max-width: 800px;
    line-height: 1.8;
}

.hero-actions {
    display: flex;
    gap: 20px;
}

.btn-gradient-home {
    background: linear-gradient(135deg, #0a3c88, #6610f2);
    color: white;
    border: none;
    padding: 15px 35px;
    border-radius: 12px;
    font-weight: 700;
    transition: all 0.3s;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
}

.btn-gradient-home:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(102, 16, 242, 0.4);
}

.btn-outline-home {
    background: rgba(255, 255, 255, 0.05);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 15px 35px;
    border-radius: 12px;
    font-weight: 700;
    transition: all 0.3s;
}

.btn-outline-home:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
    transform: translateY(-3px);
}

/* Glass Cards */
.hero-stats-card {
    display: flex;
    justify-content: center;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 30px;
    max-width: 800px;
}

.stat-item {
    flex: 1;
    padding: 0 30px;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-item h3 { font-size: 2rem; font-weight: 800; margin-bottom: 5px; }
.stat-item p { color: #94a3b8; font-size: 0.9rem; margin: 0; }

.text-gold { color: #ffd700; }

.grid-3-cols {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.glass-feature-card {
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    padding: 40px;
    border-radius: 24px;
    transition: all 0.3s;
}

.glass-feature-card:hover {
    transform: translateY(-10px);
    background: rgba(15, 23, 42, 0.6);
    border-color: rgba(255, 215, 0, 0.3);
}

.feature-icon-wrapper {
    width: 60px; height: 60px;
    background: rgba(255, 215, 0, 0.1);
    border-radius: 16px;
    display: flex;
    align-items: center; justify-content: center;
    font-size: 2rem;
}

.glass-card {
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(12px);
}

.shadow-glow-light {
    box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
}

.glass-info {
    background: rgba(15, 23, 42, 0.8);
    backdrop-filter: blur(12px);
}

/* Process Section */
.process-step-card {
    background: rgba(15, 23, 42, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 30px;
    position: relative;
    transition: all 0.4s;
    height: 100%;
}

.process-step-card:hover {
    background: rgba(15, 23, 42, 0.5);
    border-color: #ffd700;
    transform: translateY(-5px);
}

.step-number {
    position: absolute;
    top: 20px; right: 30px;
    font-size: 3rem;
    font-weight: 900;
    color: rgba(255, 215, 0, 0.1);
}

.step-icon {
    font-size: 3.5rem;
}

/* Testimonials */
.glass-testimonial-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    transition: all 0.3s;
}

.glass-testimonial-card:hover {
    background: rgba(255, 255, 255, 0.06);
}

.author-avatar-small {
    width: 45px; height: 45px;
    background: linear-gradient(135deg, #ffd700, #ff8c00);
    border-radius: 50%;
    display: flex;
    align-items: center; justify-content: center;
    color: #000;
    font-weight: 700;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .hero-title { font-size: 2.5rem; }
    .hero-stats-card { flex-direction: column; gap: 20px; }
    .stat-item { border-right: none; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 20px; }
    .hero-actions { flex-direction: column; }
}
</style>