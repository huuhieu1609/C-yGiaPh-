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
                    <strong class="text-white">01 Năm (Tự động cộng dồn khi gia hạn)</strong>
                  </div>
                  <div class="info-row border-0">
                    <span>Số tiền thanh toán:</span>
                    <strong class="text-gold fs-4">{{ form.so_tien ? form.so_tien.toLocaleString() : '100,000' }} VNĐ</strong>
                  </div>
                </div>

                <div class="benefits-list mt-4">
                  <h6 class="text-white-50 small fw-bold text-uppercase mb-3">Quyền lợi đặc quyền Trưởng chi</h6>
                  <div class="benefit-item mb-3 d-flex align-items-start">
                    <i class="bx bxs-check-shield text-warning me-2 fs-5 mt-1"></i>
                    <div>
                      <strong class="text-white d-block">Quyền Trưởng tộc / Quản trị viên</strong>
                      <span class="text-white-50 small">Khởi tạo và toàn quyền thiết lập sơ đồ cây gia phả số dòng họ.</span>
                    </div>
                  </div>
                  <div class="benefit-item mb-3 d-flex align-items-start">
                    <i class="bx bx-check-circle text-success me-2 fs-5 mt-1"></i>
                    <div>
                      <strong class="text-white d-block">Thành viên Không Giới Hạn</strong>
                      <span class="text-white-50 small">Thêm mới, sửa đổi không giới hạn số đời và số lượng con cháu.</span>
                    </div>
                  </div>
                  <div class="benefit-item mb-3 d-flex align-items-start">
                    <i class="bx bx-check-circle text-success me-2 fs-5 mt-1"></i>
                    <div>
                      <strong class="text-white d-block">Phê duyệt Đề xuất Nhanh</strong>
                      <span class="text-white-50 small">Tiếp nhận và phê duyệt nhanh các cập nhật thông tin do con cháu đề xuất.</span>
                    </div>
                  </div>
                  <div class="benefit-item mb-3 d-flex align-items-start">
                    <i class="bx bx-check-circle text-success me-2 fs-5 mt-1"></i>
                    <div>
                      <strong class="text-white d-block">Bộ Công cụ Quản trị Cao cấp</strong>
                      <span class="text-white-50 small">Kích hoạt Nhật ký thao tác bảo mật, Tủ sách tài liệu dòng họ và Quản lý sự kiện.</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column: Pending state OR Donation QR -->
            <div class="col-lg-6">
              <!-- Pending request luxury view -->
              <div class="payment-section h-100 d-flex flex-column justify-content-center align-items-stretch" v-if="hasPending">
                <div class="glass-card-pending text-center p-5 rounded-3xl border border-warning/30 shadow-2xl position-relative overflow-hidden">
                  <div class="ambient-glow"></div>
                  <div class="pending-icon-wrap mb-4 mx-auto d-flex align-items-center justify-content-center">
                    <div class="pending-icon-circle d-flex align-items-center justify-content-center">
                      <i class="bx bx-time-five text-warning fs-1"></i>
                    </div>
                    <div class="pending-pulse-circle"></div>
                  </div>
                  
                  <h4 class="text-gradient-gold fw-bold mb-3">Yêu Cầu Đang Chờ Phê Duyệt</h4>
                  <p class="text-white-50 px-3 small">
                    Hệ thống đã ghi nhận thông tin chuyển khoản nâng cấp tài khoản đối tác của bạn.
                  </p>
                  
                  <div class="glass-info p-4 rounded-2xl border border-white/10 text-start my-4">
                    <div class="info-row">
                      <span>Gói đăng ký:</span>
                      <strong class="text-white">{{ ten_goi }}</strong>
                    </div>
                    <div class="info-row">
                      <span>Trạng thái:</span>
                      <strong class="text-warning d-flex align-items-center gap-1">
                        <span class="status-pulse-dot"></span>Chờ Admin phê duyệt
                      </strong>
                    </div>
                    <div class="info-row border-0">
                      <span>Thời gian gửi:</span>
                      <strong class="text-white-50">Hôm nay</strong>
                    </div>
                  </div>

                  <p class="small text-white-50 mt-3 mb-0">
                    <i class="bx bx-shield-quarter me-1 text-warning"></i> Quyền lợi đối tác sẽ tự động được kích hoạt ngay sau khi Admin xác nhận giao dịch. Vui lòng không thực hiện thanh toán lại!
                  </p>
                </div>
              </div>

              <!-- Standard QR payment form -->
              <div class="payment-section" v-else>
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
                    <span v-else><i class="bx bx-loader-alt bx-spin me-2"></i> ĐANG GHI NHẬN GIAO DỊCH...</span>
                  </button>
                </form>
                
                <p class="text-center text-white-50 small mt-3">
                  <i class="bx bx-info-circle me-1"></i> Hệ thống sẽ ghi nhận giao dịch và gửi yêu cầu phê duyệt đến Admin.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="security-note text-center mt-5">
        <div class="d-inline-flex align-items-center gap-5">
          <span class="note-item"><i class="bx bx-lock-alt"></i> Bảo mật tư liệu</span>
          <span class="note-item"><i class="bx bx-support"></i> Hỗ trợ dòng tộc 24/7</span>
          <span class="note-item"><i class="bx bx-shield-quarter"></i> Kiểm duyệt an toàn</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
  name: 'MuaGoi',
  data() {
    return {
      userName: '',
      form: { 
        so_tien: 100000, // Standard Upgrade Price
        phuong_thuc: 'bank' 
      },
      ten_goi: 'Gói Đối Tác Quản Trị Gia Phả',
      isSubmitting: false,
      checkInterval: null,
      paymentCode: null,
      hasPending: false
    }
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

    // Check pending status on mount
    this.checkPendingStatus();
    this.startAutoCheck();
  },
  beforeUnmount() {
    this.stopAutoCheck();
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    checkPendingStatus() {
      axios.get('http://127.0.0.1:8000/api/doi-tac/check-pending', this.getHeaders())
        .then(res => {
          if (res.data.status && res.data.has_pending) {
            this.hasPending = true;
            this.stopAutoCheck();
          }
        })
        .catch(() => {});
    },
    startAutoCheck() {
      this.checkInterval = setInterval(() => {
        if (this.cleanAmount > 0 && !this.isSubmitting && !this.hasPending) {
          this.checkPaymentSilent();
        }
      }, 5000);
    },
    stopAutoCheck() {
      if (this.checkInterval) {
        clearInterval(this.checkInterval);
        this.checkInterval = null;
      }
    },
    checkPaymentSilent() {
      const token = localStorage.getItem('access_token');
      if (!token) return;

      const userStr = localStorage.getItem('user');
      const user = userStr ? JSON.parse(userStr) : null;
      const userId = user ? (user.user || user).id : null;

      axios.post('http://127.0.0.1:8000/api/thanh-toan/xac-nhan-thanh-toan', {
        nguoi_dung_id: userId,
        noi_dung: this.transferContent + ' | Mua gói dịch vụ: ' + this.form.so_tien + ' VNĐ | QR Đối Tác',
        trang_thai: 'Chờ duyệt',
        so_tien: this.form.so_tien
      }, this.getHeaders())
      .then(res => {
        if (res.data.success) {
          this.stopAutoCheck();
          
          if (res.data.is_partner) {
            // Cập nhật localStorage
            if (user) {
              if (user.user) user.user.is_doi_tac = 1;
              else user.is_doi_tac = 1;
              localStorage.setItem('user', JSON.stringify(user));
            }
            toastr.success('Chúc mừng! Tài khoản của bạn đã được nâng cấp lên Đối Tác thành công!');
            window.dispatchEvent(new Event('storage'));
            
            setTimeout(() => {
              window.location.href = '/doi-tac/dashboard';
            }, 1500);
            return;
          }

          this.hasPending = true;
          toastr.success('Hệ thống đã nhận được thanh toán chuyển khoản! Yêu cầu đang ở trạng thái chờ duyệt.');
        }
      })
      .catch(() => {});
    },
    copyContent() {
      navigator.clipboard.writeText(this.transferContent);
      toastr.success('Đã sao chép nội dung thanh toán gói!');
    },
    submitPayment() {
      if (!this.cleanAmount) {
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
      const userStr = localStorage.getItem('user');
      const user = userStr ? JSON.parse(userStr) : null;
      const userId = user ? (user.user || user).id : null;

      axios.post('http://127.0.0.1:8000/api/thanh-toan/xac-nhan-thanh-toan', {
        nguoi_dung_id: userId,
        noi_dung: this.transferContent + ' | Mua gói dịch vụ: ' + this.form.so_tien + ' VNĐ | QR Đối Tác',
        trang_thai: 'Chờ duyệt',
        so_tien: this.form.so_tien
      }, this.getHeaders())
      .then(res => {
        if (res.data.success) {
          this.stopAutoCheck();

          if (res.data.is_partner) {
            // Cập nhật localStorage
            if (user) {
              if (user.user) user.user.is_doi_tac = 1;
              else user.is_doi_tac = 1;
              localStorage.setItem('user', JSON.stringify(user));
            }
            toastr.success('Chúc mừng! Tài khoản của bạn đã được nâng cấp lên Đối Tác thành công!');
            window.dispatchEvent(new Event('storage'));
            
            setTimeout(() => {
              window.location.href = '/doi-tac/dashboard';
            }, 1500);
            return;
          }

          toastr.success('Ghi nhận giao dịch thành công! Yêu cầu mua gói của bạn đang chờ Admin phê duyệt.');
          this.hasPending = true;
        } else {
          toastr.error(res.data.message || 'Hệ thống chưa nhận được giao dịch chuyển khoản mua gói.');
        }
      })
      .catch((err) => { 
        toastr.error(err.response?.data?.message || 'Có lỗi xảy ra trong quá trình kích hoạt tài khoản!'); 
        console.error(err);
      })
      .finally(() => { this.isSubmitting = false; });
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

.text-gold { color: #ffd700; }

/* QR Section */
.qr-payment-section {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.qr-image-wrapper { max-width: 240px; }
.shadow-glow { box-shadow: 0 0 30px rgba(255, 255, 255, 0.1); }
.qr-placeholder { height: 240px; }

/* Bank Details */
.glass-info {
    background: rgba(0, 0, 0, 0.2);
}

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

.benefit-item {
    font-size: 0.9rem;
    color: #cbd5e1;
}

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

.note-item {
    color: #64748b;
    font-size: 0.9rem;
    display: flex; align-items: center; gap: 8px;
}

.note-item i { font-size: 1.2rem; color: #ffd700; }

/* GLASS CARD PENDING */
.glass-card-pending {
    background: rgba(243, 156, 18, 0.04);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(243, 156, 18, 0.15) !important;
}

.ambient-glow {
    position: absolute;
    top: -50px; left: -50px; width: 150px; height: 150px;
    background: radial-gradient(circle, rgba(243, 156, 18, 0.1) 0%, transparent 70%);
    z-index: 0;
    pointer-events: none;
}

.pending-icon-wrap {
    position: relative;
    width: 80px;
    height: 80px;
}

.pending-icon-circle {
    position: relative;
    z-index: 2;
    width: 80px;
    height: 80px;
    background: rgba(243, 156, 18, 0.1);
    border: 2px solid rgba(243, 156, 18, 0.25);
    border-radius: 50%;
}

.pending-pulse-circle {
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    border: 2px solid rgba(243, 156, 18, 0.4);
    animation: pendingPulse 2s infinite;
    z-index: 1;
}

@keyframes pendingPulse {
    0% {
        transform: scale(0.95);
        opacity: 0.8;
        box-shadow: 0 0 0 0 rgba(243, 156, 18, 0.4);
    }
    70% {
        transform: scale(1.15);
        opacity: 0;
        box-shadow: 0 0 0 15px rgba(243, 156, 18, 0);
    }
    100% {
        transform: scale(0.95);
        opacity: 0;
        box-shadow: 0 0 0 0 rgba(243, 156, 18, 0);
    }
}

.status-pulse-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #d97706;
    border-radius: 50%;
    box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7);
    animation: statusDotPulse 1.6s infinite;
}

@keyframes statusDotPulse {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.5);
    }
    70% {
        transform: scale(1.1);
        box-shadow: 0 0 0 5px rgba(217, 119, 6, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(217, 119, 6, 0);
    }
}

@media (max-width: 992px) {
    .info-row { flex-direction: column; align-items: flex-start; gap: 5px; }
}
</style>
