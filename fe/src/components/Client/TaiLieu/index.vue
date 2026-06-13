<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="container content-relative pt-5 pb-5">
      <div class="glass-card shadow-2xl rounded-3xl overflow-hidden mt-5 p-4 p-md-5">
        
        <!-- Header -->
        <div class="checkout-header text-center mb-5 position-relative">
          <span class="subtitle-gradient d-block text-uppercase mb-2">TÀNG THƯ BẢO CÁC</span>
          <h2 class="text-gradient fw-bold mb-2">Tài Liệu Gia Tộc</h2>
          <p class="text-white-50">Nơi lưu trữ gia quy, sách phả hệ bản gốc, gia huấn ca và các tài liệu lưu hành nội bộ dòng họ.</p>
          <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center position-absolute end-0 top-0 mt-2" 
                  @click="loadData" 
                  :disabled="isLoading" 
                  title="Làm mới dữ liệu">
            <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
          </button>
        </div>

        <!-- Search Bar -->
        <div class="search-box mb-4">
          <div class="input-group radius-10 overflow-hidden bg-white/5 border border-white/10 px-3 py-1 align-items-center">
            <i class="bx bx-search text-white-50 me-2 fs-5"></i>
            <input type="text" 
                   class="form-control border-0 bg-transparent text-white shadow-none py-2 px-1" 
                   v-model="searchQuery" 
                   placeholder="Tìm kiếm tài liệu dòng họ...">
            <button v-if="searchQuery" 
                    @click="searchQuery = ''" 
                    type="button" 
                    class="btn bg-transparent border-0 text-white-50 p-1">
              <i class="bx bx-x fs-5"></i>
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="empty-state text-center py-5">
          <div class="ld-ring"></div>
          <p class="text-white-50 mt-3">Đang tải danh sách tài liệu...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredList.length === 0" class="empty-state text-center py-5">
          <i class="bx bx-folder-open text-white-50" style="font-size: 4rem;"></i>
          <p class="text-white-50 mt-3">Không tìm thấy tài liệu nào.</p>
        </div>

        <!-- Documents List Grid -->
        <div v-else class="row g-4">
          <div v-for="item in filteredList" :key="item.id" class="col-lg-6">
            <div class="event-card glass-panel h-100 d-flex align-items-start gap-3 justify-content-between p-4">
              <div class="d-flex gap-3 overflow-hidden">
                <!-- File Icon Block -->
                <div class="file-icon-box radius-12 d-flex align-items-center justify-content-center flex-shrink-0" :class="getFileIconClass(item.file_path)">
                  <i class="bx" :class="getFileIcon(item.file_path)"></i>
                </div>

                <div class="flex-grow-1 overflow-hidden">
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge px-2.5 py-1 radius-8 font-10" :class="item.chi_nhanh_id ? 'bg-gold-badge text-gold border border-gold/20' : 'bg-blue-badge text-blue-custom border border-blue/20'">
                      {{ item.chi_nhanh_id ? 'Chi Nhánh' : 'Hệ Thống' }}
                    </span>
                    <span class="text-white-50 font-10"><i class="bx bx-calendar me-1"></i>{{ formatDate(item.created_at) }}</span>
                  </div>

                  <h5 class="text-white fw-bold mb-1 text-truncate" :title="item.tieu_de">{{ item.tieu_de }}</h5>
                  <p class="text-white-50 font-12 line-clamp-2 mb-0">{{ item.mo_ta || 'Không có mô tả chi tiết cho tài liệu này.' }}</p>
                </div>
              </div>

              <div class="d-flex flex-column align-items-end justify-content-between h-100 flex-shrink-0 gap-3">
                <span class="file-extension text-gold fw-bold text-uppercase font-10">{{ getFileExtension(item.file_path) }} File</span>
                <a :href="item.file_path" 
                   target="_blank" 
                   class="btn btn-warning btn-sm radius-8 px-3 py-2 fw-bold d-inline-flex align-items-center gap-1">
                  <i class="bx bx-download"></i> Tải Xuống
                </a>
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
  name: 'ClientTaiLieu',
  data() {
    return {
      listData: [],
      isLoading: false,
      searchQuery: ''
    };
  },
  computed: {
    filteredList() {
      if (!this.searchQuery) return this.listData;
      const q = this.searchQuery.toLowerCase();
      return this.listData.filter(item => {
        const title = (item.tieu_de || '').toLowerCase();
        const desc = (item.mo_ta || '').toLowerCase();
        return title.includes(q) || desc.includes(q);
      });
    }
  },
  mounted() {
    this.loadData();
  },
  methods: {
    getHeaders() {
      const token = localStorage.getItem('access_token');
      return {
        headers: { Authorization: `Bearer ${token}` }
      };
    },
    loadData() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/tai-lieu/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.listData = res.data.data || [];
          } else {
            toastr.error(res.data.message || 'Không thể tải danh sách tài liệu.');
          }
        })
        .catch(err => {
          console.error(err);
          toastr.error('Lỗi kết nối máy chủ khi tải tài liệu.');
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      return date.toLocaleDateString('vi-VN');
    },
    getFileExtension(filePath) {
      if (!filePath) return 'doc';
      const parts = filePath.split('.');
      return parts[parts.length - 1].toLowerCase();
    },
    getFileIcon(filePath) {
      const ext = this.getFileExtension(filePath);
      if (ext === 'pdf') return 'bxs-file-pdf';
      if (['doc', 'docx'].includes(ext)) return 'bxs-file-doc';
      if (['xls', 'xlsx'].includes(ext)) return 'bxs-file-plus';
      if (['png', 'jpg', 'jpeg', 'gif'].includes(ext)) return 'bxs-file-image';
      return 'bxs-file-blank';
    },
    getFileIconClass(filePath) {
      const ext = this.getFileExtension(filePath);
      if (ext === 'pdf') return 'bg-pdf';
      if (['doc', 'docx'].includes(ext)) return 'bg-doc';
      if (['xls', 'xlsx'].includes(ext)) return 'bg-excel';
      if (['png', 'jpg', 'jpeg', 'gif'].includes(ext)) return 'bg-image';
      return 'bg-other';
    }
  }
};
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

