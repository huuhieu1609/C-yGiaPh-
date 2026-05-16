<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="container-custom content-relative pt-5 pb-5">
      <div class="glass-card shadow-2xl rounded-3xl overflow-hidden mt-5">
        <div class="checkout-header p-5 text-center">
          <h2 class="text-gradient fw-bold mb-2">Xác Nhận Thanh Toán</h2>
          <p class="text-white-50">Hoàn tất thanh toán để nâng cấp tài khoản và trải nghiệm đầy đủ tính năng</p>
        </div>
        
        <div class="card-body p-4 p-md-5 pt-0">
          <div class="row g-5">
            <!-- Left Column: Package Details -->
            <div class="col-lg-6">
              <div class="package-info-section h-100">
                <h4 class="text-gold mb-4 fw-bold"><i class="bx bx-receipt me-2"></i>Thông tin đơn hàng</h4>
                
                <div class="glass-info p-4 rounded-2xl border border-white/10 mb-4">
                  <div class="info-row">
                    <span>Gói dịch vụ:</span>
                    <strong class="text-white">{{ ten_goi || 'Gia Phả Tộc' }}</strong>
                  </div>
                  <div class="info-row">
                    <span>Thời hạn:</span>
                    <strong class="text-white">Vĩnh viễn (Server)</strong>
                  </div>
                  <div class="info-row border-0">
                    <span>Tổng tiền:</span>
                    <strong class="text-gold fs-4">{{ form.so_tien ? form.so_tien.toLocaleString() : '20,000' }} VNĐ</strong>
                  </div>
                </div>

                <div class="benefits-list mt-4">
                  <h6 class="text-white-50 small fw-bold text-uppercase mb-3">Quyền lợi gói dịch vụ</h6>
                  <div class="benefit-item mb-2">
                    <i class="bx bx-check-circle text-success me-2"></i>
                    <span>Tạo 01 cây gia phả dòng họ trực tuyến</span>
                  </div>
                  <div class="benefit-item mb-2">
                    <i class="bx bx-check-circle text-success me-2"></i>
                    <span>Không giới hạn số lượng thành viên</span>
                  </div>
                  <div class="benefit-item mb-2">
                    <i class="bx bx-check-circle text-success me-2"></i>
                    <span>Tra cứu và tìm kiếm thông tin nhanh chóng</span>
                  </div>
                  <div class="benefit-item mb-2">
                    <i class="bx bx-check-circle text-success me-2"></i>
                    <span>Bảo mật và lưu trữ an toàn trên đám mây</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column: Payment Details & QR -->
            <div class="col-lg-6">
              <div class="payment-section">
                <h4 class="text-gold mb-4 fw-bold"><i class="bx bx-qr-scan me-2"></i>Chuyển khoản qua mã QR</h4>
                
                <div class="qr-payment-section text-center p-4 rounded-3xl mb-4">
                  <div class="qr-image-wrapper mx-auto mb-4">
                    <img :src="qrUrl" alt="QR Code" class="img-fluid rounded-2xl border-2 border-white/10 p-2 bg-white shadow-glow" v-if="form.so_tien">
                    <div v-else class="qr-placeholder rounded-2xl border-2 border-dashed border-white/20 d-flex align-items-center justify-content-center bg-white/5">
                      <span class="text-white-50">Nhập số tiền để tạo mã QR</span>
                    </div>
                  </div>
                  
                  <div class="bank-details text-start glass-info p-4 rounded-2xl border border-white/10">
                    <div class="info-row"><span>Ngân hàng:</span><strong>MB Bank (Quân Đội)</strong></div>
                    <div class="info-row"><span>Số tài khoản:</span><strong class="text-gold">0342211914</strong></div>
                    <div class="info-row"><span>Chủ tài khoản:</span><strong>TRAN HUU HIEU</strong></div>
                    <div class="info-row border-0">
                      <span>Nội dung:</span>
                      <strong class="text-warning">{{ transferContent }}</strong>
                      <button type="button" class="btn-copy ms-2" @click="copyContent">
                        <i class="bx bx-copy"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <form @submit.prevent="submitPayment">
                  <button type="submit" class="btn-gradient-submit w-100" :disabled="isSubmitting">
                    <span v-if="!isSubmitting"><i class="bx bx-check-circle me-2"></i> XÁC NHẬN ĐÃ CHUYỂN KHOẢN</span>
                    <span v-else><i class="bx bx-loader-alt bx-spin me-2"></i> ĐANG XỬ LÝ...</span>
                  </button>
                </form>
                
                <p class="text-center text-white-50 small mt-3">
                  <i class="bx bx-info-circle me-1"></i> Hệ thống sẽ tự động xác nhận sau 1-3 phút
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="security-note text-center mt-5">
        <div class="d-inline-flex align-items-center gap-5">
          <span class="note-item"><i class="bx bx-lock-alt"></i> An toàn & Bảo mật</span>
          <span class="note-item"><i class="bx bx-support"></i> Hỗ trợ 24/7</span>
          <span class="note-item"><i class="bx bx-shield-quarter"></i> Chính sách hoàn tiền</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useToast } from 'vue-toastification';

