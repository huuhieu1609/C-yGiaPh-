<template>
  <div class="quan-ly-goi-wrapper">

    <!-- Header Banner -->
    <div class="qg-banner mb-4">
      <div class="qg-banner-content">
        <div>
          <h4 class="qg-banner-title fw-bold mb-1">
            <i class="bx bx-package me-2"></i>Quản Lý Gói Đăng Ký
          </h4>
          <p class="qg-banner-sub mb-0">
            Theo dõi quyền sử dụng và thời hạn của từng gói dịch vụ đã mua.
          </p>
        </div>
        <button class="btn-qg-refresh" @click="loadData" :disabled="isLoading" title="Làm mới">
          <i class="bx bx-sync" :class="{'bx-spin': isLoading}"></i>
        </button>
      </div>
    </div>

    <!-- Skeleton / Loading -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-warning" role="status"></div>
      <p class="text-secondary small mt-3">Đang tải thông tin gói...</p>
    </div>

    <template v-else>
      <!-- Stats Row -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="qg-stat-card qg-stat-active">
            <div class="qg-stat-icon"><i class="bx bx-check-circle"></i></div>
            <div class="qg-stat-num">{{ activeCount }}</div>
            <div class="qg-stat-label">Gói đang hoạt động</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="qg-stat-card qg-stat-feature">
            <div class="qg-stat-icon"><i class="bx bx-bolt-circle"></i></div>
            <div class="qg-stat-num">{{ effectiveFeatures.length }}</div>
            <div class="qg-stat-label">Quyền đang có hiệu lực</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="qg-stat-card qg-stat-gen">
            <div class="qg-stat-icon"><i class="bx bx-layer"></i></div>
            <div class="qg-stat-num">{{ effectiveMaxDoi >= 999 ? '∞' : effectiveMaxDoi }}</div>
            <div class="qg-stat-label">Đời tối đa</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="qg-stat-card qg-stat-member">
            <div class="qg-stat-icon"><i class="bx bx-group"></i></div>
            <div class="qg-stat-num">{{ effectiveMaxMember >= 99999 ? '∞' : effectiveMaxMember.toLocaleString() }}</div>
            <div class="qg-stat-label">Thành viên tối đa</div>
          </div>
        </div>
      </div>

      <!-- No Packages -->
      <div v-if="packages.length === 0" class="qg-empty-state text-center py-5">
        <i class="bx bx-package fs-1 text-warning opacity-50 mb-3 d-block"></i>
        <h5 class="fw-bold text-secondary">Bạn chưa có gói nào</h5>
        <p class="text-muted small mb-4">Hãy đăng ký gói dịch vụ để bắt đầu sử dụng đầy đủ tính năng.</p>
        <router-link to="/dich-vu-goi" class="btn-qg-buy">
          <i class="bx bx-cart-add me-2"></i>Xem Gói Dịch Vụ
        </router-link>
      </div>

      <template v-else>
        <!-- Package Cards -->
        <div class="row g-4 mb-4">
          <div class="col-12 col-lg-6" v-for="pkg in packages" :key="pkg.id">
            <div class="qg-package-card" :class="getCardClass(pkg)">

              <!-- Card Header -->
              <div class="qg-card-header">
                <div class="qg-card-title-wrap">
                  <div class="qg-pkg-icon">
                    <i class="bx bx-package"></i>
                  </div>
                  <div>
                    <h6 class="qg-pkg-name mb-0">{{ pkg.ten_goi }}</h6>
                    <span class="qg-pkg-price">{{ formatCurrency(pkg.so_tien) }}</span>
                  </div>
                </div>
                <span class="qg-status-badge" :class="getStatusClass(pkg)">
                  <span class="qg-pulse" v-if="pkg.is_active && !pkg.is_expiring_soon"></span>
                  <span class="qg-pulse qg-pulse-warn" v-if="pkg.is_expiring_soon"></span>
                  {{ getStatusText(pkg) }}
                </span>
              </div>

              <!-- Countdown Section -->
              <div class="qg-countdown-section">
                <div class="qg-countdown-label">
                  <span>{{ pkg.is_active ? 'Thời gian còn lại' : 'Đã hết hạn' }}</span>
                  <span class="qg-expiry-date">
                    <i class="bx bx-calendar me-1"></i>
                    {{ pkg.ngay_ket_thuc ? formatDate(pkg.ngay_ket_thuc) : 'Vô thời hạn' }}
                  </span>
                </div>

                <!-- Countdown Display -->
                <div class="qg-countdown-display" v-if="pkg.is_active && pkg.days_remaining !== null">
                  <div class="qg-countdown-block" v-for="(unit, idx) in getCountdown(pkg)" :key="idx">
                    <div class="qg-countdown-num">{{ unit.value }}</div>
                    <div class="qg-countdown-unit">{{ unit.label }}</div>
                  </div>
                </div>
                <div class="qg-countdown-forever" v-else-if="pkg.is_active && pkg.days_remaining === null">
                  <i class="bx bx-infinite me-2"></i> Không giới hạn thời gian
                </div>
                <div class="qg-countdown-expired" v-else>
                  <i class="bx bx-time me-2"></i> Gói này đã hết hạn sử dụng
                </div>

                <!-- Progress Bar -->
                <div class="qg-progress-wrap mt-3" v-if="pkg.is_active">
                  <div class="qg-progress-bar" :class="getProgressClass(pkg)">
                    <div class="qg-progress-fill" :style="{ width: pkg.progress_pct + '%' }"></div>
                  </div>
                  <div class="qg-progress-labels">
                    <span>{{ formatDate(pkg.ngay_bat_dau) }}</span>
                    <span class="fw-semibold">{{ pkg.progress_pct.toFixed(0) }}% còn lại</span>
                    <span>{{ formatDate(pkg.ngay_ket_thuc) }}</span>
                  </div>
                </div>
              </div>

              <!-- Package Details -->
              <div class="qg-card-details">
                <div class="qg-detail-row">
                  <span><i class="bx bx-layer me-1 text-info"></i>Giới hạn đời:</span>
                  <strong>{{ pkg.max_doi >= 999 ? 'Không giới hạn' : pkg.max_doi + ' đời' }}</strong>
                </div>
                <div class="qg-detail-row">
                  <span><i class="bx bx-group me-1 text-success"></i>Giới hạn thành viên:</span>
                  <strong>{{ pkg.max_thanh_vien >= 99999 ? 'Không giới hạn' : pkg.max_thanh_vien.toLocaleString() + ' người' }}</strong>
                </div>
              </div>

              <!-- Features List -->
              <div class="qg-features-section">
                <div class="qg-features-title">
                  <i class="bx bx-check-shield me-1 text-warning"></i>
                  Quyền trong gói này ({{ pkg.features.length }})
                </div>
                <div class="qg-features-grid" v-if="pkg.features.length > 0">
                  <span
                    v-for="feat in pkg.features"
                    :key="feat"
                    class="qg-feat-chip"
                    :class="{ 'qg-feat-chip-active': pkg.is_active, 'qg-feat-chip-expired': !pkg.is_active }"
                    :title="getFeatureInfo(feat).desc"
                  >
                    <i :class="getFeatureInfo(feat).icon" class="me-1"></i>
                    {{ getFeatureInfo(feat).label || feat }}
                  </span>
                </div>
                <div class="text-muted small fst-italic" v-else>Chưa cấu hình tính năng cho gói này.</div>
              </div>

            </div>
          </div>
        </div>

        <!-- Effective Permissions Section -->
        <div class="qg-effective-section" v-if="effectiveFeatures.length > 0">
          <div class="qg-effective-header">
            <div>
              <h5 class="fw-bold mb-1">
                <i class="bx bx-bolt-circle me-2 text-warning"></i>
                Quyền Tổng Hợp Đang Có Hiệu Lực
              </h5>
              <p class="text-secondary small mb-0">
                Tổng hợp tất cả quyền từ {{ activeCount }} gói đang hoạt động.
                Khi một gói hết hạn, các quyền riêng của gói đó sẽ bị thu hồi.
              </p>
            </div>
            <div class="qg-effective-count">{{ effectiveFeatures.length }}</div>
          </div>

          <div class="qg-effective-grid mt-3">
            <div
              v-for="feat in effectiveFeatures"
              :key="feat"
              class="qg-effective-chip"
            >
              <i :class="getFeatureInfo(feat).icon" class="me-2 text-warning"></i>
              <div>
                <div class="fw-semibold" style="font-size:12.5px;">{{ getFeatureInfo(feat).label || feat }}</div>
                <div class="text-secondary" style="font-size:11px;">{{ getFeatureInfo(feat).desc }}</div>
              </div>
            </div>
          </div>

          <!-- Limits Summary -->
          <div class="qg-limits-summary mt-3">
            <div class="qg-limit-item">
              <i class="bx bx-layer text-info"></i>
              <span>Tối đa <strong>{{ effectiveMaxDoi >= 999 ? 'không giới hạn' : effectiveMaxDoi + ' đời' }}</strong></span>
            </div>
            <div class="qg-limit-sep"></div>
            <div class="qg-limit-item">
              <i class="bx bx-group text-success"></i>
              <span>Tối đa <strong>{{ effectiveMaxMember >= 99999 ? 'không giới hạn' : effectiveMaxMember.toLocaleString() + ' thành viên' }}</strong></span>
            </div>
            <div class="qg-limit-sep"></div>
            <div class="qg-limit-item">
              <i class="bx bx-calendar text-warning"></i>
              <span>Hết hạn muộn nhất: <strong>{{ latestExpiry ? formatDate(latestExpiry) : 'Vô thời hạn' }}</strong></span>
            </div>
          </div>
        </div>

        <!-- Buy More Button -->
        <div class="text-center mt-4 pt-2">
          <router-link to="/dich-vu-goi" class="btn-qg-buy">
            <i class="bx bx-plus-circle me-2"></i>Mua Thêm Gói / Gia Hạn
          </router-link>
        </div>
      </template>
    </template>

  </div>
