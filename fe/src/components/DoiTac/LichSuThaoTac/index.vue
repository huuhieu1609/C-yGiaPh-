<template>
  <div class="container-fluid px-0">
    <div class="card genealogy-main-card border-0 shadow-sm radius-16">
      <div class="card-header bg-transparent border-0 py-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <h5 class="mb-0 fw-bold theme-text-main">
          <i class="bx bx-history text-warning me-2 fs-4"></i>Nhật Ký Thao Tác Của Bạn
        </h5>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="loadLogs" :disabled="loading" title="Làm mới dữ liệu">
            <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': loading}"></i>
          </button>
          <span class="badge bg-orange-light-premium text-orange-premium">Minh bạch hoạt động</span>
        </div>
      </div>
      <div class="card-body px-4 pb-4">
        <div class="row mb-4 g-3 align-items-center">
          <div class="col-md-4">
            <div class="p-3 rounded-4 bg-adaptive-input border d-flex align-items-center gap-3 widget-kpi-log">
              <div class="widgets-icons text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="bx bx-bolt-circle fs-4 text-warning"></i>
              </div>
              <div>
                <h6 class="text-secondary small mb-1 font-medium">Tổng thao tác ghi nhận</h6>
                <h4 class="fw-bold mb-0 theme-text-main font-bold">{{ filteredLogs.length }}</h4>
              </div>
            </div>
          </div>
          <div class="col-md-8">
            <div class="row g-3 justify-content-end">
              <div class="col-sm-6 col-md-5">
                <div class="position-relative">
                  <input 
                    type="text" 
                    class="form-control premium-input ps-5" 
                    v-model="searchQuery" 
                    placeholder="Tìm hành động..."
                  >
                  <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary">
                    <i class="bx bx-search fs-5"></i>
                  </span>
                </div>
              </div>
              <div class="col-sm-6 col-md-4">
                <select class="form-select premium-select" v-model="filterType">
                  <option value="Tất cả">-- Tất cả loại --</option>
                  <option value="Tạo mới">Tạo mới / Thêm</option>
                  <option value="Cập nhật">Cập nhật / Sửa</option>
                  <option value="Xóa">Xóa bỏ</option>
                  <option value="Hệ thống">Hệ thống & Khác</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div v-if="loading" class="text-center py-5 bg-transparent">
          <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Đang tải...</span>
          </div>
          <p class="text-secondary mt-2 small fw-medium">Đang tải nhật ký thao tác cá nhân...</p>
        </div>

        <div v-else-if="filteredLogs.length === 0" class="text-center py-5 bg-transparent">
          <div class="mb-3">
            <i class="bx bx-receipt text-warning opacity-25" style="font-size: 70px;"></i>
          </div>
          <h5 class="fw-bold theme-text-main">Chưa Có Hoạt Động Nào</h5>
          <p class="text-secondary mx-auto small" style="max-width: 400px;">
            Không có nhật ký nào được ghi nhận cho tài khoản của bạn, hoặc không tìm thấy hành động khớp với bộ lọc hiện tại.
          </p>
        </div>

        <div v-else class="timeline-container px-2">
          <div class="timeline-line"></div>
          
          <div 
            class="timeline-item mb-4 animate-timeline-in" 
            v-for="log in filteredLogs" 
            :key="log.id"
          >
            <div class="timeline-badge" :class="getLogBadgeClass(log.hanh_dong)">
              <i :class="getLogIconClass(log.hanh_dong)"></i>
            </div>

            <div class="card timeline-card p-3 border radius-12 bg-adaptive-card hover-timeline-card shadow-none">
              <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <span class="badge font-semibold" :class="getLogLabelClass(log.hanh_dong)">
                  {{ classifyLog(log.hanh_dong) }}
                </span>
                <span class="text-secondary small font-medium">
                  <i class="bx bx-time-five me-1 text-warning"></i>{{ formatDateTime(log.thoi_gian || log.created_at) }}
                </span>
              </div>
              <h6 class="fw-bold theme-text-main mb-2 lh-base text-wrap" style="font-size: 14.5px;">
                {{ log.hanh_dong }}
              </h6>
              <div class="text-secondary small d-flex align-items-center gap-1 font-medium">
                <i class="bx bx-user-circle fs-6"></i>
                Nơi thực hiện: <span class="theme-text-main fw-semibold">{{ log.nguoi_dung?.ho_ten || log.nguoi_dung?.username || 'Bạn' }}</span>
              </div>
            </div>
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
  name: 'PartnerActivityLogs',
  data() {
    return {
      allLogs: [],
      currentUser: null,
      searchQuery: '',
      filterType: 'Tất cả',
      loading: false
    };
  },
  computed: {
    filteredLogs() {
      let partnerLogs = [...this.allLogs];

      if (this.filterType !== 'Tất cả') {
        partnerLogs = partnerLogs.filter(log => this.classifyLog(log.hanh_dong) === this.filterType);
      }

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase().trim();
        partnerLogs = partnerLogs.filter(log => 
          log.hanh_dong && log.hanh_dong.toLowerCase().includes(query)
        );
      }

      return partnerLogs;
    }
  },
  mounted() {
    this.getCurrentUser();
    this.loadLogs();
  },
  methods: {
    getHeaders() {
      return { 
        headers: { 
          Authorization: `Bearer ${localStorage.getItem('access_token')}` 
        } 
      };
    },
    getCurrentUser() {
      const userStr = localStorage.getItem('user');
      if (userStr) {
        try {
          this.currentUser = JSON.parse(userStr);
        } catch (e) {
          toastr.error('Lỗi định dạng tài khoản đăng nhập.');
        }
      }
    },
    loadLogs() {
      this.loading = true;
      axios.get('http://127.0.0.1:8000/api/nhat-ky-hoat-dong/get-my-logs', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.allLogs = res.data.data;
          } else {
            toastr.error(res.data.message || 'Không thể lấy dữ liệu nhật ký.');
          }
        })
        .catch(() => {
          toastr.error('Lỗi kết nối máy chủ khi lấy lịch sử hoạt động.');
        })
        .finally(() => {
          this.loading = false;
        });
    },
    formatDateTime(dateStr) {
      if (!dateStr) return '---';
      const date = new Date(dateStr);
      return date.toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    classifyLog(action) {
      if (!action) return 'Hệ thống';
      const act = action.toLowerCase();
      if (act.includes('tạo') || act.includes('thêm') || act.includes('khởi tạo')) return 'Tạo mới';
      if (act.includes('cập nhật') || act.includes('sửa') || act.includes('thay đổi')) return 'Cập nhật';
      if (act.includes('xóa')) return 'Xóa';
      return 'Hệ thống';
    },
    getLogIconClass(action) {
      const type = this.classifyLog(action);
      switch (type) {
        case 'Tạo mới': return 'bx bx-plus';
        case 'Cập nhật': return 'bx bx-edit-alt';
        case 'Xóa': return 'bx bx-trash';
        default: return 'bx bx-cog';
      }
    },
    getLogBadgeClass(action) {
      const type = this.classifyLog(action);
      switch (type) {
        case 'Tạo mới': return 'badge-create-node text-white';
        case 'Cập nhật': return 'badge-update-node text-white';
        case 'Xóa': return 'badge-delete-node text-white';
        default: return 'badge-system-node text-white';
      }
    },
    getLogLabelClass(action) {
      const type = this.classifyLog(action);
      switch (type) {
        case 'Tạo mới': return 'label-create-pastel';
        case 'Cập nhật': return 'label-update-pastel';
        case 'Xóa': return 'label-delete-pastel';
        default: return 'label-system-pastel';
      }
    }
  }
};
</script>

