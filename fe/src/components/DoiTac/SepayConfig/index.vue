<template>
  <div class="sepay-config-wrapper">
    <!-- Header Banner -->
    <div class="sc-banner mb-4">
      <div class="sc-banner-content">
        <div>
          <h4 class="sc-banner-title fw-bold mb-1">
            <i class="bx bx-cog me-2"></i>Cấu Hình Cổng SePay
          </h4>
          <p class="sc-banner-sub mb-0">
            Cấu hình thông tin tài khoản ngân hàng và SePay API Token của bạn để nhận đóng góp công đức trực tiếp từ người dùng.
          </p>
        </div>
        <button class="btn-sc-refresh" @click="loadConfig" :disabled="isLoading" title="Làm mới">
          <i class="bx bx-sync" :class="{'bx-spin': isLoading}"></i>
        </button>
      </div>
    </div>

    <!-- Main Config Card -->
    <div class="row justify-content-center">
      <div class="col-lg-8 col-xl-7">
        <div class="glass-card shadow-lg rounded-3xl overflow-hidden mb-4">
          <div class="card-header-gradient p-4 text-center">
            <h5 class="fw-bold mb-1 text-gold"><i class="bx bx-credit-card me-2"></i>Thông Tin Cổng Thanh Toán</h5>
            <p class="text-white-50 small mb-0">Đảm bảo nhập chính xác thông tin thụ hưởng để tránh gián đoạn giao dịch.</p>
          </div>

          <div class="card-body p-4 p-md-5">
            <div v-if="isLoading" class="text-center py-4">
              <div class="spinner-border text-warning" role="status"></div>
              <p class="text-secondary small mt-3">Đang tải cấu hình...</p>
            </div>

            <form v-else @submit.prevent="saveConfig">
              <!-- Bank Name -->
              <div class="mb-4">
                <label class="form-label text-white-50 small fw-bold text-uppercase mb-2">Ngân hàng thụ hưởng</label>
                <div class="input-group-custom">
                  <span class="input-icon"><i class="bx bx-building"></i></span>
                  <select class="form-control select-custom" v-model="form.sepay_bank_name" required>
                    <option value="" disabled selected>Chọn ngân hàng...</option>
                    <option v-for="bank in popularBanks" :key="bank.code" :value="bank.code">
                      {{ bank.name }} ({{ bank.code }})
                    </option>
                  </select>
                </div>
              </div>

              <!-- Bank Account -->
              <div class="mb-4">
                <label class="form-label text-white-50 small fw-bold text-uppercase mb-2">Số tài khoản ngân hàng</label>
                <div class="input-group-custom">
                  <span class="input-icon"><i class="bx bx-hash"></i></span>
                  <input type="text" class="form-control input-custom" v-model="form.sepay_bank_account" placeholder="Ví dụ: 0342211914" required>
                </div>
              </div>

              <!-- Bank Owner -->
              <div class="mb-4">
                <label class="form-label text-white-50 small fw-bold text-uppercase mb-2">Họ &amp; Tên Chủ tài khoản</label>
                <div class="input-group-custom">
                  <span class="input-icon"><i class="bx bx-user"></i></span>
                  <input type="text" class="form-control input-custom" v-model="form.sepay_bank_owner" placeholder="Ví dụ: NGUYEN VAN A" @input="upperCaseOwner" required>
                </div>
                <small class="text-white-50 small-tip mt-1">Viết hoa không dấu (Ví dụ: TRAN VAN AN)</small>
              </div>

              <!-- SePay API Token -->
              <div class="mb-4">
                <label class="form-label text-white-50 small fw-bold text-uppercase mb-2">SePay API Token</label>
                <div class="input-group-custom">
                  <span class="input-icon"><i class="bx bx-key"></i></span>
                  <input :type="showToken ? 'text' : 'password'" class="form-control input-custom pe-5" v-model="form.sepay_api_token" placeholder="Nhập API Token từ my.sepay.vn" required>
                  <button type="button" class="btn-toggle-eye" @click="showToken = !showToken">
                    <i class="bx" :class="showToken ? 'bx-hide' : 'bx-show'"></i>
                  </button>
                </div>
                <small class="text-white-50 small-tip mt-1">Tạo API Token có quyền truy cập danh sách giao dịch (Transactions Read) trên tài khoản SePay của bạn.</small>
              </div>

              <!-- Action Buttons -->
              <div class="d-flex gap-3 mt-5">
                <button type="submit" class="btn-gradient-submit flex-grow-1" :disabled="isSaving">
                  <span v-if="!isSaving"><i class="bx bx-save me-2"></i>LƯU CẤU HÌNH</span>
                  <span v-else><i class="bx bx-loader-alt bx-spin me-2"></i>ĐANG LƯU...</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Instruction / Support Column -->
      <div class="col-lg-4">
        <div class="instruction-card p-4 rounded-3xl mb-4 border border-white/10">
          <h6 class="text-gold fw-bold mb-3"><i class="bx bx-info-circle me-2"></i>Hướng Dẫn Cấu Hình</h6>
          <ol class="instruction-list ps-3 text-white-50 small">
            <li class="mb-2">Đăng nhập tài khoản ngân hàng thụ hưởng của bạn trên <a href="https://my.sepay.vn" target="_blank" class="text-gold text-decoration-none">SePay.vn</a>.</li>
            <li class="mb-2">Tạo một <strong>API Token</strong> mới trong menu Cài đặt API trên hệ thống SePay.</li>
            <li class="mb-2">Sao chép chuỗi mã API Token thu được và dán vào ô nhập liệu bên cạnh.</li>
            <li class="mb-2">Chọn đúng tên ngân hàng và số tài khoản thụ hưởng đã liên kết trên SePay.</li>
            <li class="mb-2">Nhấn <strong>Lưu cấu hình</strong> để hoàn tất kết nối.</li>
          </ol>

          <div class="alert-info-custom p-3 rounded-2xl mt-4 border border-info/20">
            <i class="bx bx-bolt-circle text-info me-2 fs-5"></i>
            <span class="text-white-50 small">
              Khi thông tin được cấu hình thành công, toàn bộ đóng góp công đức của dòng họ do bạn quản lý sẽ được chuyển thẳng về tài khoản ngân hàng này. Hệ thống tự động đối soát giao dịch thời gian thực qua SePay API của riêng bạn.
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
  name: 'SepayConfig',
  data() {
    return {
      isLoading: false,
      isSaving: false,
      showToken: false,
      form: {
        sepay_api_token: '',
        sepay_bank_account: '',
        sepay_bank_name: '',
        sepay_bank_owner: ''
      },
      popularBanks: [
        { code: 'MBBank', name: 'MB Bank (Quân Đội)' },
        { code: 'Vietcombank', name: 'Vietcombank' },
        { code: 'VietinBank', name: 'VietinBank' },
        { code: 'BIDV', name: 'BIDV' },
        { code: 'Agribank', name: 'Agribank' },
        { code: 'Techcombank', name: 'Techcombank' },
        { code: 'ACB', name: 'ACB' },
        { code: 'VPBank', name: 'VPBank' },
        { code: 'TPBank', name: 'TPBank' },
        { code: 'Sacombank', name: 'Sacombank' },
        { code: 'VIB', name: 'VIB' }
      ]
    };
  },
  mounted() {
    this.loadConfig();
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: 'Bearer ' + localStorage.getItem('access_token') } };
    },
    upperCaseOwner() {
      if (this.form.sepay_bank_owner) {
        // Remove vietnamese accents and set to uppercase
        let owner = this.form.sepay_bank_owner.toUpperCase();
        owner = owner.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/Đ/g, 'D');
        this.form.sepay_bank_owner = owner;
      }
    },
    async loadConfig() {
      this.isLoading = true;
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/doi-tac/sepay-config', this.getHeaders());
        if (res.data.status && res.data.data) {
          const d = res.data.data;
          this.form.sepay_api_token = d.sepay_api_token || '';
          this.form.sepay_bank_account = d.sepay_bank_account || '';
          this.form.sepay_bank_name = d.sepay_bank_name || '';
          this.form.sepay_bank_owner = d.sepay_bank_owner || '';
        }
      } catch (e) {
        console.error('Lỗi tải cấu hình SePay:', e);
        toastr.error('Không thể tải cấu hình SePay!');
      } finally {
        this.isLoading = false;
      }
    },
    async saveConfig() {
      this.isSaving = true;
      try {
        const res = await axios.post('http://127.0.0.1:8000/api/doi-tac/sepay-config', this.form, this.getHeaders());
        if (res.data.status) {
          toastr.success(res.data.message || 'Cập nhật cấu hình SePay thành công!');
          this.loadConfig();
        } else {
          toastr.error(res.data.message || 'Cập nhật cấu hình thất bại!');
        }
      } catch (e) {
        console.error('Lỗi lưu cấu hình SePay:', e);
        toastr.error('Có lỗi xảy ra trong quá trình cập nhật cấu hình!');
      } finally {
        this.isSaving = false;
      }
    }
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap');

