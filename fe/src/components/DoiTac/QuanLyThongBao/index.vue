<template>
  <div class="container-fluid px-0">
    <div class="card genealogy-main-card border-0 shadow-sm radius-16">
      <div class="card-header bg-transparent border-0 py-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <h5 class="mb-0 fw-bold theme-text-main">
          <i class="bx bx-bell text-warning me-2 fs-4 animate-bell"></i>Quản Lý Thông Báo Dòng Họ
        </h5>
        <span class="badge bg-orange-light-premium text-orange-premium">Phát tin tức gia tộc</span>
      </div>
      <div class="card-body px-4 pb-4">
        <div class="row mb-4 g-3 align-items-center">
          <div class="col-md-6 col-lg-4">
            <div class="position-relative">
              <input 
                type="text" 
                class="form-control premium-input ps-5" 
                v-model="searchQuery" 
                placeholder="Tìm kiếm thông báo theo tiêu đề..."
              >
              <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary">
                <i class="bx bx-search fs-5"></i>
              </span>
            </div>
          </div>
          <div class="col-md-6 col-lg-8 text-md-end">
            <button class="btn btn-sm btn-gradient-orange text-white radius-30 fw-bold px-4 shadow-sm" @click="openAddModal">
              <i class="bx bx-plus-circle me-1 fs-5"></i> Viết Thông Báo Mới
            </button>
          </div>
        </div>

        <div v-if="loading" class="text-center py-5 bg-transparent">
          <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Đang tải...</span>
          </div>
          <p class="text-secondary mt-2 small fw-medium">Đang tải danh sách thông báo...</p>
        </div>

        <div v-else-if="filteredAnnouncements.length === 0" class="text-center py-5 bg-transparent">
          <div class="mb-3">
            <i class="bx bx-bell-off text-warning opacity-25" style="font-size: 70px;"></i>
          </div>
          <h5 class="fw-bold theme-text-main">Không Tìm Thấy Thông Báo Nào</h5>
          <p class="text-secondary mx-auto small mb-3" style="max-width: 400px;">
            Chưa có thông báo nào được đăng hoặc không có kết quả nào khớp với tìm kiếm của bạn.
          </p>
          <button v-if="searchQuery === ''" class="btn btn-sm btn-action-edit px-4" @click="openAddModal">
            <i class="bx bx-plus"></i> Tạo Thông Báo Đầu Tiên
          </button>
        </div>

        <div v-else class="table-responsive table-container-premium">
          <table class="table align-middle mb-0 text-nowrap table-hover">
            <thead>
              <tr class="text-secondary text-uppercase small">
                <th style="width: 70px;" class="text-center">STT</th>
                <th style="min-width: 220px;" class="text-start ps-3">Tiêu Đề</th>
                <th style="min-width: 320px;" class="text-start">Nội Dung Chi Tiết</th>
                <th style="width: 180px;" class="text-center">Ngày Đăng</th>
                <th style="width: 140px;" class="text-center">Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in filteredAnnouncements" :key="item.id" class="table-row-premium">
                <td class="text-center fw-bold text-secondary bg-transparent">{{ index + 1 }}</td>
                <td class="bg-transparent text-start ps-3">
                  <div class="fw-bold row-member-name text-truncate-1">
                    {{ item.tieu_de }}
                  </div>
                </td>
                <td class="bg-transparent text-start">
                  <p class="text-secondary mb-0 text-truncate-2 text-wrap line-clamp-desc">
                    {{ item.noi_dung || 'Không có mô tả chi tiết' }}
                  </p>
                </td>
                <td class="text-center text-secondary bg-transparent small font-medium">
                  <i class="bx bx-calendar-event me-1 text-warning"></i>{{ formatDate(item.created_at) }}
                </td>
                <td class="text-center bg-transparent">
                  <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-sm btn-action-edit" title="Chỉnh sửa" @click="onEdit(item)">
                      <i class="bx bx-edit-alt"></i>
                    </button>
                    <button class="btn btn-sm btn-action-delete" title="Xóa thông báo" @click="handleDelete(item.id)">
                      <i class="bx bx-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="modal fade" id="announcementModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content radius-24 shadow-lg border-0 bg-adaptive-card overflow-hidden">
          <div class="modal-header border-0 bg-dark-premium text-white p-4">
            <h5 class="modal-title fw-bold text-warning">
              <i class="bx bx-paper-plane me-2"></i>
              {{ isEditing ? 'Chỉnh Sửa Thông Báo' : 'Đăng Thông Báo Dòng Họ Mới' }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 text-start">
            <form @submit.prevent="saveAnnouncement">
              <div class="mb-3">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Tiêu Đề Thông Báo <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control premium-input" 
                  v-model="currentAnnouncement.tieu_de" 
                  placeholder="Ví dụ: Lễ chắt tế xuân dòng họ Nguyễn Đức năm 2026"
                  required
                >
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Nội Dung Chi Tiết Tin Tức</label>
                <textarea 
                  class="form-control premium-textarea" 
                  rows="5" 
                  v-model="currentAnnouncement.noi_dung" 
                  placeholder="Nhập thông tin chi tiết gửi tới toàn thể dòng họ..."
                ></textarea>
              </div>
              
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light radius-30 px-4 fw-medium" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-gradient-orange radius-30 px-4 fw-bold text-white border-0 shadow-sm">
                  <i class="bx bx-check me-1"></i>{{ isEditing ? 'Cập Nhật' : 'Đăng Tin Ngay' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirm Modal -->
     <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content radius-16 shadow-lg border-0 bg-adaptive-card overflow-hidden">
          <div class="modal-header border-0 bg-dark-premium text-white p-3">
            <h6 class="modal-title fw-bold text-warning">Xác nhận xóa</h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-3 text-start">
            <p class="mb-0 text-secondary">Bạn có chắc chắn muốn xóa thông báo này? Hành động này không thể hoàn tác.</p>
          </div>
          <div class="modal-footer border-0 p-3 justify-content-end">
            <button type="button" class="btn btn-light radius-30 px-3" data-bs-dismiss="modal">Hủy</button>
            <button type="button" class="btn btn-gradient-orange text-white radius-30 px-3" @click="confirmDelete">Xóa</button>
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
  name: 'PartnerAnnouncements',
  data() {
    return {
      listAnnouncements: [],
      searchQuery: '',
      loading: false,
      isEditing: false,
      modal: null,
      currentAnnouncement: {
        id: null,
        tieu_de: '',
        noi_dung: ''
      },
      pendingDeleteId: null,
      deleteModal: null
    };
  },
  computed: {
    filteredAnnouncements() {
      if (!this.searchQuery) {
        return this.listAnnouncements;
      }
      const query = this.searchQuery.toLowerCase().trim();
      return this.listAnnouncements.filter(item => 
        (item.tieu_de && item.tieu_de.toLowerCase().includes(query)) ||
        (item.noi_dung && item.noi_dung.toLowerCase().includes(query))
      );
    }
  },
  mounted() {
    this.$nextTick(() => {
      const modalEl = document.getElementById('announcementModal');
      if (window.bootstrap && modalEl) {
        this.modal = new window.bootstrap.Modal(modalEl);
      }
        const delEl = document.getElementById('deleteConfirmModal');
        if (window.bootstrap && delEl) {
          this.deleteModal = new window.bootstrap.Modal(delEl);
        }
    });
    this.loadAnnouncements();
  },
  methods: {
    getHeaders() {
      return { 
        headers: { 
          Authorization: `Bearer ${localStorage.getItem('access_token')}` 
        } 
      };
    },
    loadAnnouncements() {
      this.loading = true;
      axios.get('http://127.0.0.1:8000/api/thong-bao/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.listAnnouncements = res.data.data;
          } else {
            toastr.error(res.data.message || 'Không thể lấy danh sách thông báo.');
          }
        })
        .catch(err => {
          toastr.error('Lỗi kết nối máy chủ khi lấy dữ liệu.');
        })
        .finally(() => {
          this.loading = false;
        });
    },
    formatDate(date) {
      if (!date) return '---';
      return new Date(date).toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    openAddModal() {
      this.isEditing = false;
      this.currentAnnouncement = {
        id: null,
        tieu_de: '',
        noi_dung: ''
      };
      if (this.modal) {
        this.modal.show();
      } else {
        toastr.error('Lỗi tải giao diện Modal. Vui lòng thử lại!');
      }
    },
    onEdit(item) {
      this.isEditing = true;
      this.currentAnnouncement = { ...item };
      if (this.modal) {
        this.modal.show();
      } else {
        toastr.error('Lỗi tải giao diện Modal. Vui lòng thử lại!');
      }
    },
    saveAnnouncement() {
      if (!this.currentAnnouncement.tieu_de.trim()) {
        toastr.warning('Vui lòng nhập tiêu đề thông báo.');
        return;
      }

      const url = this.isEditing 
        ? 'http://127.0.0.1:8000/api/thong-bao/update' 
        : 'http://127.0.0.1:8000/api/thong-bao/create';

      axios.post(url, this.currentAnnouncement, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(res.data.message || 'Thao tác thành công!');
            this.loadAnnouncements();
            if (this.modal) {
              this.modal.hide();
            }
          } else {
            toastr.error(res.data.message || 'Lỗi xử lý dữ liệu.');
          }
        })
        .catch(() => {
          toastr.error('Lỗi kết nối máy chủ.');
        });
    },
    handleDelete(id) {
      this.pendingDeleteId = id;
      if (this.deleteModal) {
        this.deleteModal.show();
      } else {
        // fallback to confirm
        if (confirm('Bạn có chắc chắn muốn xóa thông báo này? Hành động này không thể hoàn tác.')) {
          this.confirmDelete();
        }
      }
    },
    confirmDelete() {
      const id = this.pendingDeleteId;
      if (!id) return;
      axios.post('http://127.0.0.1:8000/api/thong-bao/delete', { id }, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(res.data.message || 'Xóa thông báo thành công.');
            this.loadAnnouncements();
          } else {
            toastr.error(res.data.message || 'Không thể xóa thông báo.');
          }
        })
        .catch(() => {
          toastr.error('Lỗi kết nối máy chủ khi thực hiện xóa.');
        })
        .finally(() => {
          if (this.deleteModal) this.deleteModal.hide();
          this.pendingDeleteId = null;
        });
    }
  }
};
</script>