.content-relative {
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

.glass-card {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.subtitle-gradient {
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  color: #d4af37;
}

.text-gradient {
  background: linear-gradient(90deg, #fbff00, #ff8c00);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

.ld-ring {
  width: 50px; height: 50px;
  border: 4px solid rgba(255,255,255,0.1);
  border-top-color: #ffd700;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}
@keyframes spin { to { transform: rotate(360deg); } }

.radius-15 { border-radius: 15px !important; }
.radius-12 { border-radius: 12px !important; }
.radius-10 { border-radius: 10px !important; }
.radius-8 { border-radius: 8px !important; }

/* Document card styles */
.event-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.event-card:hover {
  transform: translateY(-4px);
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.15);
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.bg-gold-badge { background: rgba(212, 175, 55, 0.15); }
.bg-blue-badge { background: rgba(59, 130, 246, 0.15); }
.text-gold { color: #ffd700 !important; }
.text-blue-custom { color: #3b82f6 !important; }

.file-icon-box {
  width: 54px;
  height: 54px;
  font-size: 28px;
}

.bg-pdf   { background-color: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25); }
.bg-doc   { background-color: rgba(59, 130, 246, 0.15); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.25); }
.bg-excel { background-color: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); }
.bg-image { background-color: rgba(139, 92, 246, 0.15); color: #8b5cf6; border: 1px solid rgba(139, 92, 246, 0.25); }
.bg-other { background-color: rgba(156, 163, 175, 0.15); color: #9ca3af; border: 1px solid rgba(156, 163, 175, 0.25); }

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.btn-refresh-premium {
  background: rgba(255, 255, 255, 0.05) !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
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
  border-color: #ffd700 !important;
  box-shadow: 0 4px 12px rgba(255, 215, 0, 0.15);
}
.btn-refresh-premium:active {
  transform: scale(0.95);
}
</style>
