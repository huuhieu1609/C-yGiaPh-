<template>
  <div class="card shadow-sm border-0 radius-10">
    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
      <h5 class="mb-0 fw-bold text-dark">
        <i class="bx bx-book-open text-primary me-2 fs-4"></i>Thư Viện Tài Liệu & Di Sản Dòng Họ
      </h5>
      <span class="badge bg-light-primary text-primary px-3 py-2 radius-30">Lưu giữ ngàn đời</span>
    </div>
    <div class="card-body">
      <!-- Description Banner -->
      <div class="alert alert-custom-indigo border-0 radius-10 p-3 mb-4 d-flex align-items-start gap-3">
        <i class="bx bxs-info-circle fs-3 text-indigo mt-1"></i>
        <div>
          <h6 class="fw-bold text-indigo mb-1">Bảo tàng số hóa di sản dòng tộc</h6>
          <p class="mb-0 text-muted small lh-base">
            Nơi lưu trữ, bảo tồn và số hóa các sắc phong, gia phả viết tay, chiếu chỉ vua ban, văn tự cổ hoặc hình ảnh di tích nhà thờ tổ. Mọi thông tin được mã hóa lưu trữ lâu dài để con cháu mai sau tra cứu và tự hào.
          </p>
        </div>
      </div>

      <!-- Action Panel -->
      <div class="row mb-4 g-3 align-items-center">
        <div class="col-md-6 col-lg-4">
          <div class="position-relative">
            <input 
              type="text" 
              class="form-control ps-5 radius-10 border-2 shadow-none border-light" 
              v-model="searchQuery" 
              placeholder="Tìm kiếm tài liệu, sắc phong..."
            >
            <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary">
              <i class="bx bx-search fs-5"></i>
            </span>
          </div>
        </div>
        <div class="col-md-6 col-lg-8 text-md-end">
          <button class="btn btn-indigo text-white radius-10 fw-bold px-4 shadow-sm hover-elevate" @click="openAddModal">
            <i class="bx bx-plus-circle me-1 fs-5"></i> Thêm Tài Liệu Mới
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-indigo" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="text-muted mt-2">Đang tìm lục tủ sách cổ...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredDocuments.length === 0" class="text-center py-5">
        <div class="mb-4">
          <i class="bx bx-book-reader text-muted opacity-25" style="font-size: 80px;"></i>
        </div>
        <h4 class="fw-bold text-dark">Thư Viện Chưa Có Sách Vở</h4>
        <p class="text-muted mx-auto" style="max-width: 420px;">
          Chưa có tài liệu hay sắc phong số hóa nào được lưu trữ, hoặc từ khóa tìm kiếm không khớp. Hãy tải lên tài liệu đầu tiên!
        </p>
        <button v-if="searchQuery === ''" class="btn btn-outline-indigo radius-10 px-4 mt-2" @click="openAddModal">
          <i class="bx bx-plus"></i> Số Hóa Tài Liệu
        </button>
      </div>

      <!-- Grid Cards -->
      <div v-else class="row g-4">
        <div 
          class="col-md-6 col-lg-4" 
          v-for="item in filteredDocuments" 
          :key="item.id"
        >
          <div class="card h-100 border-2 border-light shadow-none radius-12 hover-card-indigo position-relative overflow-hidden">
            <!-- Styling corner badge -->
            <div class="position-absolute top-0 end-0 p-2 d-flex gap-1 z-3">
              <button 
                class="btn btn-light btn-icon shadow-sm rounded-circle border btn-edit-hover" 
                title="Sửa" 
                @click="onEdit(item)"
              >
                <i class="bx bx-edit-alt text-warning"></i>
              </button>
              <button 
                class="btn btn-light btn-icon shadow-sm rounded-circle border btn-delete-hover" 
                title="Xóa" 
                @click="handleDelete(item.id)"
              >
                <i class="bx bx-trash text-danger"></i>
              </button>
            </div>

            <div class="card-body p-4 d-flex flex-column h-100">
              <!-- Icon based on format -->
              <div class="mb-3 d-inline-flex align-items-center justify-content-center bg-indigo-light text-indigo rounded-3 p-3" style="width: 54px; height: 54px;">
                <i :class="getDocumentIcon(item.file_path)" class="fs-2"></i>
              </div>

              <!-- Title -->
              <h5 class="fw-bold text-dark mb-2 text-truncate-2 lh-base flex-grow-0" style="min-height: 48px;" :title="item.tieu_de">
                {{ item.tieu_de }}
              </h5>

              <!-- Description -->
              <p class="text-muted small flex-grow-1 text-truncate-3 mb-3" style="line-height: 1.6;" :title="item.mo_ta">
                {{ item.mo_ta || 'Không có mô tả chi tiết cho di sản này.' }}
              </p>

              <!-- Actions/Links -->
              <div class="mt-auto pt-3 border-top border-light d-flex align-items-center justify-content-between">
                <span class="text-secondary small">
                  <i class="bx bx-time-five me-1"></i>{{ formatDate(item.created_at) }}
                </span>
                <a 
                  :href="item.file_path" 
                  target="_blank" 
                  class="btn btn-sm btn-indigo text-white px-3 radius-8 fw-semibold"
                  v-if="item.file_path && isValidUrl(item.file_path)"
                >
                  <i class="bx bx-link-external me-1"></i> Xem File
                </a>
                <span class="text-secondary small italic-style" v-else>
                  Không có tệp tin đính kèm
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Thêm/Sửa Tài Liệu -->
    <div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content radius-15 shadow-lg border-0">
          <div class="modal-header border-0 text-white radius-top-15" :class="isEditing ? 'bg-warning text-dark' : 'bg-indigo'">
            <h5 class="modal-title fw-bold">
              <i class="bx bx-bookmark-plus me-1"></i>
              {{ isEditing ? 'Chỉnh Sửa Tài Liệu' : 'Số Hóa Tài Liệu Gia Tộc Mới' }}
            </h5>
            <button 
              type="button" 
              class="btn-close" 
              :class="!isEditing ? 'btn-close-white' : ''" 
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="saveDocument">
              <div class="mb-3">
                <label class="form-label fw-bold">Tên Tài Liệu / Sắc Phong <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control radius-8 border-2 shadow-none border-light focus-indigo" 
                  v-model="currentDocument.tieu_de" 
                  placeholder="Ví dụ: Sắc phong Hậu Thần dòng họ Nguyễn Đức 1845"
                  required
                >
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Đường dẫn Tệp tin / URL Tài Liệu <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control radius-8 border-2 shadow-none border-light focus-indigo" 
                  v-model="currentDocument.file_path" 
                  placeholder="Ví dụ: https://drive.google.com/file/d/abc123xyz"
                  required
                >
                <span class="text-muted small d-block mt-1">Hỗ trợ các liên kết Drive, Dropbox, PDF hoặc tệp tin lưu trữ trực tuyến.</span>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Mô Tả Di Sản / Ý Nghĩa Lịch Sử</label>
                <textarea 
                  class="form-control radius-8 border-2 shadow-none border-light focus-indigo" 
                  rows="4" 
                  v-model="currentDocument.mo_ta" 
                  placeholder="Nhập nguồn gốc lịch sử, niên đại sắc phong hoặc tóm tắt nội dung văn tự chép tay..."
                ></textarea>
              </div>
              
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light px-4 radius-8" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn px-4 radius-8 fw-bold text-white shadow-sm" :class="isEditing ? 'btn-warning text-dark' : 'btn-indigo'">
                  <i class="bx bx-check me-1"></i>{{ isEditing ? 'Cập Nhật' : 'Lưu Tài Liệu' }}
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
  name: 'PartnerDocuments',
  data() {
    return {
      listDocuments: [],
      searchQuery: '',
      loading: false,
      isEditing: false,
      modal: null,
      currentDocument: {
        id: null,
        tieu_de: '',
        file_path: '',
        mo_ta: ''
      }
    };
  },
  computed: {
    filteredDocuments() {
      if (!this.searchQuery) {
        return this.listDocuments;
      }
      const query = this.searchQuery.toLowerCase().trim();
      return this.listDocuments.filter(item => 
        (item.tieu_de && item.tieu_de.toLowerCase().includes(query)) ||
        (item.mo_ta && item.mo_ta.toLowerCase().includes(query)) ||
        (item.file_path && item.file_path.toLowerCase().includes(query))
      );
    }
  },
  mounted() {
    this.$nextTick(() => {
      const modalEl = document.getElementById('documentModal');
      if (window.bootstrap && modalEl) {
        this.modal = new window.bootstrap.Modal(modalEl);
      }
    });
    this.loadDocuments();
  },
  methods: {
    getHeaders() {
      return { 
        headers: { 
          Authorization: `Bearer ${localStorage.getItem('access_token')}` 
        } 
      };
    },
    loadDocuments() {
      this.loading = true;
      axios.get('http://127.0.0.1:8000/api/tai-lieu/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.listDocuments = res.data.data;
          } else {
            toastr.error(res.data.message || 'Không thể tải danh sách tài liệu.');
          }
        })
        .catch(() => {
          toastr.error('Lỗi liên lạc hệ thống để lấy tủ sách dòng tộc.');
        })
        .finally(() => {
          this.loading = false;
        });
    },
    formatDate(date) {
      if (!date) return '---';
      return new Date(date).toLocaleDateString('vi-VN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    },
    isValidUrl(str) {
      return str && (str.startsWith('http://') || str.startsWith('https://') || str.startsWith('/'));
    },
    getDocumentIcon(path) {
      if (!path) return 'bx bx-file';
      const lowercasePath = path.toLowerCase();
      if (lowercasePath.includes('.pdf')) return 'bx bxs-file-pdf text-danger';
      if (lowercasePath.includes('.doc') || lowercasePath.includes('.docx')) return 'bx bxs-file-doc text-primary';
      if (lowercasePath.includes('.jpg') || lowercasePath.includes('.jpeg') || lowercasePath.includes('.png') || lowercasePath.includes('.gif')) return 'bx bxs-file-image text-success';
      if (lowercasePath.includes('.xls') || lowercasePath.includes('.xlsx')) return 'bx bxs-file-json text-success';
      return 'bx bx-book-content text-indigo';
    },
    openAddModal() {
      this.isEditing = false;
      this.currentDocument = {
        id: null,
        tieu_de: '',
        file_path: '',
        mo_ta: ''
      };
      if (this.modal) {
        this.modal.show();
      } else {
        toastr.error('Lỗi hiển thị form. Vui lòng tải lại trang.');
      }
    },
    onEdit(item) {
      this.isEditing = true;
      this.currentDocument = { ...item };
      if (this.modal) {
        this.modal.show();
      } else {
        toastr.error('Lỗi hiển thị form. Vui lòng tải lại trang.');
      }
    },
    saveDocument() {
      if (!this.currentDocument.tieu_de.trim()) {
        toastr.warning('Vui lòng điền tên tài liệu.');
        return;
      }
      if (!this.currentDocument.file_path.trim()) {
        toastr.warning('Vui lòng điền đường dẫn tài liệu.');
        return;
      }

      const url = this.isEditing 
        ? 'http://127.0.0.1:8000/api/tai-lieu/update' 
        : 'http://127.0.0.1:8000/api/tai-lieu/create';

      axios.post(url, this.currentDocument, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(res.data.message || 'Thành công!');
            this.loadDocuments();
            if (this.modal) {
              this.modal.hide();
            }
          } else {
            toastr.error(res.data.message || 'Thao tác không thành công.');
          }
        })
        .catch(() => {
          toastr.error('Có lỗi xảy ra khi kết nối máy chủ.');
        });
    },
    handleDelete(id) {
      if (confirm('Xác nhận xóa tài liệu cổ này khỏi thư viện số? Hành động này không thể khôi phục.')) {
        axios.post('http://127.0.0.1:8000/api/tai-lieu/delete', { id }, this.getHeaders())
          .then(res => {
            if (res.data.status) {
              toastr.success(res.data.message || 'Đã xóa tài liệu.');
              this.loadDocuments();
            } else {
              toastr.error(res.data.message || 'Không thể xóa dữ liệu.');
            }
          })
          .catch(() => {
            toastr.error('Lỗi truyền thông máy chủ.');
          });
      }
    }
  }
};
</script>