<style scoped>
/* ─── KHUNG CARD VÀ PHÔNG NỀN THÍCH ỨNG THEME ─── */
.genealogy-main-card {
  background: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 16px !important;
}
.theme-text-main { color: var(--text-main) !important; }
.text-secondary { color: var(--text-sub) !important; }
.bg-adaptive-card { background: var(--card-bg) !important; }
.bg-adaptive-input { background: var(--input-bg) !important; border: 1px solid var(--border-color) !important; }

.widget-kpi-log { border-left: 4px solid #f97316 !important; }

/* Thanh nhập liệu & Selectbox viên thuốc */
.premium-input, .premium-select {
  border-radius: 30px !important;
  border: 1px solid var(--border-color) !important;
  padding: 8px 16px !important;
  font-size: 14px;
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  box-shadow: none !important;
  transition: all 0.25s ease;
}
.premium-input:focus, .premium-select:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg) !important;
}

/* ─── HỆ THỐNG TRỤC DÒNG THỜI GIAN TIMELINE ADAPTIVE ─── */
.timeline-container {
  position: relative;
  margin-top: 20px;
}

/* Đường chỉ dọc của trục */
.timeline-line {
  position: absolute;
  left: 20px; top: 10px; bottom: 10px;
  width: 2px;
  background-color: var(--border-color);
  z-index: 1;
}

