<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="container-custom content-relative pt-5 pb-5">
      <div class="glass-card shadow-2xl rounded-3xl overflow-hidden mt-5">
        <div class="checkout-header p-5 text-center">
          <h2 class="text-gradient fw-bold mb-2">Thăng Cấp Tài Khoản Đối Tác</h2>
          <p class="text-white-50">Mở khóa quyền Trưởng tộc/Trưởng chi để khởi tạo, xây dựng cây gia phả trực tuyến và dẫn dắt dòng tộc.</p>
        </div>

        <div class="card-body p-4 p-md-5 pt-0">
          <div class="row g-5">
            <!-- Left Column: Package Details -->
            <div class="col-lg-6">
              <div class="package-info-section h-100">
                <h4 class="text-gold mb-4 fw-bold"><i class="bx bx-package me-2"></i>Thông tin gói dịch vụ</h4>

                <div class="glass-info p-4 rounded-2xl border border-white/10 mb-4">
                  <div class="info-row">
                    <span>Gói đăng ký:</span>
                    <strong class="text-white">{{ ten_goi || 'Gói Đối Tác Quản Trị Gia Phả' }}</strong>
                  </div>
                  <div class="info-row">
                    <span>Thời gian sử dụng:</span>
                    <strong class="text-white">
                      {{ currentPkg ? currentPkg.thoi_han + ' tháng' : '12 tháng' }} (Tự động cộng dồn khi gia hạn)
                    </strong>
                  </div>
                  <div class="info-row border-0">
                    <span>Số tiền thanh toán:</span>
                    <strong class="text-gold fs-4">{{ form.so_tien !== null && form.so_tien !== undefined ? form.so_tien.toLocaleString() : '100,000' }} VNĐ</strong>
                  </div>
                </div>

                <!-- Auto-approve notice -->
                <div class="auto-approve-notice p-3 rounded-2xl mb-4 d-flex align-items-start gap-3">
                  <i class="bx bx-bolt-circle text-success fs-4 mt-1 flex-shrink-0"></i>
                  <div>
                    <strong class="text-white d-block mb-1">Kích hoạt tức thì!</strong>
                    <span class="text-white-50 small">Tài khoản đối tác được kích hoạt <strong class="text-success">ngay lập tức</strong> sau khi chuyển khoản đúng số tiền — không cần chờ phê duyệt thủ công.</span>
                  </div>
                </div>

                <div class="benefits-list mt-4 flex-grow-1 d-flex flex-column" style="max-height: 400px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: rgba(212, 175, 55, 0.3) transparent; padding-right: 5px;">
                  <h6 class="text-white-50 small fw-bold text-uppercase mb-3">Tính Năng Đặc Quyền Gói {{ ten_goi }}</h6>
                  
                  <!-- Loading state -->
                  <div v-if="isLoadingPackages" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                    <span class="text-white-50 small ms-2">Đang tải tính năng gói...</span>
                  </div>

                  <!-- Dynamic features list -->
                  <div v-else-if="currentPkg" class="pe-1">
                    <div class="benefit-item mb-3 d-flex align-items-start animate__animated animate__fadeIn">
                      <i class="bx bx-check-circle text-success me-2 fs-5 mt-0.5"></i>
                      <div>
                        <strong class="text-white d-block">Giới hạn thế hệ (Đời)</strong>
                        <span class="text-white-50 small">Tối đa <strong>{{ currentPkg.max_doi >= 999 ? 'Không giới hạn' : currentPkg.max_doi + ' đời' }}</strong></span>
                      </div>
                    </div>

                    <div class="benefit-item mb-3 d-flex align-items-start animate__animated animate__fadeIn">
                      <i class="bx bx-check-circle text-success me-2 fs-5 mt-0.5"></i>
                      <div>
                        <strong class="text-white d-block">Giới hạn thành viên</strong>
                        <span class="text-white-50 small">Tối đa <strong>{{ currentPkg.max_thanh_vien >= 99999 ? 'Không giới hạn' : currentPkg.max_thanh_vien + ' thành viên' }}</strong></span>
                      </div>
                    </div>

                    <div v-for="feat in getItemFeatures(currentPkg)" :key="feat.key" class="benefit-item mb-3 d-flex align-items-start animate__animated animate__fadeIn">
                      <i :class="feat.icon || 'bx bx-check-circle'" class="text-success me-2 fs-5 mt-0.5"></i>
                      <div>
                        <strong class="text-white d-block">{{ feat.label }}</strong>
                        <span class="text-white-50 small">Tính năng đã được kích hoạt trong gói.</span>
                      </div>
                    </div>
                  </div>

                  <!-- Fallback static features if not found -->
                  <div v-else class="pe-1">
                    <div class="benefit-item mb-3 d-flex align-items-start">
                      <i class="bx bxs-check-shield text-warning me-2 fs-5 mt-0.5"></i>
                      <div>
                        <strong class="text-white d-block">Quyền Trưởng tộc / Quản trị viên</strong>
                        <span class="text-white-50 small">Khởi tạo và toàn quyền thiết lập sơ đồ cây gia phả số dòng họ.</span>
                      </div>
                    </div>
                    <div class="benefit-item mb-3 d-flex align-items-start">
                      <i class="bx bx-check-circle text-success me-2 fs-5 mt-0.5"></i>
                      <div>
                        <strong class="text-white d-block">Thành viên Không Giới Hạn</strong>
                        <span class="text-white-50 small">Thêm mới, sửa đổi không giới hạn số đời và số lượng con cháu.</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column: Success State OR QR Payment -->
            <div class="col-lg-6">

              <!-- ✅ SUCCESS: Tài khoản đã được kích hoạt -->
              <div class="payment-section h-100 d-flex flex-column justify-content-center align-items-stretch" v-if="isActivated">
                <div class="glass-card-success text-center p-5 rounded-3xl position-relative overflow-hidden">
                  <div class="success-glow"></div>
                  <div class="success-icon-wrap mb-4 mx-auto d-flex align-items-center justify-content-center">
                    <div class="success-icon-circle d-flex align-items-center justify-content-center">
                      <i class="bx bx-check-circle text-success fs-1"></i>
                    </div>
                    <div class="success-pulse-circle"></div>
                  </div>

                  <h4 class="text-gradient-green fw-bold mb-3">Kích Hoạt Thành Công!</h4>
                  <p class="text-white-50 px-3 small mb-4">
                    Tài khoản Đối Tác của bạn đã được kích hoạt tức thì. Bạn đang được chuyển đến trang quản lý...
                  </p>

                  <div class="glass-info p-4 rounded-2xl border border-white/10 text-start my-4">
                    <div class="info-row">
                      <span>Gói kích hoạt:</span>
                      <strong class="text-white">{{ activatedPackage }}</strong>
                    </div>
                    <div class="info-row border-0">
                      <span>Hiệu lực đến:</span>
                      <strong class="text-success">{{ activatedExpiry }}</strong>
                    </div>
                  </div>

                  <div class="countdown-bar-wrap mt-3">
                    <div class="countdown-bar" :style="{ width: countdownPct + '%' }"></div>
                  </div>
                  <p class="text-white-50 small mt-2">Chuyển hướng sau {{ countdownSec }} giây...</p>

                  <button class="btn-gradient-success w-100 mt-3" @click="goToDashboard">
                    <i class="bx bx-rocket me-2"></i> Vào Trang Quản Lý Ngay
                  </button>
                </div>
              </div>

              <!-- ⚠️ INSUFFICIENT: Thiếu tiền -->
              <div class="payment-section h-100 d-flex flex-column justify-content-center align-items-stretch" v-else-if="insufficientError">
                <div class="glass-card-error text-center p-5 rounded-3xl position-relative overflow-hidden">
                  <div class="error-glow"></div>
                  <i class="bx bx-error-circle text-danger fs-1 mb-3 d-block"></i>
                  <h4 class="text-danger fw-bold mb-3">Số Tiền Chưa Đủ</h4>
                  <p class="text-white-50 px-3 small mb-4">{{ insufficientError }}</p>

                  <div class="glass-info p-3 rounded-2xl border border-danger/20 text-start mb-4">
                    <div class="info-row">
                      <span>Số tiền gói:</span>
                      <strong class="text-white">{{ form.so_tien !== null && form.so_tien !== undefined ? form.so_tien.toLocaleString() : '' }} VNĐ</strong>
                    </div>
                    <div class="info-row border-0">
                      <span>Nội dung CK:</span>
                      <strong class="text-warning">{{ transferContent }}</strong>
                    </div>
                  </div>

                  <button class="btn-gradient-submit w-100" @click="insufficientError = null; submitPayment()">
                    <i class="bx bx-refresh me-2"></i> Kiểm Tra Lại Giao Dịch
                  </button>
                  <p class="text-white-50 small mt-3">
                    <i class="bx bx-info-circle me-1"></i>Vui lòng chuyển thêm số tiền còn thiếu với <strong class="text-warning">đúng nội dung chuyển khoản</strong> rồi bấm kiểm tra lại.
                  </p>
                </div>
              </div>

              <!-- 📱 QR Payment Form or Free Activation (mặc định) -->
              <div class="payment-section" v-else>
                <!-- TH luồng Gói Dùng Thử 0đ -->
                <div v-if="form.so_tien === 0" class="free-activation-section text-center p-4 rounded-3xl">
                  <h4 class="text-gold mb-3 fw-bold"><i class="bx bx-gift me-2"></i>Kích Hoạt Gói Dùng Thử Miễn Phí</h4>
                  
                  <div class="glass-info p-4 rounded-2xl border border-white/10 text-start mb-4">
                    <p class="text-white-50 mb-0 small">
                      Gói dịch vụ này hoàn toàn <strong>Miễn phí / Dùng thử</strong>. Bạn không cần thực hiện bất kỳ giao dịch chuyển khoản nào. 
                      Hãy bấm nút bên dưới để hệ thống tự động kích hoạt và mở khóa quyền đối tác cho bạn ngay lập tức!
                    </p>
                  </div>

                  <form @submit.prevent="submitPayment">
                    <button type="submit" class="btn-gradient-submit w-100" :disabled="isSubmitting">
                      <span v-if="!isSubmitting"><i class="bx bx-rocket me-2"></i> KÍCH HOẠT GÓI MIỄN PHÍ NGAY</span>
                      <span v-else><i class="bx bx-loader-alt bx-spin me-2"></i> ĐANG XỬ LÝ...</span>
                    </button>
                  </form>
                </div>

                <!-- TH luồng Gói Trả Phí (> 0) -->
                <div v-else>
                  <h4 class="text-gold mb-4 fw-bold"><i class="bx bx-qr-scan me-2"></i>Thanh toán nhanh qua mã QR</h4>

                  <div class="qr-payment-section text-center p-4 rounded-3xl mb-4">
                    <div class="qr-image-wrapper mx-auto mb-4">
                      <img :src="qrUrl" alt="QR Code" class="img-fluid rounded-2xl border-2 border-white/10 p-2 bg-white shadow-glow" v-if="form.so_tien">
                      <div v-else class="qr-placeholder rounded-2xl border-2 border-dashed border-white/20 d-flex align-items-center justify-content-center bg-white/5">
                        <span class="text-white-50">Đang tải mã QR thanh toán...</span>
                      </div>
                    </div>

                    <div class="bank-details text-start glass-info p-4 rounded-2xl border border-white/10">
                      <div class="info-row"><span>Ngân hàng thụ hưởng:</span><strong>MB Bank (Quân Đội)</strong></div>
                      <div class="info-row"><span>Số tài khoản:</span><strong class="text-gold">0342211914</strong></div>
                      <div class="info-row"><span>Chủ tài khoản:</span><strong>TRAN HUU HIEU</strong></div>
                      <div class="info-row border-0">
                        <span>Nội dung chuyển khoản:</span>
                        <strong class="text-warning">{{ transferContent }}</strong>
                        <button type="button" class="btn-copy ms-2" @click="copyContent">
                          <i class="bx bx-copy"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  <form @submit.prevent="submitPayment">
                    <button type="submit" class="btn-gradient-submit w-100" :disabled="isSubmitting || !form.so_tien">
                      <span v-if="!isSubmitting"><i class="bx bx-check-circle me-2"></i> XÁC NHẬN ĐÃ CHUYỂN KHOẢN THANH TOÁN</span>
                      <span v-else><i class="bx bx-loader-alt bx-spin me-2"></i> ĐANG KIỂM TRA GIAO DỊCH...</span>
                    </button>
                  </form>

                  <div class="auto-verify-note text-center mt-3 p-3 rounded-2xl">
                    <i class="bx bx-bolt-circle text-success me-1"></i>
                    <span class="text-white-50 small">Hệ thống sẽ <strong class="text-success">tự động kiểm tra</strong> và kích hoạt tài khoản ngay sau khi xác nhận giao dịch đủ tiền.</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="security-note text-center mt-5">
        <div class="d-inline-flex align-items-center gap-5">
          <span class="note-item"><i class="bx bx-lock-alt"></i> Bảo mật tư liệu</span>
          <span class="note-item"><i class="bx bx-bolt-circle"></i> Kích hoạt tức thì</span>
          <span class="note-item"><i class="bx bx-shield-quarter"></i> Thanh toán an toàn</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

