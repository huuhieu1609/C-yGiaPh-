<template>
  <div class="checkout-page container-fluid pt-5 mt-5 mb-5">
    <div class="container">
      <div class="row justify-content-center">
        <!-- Payment Form -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-lg rounded-4 mb-4">
            <div class="card-header checkout-header border-0 rounded-top-4 p-4 text-center">
              <h4 class="mb-0 fw-bold text-white"><i class="bx bx-donate-heart me-2 text-gold"></i>Thanh Toán Mua Gói</h4>
              <p class="mb-0 text-white-50 mt-2">Thanh toán gói để trải nghiệm các tính năng cao cấp của dòng họ</p>
            </div>
            <div class="card-body p-4 p-md-5">
              <form @submit.prevent="submitPayment">
                <div class="mb-4">
                  <label class="form-label fw-semibold">Số tiền thanh toán (VNĐ) <span class="text-danger">*</span></label>
                  <input type="number" class="form-control form-control-lg" v-model.number="form.so_tien" placeholder="Nhập số tiền..." required>
                </div>
                
                <div class="mb-5">
                  <label class="form-label fw-semibold">Phương thức thanh toán <span class="text-danger">*</span></label>
                  <div class="payment-methods">
                    <label class="method-option" v-for="(m, i) in methods" :key="i" :class="{ 'active': form.phuong_thuc === m.value }">
                      <input type="radio" v-model="form.phuong_thuc" :value="m.value" class="d-none">
                      <i :class="m.icon"></i>
                      <span>{{ m.label }}</span>
                    </label>
                  </div>
                </div>

                <!-- QR Transfer -->
                <div class="qr-payment-section text-center p-4 rounded-4 mb-5" v-if="form.phuong_thuc === 'bank'">
                  <h5 class="fw-bold mb-4">Quét mã QR để chuyển khoản</h5>
                  <div class="qr-image-wrapper mx-auto mb-3">
                    <img :src="qrUrl" alt="QR Code Thanh Toán" class="img-fluid rounded-3 border p-2 bg-white" v-if="form.so_tien">
                    <div v-else class="qr-placeholder rounded-3 border d-flex align-items-center justify-content-center bg-light">
                      <span class="text-muted"><i class="bx bx-qr-scan fs-1 mb-2 d-block"></i>Nhập số tiền để tạo mã QR</span>
                    </div>
                  </div>
                  
                  <div class="bank-details text-start bg-white p-4 rounded-3 border mt-4">
                    <div class="info-row"><span>Ngân hàng:</span><strong class="text-dark">MB Bank (Ngân hàng Quân Đội)</strong></div>
                    <div class="info-row"><span>Số tài khoản:</span><strong class="text-dark fs-5">0342211914</strong></div>
                    <div class="info-row"><span>Chủ tài khoản:</span><strong class="text-dark">TRAN HUU HIEU</strong></div>
                    <div class="info-row">
                      <span>Nội dung CK:</span>
                      <strong class="text-danger">{{ transferContent }}</strong>
                      <button type="button" class="btn btn-sm btn-light py-0 px-2 ms-2" @click="copyContent" title="Sao chép">
                        <i class="bx bx-copy"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Other Methods Warning -->
                <div class="alert alert-warning border-0 rounded-3 mb-4" v-if="form.phuong_thuc === 'momo' || form.phuong_thuc === 'zalopay'">
                  <div class="d-flex align-items-center">
                    <i class="bx bx-info-circle fs-3 me-3"></i>
                    <div>Phương thức thanh toán qua ví điện tử đang được bảo trì. Vui lòng chọn <strong>Chuyển khoản ngân hàng</strong>.</div>
                  </div>
                </div>

                <button type="submit" class="btn-submit w-100" :disabled="isSubmitting || (form.phuong_thuc !== 'bank')">
                  <span v-if="!isSubmitting"><i class="bx bx-check-circle me-2"></i> Xác Nhận Đã Chuyển Khoản</span>
                  <span v-else><i class="bx bx-loader-alt bx-spin me-2"></i> Đang xử lý...</span>
                </button>
              </form>
            </div>
          </div>

          <div class="security-note text-center">
            <div class="d-inline-flex align-items-center gap-3">
              <span class="note-item"><i class="bx bx-lock-alt"></i> An toàn & Bảo mật</span>
              <span class="note-item"><i class="bx bx-support"></i> Hỗ trợ 24/7</span>
            </div>
          </div>
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
      methods: [
        { value: 'bank', label: 'Chuyển khoản / QR', icon: 'bx bx-qr-scan' },
        { value: 'momo', label: 'MoMo', icon: 'bx bx-wallet' },
        { value: 'zalopay', label: 'ZaloPay', icon: 'bx bx-mobile' }
      ],
      form: { 
        so_tien: null, 
        phuong_thuc: 'bank' 
      },
      isSubmitting: false,
      checkInterval: null,
      paymentCode: Math.floor(10000 + Math.random() * 90000)
    }
  },
  computed: {
    cleanAmount() {
      // Return numeric value only
      return this.form.so_tien.toString().replace(/[^\d]/g, '');
    },
    transferContent() {
      let name = 'KH';
      if (this.userName) {
        name = this.userName.split(' ').pop().toUpperCase();
        // Remove accents using standard JS normalize
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
    if (user) {
      this.userName = (user.user || user).ho_ten || '';
    }
    this.startAutoCheck();
  },
  beforeUnmount() {
    this.stopAutoCheck();
  },
  methods: {
    startAutoCheck() {
      this.checkInterval = setInterval(() => {
        if (this.cleanAmount > 0 && this.form.phuong_thuc === 'bank' && !this.isSubmitting) {
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
        this.toast.warning('Vui lòng đăng nhập để lưu lại lịch sử đóng góp!');
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
          this.toast.error(res.data.message || 'Chưa nhận được thanh toán. Vui lòng chờ thêm ít phút.');
        }
      })
      .catch((err) => { 
        this.toast.error('Có lỗi xảy ra, vui lòng thử lại!'); 
        console.error(err);
      })
      .finally(() => { this.isSubmitting = false; });
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap');

.checkout-page {
  font-family: 'Inter', sans-serif;
  min-height: calc(100vh - 200px);
  background-color: #f8f9fa;
}

.text-gold { color: #d4af37 !important; }

/* Header */
.checkout-header {
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%) !important;
  color: #fff;
}
.rounded-top-4 { border-radius: 16px 16px 0 0 !important; }
.rounded-4 { border-radius: 16px !important; }

/* Form Elements */
.form-control {
  border-radius: 12px;
  padding: 14px 18px;
  border: 1px solid #dee2e6;
  background-color: #fcfcfc;
  transition: all 0.3s ease;
  font-size: 15px;
}
.form-control:focus {
  border-color: #d4af37;
  box-shadow: 0 0 0 4px rgba(212,175,55,0.1);
  background-color: #fff;
}
.form-control-lg {
  font-size: 18px;
  font-weight: 600;
  color: #d4af37;
}

/* Payment Methods */
.payment-methods {
  display: flex;
  gap: 15px;
}
.method-option {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 20px 10px;
  border: 2px solid #e9ecef;
  background: #fff;
  border-radius: 14px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 14px;
  font-weight: 600;
  color: #6c757d;
}
.method-option i { 
  font-size: 32px; 
  color: #adb5bd;
  transition: color 0.3s;
}
.method-option:hover { 
  border-color: #d4af37; 
  color: #d4af37; 
  transform: translateY(-2px);
}
.method-option:hover i { color: #d4af37; }
.method-option.active {
  border-color: #d4af37;
  background: #fffdf5;
  color: #d4af37;
  box-shadow: 0 8px 16px rgba(212,175,55,0.12);
}
.method-option.active i { color: #d4af37; }

/* QR Section */
.qr-payment-section {
  background: linear-gradient(to bottom, #fdfdfd, #f8f9fa);
  border: 1px solid #e9ecef;
}
.qr-image-wrapper {
  max-width: 250px;
}
.qr-placeholder {
  height: 250px;
  border: 2px dashed #dee2e6 !important;
}

/* Bank Details */
.bank-details {
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  font-size: 15px;
  border-bottom: 1px dashed #e9ecef;
}
.info-row:last-child { 
  border-bottom: none; 
  padding-bottom: 0;
}
.info-row span { color: #6c757d; font-weight: 500; }

/* Submit Button */
.btn-submit {
  padding: 16px;
  background: linear-gradient(135deg, #d4af37, #c49b2a);
  color: #000;
  border: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 16px;
  letter-spacing: 0.5px;
  cursor: pointer;
  transition: all 0.3s ease;
}
.btn-submit:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(212,175,55,0.25);
}
.btn-submit:disabled { 
  opacity: 0.6; 
  cursor: not-allowed; 
  background: #e9ecef;
  color: #6c757d;
}

/* Security Note */
.security-note .note-item {
  color: #adb5bd;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
}
.security-note i { font-size: 18px; }

@media (max-width: 768px) {
  .payment-methods { flex-direction: column; }
  .info-row { flex-direction: column; align-items: flex-start; gap: 4px; }
  .info-row strong { font-size: 16px !important; }
}
</style>