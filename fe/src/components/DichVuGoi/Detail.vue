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
                <template v-if="currentPackage">
                    <span class="badge mb-3" :class="currentPackage.badgeClass" style="padding: 8px 16px; border-radius: 99px; font-weight: 700; font-size: 0.8rem; letter-spacing: 1px; text-transform: uppercase;">
                        Gói {{ currentPackage.tag }}
                    </span>
                    <h1 class="hero-title">
                        Chi Tiết <span class="text-gradient">{{ currentPackage.name }}</span> Gia Phả Số
                    </h1>
                    <p class="hero-subtitle">
                        {{ currentPackage.desc }} Đăng ký ngay để kích hoạt các đặc quyền cao cấp, giúp bạn kết nối dòng tộc, lưu trữ tư liệu gia phả bền vững qua muôn đời.
                    </p>

                    <div class="hero-actions">
                        <button class="btn btn-gold-premium px-4 py-3" @click="dangKyGoi(currentPackage.priceVal, currentPackage.name)">
                            <i class="bx bx-rocket me-1"></i> ĐĂNG KÝ GÓI ({{ currentPackage.price }})
                        </button>
                        <router-link to="/dich-vu-goi" class="btn btn-outline px-4 py-3">
                            <i class="bx bx-grid-alt me-1"></i> BẢNG GIÁ DỊCH VỤ
                        </router-link>
                    </div>

                    <div class="hero-stats-card">
                        <div class="stat-item">
                            <h4>{{ currentPackage.stat1_title }}</h4>
                            <p>{{ currentPackage.stat1_desc }}</p>
                        </div>
                        <div class="stat-item">
                            <h4>{{ currentPackage.stat2_title }}</h4>
                            <p>{{ currentPackage.stat2_desc }}</p>
                        </div>
                        <div class="stat-item">
                            <h4>{{ currentPackage.stat3_title }}</h4>
                            <p>{{ currentPackage.stat3_desc }}</p>
                        </div>
                    </div>
                </template>
                <template v-else>
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
                </template>
            </section>

            <!-- DETAILED PACKAGE HIGHLIGHTS (Rendered ONLY if package is selected) -->
            <template v-if="currentPackage">
                <!-- Highlights Grid -->
                <section class="package-highlights-section mt-5 mb-5 animate-fade-in">
                    <div class="section-heading text-center">
                        <span class="badge badge-gold-outline mb-2">ƯU ĐIỂM VƯỢT TRỘI</span>
                        <h2>Giá Trị Nổi Bật Gói {{ currentPackage.name }}</h2>
                        <p class="text-white-50 max-width-600 mx-auto mb-4">
                            Những đặc quyền ưu việt được thiết kế chuyên biệt nhằm mang lại giải pháp số hóa gia tộc hoàn hảo nhất.
                        </p>
                    </div>

                    <div class="row g-4 justify-content-center">
                        <div v-for="(highlight, idx) in currentPackage.highlights" :key="idx" class="col-lg-6 d-flex">
                            <div class="glass-card highlight-card p-4 d-flex flex-column w-100 border-gold-soft-hover">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="highlight-icon-wrapper me-3">
                                        <i :class="highlight.icon"></i>
                                    </div>
                                    <h4 class="highlight-title text-white mb-0">{{ highlight.title }}</h4>
                                </div>
                                <p class="highlight-desc text-white-50 mb-0 flex-grow-1">
                                    {{ highlight.desc }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- CUSTOM DETAILED EXCLUSIVE HIGHLIGHTS (100% Custom for each package) -->
                <!-- Gói Khởi Tạo (khoi-tao) Custom Section -->
                <section v-if="currentGoiKey === 'khoi-tao'" class="exclusive-package-section mt-5 mb-5 animate-fade-in">
                    <div class="section-heading text-center mb-5">
                        <span class="badge badge-blue-outline mb-2">TÍNH NĂNG ĐỘC QUYỀN GÓI KHỞI TẠO</span>
                        <h2 class="text-white">Quy Trình Khởi Tạo & Số Hóa Nhanh Gọn</h2>
                        <p class="text-white-50 max-width-600 mx-auto">
                            Giải pháp được tối ưu hóa giúp các chi họ nhỏ dễ dàng số hóa gia phả giấy truyền thống sang dạng số chỉ trong 3 bước đơn giản.
                        </p>
                    </div>

                    <div class="row g-4 justify-content-center setup-flow-wrapper">
                        <!-- Step 1 -->
                        <div class="col-md-4">
                            <div class="glass-card flow-step-card text-center p-4 border-blue-soft">
                                <div class="step-badge">1</div>
                                <div class="step-icon-wrapper mb-3 text-blue">
                                    <i class="bx bx-user-plus fs-1"></i>
                                </div>
                                <h4 class="text-white mb-2">Đăng Ký & Tạo Gia Tộc</h4>
                                <p class="text-white-50 fs-7 mb-0">
                                    Khởi tạo tài khoản bảo mật và thiết lập thông tin cơ bản về chi họ của bạn trên hệ thống đám mây an toàn.
                                </p>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div class="col-md-4">
                            <div class="glass-card flow-step-card text-center p-4 border-blue-soft">
                                <div class="step-badge">2</div>
                                <div class="step-icon-wrapper mb-3 text-blue">
                                    <i class="bx bx-network-chart fs-1"></i>
                                </div>
                                <h4 class="text-white mb-2">Dựng Sơ Đồ Cây 5 Đời</h4>
                                <p class="text-white-50 fs-7 mb-0">
                                    Nhập liệu dễ dàng với giao diện trực quan, tự động kết nối và vẽ sơ đồ cây phả hệ 2D động cho tối đa 100 thành viên.
                                </p>
                            </div>
                        </div>
                        <!-- Step 3 -->
                        <div class="col-md-4">
                            <div class="glass-card flow-step-card text-center p-4 border-blue-soft">
                                <div class="step-badge">3</div>
                                <div class="step-icon-wrapper mb-3 text-blue">
                                    <i class="bx bx-share-alt fs-1"></i>
                                </div>
                                <h4 class="text-white mb-2">Chia Sẻ Liên Kết Nhanh</h4>
                                <p class="text-white-50 fs-7 mb-0">
                                    Gửi liên kết bảo mật hoặc mã QR cho con cháu trong chi họ để mọi người có thể xem cội nguồn trực tuyến mọi lúc.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Gói Hưng Thịnh (hung-thinh) Custom Section -->
                <section v-else-if="currentGoiKey === 'hung-thinh'" class="exclusive-package-section mt-5 mb-5 animate-fade-in">
                    <div class="section-heading text-center mb-5">
                        <span class="badge badge-gold-outline mb-2">TÍNH NĂNG ĐỘC QUYỀN GÓI HƯNG THỊNH</span>
                        <h2 class="text-white">Không Gian Văn Hóa & Cơ Chế Kiểm Duyệt Chặt Chẽ</h2>
                        <p class="text-white-50 max-width-600 mx-auto">
                            Đặc quyền nâng cao giúp dòng họ lưu trữ thư viện tài liệu cổ truyền và hỗ trợ quy trình Trưởng tộc duyệt thông tin chặt chẽ.
                        </p>
                    </div>

                    <div class="row g-4">
                        <!-- Feature 1: Digital Library Showcase -->
                        <div class="col-lg-6">
                            <div class="glass-card custom-feature-card p-4 h-100 border-gold-soft">
                                <h3 class="text-gold mb-3 d-flex align-items-center">
                                    <i class="bx bx-book-reader me-2"></i> Tủ Sách Tư Liệu Gia Tộc Số Hóa
                                </h3>
                                <p class="text-white-50 fs-7 mb-4">
                                    Không chỉ lưu trữ tên tuổi, gói Hưng Thịnh mở ra không gian lưu trữ số hóa cho các tài liệu thiêng liêng của dòng tộc:
                                </p>
                                
                                <div class="library-mockup-list">
                                    <div class="mockup-item p-2 mb-2 rounded bg-white/5 border border-white/5 d-flex align-items-center">
                                        <i class="bx bxs-file-pdf text-danger fs-4 me-2"></i>
                                        <div class="flex-grow-1">
                                            <span class="text-white fs-7 d-block">Gia phả cổ chữ Hán Nôm (Dịch nghĩa)</span>
                                            <small class="text-white-50" style="font-size: 0.7rem;">Định dạng PDF - 14.5 MB</small>
                                        </div>
                                        <span class="badge bg-gold/20 text-gold fs-8">Đang lưu giữ</span>
                                    </div>
                                    <div class="mockup-item p-2 mb-2 rounded bg-white/5 border border-white/5 d-flex align-items-center">
                                        <i class="bx bxs-image text-primary fs-4 me-2"></i>
                                        <div class="flex-grow-1">
                                            <span class="text-white fs-7 d-block">Sắc phong thời Lê Trung Hưng</span>
                                            <small class="text-white-50" style="font-size: 0.7rem;">Độ phân giải cao - 8.2 MB</small>
                                        </div>
                                        <span class="badge bg-gold/20 text-gold fs-8">Đang lưu giữ</span>
                                    </div>
                                    <div class="mockup-item p-2 rounded bg-white/5 border border-white/5 d-flex align-items-center">
                                        <i class="bx bxs-music text-success fs-4 me-2"></i>
                                        <div class="flex-grow-1">
                                            <span class="text-white fs-7 d-block">Văn khấn cổ truyền & Lễ cúng Tổ</span>
                                            <small class="text-white-50" style="font-size: 0.7rem;">Audio ghi âm - 22 MB</small>
                                        </div>
                                        <span class="badge bg-gold/20 text-gold fs-8">Đang lưu giữ</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 2: Approval Workflow Diagram -->
                        <div class="col-lg-6">
                            <div class="glass-card custom-feature-card p-4 h-100 border-gold-soft">
                                <h3 class="text-gold mb-3 d-flex align-items-center">
                                    <i class="bx bx-shield-quarter me-2"></i> Quy Trình Phê Duyệt Thông Tin
                                </h3>
                                <p class="text-white-50 fs-7 mb-4">
                                    Bảo vệ tính tôn nghiêm và chính xác của gia phả với quy trình kiểm duyệt khép kín, duy nhất Trưởng tộc/Ban quản trị có quyền duyệt:
                                </p>

                                <div class="workflow-steps ps-3 border-start border-gold/30">
                                    <div class="workflow-step mb-3 position-relative">
                                        <div class="workflow-dot"></div>
                                        <h5 class="text-white fs-7 mb-1">Bước 1: Thành viên gửi đề xuất</h5>
                                        <p class="text-white-50 fs-8 mb-0">Con cháu ở xa cập nhật thông tin cá nhân hoặc con cái mới sinh.</p>
                                    </div>
                                    <div class="workflow-step mb-3 position-relative">
                                        <div class="workflow-dot"></div>
                                        <h5 class="text-white fs-7 mb-1">Bước 2: Trưởng tộc nhận thông báo</h5>
                                        <p class="text-white-50 fs-8 mb-0">Hệ thống gửi thông báo bảo mật đến tài khoản quản trị của Trưởng tộc.</p>
                                    </div>
                                    <div class="workflow-step position-relative">
                                        <div class="workflow-dot"></div>
                                        <h5 class="text-white fs-7 mb-1">Bước 3: Đối chiếu & Duyệt cập nhật</h5>
                                        <p class="text-white-50 fs-8 mb-0">Trưởng tộc đối chiếu sổ gia phả gốc, bấm "Duyệt" để cập nhật lên cây phả hệ chung.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Gói Trường Tồn (truong-ton) Custom Section -->
                <section v-else-if="currentGoiKey === 'truong-ton'" class="exclusive-package-section mt-5 mb-5 animate-fade-in">
                    <div class="section-heading text-center mb-5">
                        <span class="badge badge-purple-outline mb-2">TÍNH NĂNG ĐỘC QUYỀN GÓI TRƯỜNG TỒN</span>
                        <h2 class="text-gradient">Giải Pháp Trường Tồn Vĩnh Cửu Cho Đại Gia Tộc</h2>
                        <p class="text-white-50 max-width-600 mx-auto">
                            Đặc quyền tối cao không giới hạn quy mô cùng dịch vụ xuất bản phả hệ in ấn và hệ thống liên lạc tự động thông minh.
                        </p>
                    </div>

                    <div class="row g-4">
                        <!-- Detail 1: Large Print PDF -->
                        <div class="col-lg-4">
                            <div class="glass-card custom-feature-card p-4 h-100 border-purple-soft text-center">
                                <div class="icon-circle-purple mx-auto mb-3">
                                    <i class="bx bx-download fs-2 text-purple-300"></i>
                                </div>
                                <h4 class="text-white mb-2">In Ấn Khổ Lớn Hàng Mét</h4>
                                <p class="text-white-50 fs-7 mb-0">
                                    Xuất file sơ đồ cây phả hệ PDF định dạng vector chất lượng cực cao (Ultra HD), sẵn sàng in ấn khổ lớn (2m - 10m) trên vải lụa hoặc giấy cao cấp để treo trang nghiêm tại Từ đường dòng họ.
                                </p>
                            </div>
                        </div>

                        <!-- Detail 2: Automatic SMS/Email Giỗ Tổ -->
                        <div class="col-lg-4">
                            <div class="glass-card custom-feature-card p-4 h-100 border-purple-soft">
                                <div class="text-center mb-3">
                                    <div class="icon-circle-purple mx-auto mb-3">
                                        <i class="bx bx-bell fs-2 text-purple-300"></i>
                                    </div>
                                    <h4 class="text-white mb-2">Nhắc Giỗ Tổ Tự Động</h4>
                                </div>
                                <p class="text-white-50 fs-7 mb-3">
                                    Hệ thống thông minh tự động quy đổi và gửi SMS/Email nhắc nhở ngày Giỗ, lễ chạp dòng tộc đến toàn bộ con cháu:
                                </p>
                                
                                <div class="sms-preview-card p-3 rounded bg-black/40 border border-purple-500/20">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-white/5">
                                        <span class="text-purple-300 fs-8"><i class="bx bx-message-rounded-dots me-1"></i> Tin nhắn Gia Phả Số</span>
                                        <small class="text-white-50 fs-9">Hôm nay</small>
                                    </div>
                                    <p class="text-white mb-0 font-monospace fs-8" style="line-height: 1.4;">
                                        Kính gửi ông/bà Nguyễn Văn A, ngày mai 24/05 (tức 08/04 Âm lịch) là ngày Giỗ Tổ chi họ Nguyễn Đông Tác. Kính mời dòng họ tề tựu đông đủ tại Từ Đường lúc 9h00.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Detail 3: Expert VIP Support -->
                        <div class="col-lg-4">
                            <div class="glass-card custom-feature-card p-4 h-100 border-purple-soft text-center">
                                <div class="icon-circle-purple mx-auto mb-3">
                                    <i class="bx bx-support fs-2 text-purple-300"></i>
                                </div>
                                <h4 class="text-white mb-2">Cố Vấn & Nhập Liệu Hộ VIP</h4>
                                <p class="text-white-50 fs-7 mb-0">
                                    Nhận hỗ trợ đặc quyền từ các chuyên gia gia phả: Trực tiếp số hóa thông tin từ sổ giấy cũ của dòng họ, tối ưu hóa bố cục cây phả hệ phức tạp nhiều chi nhánh và bảo mật đa vùng đám mây trọn đời.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Detailed package features listing -->
                <section class="package-features-detail-section text-center mt-5 mb-5 animate-fade-in">
                    <div class="section-heading">
                        <span class="badge badge-gold-outline mb-2">DANH SÁCH CHI TIẾT</span>
                        <h2>Bảng Đặc Quyền Của Gói</h2>
                        <p class="text-white-50">So sánh đầy đủ các tính năng đi kèm trong gói dịch vụ này</p>
                    </div>
                    
                    <div class="glass-card mt-4 p-4 p-md-5 max-width-800 mx-auto text-start border-gold-soft">
                        <div class="row g-4">
                            <div v-for="(feat, idx) in currentPackage.features" :key="idx" class="col-md-6 d-flex align-items-start">
                                <i class="bx fs-4 me-3 mt-1" :class="feat.active ? 'bx-check-circle text-success' : 'bx-x-circle text-muted opacity-50'"></i>
                                <div>
                                    <h5 class="mb-1" :class="{ 'text-white': feat.active, 'text-muted text-decoration-line-through': !feat.active }">
                                        {{ feat.text }}
                                    </h5>
                                    <p class="text-white-50 fs-7 mb-0" v-if="feat.active">Được kích hoạt và hỗ trợ đầy đủ</p>
                                    <p class="text-muted fs-7 mb-0" v-else>Không khả dụng cho gói này</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-5 border-top border-white/5 pt-4">
                            <h4 class="mb-3 text-gold">Mức đầu tư: {{ currentPackage.price }} <span class="fs-6 text-white-50">{{ currentPackage.duration }}</span></h4>
                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <button class="btn btn-gold-premium px-4 py-3" @click="dangKyGoi(currentPackage.priceVal, currentPackage.name)">
                                    <i class="bx bx-check-double me-1"></i> KÍCH HOẠT GÓI DỊCH VỤ NGAY
                                </button>
                                <router-link to="/dich-vu-goi" class="btn btn-outline px-4 py-3">
                                    <i class="bx bx-arrow-back me-1"></i> CHỌN GÓI DỊCH VỤ KHÁC
                                </router-link>
                            </div>
                        </div>
                    </div>
                </section>
            </template>

            <!-- ONLY RENDER THIS IF NO PACKAGE IS SELECTED (GENERAL Solution Landing Page) -->
            <template v-if="!currentPackage">
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
            </template>

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
            showModal: false,
            packages: {
                'khoi-tao': {
                    name: 'Gói Khởi Tạo',
                    tag: 'Cơ bản',
                    price: '100,000đ',
                    priceVal: 100000,
                    duration: '/ Năm',
                    desc: 'Phù hợp cho chi ngành nhỏ hoặc dòng tộc bắt đầu số hóa phả hệ.',
                    stat1_title: 'Tối đa 5 đời',
                    stat1_desc: 'Số thế hệ cho phép',
                    stat2_title: '100 thành viên',
                    stat2_desc: 'Quy mô dòng họ tối đa',
                    stat3_title: 'Bảo mật Cloud',
                    stat3_desc: 'Sao lưu đám mây an toàn',
                    highlights: [
                        {
                            title: 'Khởi đầu số hóa đơn giản',
                            icon: 'bx bx-layer',
                            desc: 'Giải pháp hoàn hảo để chuyển đổi từ sổ giấy truyền thống sang sơ đồ số trực quan cho quy mô chi họ gia đình nhỏ.'
                        },
                        {
                            title: 'Sơ đồ cây 2D sinh động',
                            icon: 'bx bx-sitemap',
                            desc: 'Hiển thị trực quan mối quan hệ 5 thế hệ một cách sinh động, dễ dàng xem trên mọi thiết bị di động, máy tính bảng.'
                        },
                        {
                            title: 'Tìm kiếm & Bảo mật Cloud',
                            icon: 'bx bx-cloud',
                            desc: 'Dữ liệu dòng tộc được bảo mật tuyệt đối trên hệ thống đám mây, giúp con cháu tra cứu thông tin thành viên chỉ trong 1 giây.'
                        }
                    ],
                    features: [
                        { text: 'Tối đa 5 đời (thế hệ)', active: true },
                        { text: 'Tối đa 100 thành viên dòng tộc', active: true },
                        { text: 'Sơ đồ cây phả hệ 2D tương tác động', active: true },
                        { text: 'Công cụ tìm kiếm thành viên cơ bản', active: true },
                        { text: 'Bảo mật dữ liệu đám mây an toàn', active: true },
                        { text: 'Nhật ký hoạt động bảo mật', active: false },
                        { text: 'Tủ sách tài liệu & Sự kiện dòng họ', active: false },
                        { text: 'Phê duyệt đề xuất của con cháu', active: false }
                    ],
                    badgeClass: 'bg-slate-700'
                },
                'hung-thinh': {
                    name: 'Gói Hưng Thịnh',
                    tag: 'Đặc quyền',
                    price: '250,000đ',
                    priceVal: 250000,
                    duration: '/ Năm',
                    desc: 'Giải pháp toàn diện cho các dòng tộc quy mô trung bình.',
                    stat1_title: 'Tối đa 10 đời',
                    stat1_desc: 'Số thế hệ cho phép',
                    stat2_title: '500 thành viên',
                    stat2_desc: 'Quy mô chi họ vừa',
                    stat3_title: 'Tủ sách & Nhật ký',
                    stat3_desc: 'Quản trị & lưu trữ chuyên nghiệp',
                    highlights: [
                        {
                            title: 'Quy mô chi họ đến 500 thành viên',
                            icon: 'bx bx-group',
                            desc: 'Mở rộng không gian lưu trữ cho tối đa 10 thế hệ, đáp ứng hoàn hảo nhu cầu của các dòng họ có quy mô trung bình.'
                        },
                        {
                            title: 'Tủ sách tài liệu số hóa dòng họ',
                            icon: 'bx bx-book-bookmark',
                            desc: 'Lưu trữ tài liệu cổ, hình ảnh gia tộc, sắc phong và các văn khấn trang nghiêm. Con cháu mọi nơi đều có thể tiếp cận.'
                        },
                        {
                            title: 'Quyền Trưởng tộc kiểm duyệt',
                            icon: 'bx bx-shield-quarter',
                            desc: 'Hệ thống phê duyệt đề xuất từ con cháu giúp Trưởng tộc kiểm soát chặt chẽ và đảm bảo tính chính xác tuyệt đối của phả hệ.'
                        },
                        {
                            title: 'Nhật ký thao tác minh bạch',
                            icon: 'bx bx-history',
                            desc: 'Ghi lại toàn bộ lịch sử chỉnh sửa, cập nhật thông tin thành viên, giúp dòng tộc quản lý lịch sử vận hành cây gia phả.'
                        }
                    ],
                    features: [
                        { text: 'Tối đa 10 đời (thế hệ)', active: true },
                        { text: 'Tối đa 500 thành viên dòng tộc', active: true },
                        { text: 'Sơ đồ cây tương tác nâng cao đa chiều', active: true },
                        { text: 'Nhật ký hoạt động & Thao tác lịch sử', active: true },
                        { text: 'Tủ sách tài liệu dòng họ lưu trữ số', active: true },
                        { text: 'Phê duyệt đề xuất thông tin của con cháu', active: true },
                        { text: 'Thông báo tự động & VIP hỗ trợ', active: false },
                        { text: 'Xuất cây phả hệ PDF khổ lớn', active: false }
                    ],
                    badgeClass: 'bg-gold text-dark'
                },
                'truong-ton': {
                    name: 'Gói Trường Tồn',
                    tag: 'Vĩnh cửu',
                    price: '500,000đ',
                    priceVal: 500000,
                    duration: '/ Năm',
                    desc: 'Không giới hạn đặc quyền dành cho đại gia tộc lớn nhiều chi nhánh.',
                    stat1_title: 'Vô hạn số đời',
                    stat1_desc: 'Không giới hạn số thế hệ',
                    stat2_title: 'Vô hạn thành viên',
                    stat2_desc: 'Không giới hạn quy mô dòng tộc',
                    stat3_title: 'Hỗ trợ VIP 24/7',
                    stat3_desc: 'Kỹ thuật viên chuyên nghiệp hỗ trợ',
                    highlights: [
                        {
                            title: 'Đặc quyền Vô hạn số đời & Thành viên',
                            icon: 'bx bx-infinite',
                            desc: 'Giải pháp tối thượng dành cho các đại gia tộc lớn, không giới hạn số thế hệ và thành viên, kết nối tất cả các chi nhánh.'
                        },
                        {
                            title: 'Xuất cây phả hệ PDF khổ lớn để in ấn',
                            icon: 'bx bx-download',
                            desc: 'Hỗ trợ xuất sơ đồ phả hệ toàn gia tộc ra định dạng PDF độ phân giải cao để in trên vải/giấy treo trang nghiêm tại nhà thờ tổ.'
                        },
                        {
                            title: 'Hệ thống nhắc nhở Giỗ Tổ tự động',
                            icon: 'bx bx-bell',
                            desc: 'Tự động gửi thông báo nhắc nhở ngày giỗ, sự kiện trọng đại của dòng tộc qua email/sms đến toàn bộ con cháu trong cây phả hệ.'
                        },
                        {
                            title: 'Đội ngũ chuyên gia VIP hỗ trợ 24/7',
                            icon: 'bx bx-support',
                            desc: 'Kỹ thuật viên cao cấp đồng hành cùng dòng tộc trong việc thiết lập sơ đồ ban đầu, nhập liệu và bảo trì hệ thống bảo mật.'
                        }
                    ],
                    features: [
                        { text: 'Không giới hạn số đời (thế hệ)', active: true },
                        { text: 'Không giới hạn thành viên dòng tộc', active: true },
                        { text: 'Tất cả đặc quyền Quản trị cao cấp nhất', active: true },
                        { text: 'Quản lý sự kiện, lễ giỗ Tổ họ trang nghiêm', active: true },
                        { text: 'Hệ thống tự động gửi thông báo qua email/sms', active: true },
                        { text: 'Xuất cây phả hệ PDF in khổ lớn sắc nét', active: true },
                        { text: 'Hỗ trợ kỹ thuật VIP 24/7 từ chuyên gia', active: true }
                    ],
                    badgeClass: 'bg-purple-600 text-white'
                }
            }
        }
    },

    computed: {
        currentGoiKey() {
            return this.$route.query.goi;
        },
        currentPackage() {
            if (this.currentGoiKey && this.packages[this.currentGoiKey]) {
                return this.packages[this.currentGoiKey];
            }
            return null;
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

/* ================= PREMIUM DETAILED PACKAGE STYLES ================= */
.border-gold-soft {
    border: 1px solid rgba(212, 175, 55, 0.25) !important;
    background: rgba(15, 23, 42, 0.75) !important;
    box-shadow: 0 15px 35px rgba(212, 175, 55, 0.05) !important;
}

.btn-gold-premium {
    background: linear-gradient(135deg, #ffd700, #ff8c00);
    color: #0f172a !important;
    font-weight: 800;
    border: none;
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
}

.btn-gold-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.5);
    background: linear-gradient(135deg, #ffe033, #ffa500);
}

.max-width-800 {
    max-width: 800px;
}

.text-muted {
    color: #64748b !important;
}

.text-decoration-line-through {
    text-decoration: line-through !important;
}

.fs-7 {
    font-size: 0.85rem !important;
}

.opacity-50 {
    opacity: 0.5 !important;
}

.bg-slate-700 {
    background-color: rgba(71, 85, 105, 0.25) !important;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.bg-gold {
    background-color: #d4af37 !important;
    color: #0f172a !important;
}

.bg-purple-600 {
    background-color: #7c3aed !important;
}

.text-gold {
    color: #d4af37 !important;
}

/* Micro-animations */
.animate-fade-in {
    animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ================= NEW EXCLUSIVE DETAILS STYLES ================= */
.border-gold-soft-hover {
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.border-gold-soft-hover:hover {
    border-color: rgba(212, 175, 55, 0.4) !important;
    box-shadow: 0 10px 30px rgba(212, 175, 55, 0.08) !important;
    transform: translateY(-4px);
}
.highlight-icon-wrapper {
    background: rgba(212, 175, 55, 0.1);
    color: #d4af37;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    border: 1px solid rgba(212, 175, 55, 0.2);
}
.highlight-title {
    font-size: 1.15rem;
    font-weight: 700;
}
.highlight-desc {
    font-size: 0.95rem;
    line-height: 1.5;
}

.badge-blue-outline {
    border: 1px solid rgba(56, 189, 248, 0.4);
    color: #38bdf8;
    background: rgba(56, 189, 248, 0.05);
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 99px;
}
.badge-purple-outline {
    border: 1px solid rgba(192, 132, 252, 0.4);
    color: #c084fc;
    background: rgba(192, 132, 252, 0.05);
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 99px;
}
.border-blue-soft {
    border: 1px solid rgba(56, 189, 248, 0.2) !important;
    background: rgba(15, 23, 42, 0.75) !important;
}
.border-purple-soft {
    border: 1px solid rgba(192, 132, 252, 0.2) !important;
    background: rgba(15, 23, 42, 0.75) !important;
}
.flow-step-card {
    position: relative;
    overflow: visible;
    transition: all 0.3s ease;
}
.flow-step-card:hover {
    transform: translateY(-5px);
    border-color: rgba(56, 189, 248, 0.5) !important;
    box-shadow: 0 12px 30px rgba(56, 189, 248, 0.08);
}
.step-badge {
    position: absolute;
    top: -15px;
    left: 20px;
    width: 32px;
    height: 32px;
    background: #38bdf8;
    color: #0f172a;
    font-weight: 800;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    box-shadow: 0 4px 10px rgba(56, 189, 248, 0.3);
}
.step-icon-wrapper {
    background: rgba(56, 189, 248, 0.1);
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
.custom-feature-card {
    border-radius: 20px;
    transition: all 0.3s ease;
}
.custom-feature-card:hover {
    border-color: rgba(255, 255, 255, 0.15) !important;
}
.library-mockup-list .mockup-item {
    transition: all 0.2s ease;
}
.library-mockup-list .mockup-item:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    transform: translateX(4px);
}
.workflow-steps {
    position: relative;
}
.workflow-step {
    padding-left: 24px;
}
.workflow-dot {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #d4af37;
    border: 2px solid #0f172a;
    box-shadow: 0 0 10px #d4af37;
}
.icon-circle-purple {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(192, 132, 252, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(192, 132, 252, 0.2);
}
.sms-preview-card {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
}
.fs-8 {
    font-size: 0.8rem !important;
}
.fs-9 {
    font-size: 0.72rem !important;
}
</style>