<style scoped>
.radius-10 { border-radius: 10px !important; }
.radius-12 { border-radius: 12px !important; }
.radius-15 { border-radius: 15px !important; }
.radius-30 { border-radius: 30px !important; }
.radius-8 { border-radius: 8px !important; }
.radius-top-15 { border-top-left-radius: 15px; border-top-right-radius: 15px; }

/* Custom Indigo Style definitions matching premium design */
.text-indigo { color: #6610f2 !important; }
.bg-indigo { background-color: #6610f2 !important; }
.bg-indigo-light { background-color: rgba(102, 16, 242, 0.08) !important; }
.btn-indigo {
  background-color: #6610f2 !important;
  border-color: #6610f2 !important;
}
.btn-indigo:hover {
  background-color: #520dc2 !important;
  border-color: #520dc2 !important;
  box-shadow: 0 4px 12px rgba(102, 16, 242, 0.25) !important;
}

.alert-custom-indigo {
  background-color: rgba(102, 16, 242, 0.05);
  border-left: 4px solid #6610f2 !important;
}

.text-indigo {
  color: #520dc2 !important;
}

.hover-elevate {
  transition: all 0.25s ease;
}
.hover-elevate:hover {
  transform: translateY(-2px);
}

.focus-indigo:focus {
  border-color: #6610f2 !important;
  box-shadow: 0 0 0 0.25rem rgba(102, 16, 242, 0.15) !important;
}

.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.text-truncate-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

.hover-card-indigo {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-card-indigo:hover {
  transform: translateY(-5px);
  border-color: rgba(102, 16, 242, 0.3) !important;
  box-shadow: 0 10px 25px rgba(102, 16, 242, 0.08) !important;
}

/* Action hover buttons inside cards */
.btn-icon {
  width: 32px;
  height: 32px;
  padding: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  opacity: 0.8;
  transition: all 0.2s ease;
}

.btn-icon:hover {
  opacity: 1;
  transform: scale(1.15);
}

.btn-edit-hover:hover {
  background-color: rgba(255, 193, 7, 0.15) !important;
}
.btn-delete-hover:hover {
  background-color: rgba(220, 53, 69, 0.15) !important;
}

.italic-style {
  font-style: italic;
  font-size: 13px;
}
</style>