</template>

<script>
import axios from 'axios';

// ── Bảng tra cứu features (đồng bộ với GoiDichVu/index.vue) ─────────────────
const FEATURE_MAP = {
  tao_cay_gia_pha:     { label: 'Tạo Cây Gia Phả',       icon: 'bx bx-sitemap',       desc: 'Khởi tạo và quản lý cây gia phả dòng họ riêng' },
  them_thanh_vien:     { label: 'Thêm Thành Viên',        icon: 'bx bx-user-plus',     desc: 'Thêm mới thành viên không giới hạn vào cây' },
  sua_xoa_thanh_vien:  { label: 'Sửa/Xóa Thành Viên',    icon: 'bx bx-edit',          desc: 'Chỉnh sửa và xóa thông tin thành viên dòng họ' },
  quan_ly_vo_chong:    { label: 'Quản Lý Vợ/Chồng',      icon: 'bx bx-heart',         desc: 'Thiết lập và quản lý quan hệ hôn nhân' },
  quan_ly_con_nuoi:    { label: 'Quản Lý Con Nuôi',       icon: 'bx bx-user',          desc: 'Thêm và quản lý con nuôi trong gia đình' },
  xuat_pdf:            { label: 'Xuất PDF Gia Phả',       icon: 'bx bx-file-pdf',      desc: 'Xuất toàn bộ cây gia phả ra file PDF' },
  phe_duyet_de_xuat:   { label: 'Phê Duyệt Đề Xuất',     icon: 'bx bx-check-circle',  desc: 'Tiếp nhận và phê duyệt đề xuất chỉnh sửa từ thành viên' },
  tu_dong_phe_duyet:   { label: 'Tự Động Phê Duyệt',     icon: 'bx bx-check-double',  desc: 'Bật/tắt chế độ phê duyệt tự động cho đề xuất' },
  quan_ly_chi_nhanh:   { label: 'Quản Lý Chi Nhánh',     icon: 'bx bx-git-branch',    desc: 'Tạo và quản lý nhiều chi nhánh dòng họ' },
  phan_quyen_thanh_vien:{ label: 'Phân Quyền Thành Viên', icon: 'bx bx-shield',        desc: 'Cấp quyền quản lý cho các thành viên khác' },
  nhat_ky_hoat_dong:   { label: 'Nhật Ký Hoạt Động',     icon: 'bx bx-history',       desc: 'Xem lịch sử toàn bộ thao tác trong hệ thống' },
  quan_ly_su_kien:     { label: 'Quản Lý Sự Kiện',       icon: 'bx bx-calendar',      desc: 'Tạo, quản lý sự kiện họ tộc và lịch gặp mặt' },
  dang_ky_su_kien:     { label: 'Đăng Ký Sự Kiện',       icon: 'bx bx-calendar-check',desc: 'Thành viên đăng ký tham gia sự kiện' },
  tuong_niem:          { label: 'Tưởng Niệm',             icon: 'bx bx-moon',          desc: 'Thêm, xem tưởng niệm ngày giỗ, kỷ niệm' },
  nhac_gio_tu:         { label: 'Nhắc Ngày Giỗ Tự',      icon: 'bx bx-bell',          desc: 'Nhắc nhở tự động ngày giỗ âm lịch hàng năm' },
  quan_ly_tai_lieu:    { label: 'Tủ Sách Tài Liệu',      icon: 'bx bx-folder',        desc: 'Upload và quản lý tài liệu, hình ảnh dòng họ' },
  upload_hinh_anh:     { label: 'Upload Hình Ảnh',        icon: 'bx bx-image',         desc: 'Đăng tải hình ảnh thành viên và sự kiện' },
  album_gia_dinh:      { label: 'Album Gia Đình',         icon: 'bx bx-images',        desc: 'Tạo và quản lý album ảnh theo từng gia đình' },
  ban_do_mo_phan:      { label: 'Bản Đồ Mộ Phần',        icon: 'bx bx-map-pin',       desc: 'Đánh dấu và quản lý vị trí mộ phần GPS' },
  ban_do_nha_tho:      { label: 'Bản Đồ Nhà Thờ Họ',     icon: 'bx bx-map',           desc: 'Lưu vị trí và thông tin nhà thờ họ' },
  tra_cuu_ban_do:      { label: 'Tra Cứu Bản Đồ',        icon: 'bx bx-search-alt',    desc: 'Tìm kiếm địa điểm liên quan trên bản đồ' },
  quan_ly_dong_gop:    { label: 'Quản Lý Đóng Góp',      icon: 'bx bx-donate-heart',  desc: 'Ghi nhận và theo dõi đóng góp tài chính dòng họ' },
  bao_cao_tai_chinh:   { label: 'Báo Cáo Tài Chính',     icon: 'bx bx-bar-chart',     desc: 'Xem báo cáo thu chi quỹ dòng họ' },
  tra_cuu_quan_he:     { label: 'Tra Cứu Quan Hệ',       icon: 'bx bx-search',        desc: 'Tính toán và hiển thị mối quan hệ giữa hai thành viên' },
  xuat_csv:            { label: 'Xuất Dữ Liệu CSV',      icon: 'bx bx-spreadsheet',   desc: 'Xuất toàn bộ danh sách thành viên ra file Excel/CSV' },
  thong_ke_nang_cao:   { label: 'Thống Kê Nâng Cao',     icon: 'bx bx-analyse',       desc: 'Xem thống kê chi tiết về dòng họ theo thế hệ' },
  api_tich_hop:        { label: 'API Tích Hợp',           icon: 'bx bx-code-alt',      desc: 'Truy cập dữ liệu qua API dành cho developer' },
};

