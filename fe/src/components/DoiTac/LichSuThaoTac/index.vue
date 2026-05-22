<template>
  <div class="card shadow-sm border-0 radius-10">
    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
      <h5 class="mb-0 fw-bold text-dark">
        <i class="bx bx-history text-danger me-2 fs-4"></i>Nhật Ký Thao Tác Của Bạn
      </h5>
      <span class="badge bg-light-danger text-danger px-3 py-2 radius-30">Minh bạch hoạt động</span>
    </div>
    <div class="card-body">
      <!-- Quick Stats Banner -->
      <div class="row mb-4 g-3">
        <div class="col-md-4">
          <div class="p-3 rounded-3 bg-light-danger bg-opacity-10 border border-danger border-opacity-10 d-flex align-items-center gap-3">
            <div class="widgets-icons bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
              <i class="bx bx-bolt-circle fs-4"></i>
            </div>
            <div>
              <h6 class="text-secondary small mb-1">Tổng Thao Tác Ghi Nhận</h6>
              <h4 class="fw-bold mb-0 text-dark">{{ filteredLogs.length }}</h4>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <!-- Filter and Search -->
          <div class="row g-3 h-100 align-items-center justify-content-end">
            <div class="col-sm-6 col-md-5">
              <div class="position-relative">
                <input 
                  type="text" 
                  class="form-control ps-5 radius-10 border-2 shadow-none border-light focus-danger" 
                  v-model="searchQuery" 
                  placeholder="Tìm hành động..."
                >
                <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary">
                  <i class="bx bx-search fs-5"></i>
                </span>
              </div>
            </div>
            <div class="col-sm-4 col-md-4">
              <select class="form-select radius-10 border-2 shadow-none border-light focus-danger" v-model="filterType">
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

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-danger" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="text-muted mt-2">Đang tải nhật ký thao tác cá nhân...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredLogs.length === 0" class="text-center py-5">
        <div class="mb-4">
          <i class="bx bx-receipt text-muted opacity-25" style="font-size: 80px;"></i>
        </div>
        <h4 class="fw-bold text-dark">Chưa Có Hoạt Động Nào</h4>
        <p class="text-muted mx-auto" style="max-width: 400px;">
          Không có nhật ký nào được ghi nhận cho tài khoản của bạn, hoặc không tìm thấy hành động khớp với bộ lọc hiện tại.
        </p>
      </div>

      <!-- Chronological Timeline -->
      <div v-else class="timeline-container px-2">
        <div class="timeline-line"></div>
        
        <div 
          class="timeline-item mb-4" 
          v-for="log in filteredLogs" 
          :key="log.id"
        >
          <!-- Timeline point icon -->
          <div class="timeline-badge" :class="getLogBadgeClass(log.hanh_dong)">
            <i :class="getLogIconClass(log.hanh_dong)"></i>
          </div>

          <!-- Timeline card content -->
          <div class="timeline-card p-3 border-2 border-light bg-white rounded-3 shadow-none hover-timeline-card">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
              <span class="badge radius-30 px-3 py-1 font-semibold" :class="getLogLabelClass(log.hanh_dong)">
                {{ classifyLog(log.hanh_dong) }}
              </span>
              <span class="text-secondary small">
                <i class="bx bx-time-five me-1"></i>{{ formatDateTime(log.thoi_gian || log.created_at) }}
              </span>
            </div>
            <h6 class="fw-bold text-dark mb-1" style="line-height: 1.5;">
              {{ log.hanh_dong }}
            </h6>
            <div class="text-muted small d-flex align-items-center gap-1">
              <i class="bx bx-user-circle"></i>
              Người thực hiện: <b>{{ log.nguoi_dung?.ho_ten || log.nguoi_dung?.username || 'Bạn' }}</b>
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
      if (!this.currentUser) return [];

      // First filter by partner's own ID
      let partnerLogs = this.allLogs.filter(log => log.nguoi_dung_id === this.currentUser.id);

      // Filter by type selection
      if (this.filterType !== 'Tất cả') {
        partnerLogs = partnerLogs.filter(log => this.classifyLog(log.hanh_dong) === this.filterType);
      }

      // Filter by search query
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
      axios.get('http://127.0.0.1:8000/api/nhat-ky-hoat-dong/get-data', this.getHeaders())
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
        case 'Tạo mới': return 'bg-success text-white border-success';
        case 'Cập nhật': return 'bg-warning text-dark border-warning';
        case 'Xóa': return 'bg-danger text-white border-danger';
        default: return 'bg-secondary text-white border-secondary';
      }
    },
    getLogLabelClass(action) {
      const type = this.classifyLog(action);
      switch (type) {
        case 'Tạo mới': return 'bg-light-success text-success';
        case 'Cập nhật': return 'bg-light-warning text-warning-custom';
        case 'Xóa': return 'bg-light-danger text-danger';
        default: return 'bg-light-secondary text-secondary';
      }
    }
  }
};
</script>

<style scoped>
.radius-10 { border-radius: 10px !important; }
.radius-30 { border-radius: 30px !important; }

.focus-danger:focus {
  border-color: #dc3545 !important;
  box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15) !important;
}

/* Timeline Layout Styling */
.timeline-container {
  position: relative;
  margin-top: 15px;
}

.timeline-line {
  position: absolute;
  left: 20px;
  top: 10px;
  bottom: 10px;
  width: 2px;
  background-color: #e9ecef;
  z-index: 1;
}

.timeline-item {
  position: relative;
  display: flex;
  align-items: flex-start;
  z-index: 2;
}

.timeline-badge {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  border: 4px solid #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  z-index: 3;
}

.timeline-card {
  margin-left: 20px;
  flex-grow: 1;
  border: 1px solid #f0f0f0;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-timeline-card:hover {
  transform: translateX(5px);
  border-color: rgba(220, 53, 69, 0.2) !important;
  box-shadow: 0 6px 15px rgba(220, 53, 69, 0.04) !important;
}

/* Color Palette details */
.bg-light-success { background-color: rgba(40, 167, 69, 0.08) !important; }
.bg-light-warning { background-color: rgba(255, 193, 7, 0.1) !important; }
.bg-light-danger { background-color: rgba(220, 53, 69, 0.08) !important; }
.bg-light-secondary { background-color: rgba(108, 117, 125, 0.08) !important; }

.text-warning-custom { color: #856404 !important; }

.font-semibold {
  font-weight: 600;
}
</style>