<style scoped>
/* ─── KHUNG CARD CHỦ ĐẠO THÍCH ỨNG THEME ─── */
.genealogy-main-card {
  background: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 16px !important;
}
.theme-text-main { color: var(--text-main) !important; }
.text-secondary { color: var(--text-sub) !important; }
.bg-adaptive-card { background: var(--card-bg) !important; }

/* Các ô nhập liệu viên thuốc đồng bộ */
.premium-input {
  border-radius: 12px !important;
  border: 1px solid var(--border-color) !important;
  padding: 10px 16px !important;
  font-size: 14px;
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  box-shadow: none !important;
  transition: all 0.2s ease;
}
.premium-input:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg) !important;
}

.premium-textarea {
  border-radius: 14px !important;
  border: 1px solid var(--border-color) !important;
  padding: 12px 16px !important;
  font-size: 14px;
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  box-shadow: none !important;
  resize: none;
}
.premium-textarea:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg) !important;
}

/* Nút bấm Cam Đào Gradient */
.btn-gradient-orange {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
  border: none !important;
  color: #ffffff !important;
  box-shadow: 0 4px 12px rgba(244, 63, 94, 0.15) !important;
  transition: all 0.25s ease;
}
.btn-gradient-orange:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(244, 63, 94, 0.25) !important;
}
.radius-30 { border-radius: 30px !important; }
.radius-24 { border-radius: 24px !important; }

