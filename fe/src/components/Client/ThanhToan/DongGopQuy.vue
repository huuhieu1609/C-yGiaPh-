<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="container-custom content-relative pt-5 pb-5">
      <div class="glass-card shadow-2xl rounded-3xl overflow-hidden mt-5">
        <div class="checkout-header p-5 text-center">
          <h2 class="text-gradient fw-bold mb-2">Đóng Góp Quỹ Dòng Họ</h2>
          <p class="text-white-50">Góp phần xây dựng, bảo tồn gia phả số và tri ân tổ tiên, gìn giữ di sản dòng họ muôn đời sau.</p>
        </div>
        
        <div class="card-body p-4 p-md-5 pt-0">
          <div class="row g-5">
            <!-- Left Column: Contribution Details -->
            <div class="col-lg-6">
              <div class="package-info-section h-100">
                <h4 class="text-gold mb-4 fw-bold"><i class="bx bx-heart me-2"></i>Thông tin đóng góp</h4>
                
                <!-- Preset selection -->
                <div class="mb-4">
                  <label class="form-label text-white-50 small fw-bold text-uppercase mb-2">Chọn số tiền đóng góp tâm đức</label>
                  <div class="row g-2">
                    <div class="col-6 col-sm-3" v-for="amount in presetAmounts" :key="amount">
                      <button type="button" class="btn btn-preset w-100" :class="{ 'active': form.so_tien === amount && !isCustomAmount }" @click="selectPreset(amount)">
                        {{ (amount / 1000) }}K
                      </button>
                    </div>
                    <div class="col-12 col-sm-12 mt-2">
                      <button type="button" class="btn btn-preset w-100" :class="{ 'active': isCustomAmount }" @click="enableCustomAmount">
                        Số tiền khác (Nhập tùy chọn)
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Custom Amount Input -->
                <div class="mb-4 fade-in" v-if="isCustomAmount">
                  <label class="form-label text-white-50 small fw-bold text-uppercase mb-2">Nhập số tiền tùy chọn (VNĐ)</label>
                  <div class="input-group">
                    <input type="number" class="form-control custom-amount-input" v-model.number="form.so_tien" placeholder="Ví dụ: 100000" min="1000" @input="onCustomAmountInput">
                    <span class="input-group-text bg-transparent border-white/10 text-white-50">VNĐ</span>
                  </div>
                </div>
                
                <div class="glass-info p-4 rounded-2xl border border-white/10 mb-4">
                  <div class="info-row">
                    <span>Mục đích đóng góp:</span>
                    <strong class="text-white">{{ ten_goi || 'Quỹ phát triển Gia phả Dòng tộc' }}</strong>
                  </div>
                  <div class="info-row">
                    <span>Thời gian ghi nhận:</span>
                    <strong class="text-white">Lưu danh bảng vàng công đức</strong>
                  </div>
                  <div class="info-row border-0">
                    <span>Số tiền đóng góp:</span>
                    <strong class="text-gold fs-4">{{ form.so_tien ? form.so_tien.toLocaleString() : '0' }} VNĐ</strong>
                  </div>
                </div>

                <div class="benefits-list mt-4">
                  <h6 class="text-white-50 small fw-bold text-uppercase mb-3">Ý nghĩa và Quyền lợi dòng tộc</h6>
                  <div class="benefit-item mb-2">
                    <i class="bx bxs-award text-warning me-2"></i>
                    <span>Khắc ghi tên tuổi trên Bảng Vàng Công Đức dòng họ</span>
                  </div>
                  <div class="benefit-item mb-2">
                    <i class="bx bx-check-circle text-success me-2"></i>
                    <span>Góp phần duy trì hệ thống gia phả số trực tuyến</span>
                  </div>
                  <div class="benefit-item mb-2">
                    <i class="bx bx-check-circle text-success me-2"></i>
                    <span>Đồng hành bảo mật, lưu trữ di sản và hỗ trợ duy trì server lâu dài</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column: Donation QR -->
            <div class="col-lg-6">
              <div class="payment-section" v-if="isConfigured">
                <h4 class="text-gold mb-4 fw-bold"><i class="bx bx-qr-scan me-2"></i>Chuyển khoản tâm đức qua mã QR</h4>
                
                <div class="qr-payment-section text-center p-4 rounded-3xl mb-4">
                  <div class="qr-image-wrapper mx-auto mb-4">
                    <img :src="qrUrl" alt="QR Code" class="img-fluid rounded-2xl border-2 border-white/10 p-2 bg-white shadow-glow" v-if="form.so_tien && form.so_tien >= 1000">
                    <div v-else class="qr-placeholder rounded-2xl border-2 border-dashed border-white/20 d-flex align-items-center justify-content-center bg-white/5">
                      <span class="text-white-50">Vui lòng chọn hoặc nhập số tiền để tạo mã QR công đức</span>
                    </div>
                  </div>
                  
                  <div class="bank-details text-start glass-info p-4 rounded-2xl border border-white/10">
                    <div class="info-row"><span>Ngân hàng thụ hưởng:</span><strong>{{ getFriendlyBankName(sepayBankName) }}</strong></div>
                    <div class="info-row"><span>Số tài khoản:</span><strong class="text-gold">{{ sepayBankAccount }}</strong></div>
                    <div class="info-row"><span>Chủ tài khoản:</span><strong>{{ sepayBankOwner }}</strong></div>
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
                  <button type="submit" class="btn-gradient-submit w-100" :disabled="isSubmitting || !form.so_tien || form.so_tien < 1000">
                    <span v-if="!isSubmitting"><i class="bx bx-check-circle me-2"></i> XÁC NHẬN ĐÃ CHUYỂN KHOẢN ĐỒNG GÓP</span>
                    <span v-else><i class="bx bx-loader-alt bx-spin me-2"></i> ĐANG GHI NHẬN HÓA ĐƠN...</span>
                  </button>
                </form>
                
                <div class="auto-verify-note text-center mt-3 p-3 rounded-2xl border border-white/10">
                  <i class="bx bx-bolt-circle text-success me-1 animate-pulse"></i>
                  <span class="text-white-50 small">Hệ thống đang <strong class="text-success">tự động kiểm tra</strong> và ghi nhận bảng vàng sau khi giao dịch chuyển khoản thành công (mỗi 8 giây).</span>
                </div>
              </div>
              <div class="payment-section text-center p-5 rounded-3xl border border-dashed border-white/10 bg-black/20" v-else>
                <i class="bx bx-shield-x text-warning fs-1 mb-3 animate-pulse"></i>
                <h5 class="fw-bold text-gradient mb-2">Chưa cấu hình nhận đóng góp</h5>
                <p class="text-white-50 small mb-0">{{ configuredMessage }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="security-note text-center mt-5">
        <div class="d-inline-flex align-items-center gap-5">
          <span class="note-item"><i class="bx bx-lock-alt"></i> Bảo mật tư liệu</span>
          <span class="note-item"><i class="bx bx-support"></i> Hỗ trợ dòng tộc 24/7</span>
          <span class="note-item"><i class="bx bx-shield-quarter"></i> Ghi nhận công đức minh bạch</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
  name: 'DongGopQuy',
  data() {
    return {
      userName: '',
      form: { 
        so_tien: 100000, 
        phuong_thuc: 'bank' 
      },
      presetAmounts: [50000, 100000, 200000, 500000],
      isCustomAmount: false,
      ten_goi: 'Quỹ phát triển Gia phả Dòng tộc',
      isSubmitting: false,
      checkInterval: null,
      paymentCode: null,
      sepayBankName: 'MBBank',
      sepayBankAccount: '0342211914',
      sepayBankOwner: 'TRAN HUU HIEU',
      isConfigured: true,
      configuredMessage: ''
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
      return `DONGGOP ${name}${this.paymentCode}`;
    },
    qrUrl() {
      if (!this.cleanAmount) return '';
      const amount = this.cleanAmount;
      const desc = encodeURIComponent(this.transferContent);
      return `https://qr.sepay.vn/img?bank=${this.sepayBankName}&acc=${this.sepayBankAccount}&template=compact&amount=${amount}&des=${desc}`;
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
      if (!this.presetAmounts.includes(this.form.so_tien)) {
        this.isCustomAmount = true;
      }
    }

    // Fetch custom SePay configuration for this lineage
    this.fetchSepayConfig();

    // Start auto-checking payment
    this.startAutoCheck();
  },
  beforeUnmount() {
    this.stopAutoCheck();
  },
  methods: {
    fetchSepayConfig() {
      const chiNhanhId = this.$route.query.chi_nhanh_id || '';
      axios.get(`http://127.0.0.1:8000/api/dong-gop/sepay-config?chi_nhanh_id=${chiNhanhId}`, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            if (res.data.is_configured === false) {
              this.isConfigured = false;
              this.configuredMessage = res.data.message || 'Quản trị viên dòng họ này chưa thiết lập cấu hình cổng SePay.';
            } else if (res.data.data) {
              const d = res.data.data;
              this.sepayBankName = d.sepay_bank_name || 'MBBank';
              this.sepayBankAccount = d.sepay_bank_account || '0342211914';
              this.sepayBankOwner = d.sepay_bank_owner || 'TRAN HUU HIEU';
              this.isConfigured = true;
            }
          }
        })
        .catch(err => {
          console.error("Lỗi khi tải cấu hình SePay đóng góp:", err);
        });
    },
    getFriendlyBankName(code) {
      const popularBanks = {
        'MBBank': 'MB Bank (Quân Đội)',
        'Vietcombank': 'Vietcombank',
        'VietinBank': 'VietinBank',
        'BIDV': 'BIDV',
        'Agribank': 'Agribank',
        'Techcombank': 'Techcombank',
        'ACB': 'ACB',
        'VPBank': 'VPBank',
        'TPBank': 'TPBank',
        'Sacombank': 'Sacombank',
        'VIB': 'VIB'
      };
      return popularBanks[code] || code;
    },
    selectPreset(amount) {
      this.form.so_tien = amount;
      this.isCustomAmount = false;
    },
    enableCustomAmount() {
      this.isCustomAmount = true;
      this.form.so_tien = null;
    },
    onCustomAmountInput(e) {
      const val = parseInt(e.target.value);
      this.form.so_tien = isNaN(val) ? null : val;
    },
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    copyContent() {
      navigator.clipboard.writeText(this.transferContent);
      toastr.success('Đã sao chép nội dung chuyển khoản đóng góp!');
    },
    submitPayment() {
      if (!this.cleanAmount || this.form.so_tien < 1000) {
        toastr.warning('Vui lòng nhập hoặc chọn số tiền đóng góp hợp lệ (tối thiểu 1,000đ)!');
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
        chi_nhanh_id: this.$route.query.chi_nhanh_id || null,
        noi_dung: this.transferContent + ' | Đóng góp quỹ: ' + this.form.so_tien + ' VNĐ | QR Công Đức',
        trang_thai: 'Chờ duyệt'
      }, this.getHeaders())
      .then(res => {
        if (res.data.success) {
          this.stopAutoCheck();
          
          // Clear payment code
          if (userId) {
            localStorage.removeItem(`payment_code_${userId}`);
          }

          toastr.success('Cảm ơn đóng góp tâm đức của bạn! Hệ thống đã ghi danh Bảng Vàng.');
          this.$router.push('/profile');
        } else {
          toastr.error(res.data.message || 'Hệ thống chưa nhận được khoản chuyển đóng góp công đức.');
        }
      })
      .catch((err) => { 
        toastr.error('Có lỗi xảy ra trong quá trình ghi nhận đóng góp!'); 
        console.error(err);
      })
      .finally(() => { this.isSubmitting = false; });
    },
    startAutoCheck() {
      this.checkInterval = setInterval(() => {
        if (this.cleanAmount > 0 && !this.isSubmitting && this.isConfigured) {
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
        chi_nhanh_id: this.$route.query.chi_nhanh_id || null,
        noi_dung: this.transferContent + ' | Đóng góp quỹ: ' + this.form.so_tien + ' VNĐ | QR Công Đức',
        trang_thai: 'Chờ duyệt'
      }, this.getHeaders())
      .then(res => {
        if (res.data.success) {
          this.stopAutoCheck();

          // Clear payment code
          if (userId) {
            localStorage.removeItem(`payment_code_${userId}`);
          }

          toastr.success('Cảm ơn đóng góp tâm đức của bạn! Hệ thống đã ghi danh Bảng Vàng.');
          this.$router.push('/profile');
        }
      })
      .catch(() => {});
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

/* Preset Button */
.btn-preset {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #cbd5e1;
  padding: 10px 15px;
  font-weight: 600;
  border-radius: 12px;
  transition: all 0.2s ease;
}

.btn-preset:hover {
  background: rgba(212, 175, 55, 0.15);
  border-color: rgba(212, 175, 55, 0.4);
  color: #ffd700;
}

.btn-preset.active {
  background: #d4af37 !important;
  color: #000 !important;
  border-color: #d4af37 !important;
  box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
}

.custom-amount-input {
  background: rgba(255, 255, 255, 0.05) !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
  color: #fff !important;
  border-radius: 12px 0 0 12px !important;
  padding: 12px !important;
}

.custom-amount-input:focus {
  border-color: #d4af37 !important;
  box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.2) !important;
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

.auto-verify-note {
    background: rgba(16, 185, 129, 0.04);
    border: 1px solid rgba(16, 185, 129, 0.15);
}

.note-item {
    color: #64748b;
    font-size: 0.9rem;
    display: flex; align-items: center; gap: 8px;
}

.note-item i { font-size: 1.2rem; color: #ffd700; }

.fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 992px) {
    .info-row { flex-direction: column; align-items: flex-start; gap: 5px; }
}
</style>
