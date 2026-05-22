<template>
    <div class="landing-page-wrapper">
        <div class="hero-background"></div>

        <div class="glow-blob blob-purple"></div>
        <div class="glow-blob blob-blue"></div>

        <br>
        <div class="container-custom content-relative mt-5">

            <header class="header pt-3">
                <div class="logo">
                    <span class="logo-icon"><i class='bx bx-sitemap'></i></span>
                    <span class="logo-text">LƯU GIỮ DI SẢN DÒNG TỘC</span>
                </div>
            </header>

            <section class="hero-section text-center">
                <h1 class="hero-title">
                    Cây Gia Phả Số - Nơi lưu giữ <span class="text-gradient">Di Sản Cội Nguồn</span> dòng tộc
                </h1>
                <p class="hero-subtitle">
                    Khởi tạo, lưu trữ và bảo tồn cây gia phả dòng họ trực tuyến một cách trang trọng. Kết nối các thế hệ, tra cứu thông tin họ hàng và truyền lại ngọn lửa văn hóa gia tộc cho muôn đời sau.
                </p>

                <div class="hero-actions">
                    <button class="btn btn-gradient" @click="dangKyGoi(20000, 'Quỹ Gia Phả Dòng Tộc')">ĐỒNG GÓP QUỸ DÒNG HỌ 20.000Đ</button>
                    <button class="btn btn-outline" @click="showSampleImage">
                        XEM CÂY GIA PHẢ MẪU
                    </button>
                </div>

                <div class="hero-stats-card">
                    <div class="stat-item">
                        <h4>01 cây duy nhất</h4>
                        <p>Dành riêng cho tộc của bạn</p>
                    </div>
                    <div class="stat-item">
                        <h4>Không giới hạn</h4>
                        <p>Số lượng thành viên thêm vào</p>
                    </div>
                    <div class="stat-item">
                        <h4>Lưu danh công đức</h4>
                        <p>Lưu truyền bảng vàng dòng họ</p>
                    </div>
                </div>
            </section>

            <section class="pain-points-section">
                <div class="section-heading text-center">
                    <h2>Những trăn trở đối với gia phả truyền thống</h2>
                    <p>Khi bảo tồn gia phả bằng sổ sách giấy, các dòng tộc thường gặp phải các thách thức lớn</p>
                </div>

                <div class="grid-4-cols mt-5">
                    <div class="feature-card">
                        <i class='bx bx-book-reader icon-blue'></i>
                        <h3>Dễ hư hại, thất lạc</h3>
                        <p>Sổ sách ghi chép bằng tay dễ bị rách nát, mối mọt, ẩm mốc hoặc thất lạc qua biến thiên lịch sử.</p>
                    </div>
                    <div class="feature-card">
                        <i class='bx bx-edit-alt icon-blue'></i>
                        <h3>Khó khăn khi cập nhật</h3>
                        <p>Mỗi khi có thêm thành viên mới sinh, kết hôn hay tạ thế, việc viết chèn, sửa chữa làm mất đi sự trang nghiêm.</p>
                    </div>
                    <div class="feature-card">
                        <i class='bx bx-globe icon-blue'></i>
                        <h3>Con cháu ở xa khó tiếp cận</h3>
                        <p>Sổ gốc thường chỉ giữ ở nhà thờ họ, con cháu sinh sống làm ăn xa xứ rất khó xem và nhớ về nguồn cội.</p>
                    </div>
                    <div class="feature-card">
                        <i class='bx bx-network-chart icon-blue'></i>
                        <h3>Khó hình dung vai vế</h3>
                        <p>Đọc văn bản giấy rất khó hình dung trực quan toàn bộ sơ đồ phả hệ của một dòng họ lớn, nhiều chi ngành.</p>
                    </div>
                </div>
            </section>

            <section class="market-section">
                <div class="section-heading text-center">
                    <h2>Giải pháp Gia Phả Số toàn diện</h2>
                    <p>Ứng dụng công nghệ hiện đại vào việc gìn giữ giá trị truyền thống</p>
                </div>

                <div class="grid-3-cols mt-5">
                    <div class="stat-card">
                        <h3 class="text-blue">20.000đ</h3>
                        <p>Ủng hộ đóng góp tùy tâm</p>
                        <small>Ghi danh công đức, duy trì hệ thống</small>
                    </div>
                    <div class="stat-card">
                        <h3 class="text-blue">1 Click</h3>
                        <p>Chia sẻ cho toàn bộ họ hàng</p>
                        <small>Gửi đường dẫn liên kết cho con cháu muôn phương</small>
                    </div>
                    <div class="stat-card">
                        <h3 class="text-blue">24/7</h3>
                        <p>Truy cập mọi lúc, mọi nơi</p>
                        <small>Xem trên máy tính, điện thoại, máy tính bảng</small>
                    </div>
                </div>

                <div class="comparison-table-wrapper mt-5">
                    <table class="comparison-table">
                        <thead>
                            <tr>
                                <th class="text-blue">TIÊU CHÍ</th>
                                <th>GIA PHẢ SỔ SÁCH CŨ</th>
                                <th class="text-green">BẢN SỐ HÓA DÒNG HỌ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold text-white">Lưu trữ dữ liệu</td>
                                <td>Dễ rách nát, phai màu mực, mối mọt</td>
                                <td class="text-green">Lưu trữ an toàn, bảo mật vĩnh viễn trên máy chủ đám mây</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-white">Khả năng cập nhật</td>
                                <td>Gạch xóa, viết chèn gây khó đọc và thiếu thẩm mỹ</td>
                                <td class="text-green">Thêm, sửa đổi thông tin thành viên nhanh chóng với vài thao tác</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-white">Hiển thị & Tra cứu</td>
                                <td>Đọc chữ khó hình dung liên kết, tìm kiếm lâu</td>
                                <td class="text-green">Sơ đồ cây phả hệ sinh động, tìm kiếm thành viên trong 1 giây</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-white">Gắn kết dòng tộc</td>
                                <td>Chỉ người giữ sổ ở quê mới tiếp cận được</td>
                                <td class="text-green">Tất cả con cháu trong dòng tộc ở bất cứ đâu đều xem được cội nguồn</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="footer-cta text-center mt-5 pb-5">
                <h2>Bắt đầu lưu giữ cội nguồn ngay hôm nay</h2>
                <p>Ủng hộ quỹ dòng tộc chỉ với 20.000đ để bắt đầu khởi tạo và bảo tồn cây phả hệ của riêng gia tộc bạn.</p>
            </section>

        </div>
    </div>
    <!-- Modal hiển thị ảnh -->
    <div v-if="showModal" class="image-modal" @click="closeModal">
        <div class="image-modal-content" @click.stop>

            <button class="close-btn" @click="closeModal">
                ✕
            </button>

            <img src="@/assets/images/anh_mau.jpg" alt="Gia phả mẫu" class="sample-image" />

        </div>
    </div>