export default {
  name: 'ClientCheckout',
  setup() {
    const toast = useToast();
    return { toast };
  },
  data() {
    return {
      userName: '',
      form: { 
        so_tien: null, 
        phuong_thuc: 'bank' 
      },
      ten_goi: '',
      isSubmitting: false,
      checkInterval: null,
      paymentCode: null
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
    const user = JSON.parse(localStorage.getItem('user'));
    const userData = user ? (user.user || user) : null;
    if (userData) {
      this.userName = userData.ho_ten || '';
      // Persistent payment code for this user
      const storageKey = `payment_code_${userData.id}`;
      let savedCode = localStorage.getItem(storageKey);
      if (!savedCode) {
        savedCode = Math.floor(10000 + Math.random() * 90000);
        localStorage.setItem(storageKey, savedCode);
      }
      this.paymentCode = savedCode;
    }
    
    // Get amount from query if available
    if (this.$route.query.so_tien) {
      this.form.so_tien = Number(this.$route.query.so_tien);
    }
    if (this.$route.query.ten_goi) {
      this.ten_goi = this.$route.query.ten_goi;
    }

    this.startAutoCheck();
  },
  beforeUnmount() {
    this.stopAutoCheck();
  },
  methods: {
    startAutoCheck() {
      this.checkInterval = setInterval(() => {
        if (this.cleanAmount > 0 && !this.isSubmitting) {
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

      const user = JSON.parse(localStorage.getItem('user'));
      const userId = user ? (user.user || user).id : null;

      axios.post('http://127.0.0.1:8000/api/thanh-toan/xac-nhan-thanh-toan', {
        nguoi_dung_id: userId,
        noi_dung: this.transferContent + ' | Số tiền: ' + this.form.so_tien + ' VNĐ | Chuyển khoản QR',
        trang_thai: 'Chờ duyệt'
      }, this.getHeaders())
      .then(res => {
        if (res.data.success) {
          this.stopAutoCheck();
          this.toast.success('Thanh toán thành công! Hệ thống đang chuyển trang...');
          this.$router.push('/doi-tac/dashboard');
        }
      })
      .catch(() => {});
    },
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },

    copyContent() {
      navigator.clipboard.writeText(this.transferContent);
      this.toast.success('Đã sao chép nội dung chuyển khoản!');
    },
    submitPayment() {
      if (!this.cleanAmount) {
        this.toast.warning('Vui lòng nhập số tiền hợp lệ!');
        return;
      }

      const token = localStorage.getItem('access_token');
      if (!token) {
        this.toast.warning('Vui lòng đăng nhập!');
        this.$router.push('/login');
        return;
      }

      this.isSubmitting = true;
      const user = JSON.parse(localStorage.getItem('user'));
      const userId = user ? (user.user || user).id : null;

      axios.post('http://127.0.0.1:8000/api/thanh-toan/xac-nhan-thanh-toan', {
        nguoi_dung_id: userId,
        noi_dung: this.transferContent + ' | Số tiền: ' + this.form.so_tien + ' VNĐ | Chuyển khoản QR',
        trang_thai: 'Chờ duyệt'
      }, this.getHeaders())
      .then(res => {
        if (res.data.success) {
          this.stopAutoCheck();
          this.toast.success('Xác nhận thành công! Bạn đã được nâng cấp lên Đối Tác.');
          this.$router.push('/doi-tac/dashboard');
        } else {
          this.toast.error(res.data.message || 'Chưa nhận được thanh toán.');
        }
      })
      .catch((err) => { 
        this.toast.error('Có lỗi xảy ra!'); 
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

@media (max-width: 992px) {
    .info-row { flex-direction: column; align-items: flex-start; gap: 5px; }
}
</style>