.timeline-item {
  position: relative;
  display: flex;
  align-items: flex-start;
  z-index: 2;
}

/* Nút tròn điểm neo liên kết */
.timeline-badge {
  width: 40px; height: 40px;
  border-radius: 50%;
  border: 4px solid var(--card-bg);
  display: inline-flex; align-items: center; justify-content: center;
  font-size: 16px; flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  z-index: 3;
  transition: border-color 0.3s;
}

.timeline-card {
  margin-left: 20px;
  flex-grow: 1;
  border: 1px solid var(--border-color) !important;
  box-shadow: none !important;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-timeline-card:hover {
  transform: translateX(5px);
  border-color: rgba(249, 115, 22, 0.35) !important;
  box-shadow: 0 6px 20px rgba(249, 115, 22, 0.05) !important;
}

/* ─── ĐỊNH NGHĨA BADGE ĐIỂM NEO NÚT TRÒN ─── */
.badge-create-node { background-color: #10b981 !important; }
.badge-update-node { background-color: #f59e0b !important; }
.badge-delete-node { background-color: #ef4444 !important; }
.badge-system-node { background-color: #6b7280 !important; }

/* ─── HỆ THỐNG BADGE MỜ PASTEL MÙA XUÂN ─── */
.badge { padding: 5px 12px !important; font-size: 11px !important; font-weight: 700 !important; border-radius: 30px !important; }

.label-create-pastel { background-color: rgba(16, 185, 129, 0.08) !important; color: #10b981 !important; border: 1px solid rgba(16, 185, 129, 0.15); }
.label-update-pastel { background-color: rgba(245, 158, 11, 0.08) !important; color: #f59e0b !important; border: 1px solid rgba(245, 158, 11, 0.15); }
.label-delete-pastel { background-color: rgba(239, 68, 68, 0.08) !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.15); }
.label-system-pastel { background-color: rgba(107, 114, 128, 0.08) !important; color: #6b7280 !important; border: 1px solid rgba(107, 114, 128, 0.15); }

.bg-orange-light-premium { background-color: rgba(249, 115, 22, 0.08) !important; border: 1px solid rgba(249, 115, 22, 0.15); }
.text-orange-premium { color: #f97316 !important; font-weight: 700; }
.font-semibold { font-weight: 600; }
.font-medium { font-weight: 500; }

.btn-refresh-premium {
  background: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
  width: 36px;
  height: 36px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-refresh-premium:hover {
  transform: rotate(30deg) scale(1.05);
  border-color: #f97316 !important;
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
}
.btn-refresh-premium:active {
  transform: scale(0.95);
}

.radius-16 { border-radius: 16px !important; }
.radius-12 { border-radius: 12px !important; }
.radius-30 { border-radius: 30px !important; }
</style>