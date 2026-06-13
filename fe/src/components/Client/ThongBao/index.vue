<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="container content-relative pt-5 pb-5">
      <div class="glass-card shadow-2xl rounded-3xl overflow-hidden mt-5 p-4 p-md-5">
        
        <!-- Header -->
        <div class="checkout-header text-center mb-5 position-relative">
          <span class="subtitle-gradient d-block text-uppercase mb-2">TIN TỨC SỰ VỤ</span>
          <h2 class="text-gradient fw-bold mb-2">Bảng Tin Dòng Họ</h2>
          <p class="text-white-50">Cập nhật nhanh chóng, kịp thời các thông báo quan trọng và tin tức hoạt động nội bộ gia tộc.</p>
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
                   placeholder="Tìm kiếm thông báo...">
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
          <p class="text-white-50 mt-3">Đang tải danh sách thông báo...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredList.length === 0" class="empty-state text-center py-5">
          <i class="bx bx-notification-off text-white-50" style="font-size: 4rem;"></i>
          <p class="text-white-50 mt-3">Không có thông báo nào được tìm thấy.</p>
        </div>

        <!-- News Grid -->
        <div v-else class="row g-4">
          <div v-for="item in filteredList" :key="item.id" class="col-lg-6">
            <div class="event-card glass-panel h-100 d-flex flex-column justify-content-between">
              <div>
                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                  <span class="badge px-3 py-2 radius-8" :class="item.chi_nhanh_id ? 'bg-gold-badge text-gold border border-gold/30' : 'bg-blue-badge text-blue-custom border border-blue/30'">
                    <i class="bx" :class="item.chi_nhanh_id ? 'bx-git-branch' : 'bx-globe'"></i>
                    {{ item.chi_nhanh_id ? 'Tin Chi Nhánh' : 'Thông Báo Chung' }}
                  </span>
                  <div class="countdown-badge passed radius-8 px-2 py-1">
                    <i class="bx bx-calendar me-1"></i>{{ formatDate(item.created_at) }}
                  </div>
                </div>

                <h4 class="text-white fw-bold mb-3">{{ item.tieu_de }}</h4>
                <p class="text-white-50 opacity-90 event-desc mb-4" style="white-space: pre-line;">{{ item.noi_dung }}</p>
              </div>

              <div>
                <hr class="border-white/10 my-3" />
                <button class="btn btn-warning w-100 radius-10 fw-bold py-2.5" @click="openModal(item)">
                  <i class="bx bx-book-open me-1"></i> Xem Chi Tiết
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Modal Xem Chi Tiết Tin Tức -->
    <div class="modal fade" id="newsDetailModal" tabindex="-1" aria-hidden="true" ref="modalEl">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-15 shadow-lg border-0 bg-dark-custom text-white">
          <div class="modal-header border-bottom border-white/10 p-4">
            <h5 class="modal-title fw-bold" style="color:#d4af37">
              <i class="bx bx-news me-2 animate-float"></i>Chi Tiết Bản Tin
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" @click="closeModal"></button>
          </div>
          <div class="modal-body p-4" v-if="selectedItem">
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3 border-bottom border-white/10 pb-3">
              <span class="badge px-3 py-1.5 radius-8" :class="selectedItem.chi_nhanh_id ? 'bg-gold-badge text-gold border border-gold/30' : 'bg-blue-badge text-blue-custom border border-blue/30'">
                {{ selectedItem.chi_nhanh_id ? 'Chi Nhánh' : 'Hệ Thống' }}
              </span>
              <span class="text-white-50 small"><i class="bx bx-time-five me-1"></i>Đăng ngày: {{ formatDate(selectedItem.created_at) }}</span>
            </div>
            <h3 class="fw-bold text-white mb-4">{{ selectedItem.tieu_de }}</h3>
            <p class="text-white-50 font-14 lh-lg mb-0 text-justify" style="white-space: pre-line;">
              {{ selectedItem.noi_dung }}
            </p>
          </div>
          <div class="modal-footer border-top border-white/10 p-4">
            <button class="btn btn-light px-4 radius-10" data-bs-dismiss="modal" @click="closeModal">Đóng Lại</button>
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
  name: 'ClientThongBao',
  data() {
    return {
      listData: [],
      isLoading: false,
      searchQuery: '',
      selectedItem: null,
      bsModal: null
    };
  },
  computed: {
    filteredList() {
      if (!this.searchQuery) return this.listData;
      const q = this.searchQuery.toLowerCase();
      return this.listData.filter(item => {
        const title = (item.tieu_de || '').toLowerCase();
        const content = (item.noi_dung || '').toLowerCase();
        return title.includes(q) || content.includes(q);
      });
    }
  },
  mounted() {
    this.loadData();
    const modalElement = this.$refs.modalEl;
    if (modalElement && window.bootstrap) {
      this.bsModal = new window.bootstrap.Modal(modalElement);
    }
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
      axios.get('http://127.0.0.1:8000/api/thong-bao/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.listData = res.data.data || [];
          } else {
            toastr.error(res.data.message || 'Không thể tải danh sách thông báo.');
          }
        })
        .catch(err => {
          console.error(err);
          toastr.error('Lỗi kết nối máy chủ khi tải thông báo.');
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const date = new Date(dateStr);
      return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    },
    openModal(item) {
      this.selectedItem = item;
      if (this.bsModal) {
        this.bsModal.show();
      }
    },
    closeModal() {
      if (this.bsModal) {
        this.bsModal.hide();
      }
      this.selectedItem = null;
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
.radius-10 { border-radius: 10px !important; }
.radius-8 { border-radius: 8px !important; }

/* Cards styles */
.event-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 30px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.event-card:hover {
  transform: translateY(-5px);
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.15);
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.bg-gold-badge { background: rgba(212, 175, 55, 0.15); }
.bg-blue-badge { background: rgba(59, 130, 246, 0.15); }
.text-gold { color: #ffd700 !important; }
.text-blue-custom { color: #3b82f6 !important; }

.countdown-badge {
  background: rgba(255, 255, 255, 0.05);
  color: #9ca3af;
  border: 1px solid rgba(255, 255, 255, 0.1);
  font-size: 0.85rem;
  font-weight: 700;
}

.event-desc {
  line-height: 1.6;
  max-height: 100px;
  overflow-y: auto;
}

.bg-dark-custom {
  background-color: #111827 !important;
  border: 1px solid rgba(255,255,255,0.1);
}

.text-justify {
  text-align: justify;
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

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-3px); }
  100% { transform: translateY(0px); }
}
.animate-float {
  animation: float 3s infinite ease-in-out;
}
</style>