const FEATURE_MAP = {
  tao_cay_gia_pha:     { label: 'Tạo Cây Gia Phả',       icon: 'bx bx-sitemap' },
  them_thanh_vien:     { label: 'Thêm Thành Viên',        icon: 'bx bx-user-plus' },
  sua_xoa_thanh_vien:  { label: 'Sửa/Xóa Thành Viên',    icon: 'bx bx-edit' },
  quan_ly_vo_chong:    { label: 'Quản Lý Vợ/Chồng',      icon: 'bx bx-heart' },
  quan_ly_con_nuoi:    { label: 'Quản Lý Con Nuôi',       icon: 'bx bx-user' },
  xuat_pdf:            { label: 'Xuất PDF Gia Phả',       icon: 'bx bx-file-pdf' },
  phe_duyet_de_xuat:   { label: 'Phê Duyệt Đề Xuất',     icon: 'bx bx-check-circle' },
  tu_dong_phe_duyet:   { label: 'Tự Động Phê Duyệt',     icon: 'bx bx-check-double' },
  quan_ly_chi_nhanh:   { label: 'Quản Lý Chi Nhánh',     icon: 'bx bx-git-branch' },
  phan_quyen_thanh_vien:{ label: 'Phân Quyền Thành Viên', icon: 'bx bx-shield' },
  nhat_ky_hoat_dong:   { label: 'Nhật Ký Hoạt Động',     icon: 'bx bx-history' },
  quan_ly_su_kien:     { label: 'Quản Lý Sự Kiện',       icon: 'bx bx-calendar' },
  dang_ky_su_kien:     { label: 'Đăng Ký Sự Kiện',       icon: 'bx bx-calendar-check' },
  tuong_niem:          { label: 'Tưởng Niệm',             icon: 'bx bx-moon' },
  nhac_gio_tu:         { label: 'Nhắc Ngày Giỗ Tự',      icon: 'bx bx-bell' },
  quan_ly_tai_lieu:    { label: 'Tủ Sách Tài Liệu',      icon: 'bx bx-folder' },
  upload_hinh_anh:     { label: 'Upload Hình Ảnh',        icon: 'bx bx-image' },
  album_gia_dinh:      { label: 'Album Gia Đình',         icon: 'bx bx-images' },
  ban_do_mo_phan:      { label: 'Bản Đồ Mộ Phần',        icon: 'bx bx-map-pin' },
  ban_do_nha_tho:      { label: 'Bản Đồ Nhà Thờ Họ',     icon: 'bx bx-map' },
  tra_cuu_ban_do:      { label: 'Tra Cứu Bản Đồ',        icon: 'bx bx-search-alt' },
  quan_ly_dong_gop:    { label: 'Quản Lý Đóng Góp',      icon: 'bx bx-donate-heart' },
  bao_cao_tai_chinh:   { label: 'Báo Cáo Tài Chính',     icon: 'bx bx-bar-chart' },
  tra_cuu_quan_he:     { label: 'Tra Cứu Quan Hệ',       icon: 'bx bx-search' },
  xuat_csv:            { label: 'Xuất Dữ Liệu CSV',      icon: 'bx bx-spreadsheet' },
  thong_ke_nang_cao:   { label: 'Thống Kê Nâng Cao',     icon: 'bx bx-analyse' },
  api_tich_hop:        { label: 'API Tích Hợp',           icon: 'bx bx-code-alt' }
};