.sepay-config-wrapper {
  font-family: 'Inter', sans-serif;
  color: #f8fafc;
}

/* ── Banner ───────────────────────────────────────────────────────────────── */
.sc-banner {
  background: linear-gradient(135deg, rgba(212,175,55,0.08) 0%, rgba(243,156,18,0.04) 100%);
  border: 1px solid rgba(212,175,55,0.15);
  border-radius: 16px;
  padding: 20px 24px;
}
.sc-banner-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.sc-banner-title {
  font-family: 'Outfit', sans-serif;
  font-size: 16px;
  background: linear-gradient(135deg, #d4af37, #f39c12);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.sc-banner-sub { color: #64748b; font-size: 13px; }
.btn-sc-refresh {
  background: rgba(243,156,18,0.08);
  border: 1px solid rgba(243,156,18,0.2);
  color: #d97706;
  width: 38px; height: 38px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; cursor: pointer; transition: all 0.25s;
}
.btn-sc-refresh:hover { background: #f39c12; color: #fff; }

/* ── Glass Card ───────────────────────────────────────────────────────────── */
.glass-card {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
}

.card-header-gradient {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.03) 0%, transparent 100%);
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.text-gold { color: #ffd700; }

/* Input Customization */
.input-group-custom {
  position: relative;
  display: flex;
  align-items: center;
}
.input-icon {
  position: absolute;
  left: 16px;
  font-size: 1.25rem;
  color: #64748b;
  pointer-events: none;
}
.input-custom, .select-custom {
  background: rgba(255, 255, 255, 0.05) !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
  color: #fff !important;
  border-radius: 14px !important;
  padding: 14px 14px 14px 48px !important;
  transition: all 0.25s ease;
  height: 52px;
}
.select-custom {
  appearance: none;
  cursor: pointer;
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>") !important;
  background-repeat: no-repeat !important;
  background-position: right 16px center !important;
  background-size: 16px !important;
}
.select-custom option {
  background-color: #0f172a;
  color: #fff;
}
.input-custom:focus, .select-custom:focus {
  border-color: #d4af37 !important;
  box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.15) !important;
}

.btn-toggle-eye {
  position: absolute;
  right: 16px;
  background: none;
  border: none;
  color: #64748b;
  font-size: 1.25rem;
  cursor: pointer;
  transition: color 0.2s;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-toggle-eye:hover {
  color: #ffd700;
}

.small-tip {
  display: block;
  color: #64748b !important;
}

/* Submit Button */
.btn-gradient-submit {
  padding: 16px;
  background: linear-gradient(135deg, #d4af37, #f39c12);
  color: #000;
  border: none;
  border-radius: 14px;
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: 1px;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 6px 20px rgba(212, 175, 55, 0.2);
}
.btn-gradient-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
  opacity: 0.95;
}
.btn-gradient-submit:disabled {
  background: #1e293b;
  color: #475569;
  cursor: not-allowed;
  box-shadow: none;
}

/* Instructions Support Card */
.instruction-card {
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 20px;
}
.instruction-list strong {
  color: #f8fafc;
}
.instruction-list a:hover {
  color: #f39c12 !important;
}

.alert-info-custom {
  background: rgba(14, 165, 233, 0.03);
  display: flex;
  align-items: flex-start;
}
.alert-info-custom i {
  margin-top: 2px;
}
</style>
