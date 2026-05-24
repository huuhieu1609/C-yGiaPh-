<template>
  <div class="container-fluid px-0">
    <div class="card genealogy-main-card border-0 shadow-sm radius-16">
      <div class="card-header bg-transparent border-0 py-4 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <h5 class="mb-0 fw-bold theme-text-main">
          <i class="bx bx-book-open text-warning me-2 fs-4"></i>Thư Viện Tài Liệu & Di Sản Dòng Họ
        </h5>
        <span class="badge bg-orange-light-premium text-orange-premium">Lưu giữ ngàn đời</span>
      </div>
      <div class="card-body px-4 pb-4">
        <div class="alert alert-custom-orange border-0 radius-12 p-3 mb-4 d-flex align-items-start gap-3 bg-adaptive-input">
          <i class="bx bxs-info-circle fs-4 text-warning mt-0.5"></i>
          <div>
            <h6 class="fw-bold text-orange-premium mb-1">Bảo tàng số hóa di sản dòng tộc</h6>
            <p class="mb-0 text-secondary small lh-base">
              Nơi lưu trữ, bảo tồn và số hóa các sắc phong, gia phả viết tay, chiếu chỉ vua ban, văn tự cổ hoặc hình ảnh di tích nhà thờ tổ. Mọi thông tin được mã hóa lưu trữ lâu dài để con cháu mai sau tra cứu và tự hào.
            </p>
          </div>
        </div>

        <div class="row mb-4 g-3 align-items-center">
          <div class="col-md-6 col-lg-4">
            <div class="position-relative">
              <input 
                type="text" 
                class="form-control premium-input ps-5" 
                v-model="searchQuery" 
                placeholder="Tìm kiếm tài liệu, sắc phong..."
              >
              <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary">
                <i class="bx bx-search fs-5"></i>
              </span>
            </div>
          </div>
          <div class="col-md-6 col-lg-8 text-md-end">
            <button class="btn btn-sm btn-gradient-orange text-white radius-30 fw-bold px-4 shadow-sm" @click="openAddModal">
              <i class="bx bx-plus-circle me-1 fs-5"></i> Thêm Tài Liệu Mới
            </button>
          </div>
        </div>

        <div v-if="loading" class="text-center py-5 bg-transparent">
          <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Đang tải...</span>
          </div>
          <p class="text-secondary mt-2 small fw-medium">Đang tìm lục tủ sách cổ...</p>
        </div>

        <div v-else-if="filteredDocuments.length === 0" class="text-center py-5 bg-transparent">
          <div class="mb-3">
            <i class="bx bx-book-reader text-warning opacity-25" style="font-size: 70px;"></i>
          </div>
          <h5 class="fw-bold theme-text-main">Thư Viện Chưa Có Sách Vở</h5>
          <p class="text-secondary mx-auto small mb-3" style="max-width: 420px;">
            Chưa có tài liệu hay sắc phong số hóa nào được lưu trữ, hoặc từ khóa tìm kiếm không khớp. Hãy tải lên tài liệu đầu tiên!
          </p>
          <button v-if="searchQuery === ''" class="btn btn-sm btn-action-edit px-4" @click="openAddModal">
            <i class="bx bx-plus"></i> Số Hóa Tài Liệu
          </button>
        </div>

        <div v-else class="row g-4">
          <div class="col-md-6 col-lg-4" v-for="item in filteredDocuments" :key="item.id">
            <div class="card h-100 asset-grid-card border radius-12 position-relative overflow-hidden">
              <div class="position-absolute top-0 end-0 p-2 d-flex gap-1.5 z-3">
                <button class="btn btn-action-circle shadow-sm rounded-circle border btn-edit-hover" title="Chỉnh sửa di sản" @click="onEdit(item)">
                  <i class="bx bx-edit-alt"></i>
                </button>
                <button class="btn btn-action-circle shadow-sm rounded-circle border btn-delete-hover" title="Xóa tài liệu cổ" @click="handleDelete(item.id)">
                  <i class="bx bx-trash"></i>
                </button>
              </div>

              <div class="card-body p-4 d-flex flex-column h-100">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center bg-adaptive-icon rounded-3 p-3">
                  <i :class="getDocumentIcon(item.file_path)" class="fs-2"></i>
                </div>

                <h6 class="fw-bold theme-text-main mb-2 text-truncate-2 lh-base flex-grow-0 title-container-fix" :title="item.tieu_de">
                  {{ item.tieu_de }}
                </h6>

                <p class="text-secondary small flex-grow-1 text-truncate-3 mb-3 line-clamp-desc-fix" :title="item.mo_ta">
                  {{ item.mo_ta || 'Không có mô tả chi tiết cho di sản này.' }}
                </p>

                <div class="mt-auto pt-3 border-top border-adaptive d-flex align-items-center justify-content-between">
                  <span class="text-secondary small font-medium">
                    <i class="bx bx-time-five me-1 text-warning"></i>{{ formatDate(item.created_at) }}
                  </span>
                  <a 
                    :href="item.file_path" 
                    target="_blank" 
                    class="btn btn-sm btn-gradient-orange text-white px-3 radius-8 fw-bold"
                    v-if="item.file_path && isValidUrl(item.file_path)"
                  >
                    <i class="bx bx-link-external me-1"></i> Xem File
                  </a>
                  <span class="text-secondary small italic-style font-medium" v-else>
                    Không có tệp đính kèm
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content radius-24 shadow-lg border-0 bg-adaptive-card overflow-hidden">
          <div class="modal-header border-0 bg-dark-premium text-white p-4">
            <h5 class="modal-title fw-bold text-warning">
              <i class="bx bx-bookmark-plus me-2"></i>
              {{ isEditing ? 'Chỉnh Sửa Tài Liệu Số Hóa' : 'Số Hóa Tài Liệu Gia Tộc Mới' }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 text-start">
            <form @submit.prevent="saveDocument">
              <div class="mb-3">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Tên Tài Liệu / Sắc Phong <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control premium-input" 
                  v-model="currentDocument.tieu_de" 
                  placeholder="Ví dụ: Sắc phong Hậu Thần dòng họ Nguyễn Đức 1845"
                  required
                >
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Đường dẫn Tệp tin / URL Tài Liệu <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control premium-input" 
                  v-model="currentDocument.file_path" 
                  placeholder="Ví dụ: https://drive.google.com/file/d/abc123xyz"
                  required
                >
                <span class="text-secondary small d-block mt-1.5 opacity-75">* Hỗ trợ các liên kết bộ lưu trữ Drive, Dropbox, iCloud hoặc tệp tin lưu trữ PDF trực tuyến công khai.</span>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Mô Tả Di Sản / Ý Nghĩa Lịch Sử</label>
                <textarea 
                  class="form-control premium-textarea" 
                  rows="4" 
                  v-model="currentDocument.mo_ta" 
                  placeholder="Nhập nguồn gốc lịch sử, niên đại sắc phong hoặc tóm tắt nội dung văn tự chép tay..."
                ></textarea>
              </div>
              
              <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light radius-30 px-4 fw-medium" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-gradient-orange radius-30 px-4 fw-bold text-white border-0 shadow-sm">
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
      if (!path) return 'bx bx-file text-warning';
      const lowercasePath = path.toLowerCase();
      if (lowercasePath.includes('.pdf')) return 'bx bxs-file-pdf text-danger';
      if (lowercasePath.includes('.doc') || lowercasePath.includes('.docx')) return 'bx bxs-file-doc text-primary';
      if (lowercasePath.includes('.jpg') || lowercasePath.includes('.jpeg') || lowercasePath.includes('.png') || lowercasePath.includes('.gif')) return 'bx bxs-file-image text-success';
      if (lowercasePath.includes('.xls') || lowercasePath.includes('.xlsx')) return 'bx bxs-file-json text-info';
      return 'bx bx-book-content text-warning';
    },
    openAddModal() {
      this.isEditing = false;
      this.currentDocument = {
        id: null, tieu_de: '', file_path: '', mo_ta: ''
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
.border-adaptive { border-top: 1px solid var(--border-color) !important; }

/* Thẻ lưới con (Asset Grid Cards) */
.asset-grid-card {
  background-color: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  box-shadow: none !important;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.asset-grid-card:hover {
  transform: translateY(-5px);
  border-color: rgba(249, 115, 22, 0.3) !important;
  box-shadow: 0 10px 25px rgba(249, 115, 22, 0.06) !important;
}

/* Vùng chứa Icon động */
.bg-adaptive-icon {
  background-color: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
  width: 54px; height: 54px;
}

/* Thanh nhập liệu viên thuốc đồng bộ */
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

/* Banner Thông báo Di sản Vàng Cam */
.alert-custom-orange {
  border-left: 4px solid #f97316 !important;
}

/* Hệ nút tương tác (Buttons & Badges) */
.btn-gradient-orange {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
  border: none !important; color: #ffffff !important;
  box-shadow: 0 4px 12px rgba(244, 63, 94, 0.15) !important;
  transition: all 0.25s ease;
}
.btn-gradient-orange:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(244, 63, 94, 0.25) !important;
}

.btn-action-circle {
  width: 32px; height: 32px; padding: 0;
  display: inline-flex; align-items: center; justify-content: center;
  background-color: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  color: var(--text-sub) !important;
  transition: all 0.2s ease;
}
.btn-action-circle:hover { transform: scale(1.12); }
.btn-edit-hover:hover { color: #f59e0b !important; border-color: rgba(245, 158, 11, 0.3) !important; background-color: rgba(245, 158, 11, 0.08) !important; }
.btn-delete-hover:hover { color: #ef4444 !important; border-color: rgba(239, 68, 68, 0.3) !important; background-color: rgba(239, 68, 68, 0.08) !important; }

.btn-action-edit {
  border: 1px solid rgba(245, 158, 11, 0.3) !important; color: #f59e0b !important;
  background: transparent !important; transition: all 0.2s ease;
}
.btn-action-edit:hover { background: #f59e0b !important; color: white !important; }

.bg-orange-light-premium { background-color: rgba(249, 115, 22, 0.08) !important; border: 1px solid rgba(249, 115, 22, 0.15); }
.text-orange-premium { color: #f97316 !important; font-weight: 700; }
.font-medium { font-weight: 500; }

.radius-16 { border-radius: 16px !important; }
.radius-12 { border-radius: 12px !important; }
.radius-8 { border-radius: 8px !important; }
.radius-30 { border-radius: 30px !important; }
.radius-24 { border-radius: 24px !important; }

/* MODAL HEADER & CONTAINER ADAPTIVE */
.bg-dark-premium {
  background: linear-gradient(145deg, #1a1c2e 0%, #141625 100%) !important;
  border-bottom: 1px solid var(--border-color);
}

/* Đồng bộ khống chế dòng chữ không làm lệch chiều cao Grid */
.title-container-fix {
  min-height: 44px; font-size: 15px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.line-clamp-desc-fix {
  display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.6; min-height: 64px;
}
.italic-style { font-style: italic; font-size: 13px; }
</style>