export default {
  name: 'MuaGoi',
  data() {
    return {
      userName: '',
      form: {
        so_tien: 100000,
        phuong_thuc: 'bank'
      },
      ten_goi: 'Gói Đối Tác Quản Trị Gia Phả',
      isSubmitting: false,
      checkInterval: null,
      paymentCode: null,
      // Trạng thái mới
      isActivated: false,
      activatedPackage: '',
      activatedExpiry: '',
      insufficientError: null,
      // Countdown sau khi kích hoạt
      countdownSec: 5,
      countdownPct: 100,
      countdownTimer: null,
      
      // Dynamic packages
      listPackages: [],
      isLoadingPackages: false
    };
  },
  computed: {
    cleanAmount() {
      if (!this.form.so_tien) return '';
      return this.form.so_tien.toString().replace(/[^\d]/g, '');
    },
    transferContent() {
      let name = 'KH';
      if (this.userName) {
        name = this.userName.split(' ').pop().toUpperCase();
        name = name.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/Đ/g, 'D');
      }
      return `MUAGOI ${name}${this.paymentCode}`;
    },
    qrUrl() {
      if (!this.cleanAmount) return '';
      const amount = this.cleanAmount;
      const desc = encodeURIComponent(this.transferContent);
      return `https://qr.sepay.vn/img?bank=MBBank&acc=0342211914&template=compact&amount=${amount}&des=${desc}`;
    },
    currentPkg() {
      return this.listPackages.find(p => p.ten_goi === this.ten_goi);
    }
  },

  mounted() {
    const userStr = localStorage.getItem('user');
    const user = userStr ? JSON.parse(userStr) : null;
    const userData = user ? (user.user || user) : null;
    if (userData) {
      this.userName = userData.ho_ten || '';
      const storageKey = `payment_code_${userData.id}`;
      let savedCode = localStorage.getItem(storageKey);
      if (!savedCode) {
        savedCode = Math.floor(10000 + Math.random() * 90000);
        localStorage.setItem(storageKey, savedCode);
      }
      this.paymentCode = savedCode;
    }

    if (this.$route.query.so_tien) {
      this.form.so_tien = Number(this.$route.query.so_tien);
    }
    if (this.$route.query.ten_goi) {
      this.ten_goi = this.$route.query.ten_goi;
    }

    // Kiểm tra nếu đã là đối tác rồi và không có ý định mua gói mới thì mới redirect
    if (userData && (parseInt(userData.is_doi_tac) === 1) && !this.$route.query.ten_goi) {
      this.$router.replace('/doi-tac/dashboard');
      return;
    }

    // Tải thông tin các gói dịch vụ để hiển thị động
    this.loadPackages();

    // Bắt đầu auto-check mỗi 8 giây (kiểm tra silent)
    this.startAutoCheck();
  },
  beforeUnmount() {
    this.stopAutoCheck();
    this.stopCountdown();
  },
  methods: {
    getItemFeatures(pkg) {
      const feats = pkg.features || '';
      if (!feats) return [];
      const keys = typeof feats === 'string' ? feats.split(',').map(s => s.trim()) : feats;
      return keys.map(k => {
        const item = FEATURE_MAP[k];
        return item ? { key: k, ...item } : null;
      }).filter(Boolean);
    },
    loadPackages() {
      this.isLoadingPackages = true;
      axios.get('http://127.0.0.1:8000/api/goi-dich-vu/get-data')
        .then(res => {
          if (res.data.status) {
            this.listPackages = res.data.data.filter(p => p.trang_thai === 'Hoạt động');
          }
        })
        .catch(err => {
          this.listPackages = [
            { id: 1, ten_goi: 'Gói Khởi Tạo', gia_ca: 100000, thoi_han: 12, max_doi: 3, max_thanh_vien: 50, mo_ta: 'Phù hợp cho chi ngành nhỏ hoặc dòng tộc bắt đầu số hóa phả hệ.', trang_thai: 'Hoạt động', features: 'tao_cay_gia_pha,them_thanh_vien,sua_xoa_thanh_vien,quan_ly_vo_chong,quan_ly_con_nuoi,quan_ly_tai_lieu,upload_hinh_anh' },
            { id: 2, ten_goi: 'Gói Hưng Thịnh', gia_ca: 3000000, thoi_han: 12, max_doi: 10, max_thanh_vien: 500, mo_ta: 'Giải pháp toàn diện cho các dòng tộc quy mô trung bình.', trang_thai: 'Hoạt động', features: 'tao_cay_gia_pha,them_thanh_vien,sua_xoa_thanh_vien,quan_ly_vo_chong,quan_ly_con_nuoi,quan_ly_tai_lieu,upload_hinh_anh,xuat_pdf,phe_duyet_de_xuat,quan_ly_chi_nhanh,nhat_ky_hoat_dong,quan_ly_su_kien,dang_ky_su_kien,tuong_niem,nhac_gio_tu,album_gia_dinh,ban_do_mo_phan,ban_do_nha_tho,tra_cuu_ban_do,quan_ly_dong_gop,bao_cao_tai_chinh,tra_cuu_quan_he' },
            { id: 3, ten_goi: 'Gói Trường Tồn', gia_ca: 5000000, thoi_han: 12, max_doi: 99, max_thanh_vien: 10000, mo_ta: 'Không giới hạn đặc quyền dành cho đại gia tộc lớn nhiều chi nhánh.', trang_thai: 'Hoạt động', features: 'tao_cay_gia_pha,them_thanh_vien,sua_xoa_thanh_vien,quan_ly_vo_chong,quan_ly_con_nuoi,quan_ly_tai_lieu,upload_hinh_anh,xuat_pdf,phe_duyet_de_xuat,quan_ly_chi_nhanh,nhat_ky_hoat_dong,quan_ly_su_kien,dang_ky_su_kien,tuong_niem,nhac_gio_tu,album_gia_dinh,ban_do_mo_phan,ban_do_nha_tho,tra_cuu_ban_do,quan_ly_dong_gop,bao_cao_tai_chinh,tra_cuu_quan_he,xuat_csv,thong_ke_nang_cao,api_tich_hop,tu_dong_phe_duyet,phan_quyen_thanh_vien' }
          ];
        })
        .finally(() => {
          this.isLoadingPackages = false;
        });
    },
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },

    startAutoCheck() {
      this.checkInterval = setInterval(() => {
        if (this.cleanAmount > 0 && !this.isSubmitting && !this.isActivated) {
          this.checkPaymentSilent();
        }
      }, 8000);
    },
    stopAutoCheck() {
      if (this.checkInterval) {
        clearInterval(this.checkInterval);
        this.checkInterval = null;
      }
    },

    checkPaymentSilent() {
      const userStr = localStorage.getItem('user');
      const user = userStr ? JSON.parse(userStr) : null;
      const userId = user ? (user.user || user).id : null;
      if (!userId) return;

      axios.post('http://127.0.0.1:8000/api/thanh-toan/xac-nhan-thanh-toan', {
        nguoi_dung_id: userId,
        noi_dung: this.transferContent + ' | Mua gói dịch vụ: ' + this.form.so_tien + ' VNĐ | QR Đối Tác',
        so_tien: this.form.so_tien,
        ten_goi: this.ten_goi
      }, this.getHeaders())
      .then(res => {
        if (res.data.success && res.data.is_partner) {
          this.stopAutoCheck();
          this.handleActivationSuccess(res.data, user);
        }
      })
      .catch(() => {});
    },

    copyContent() {
      navigator.clipboard.writeText(this.transferContent);
      toastr.success('Đã sao chép nội dung thanh toán gói!');
    },

    submitPayment() {
      if (this.form.so_tien !== 0 && !this.cleanAmount) {
        toastr.warning('Vui lòng nhập số tiền hợp lệ!');
        return;
      }

      const token = localStorage.getItem('access_token');
      if (!token) {
        toastr.warning('Vui lòng đăng nhập!');
        this.$router.push('/login');
        return;
      }

      this.isSubmitting = true;
      this.insufficientError = null;

      const userStr = localStorage.getItem('user');
      const user = userStr ? JSON.parse(userStr) : null;
      const userId = user ? (user.user || user).id : null;

      axios.post('http://127.0.0.1:8000/api/thanh-toan/xac-nhan-thanh-toan', {
        nguoi_dung_id: userId,
        noi_dung: this.transferContent + ' | Mua gói dịch vụ: ' + this.form.so_tien + ' VNĐ | QR Đối Tác',
        so_tien: this.form.so_tien,
        ten_goi: this.ten_goi
      }, this.getHeaders())
      .then(res => {
        if (res.data.success && res.data.is_partner) {
          this.stopAutoCheck();
          this.handleActivationSuccess(res.data, user);
        } else if (!res.data.success) {
          toastr.error(res.data.message || 'Chưa tìm thấy giao dịch. Vui lòng kiểm tra lại.');
        }
      })
      .catch(err => {
        const msg = err.response?.data?.message || '';
        // Kiểm tra lỗi thiếu tiền (status 400)
        if (err.response?.status === 400 && msg) {
          this.insufficientError = msg;
        } else {
          toastr.error(msg || 'Có lỗi xảy ra trong quá trình kiểm tra giao dịch!');
        }
      })
      .finally(() => { this.isSubmitting = false; });
    },

    handleActivationSuccess(data, user) {
      // Cập nhật localStorage
      if (user) {
        if (user.user) user.user.is_doi_tac = 1;
        else user.is_doi_tac = 1;
        localStorage.setItem('user', JSON.stringify(user));
      }
      window.dispatchEvent(new Event('storage'));

      this.activatedPackage = data.ten_goi || this.ten_goi;
      this.activatedExpiry  = data.ngay_ket_thuc
        ? new Date(data.ngay_ket_thuc).toLocaleDateString('vi-VN')
        : '';
      this.isActivated = true;

      toastr.success('🎉 Tài khoản Đối Tác đã được kích hoạt thành công!');
      this.startCountdown();
    },

    startCountdown() {
      this.countdownSec = 5;
      this.countdownPct = 100;
      this.countdownTimer = setInterval(() => {
        this.countdownSec--;
        this.countdownPct = (this.countdownSec / 5) * 100;
        if (this.countdownSec <= 0) {
          this.stopCountdown();
          this.goToDashboard();
        }
      }, 1000);
    },
    stopCountdown() {
      if (this.countdownTimer) {
        clearInterval(this.countdownTimer);
        this.countdownTimer = null;
      }
    },
    goToDashboard() {
      this.stopCountdown();
      window.location.href = '/doi-tac/dashboard';
    }
  }
};
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
        url('@/assets/images/bg_product.webp');
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