export default {
  name: 'QuanLyGoi',
  data() {
    return {
      isLoading: false,
      packages: [],
      effectiveFeatures: [],
      effectiveMaxDoi: 0,
      effectiveMaxMember: 0,
      activeCount: 0,
      latestExpiry: null,
      earliestExpiry: null,
      // Countdown timers
      countdownTimers: {},
      // Real-time countdown per package
      countdowns: {},
    };
  },
  mounted() {
    this.loadData();
  },
  beforeUnmount() {
    this.clearAllTimers();
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: 'Bearer ' + localStorage.getItem('access_token') } };
    },

    async loadData() {
      this.isLoading = true;
      this.clearAllTimers();
      try {
        const res = await axios.get('http://127.0.0.1:8000/api/doi-tac/my-packages', this.getHeaders());
        if (res.data.status) {
          const d = res.data.data;
          this.packages           = d.packages || [];
          this.effectiveFeatures  = d.effective_features || [];
          this.effectiveMaxDoi    = d.effective_max_doi || 0;
          this.effectiveMaxMember = d.effective_max_thanh_vien || 0;
          this.activeCount        = d.active_count || 0;
          this.latestExpiry       = d.latest_expiry || null;
          this.earliestExpiry     = d.earliest_expiry || null;

          // Khởi động countdown real-time cho từng gói còn hạn
          this.$nextTick(() => this.startCountdowns());
        }
      } catch (e) {
        console.error('Lỗi tải gói:', e);
      } finally {
        this.isLoading = false;
      }
    },

    startCountdowns() {
      this.packages.forEach(pkg => {
        if (pkg.is_active && pkg.ngay_ket_thuc) {
          this.updateCountdown(pkg);
          this.countdownTimers[pkg.id] = setInterval(() => {
            this.updateCountdown(pkg);
          }, 1000);
        }
      });
    },

    updateCountdown(pkg) {
      const expiry = new Date(pkg.ngay_ket_thuc + 'T23:59:59');
      const now    = new Date();
      const diff   = expiry - now;

      if (diff <= 0) {
        this.$set ? this.$set(this.countdowns, pkg.id, null) : (this.countdowns[pkg.id] = null);
        clearInterval(this.countdownTimers[pkg.id]);
        return;
      }

      const totalSecs  = Math.floor(diff / 1000);
      const days       = Math.floor(totalSecs / 86400);
      const hours      = Math.floor((totalSecs % 86400) / 3600);
      const minutes    = Math.floor((totalSecs % 3600) / 60);
      const seconds    = totalSecs % 60;

      this.countdowns = {
        ...this.countdowns,
        [pkg.id]: { days, hours, minutes, seconds },
      };
    },

    getCountdown(pkg) {
      const cd = this.countdowns[pkg.id];
      if (!cd) {
        return [
          { value: String(pkg.days_remaining || 0).padStart(2, '0'), label: 'Ngày' },
          { value: '00', label: 'Giờ' },
          { value: '00', label: 'Phút' },
          { value: '00', label: 'Giây' },
        ];
      }
      return [
        { value: String(cd.days).padStart(3, '0'),   label: 'Ngày' },
        { value: String(cd.hours).padStart(2, '0'),  label: 'Giờ' },
        { value: String(cd.minutes).padStart(2, '0'), label: 'Phút' },
        { value: String(cd.seconds).padStart(2, '0'), label: 'Giây' },
      ];
    },

    clearAllTimers() {
      Object.values(this.countdownTimers).forEach(t => clearInterval(t));
      this.countdownTimers = {};
    },

    getFeatureInfo(key) {
      return FEATURE_MAP[key] || { label: key, icon: 'bx bx-check', desc: '' };
    },

    getCardClass(pkg) {
      if (!pkg.is_active)       return 'qg-card-expired';
      if (pkg.is_expiring_soon) return 'qg-card-warning';
      return 'qg-card-active';
    },

    getStatusClass(pkg) {
      if (!pkg.is_active)       return 'qg-badge-expired';
      if (pkg.is_expiring_soon) return 'qg-badge-warning';
      return 'qg-badge-active';
    },

    getStatusText(pkg) {
      if (!pkg.is_active)       return 'Đã hết hạn';
      if (pkg.is_expiring_soon) return 'Sắp hết hạn';
      return 'Đang hoạt động';
    },

    getProgressClass(pkg) {
      if (pkg.progress_pct <= 15) return 'qg-progress-danger';
      if (pkg.progress_pct <= 30) return 'qg-progress-warn';
      return 'qg-progress-ok';
    },

    formatDate(dateStr) {
      if (!dateStr) return '---';
      return new Date(dateStr).toLocaleDateString('vi-VN', {
        day: '2-digit', month: '2-digit', year: 'numeric'
      });
    },

    formatCurrency(val) {
      return Number(val || 0).toLocaleString('vi-VN') + ' VNĐ';
    },
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap');