</template>

<script>
export default {
    name: 'GiaPhaLandingPage',

    data() {
        return {
            showModal: false
        }
    },

    methods: {
        showSampleImage() {
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
        },
        dangKyGoi(so_tien, ten_goi) {
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
.landing-page-wrapper {
    background-color: #0b1120;
    color: #f8fafc;
    min-height: 100vh;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    position: relative;
    overflow: hidden;
}

/* KHU VỰC ẢNH NỀN HERO SECTION */
.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background-image: linear-gradient(to bottom, rgba(11, 17, 32, 0.3) 0%, rgba(11, 17, 32, 0.8) 100%),
        url('@/assets/images/bg_product.webp');
    background-size: cover;
    background-position: center;
    z-index: 0;
}

.container-custom {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}

.content-relative {
    position: relative;
    z-index: 2;
}

.glow-blob {
    position: absolute;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    filter: blur(120px);
    z-index: 1;
    opacity: 0.5;
}

.blob-purple {
    background: #120a1f;
    top: 10%;
    left: -10%;
}

.blob-blue {
    background: #031948;
    top: 40%;
    right: -10%;
}

/* --- LOGO VÀ TEXT GIA PHẢ --- */
.logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.logo-icon {
    background: linear-gradient(90deg, #ffd700, #ff8c00);
    color: white;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-text {
    color: #ffffff;
    font-size: 1.1rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
}

/* 4. HERO SECTION */
.hero-section {
    padding: 80px 0 60px;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1.3;
    margin-bottom: 20px;
    color: #ffffff;
    text-shadow: 0 4px 15px rgba(0, 0, 0, 0.9), 0 1px 3px rgba(255, 234, 0, 0.8);
}

.text-gradient {
    background: linear-gradient(90deg, #fbff00, #ff8c00);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.8));
}

.hero-subtitle {
    color: #f8fafc;
    font-weight: 500;
    max-width: 700px;
    margin: 0 auto 40px;
    font-size: 1.1rem;
    line-height: 1.6;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9), 0 1px 2px rgba(0, 0, 0, 0.8);
}

/* Buttons */
.hero-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-bottom: 50px;
}

.btn {
    padding: 12px 28px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
}

.btn-gradient {
    background: linear-gradient(135deg, #0a3c88, #6610f2);
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
}

.btn-gradient:hover {
    opacity: 0.9;
    box-shadow: 0 0 20px rgba(13, 110, 253, 0.6);
}

.btn-outline {
    background: rgba(0, 0, 0, 0.4);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.4);
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.15);
}

/* HERO STATS CARD (THIẾT KẾ MỚI) */
.hero-stats-card {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    padding: 30px;
    max-width: 900px;
    margin: 0 auto;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
}

.stat-item {
    flex: 1;
    min-width: 220px;
    position: relative;
}

/* Vạch kẻ dọc phân cách giữa các item trên màn hình PC */
@media (min-width: 768px) {
    .stat-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: -10px;
        top: 15%;
        height: 70%;
        width: 1px;
        background: rgba(255, 255, 255, 0.2);
    }
}

.stat-item h4 {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #ffd700;
    text-shadow: none;
}

.stat-item p {
    color: #f8fafc;
    font-weight: 500;
    font-size: 0.95rem;
    text-shadow: none;
    margin-bottom: 0;
}

/* 5. TIÊU ĐỀ SECTION CHUNG */
.section-heading h2 {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: #ffffff;
    text-shadow: 0 4px 15px rgba(0, 0, 0, 0.9);
}

.section-heading p {
    color: #e2e8f0;
    font-weight: 500;
    font-size: 1.05rem;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
}

.mt-5 {
    margin-top: 3rem;
}

.pb-5 {
    padding-bottom: 3rem;
}

/* 6. GRID LAYOUTS */
.grid-4-cols {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 80px;
}

.grid-3-cols {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

/* 7. CARDS (Thẻ) */
.feature-card,
.stat-card {
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 30px;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    background: #233044;
}

.icon-blue {
    font-size: 2rem;
    color: #38bdf8;
    margin-bottom: 15px;
    display: inline-block;
}

.feature-card h3 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 10px;
    line-height: 1.4;
    color: #ffffff;
}

.feature-card p,
.stat-card p {
    color: #94a3b8;
    font-size: 0.95rem;
    line-height: 1.6;
}

.stat-card {
    text-align: center;
}

.stat-card h3 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.stat-card small {
    color: #64748b;
    font-style: italic;
    font-size: 0.85rem;
}

.text-blue {
    color: #38bdf8 !important;
}

/* 8. BẢNG SO SÁNH */
.comparison-table-wrapper {
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 80px;
}

.comparison-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.comparison-table th,
.comparison-table td {
    padding: 20px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.comparison-table th {
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 1px;
    color: #94a3b8;
}

.comparison-table td {
    color: #cbd5e1;
    font-size: 0.95rem;
}

.comparison-table tbody tr:last-child td {
    border-bottom: none;
}

.text-green {
    color: #10b981 !important;
}

.fw-bold {
    font-weight: 700;
}

.text-white {
    color: #ffffff !important;
}

/* Lời kêu gọi hành động cuối trang */
.footer-cta h2 {
    color: #ffffff;
}

/* 9. RESPONSIVE (Dành cho Mobile) */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }

    .hero-actions {
        flex-direction: column;
    }

    /* Căn chỉnh lại Card trên Mobile */
    .hero-stats-card {
        flex-direction: column;
        padding: 20px;
    }

    .stat-item {
        padding: 10px 0;
    }

    .comparison-table th,
    .comparison-table td {
        padding: 15px;
    }

    .comparison-table {
        font-size: 0.85rem;
    }

    .glow-blob {
        width: 200px;
        height: 200px;
    }
}

/* ================= MODAL ẢNH ================= */

.image-modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.82);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
}

.image-modal-content {
    position: relative;
    max-width: 1100px;
    width: 100%;
}

.sample-image {
    width: 100%;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
}

.close-btn {
    position: absolute;
    top: -15px;
    right: -15px;

    width: 42px;
    height: 42px;

    border: none;
    border-radius: 50%;

    background: white;
    color: #111827;

    font-size: 1.1rem;
    font-weight: bold;

    cursor: pointer;
    transition: 0.3s;
}

.close-btn:hover {
    background: #ef4444;
    color: white;
    transform: rotate(90deg);
}
</style>