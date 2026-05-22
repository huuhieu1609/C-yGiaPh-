<template>
  <div class="card shadow-sm border-0 radius-10">
    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
      <h5 class="mb-0 fw-bold text-dark">
        <i class="bx bx-bell text-info me-2 fs-4"></i>Quản Lý Thông Báo Dòng Họ
      </h5>
      <span class="badge bg-light-info text-info px-3 py-2 radius-30">Phát tin tức gia tộc</span>
    </div>
    <div class="card-body">
      <!-- Search and Add Banner -->
      <div class="row mb-4 g-3 align-items-center">
        <div class="col-md-6 col-lg-4">
          <div class="position-relative">
            <input 
              type="text" 
              class="form-control ps-5 radius-10 border-2 shadow-none border-light" 
              v-model="searchQuery" 
              placeholder="Tìm kiếm thông báo theo tiêu đề..."
            >
            <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary">
              <i class="bx bx-search fs-5"></i>
            </span>
          </div>
        </div>
        <div class="col-md-6 col-lg-8 text-md-end">
          <button class="btn btn-info text-white radius-10 fw-bold px-4 shadow-sm hover-elevate" @click="openAddModal">
            <i class="bx bx-plus-circle me-1 fs-5"></i> Viết Thông Báo Mới
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-info" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="text-muted mt-2">Đang tải danh sách thông báo...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredAnnouncements.length === 0" class="text-center py-5">
        <div class="mb-4">
          <i class="bx bx-bell-off text-muted opacity-25" style="font-size: 80px;"></i>
        </div>
        <h4 class="fw-bold text-dark">Không Tìm Thấy Thông Báo Nào</h4>
        <p class="text-muted mx-auto" style="max-width: 400px;">
          Chưa có thông báo nào được đăng hoặc không có kết quả nào khớp với tìm kiếm của bạn. Hãy tạo thông báo đầu tiên ngay!
        </p>
        <button v-if="searchQuery === ''" class="btn btn-outline-info radius-10 px-4 mt-2" @click="openAddModal">
          <i class="bx bx-plus"></i> Tạo Thông Báo
        </button>
      </div>

      <!-- Table Content -->
      <div v-else class="table-responsive">
        <table class="table table-hover align-middle border border-light-subtle rounded-3 overflow-hidden">
          <thead class="table-light text-uppercase small fw-bold text-secondary">
            <tr>
              <th style="width: 60px;" class="text-center">STT</th>
              <th style="min-width: 200px;">Tiêu Đề</th>
              <th style="min-width: 300px;">Nội Dung Chi Tiết</th>
              <th style="width: 160px;" class="text-center">Ngày Đăng</th>
              <th style="width: 140px;" class="text-center">Hành Động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in filteredAnnouncements" :key="item.id" class="transition-row">
              <td class="text-center fw-semibold text-secondary">{{ index + 1 }}</td>
              <td>
                <div class="fw-bold text-dark text-truncate-1" style="font-size: 15px;">
                  {{ item.tieu_de }}
                </div>
              </td>
              <td>
                <p class="text-muted mb-0 text-truncate-2" style="font-size: 13.5px; max-width: 500px; line-height: 1.5;">
                  {{ item.noi_dung || 'Không có mô tả chi tiết' }}
                </p>
              </td>
              <td class="text-center text-secondary" style="font-size: 13.5px;">
                <i class="bx bx-calendar-event me-1"></i>{{ formatDate(item.created_at) }}
              </td>
              <td class="text-center">
                <div class="d-flex justify-content-center gap-2">
                  <button 
                    class="btn btn-sm btn-outline-warning btn-icon-action" 
                    title="Chỉnh sửa" 
                    @click="onEdit(item)"
                  >
                    <i class="bx bx-edit-alt"></i>
                  </button>
                  <button 
                    class="btn btn-sm btn-outline-danger btn-icon-action" 
                    title="Xóa thông báo" 
                    @click="handleDelete(item.id)"
                  >
                    <i class="bx bx-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Thêm/Sửa Thông Báo -->
    <div class="modal fade" id="announcementModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content radius-15 shadow-lg border-0">
          <div class="modal-header border-0 text-white radius-top-15" :class="isEditing ? 'bg-warning text-dark' : 'bg-info'">
            <h5 class="modal-title fw-bold">
              <i class="bx bx-paper-plane me-1"></i>
              {{ isEditing ? 'Chỉnh Sửa Thông Báo' : 'Đăng Thông Báo Dòng Họ Mới' }}
            </h5>
            <button 
              type="button" 
              class="btn-close" 
              :class="!isEditing ? 'btn-close-white' : ''" 
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="saveAnnouncement">
              <div class="mb-3">
                <label class="form-label fw-bold">Tiêu Đề Thông Báo <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control radius-8 border-2 shadow-none border-light focus-active" 
                  v-model="currentAnnouncement.tieu_de" 
                  placeholder="Ví dụ: Lễ chắt tế xuân dòng họ Nguyễn Đức năm 2026"
                  required
                >
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Nội Dung Chi Tiết</label>
                <textarea 
                  class="form-control radius-8 border-2 shadow-none border-light focus-active" 
                  rows="5" 
                  v-model="currentAnnouncement.noi_dung" 
                  placeholder="Nhập thông tin chi tiết gửi tới toàn thể dòng họ..."
                ></textarea>
              </div>
              
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light px-4 radius-8" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn px-4 radius-8 fw-bold text-white shadow-sm" :class="isEditing ? 'btn-warning text-dark' : 'btn-info'">
                  <i class="bx bx-check me-1"></i>{{ isEditing ? 'Cập Nhật' : 'Đăng Tin Ngay' }}
                </button>
              </div>
            </form>
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
      }
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
      if (confirm('Bạn có chắc chắn muốn xóa thông báo này? Hành động này không thể hoàn tác.')) {
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
          });
      }
    }
  }
};
</script>

<style scoped>
.radius-10 { border-radius: 10px !important; }
.radius-15 { border-radius: 15px !important; }
.radius-30 { border-radius: 30px !important; }
.radius-8 { border-radius: 8px !important; }
.radius-top-15 { border-top-left-radius: 15px; border-top-right-radius: 15px; }

.hover-elevate {
  transition: all 0.25s ease;
}
.hover-elevate:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(13, 202, 240, 0.25) !important;
}

.focus-active:focus {
  border-color: #0dcaf0 !important;
  box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.15) !important;
}

.text-truncate-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

.btn-icon-action {
  width: 32px;
  height: 32px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.btn-icon-action:hover {
  transform: scale(1.08);
}

.transition-row {
  transition: background-color 0.2s ease;
}

.transition-row:hover {
  background-color: rgba(13, 202, 240, 0.02) !important;
}
</style>