/* ─── HỆ THỐNG BẢNG BIỂU ĐỒNG BỘ MỜ ─── */
.table-container-premium {
  border: 1px solid var(--border-color);
  border-radius: 12px;
  overflow: hidden;
}
.table thead th {
  background-color: var(--input-bg) !important;
  color: var(--text-sub) !important;
  font-weight: 600 !important;
  font-size: 12.5px !important;
  text-transform: uppercase !important;
  border-bottom: 1px solid var(--border-color) !important;
  padding: 15px 16px !important;
}
.table-row-premium {
  border-bottom: 1px solid var(--border-color) !important;
  transition: background-color 0.2s ease;
}
.table-row-premium:hover { background-color: var(--input-bg) !important; }
.row-member-name { font-size: 14.5px; color: var(--text-main); }
.table-row-premium td { padding: 15px 16px !important; font-size: 13.5px; }

/* Truncate ép dòng an toàn */
.text-truncate-1 {
  display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
}
.line-clamp-desc {
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
  max-width: 480px; line-height: 1.5; font-size: 13.5px;
}

/* Nút sửa bút chì & nút xóa đồng bộ */
.btn-action-edit, .btn-action-delete {
  background: transparent !important;
  border-radius: 8px !important;
  font-size: 15px; padding: 5px 10px !important;
  transition: all 0.2s ease;
}
.btn-action-edit { border: 1px solid rgba(245, 158, 11, 0.3) !important; color: #f59e0b !important; }
.btn-action-edit:hover { background: #f59e0b !important; color: white !important; }

.btn-action-delete { border: 1px solid rgba(239, 68, 68, 0.3) !important; color: #ef4444 !important; }
.btn-action-delete:hover { background: #ef4444 !important; color: white !important; }

/* Badge tin tức dòng tộc mờ pastel */
.bg-orange-light-premium { background-color: rgba(249, 115, 22, 0.08) !important; border: 1px solid rgba(249, 115, 22, 0.15); }
.text-orange-premium { color: #f97316 !important; font-weight: 700; }

/* MODAL HEADER PREMIUM */
.bg-dark-premium {
  background: linear-gradient(145deg, #1a1c2e 0%, #141625 100%) !important;
  border-bottom: 1px solid var(--border-color);
}

/* Hiệu ứng chuông rung nhẹ nhàng kỹ thuật */
.animate-bell { animation: slowWobble 4s ease-in-out infinite; display: inline-block; }
@keyframes slowWobble {
  0%, 100% { transform: rotate(0deg); }
  5%, 15% { transform: rotate(10deg); }
  10%, 20% { transform: rotate(-10deg); }
  25% { transform: rotate(0deg); }
}
</style>