.quan-ly-goi-wrapper {
  font-family: 'Inter', sans-serif;
}

/* ── Banner ───────────────────────────────────────────────────────────────── */
.qg-banner {
  background: linear-gradient(135deg, rgba(212,175,55,0.08) 0%, rgba(243,156,18,0.04) 100%);
  border: 1px solid rgba(212,175,55,0.15);
  border-radius: 16px;
  padding: 20px 24px;
}
.qg-banner-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.qg-banner-title {
  font-family: 'Outfit', sans-serif;
  font-size: 16px;
  background: linear-gradient(135deg, #d4af37, #f39c12);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.qg-banner-sub { color: #64748b; font-size: 13px; }
.btn-qg-refresh {
  background: rgba(243,156,18,0.08);
  border: 1px solid rgba(243,156,18,0.2);
  color: #d97706;
  width: 38px; height: 38px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 18px; cursor: pointer; transition: all 0.25s;
}
.btn-qg-refresh:hover { background: #f39c12; color: #fff; }

/* ── Stats ────────────────────────────────────────────────────────────────── */
.qg-stat-card {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 16px 12px; border-radius: 14px; border: 1px solid;
  text-align: center; gap: 4px;
}
.qg-stat-icon { font-size: 1.5rem; margin-bottom: 4px; }
.qg-stat-num { font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; line-height: 1; }
.qg-stat-label { font-size: 11.5px; color: #64748b; margin-top: 2px; }

.qg-stat-active { background: rgba(16,185,129,0.05); border-color: rgba(16,185,129,0.15); }
.qg-stat-active .qg-stat-icon { color: #10b981; }
.qg-stat-active .qg-stat-num  { color: #059669; }

.qg-stat-feature { background: rgba(243,156,18,0.05); border-color: rgba(243,156,18,0.15); }
.qg-stat-feature .qg-stat-icon { color: #f59e0b; }
.qg-stat-feature .qg-stat-num  { color: #b45309; }

.qg-stat-gen { background: rgba(99,102,241,0.05); border-color: rgba(99,102,241,0.15); }
.qg-stat-gen .qg-stat-icon { color: #6366f1; }
.qg-stat-gen .qg-stat-num  { color: #4f46e5; }

.qg-stat-member { background: rgba(14,165,233,0.05); border-color: rgba(14,165,233,0.15); }
.qg-stat-member .qg-stat-icon { color: #0ea5e9; }
.qg-stat-member .qg-stat-num  { color: #0284c7; }

/* ── Package Card ─────────────────────────────────────────────────────────── */
.qg-package-card {
  background: #fff;
  border-radius: 20px;
  border: 1.5px solid;
  box-shadow: 0 4px 20px rgba(0,0,0,0.04);
  overflow: hidden;
  transition: box-shadow 0.2s;
}
.qg-package-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); }

.qg-card-active  { border-color: rgba(16,185,129,0.2);  }
.qg-card-warning { border-color: rgba(217,119,6,0.3); background: #fffbeb; }
.qg-card-expired { border-color: rgba(100,116,139,0.15); background: #f8fafc; opacity: 0.8; }

/* Card Header */
.qg-card-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 18px 20px 12px;
  border-bottom: 1px solid rgba(0,0,0,0.04);
}
.qg-card-title-wrap { display: flex; align-items: center; gap: 12px; }
.qg-pkg-icon {
  width: 44px; height: 44px; border-radius: 12px;
  background: linear-gradient(135deg, rgba(212,175,55,0.12), rgba(243,156,18,0.08));
  border: 1px solid rgba(212,175,55,0.2);
  display: flex; align-items: center; justify-content: center;
  color: #d97706; font-size: 1.3rem; flex-shrink: 0;
}
.qg-pkg-name  { font-family: 'Outfit', sans-serif; font-size: 14.5px; font-weight: 700; color: #1e293b; }
.qg-pkg-price { font-size: 12px; color: #10b981; font-weight: 600; }

/* Status badge */
.qg-status-badge {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 5px 12px; border-radius: 30px;
  font-size: 11.5px; font-weight: 700; flex-shrink: 0;
}
.qg-badge-active  { background: rgba(16,185,129,0.08); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
.qg-badge-warning { background: rgba(217,119,6,0.08);  color: #d97706; border: 1px solid rgba(217,119,6,0.2); }
.qg-badge-expired { background: rgba(100,116,139,0.08);color: #64748b; border: 1px solid rgba(100,116,139,0.2); }

.qg-pulse {
  width: 7px; height: 7px; background: #10b981;
  border-radius: 50%; flex-shrink: 0;
  box-shadow: 0 0 0 0 rgba(16,185,129,0.7);
  animation: qgPulse 1.8s infinite;
}
.qg-pulse-warn { background: #d97706; box-shadow: 0 0 0 0 rgba(217,119,6,0.7); }
@keyframes qgPulse {
  0%   { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16,185,129,0.7); }
  70%  { transform: scale(1);    box-shadow: 0 0 0 7px rgba(16,185,129,0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16,185,129,0); }
}

/* Countdown Section */
.qg-countdown-section { padding: 16px 20px; background: rgba(0,0,0,0.015); border-bottom: 1px solid rgba(0,0,0,0.04); }
.qg-countdown-label {
  display: flex; align-items: center; justify-content: space-between;
  font-size: 12px; color: #64748b; margin-bottom: 12px;
}
.qg-expiry-date { color: #475569; font-weight: 600; }

.qg-countdown-display {
  display: flex; gap: 8px; justify-content: center;
}
.qg-countdown-block {
  background: #0f172a; border-radius: 10px; min-width: 58px;
  padding: 10px 8px; text-align: center; flex: 1;
}
.qg-countdown-num { font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; color: #fbbf24; line-height: 1; }
.qg-countdown-unit { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px; }

.qg-countdown-forever, .qg-countdown-expired {
  text-align: center; padding: 10px 0;
  font-size: 13px; font-weight: 600;
}
.qg-countdown-forever { color: #0ea5e9; }
.qg-countdown-expired { color: #94a3b8; }

/* Progress */
.qg-progress-wrap { }
.qg-progress-bar {
  height: 5px; border-radius: 10px; background: #e2e8f0; overflow: hidden;
}
.qg-progress-fill {
  height: 100%; border-radius: 10px;
  background: linear-gradient(90deg, #10b981, #34d399);
  transition: width 0.5s ease;
}
.qg-progress-ok    .qg-progress-fill { background: linear-gradient(90deg, #10b981, #34d399); }
.qg-progress-warn  .qg-progress-fill { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.qg-progress-danger .qg-progress-fill { background: linear-gradient(90deg, #ef4444, #f87171); }
.qg-progress-labels {
  display: flex; justify-content: space-between;
  font-size: 10.5px; color: #94a3b8; margin-top: 4px;
}

/* Details */
.qg-card-details { padding: 12px 20px; display: flex; flex-direction: column; gap: 6px; }
.qg-detail-row {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 12.5px; color: #475569;
}
.qg-detail-row strong { color: #1e293b; font-size: 12.5px; }

/* Features */
.qg-features-section { padding: 14px 20px 18px; }
.qg-features-title { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 10px; font-family: 'Outfit', sans-serif; }
.qg-features-grid { display: flex; flex-wrap: wrap; gap: 6px; }
.qg-feat-chip {
  display: inline-flex; align-items: center; padding: 4px 10px;
  border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap;
  border: 1px solid;
}
.qg-feat-chip-active  { background: rgba(16,185,129,0.07); color: #15803d; border-color: rgba(16,185,129,0.18); }
.qg-feat-chip-expired { background: rgba(100,116,139,0.06); color: #94a3b8; border-color: rgba(100,116,139,0.15); }

/* ── Effective Section ────────────────────────────────────────────────────── */
.qg-effective-section {
  background: #fff;
  border: 1.5px solid rgba(212,175,55,0.2);
  border-radius: 20px;
  padding: 22px 24px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.04);
}
.qg-effective-header {
  display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
}
.qg-effective-header h5 { font-family: 'Outfit', sans-serif; font-size: 15px; color: #1e293b; }
.qg-effective-count {
  font-family: 'Outfit', sans-serif;
  background: linear-gradient(135deg, #d4af37, #f39c12);
  color: #fff; font-size: 1.2rem; font-weight: 800;
  width: 44px; height: 44px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.qg-effective-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 10px;
}
.qg-effective-chip {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 10px 14px; border-radius: 12px;
  background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);
  font-family: 'Inter', sans-serif;
}
.qg-effective-chip i { font-size: 1.1rem; margin-top: 1px; flex-shrink: 0; }

/* Limits summary */
.qg-limits-summary {
  display: flex; align-items: center; gap: 0;
  background: rgba(0,0,0,0.02); border-radius: 12px; padding: 12px 18px;
  flex-wrap: wrap; gap: 12px;
}
.qg-limit-item { display: flex; align-items: center; gap: 8px; font-size: 12.5px; color: #475569; }
.qg-limit-item i { font-size: 1rem; }
.qg-limit-item strong { color: #1e293b; }
.qg-limit-sep { width: 1px; height: 20px; background: rgba(0,0,0,0.08); }

/* Empty & Buy Button */
.qg-empty-state { background: #fff; border-radius: 20px; border: 1px solid rgba(0,0,0,0.05); padding: 40px 20px; }
.btn-qg-buy {
  display: inline-flex; align-items: center;
  padding: 12px 28px; border-radius: 30px;
  background: linear-gradient(135deg, #d4af37, #f39c12);
  color: #fff; font-weight: 700; font-size: 13.5px;
  text-decoration: none; letter-spacing: 0.3px;
  box-shadow: 0 6px 18px rgba(212,175,55,0.3);
  transition: all 0.25s;
}
.btn-qg-buy:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(212,175,55,0.4); color: #fff; }
</style>