/* Glass Card */
.glass-card {
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.text-gradient {
    background: linear-gradient(90deg, #fbff00, #ff8c00);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.text-gradient-gold {
    background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.text-gradient-green {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.text-gold { color: #ffd700; }

/* Auto-approve Notice */
.auto-approve-notice {
    background: rgba(16, 185, 129, 0.06);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

/* QR Section */
.qr-payment-section {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.qr-image-wrapper { max-width: 240px; }
.shadow-glow { box-shadow: 0 0 30px rgba(255, 255, 255, 0.1); }
.qr-placeholder { height: 240px; }

/* Bank Details */
.glass-info { background: rgba(0, 0, 0, 0.2); }

.info-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    font-size: 0.95rem;
}

.info-row span { color: #94a3b8; }
.info-row strong { color: #f1f5f9; }

.btn-copy {
    background: rgba(255, 255, 255, 0.1);
    border: none; color: #ffd700;
    padding: 4px 8px; border-radius: 6px;
    cursor: pointer; transition: 0.2s;
}
.btn-copy:hover { background: #ffd700; color: #000; }

.benefit-item { font-size: 0.9rem; color: #cbd5e1; }

/* Submit Button */
.btn-gradient-submit {
    padding: 16px;
    background: linear-gradient(135deg, #0a3c88, #6610f2);
    color: white;
    border: none; border-radius: 14px;
    font-weight: 700; font-size: 1rem;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}
.btn-gradient-submit:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(102, 16, 242, 0.4);
    opacity: 0.9;
}
.btn-gradient-submit:disabled {
    background: #1e293b;
    color: #475569;
    cursor: not-allowed;
    box-shadow: none;
}

/* Success Button */
.btn-gradient-success {
    padding: 14px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none; border-radius: 14px;
    font-weight: 700; font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
}
.btn-gradient-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(16, 185, 129, 0.5);
}

/* Auto-verify note */
.auto-verify-note {
    background: rgba(16, 185, 129, 0.04);
    border: 1px solid rgba(16, 185, 129, 0.15);
}

/* Security Note */
.note-item {
    color: #64748b;
    font-size: 0.9rem;
    display: flex; align-items: center; gap: 8px;
}
.note-item i { font-size: 1.2rem; color: #ffd700; }

/* ── SUCCESS STATE ─────────────────────────────────────────── */
.glass-card-success {
    background: rgba(16, 185, 129, 0.04);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(16, 185, 129, 0.2) !important;
}

.success-glow {
    position: absolute;
    top: -60px; right: -60px; width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
    z-index: 0;
    pointer-events: none;
}

.success-icon-wrap {
    position: relative;
    width: 80px; height: 80px;
}

.success-icon-circle {
    position: relative; z-index: 2;
    width: 80px; height: 80px;
    background: rgba(16, 185, 129, 0.1);
    border: 2px solid rgba(16, 185, 129, 0.3);
    border-radius: 50%;
}

.success-pulse-circle {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    border: 2px solid rgba(16, 185, 129, 0.4);
    animation: successPulse 2s infinite;
    z-index: 1;
}

@keyframes successPulse {
    0%   { transform: scale(0.95); opacity: 0.8; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
    70%  { transform: scale(1.15); opacity: 0;   box-shadow: 0 0 0 15px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); opacity: 0;   box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.countdown-bar-wrap {
    background: rgba(255,255,255,0.06);
    border-radius: 10px;
    height: 4px;
    overflow: hidden;
}
.countdown-bar {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #34d399);
    border-radius: 10px;
    transition: width 1s linear;
}

/* ── ERROR STATE ───────────────────────────────────────────── */
.glass-card-error {
    background: rgba(239, 68, 68, 0.04);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(239, 68, 68, 0.2) !important;
}

.error-glow {
    position: absolute;
    top: -60px; left: -60px; width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(239, 68, 68, 0.1) 0%, transparent 70%);
    z-index: 0;
    pointer-events: none;
}

@media (max-width: 992px) {
    .info-row { flex-direction: column; align-items: flex-start; gap: 5px; }
}
</style>
