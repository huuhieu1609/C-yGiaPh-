<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="container content-relative pt-5 pb-5">
      <div class="glass-card shadow-2xl rounded-3xl overflow-hidden mt-5 p-4 p-md-5">
        
        <!-- Header -->
        <div class="checkout-header text-center mb-5 position-relative">
          <span class="subtitle-gradient d-block text-uppercase mb-2">ĐỊA ĐIỂM TỔ TIÊN</span>
          <h2 class="text-gradient fw-bold mb-2">Nhà Thờ Họ & Di Tích Gia Tộc</h2>
          <p class="text-white-50">Nơi thờ phụng linh thiêng, lưu giữ cội nguồn lịch sử và kết nối truyền thống tâm linh của dòng họ.</p>
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
                   placeholder="Tìm kiếm nhà thờ họ...">
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
          <p class="text-white-50 mt-3">Đang tải thông tin địa điểm...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredList.length === 0" class="empty-state text-center py-5">
          <i class="bx bx-building-house text-white-50" style="font-size: 4rem;"></i>
          <p class="text-white-50 mt-3">Không tìm thấy nhà thờ họ nào cho chi nhánh của bạn.</p>
        </div>

        <!-- Shrines Grid -->
        <div v-else class="row g-4">
          <div v-for="item in filteredList" :key="item.id" class="col-lg-6">
            <div class="event-card glass-panel h-100 d-flex flex-column justify-content-between">
              <div>
                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                  <span class="badge bg-gold-badge text-gold border border-gold/30 px-3 py-2 radius-8">
                    <i class="bx bx-git-branch me-1"></i>
                    {{ item.chi_nhanh?.ten_chi || 'Dòng Họ Chung' }}
                  </span>
                  <div class="location-badge radius-8 px-2.5 py-1" v-if="item.kinh_do && item.vi_do">
                    <i class="bx bx-map-pin text-warning me-1"></i>GPS Ready
                  </div>
                </div>

                <h4 class="text-white fw-bold mb-2">{{ item.ten_nha_tho }}</h4>
                <div class="d-flex align-items-baseline gap-1.5 text-white-50 small mb-3">
                  <i class="bx bx-map text-gold fs-5 flex-shrink-0"></i>
                  <span>Địa chỉ: <strong>{{ item.dia_chi }}</strong></span>
                </div>
                <p class="text-white-50 opacity-90 event-desc mb-4" style="white-space: pre-line;">{{ item.mo_ta || 'Không có mô tả chi tiết cho di tích này.' }}</p>
              </div>

              <div>
                <hr class="border-white/10 my-3" />
                <div class="row g-2">
                  <div :class="item.kinh_do && item.vi_do ? 'col-6' : 'col-12'">
                    <button class="btn btn-warning w-100 radius-10 fw-bold py-2.5" @click="openModal(item)">
                      <i class="bx bx-info-circle me-1"></i> Lịch Sử Chi Tiết
                    </button>
                  </div>
                  <div class="col-6" v-if="item.kinh_do && item.vi_do">
                    <a :href="`https://www.google.com/maps/search/?api=1&query=${item.vi_do},${item.kinh_do}`" 
                       target="_blank" 
                       class="btn btn-outline-warning w-100 radius-10 fw-bold py-2.5 d-flex align-items-center justify-content-center gap-1">
                      <i class="bx bx-navigation fs-5"></i> Chỉ Đường
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Modal Lịch Sử Chi Tiết -->
    <div class="modal fade" id="shrineDetailModal" tabindex="-1" aria-hidden="true" ref="modalEl">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-15 shadow-lg border-0 bg-dark-custom text-white">
          <div class="modal-header border-bottom border-white/10 p-4">
            <h5 class="modal-title fw-bold" style="color:#d4af37">
              <i class="bx bx-building-house me-2 animate-float"></i>Lịch Sử Địa Danh
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" @click="closeModal"></button>
          </div>
          <div class="modal-body p-4" v-if="selectedItem">
            <div class="d-flex flex-wrap align-items-center gap-3 mb-3 border-bottom border-white/10 pb-3">
              <span class="badge bg-gold-badge text-gold border border-gold/30 px-3 py-1.5 radius-8">
                {{ selectedItem.chi_nhanh?.ten_chi || 'Gia Tộc' }}
              </span>
              <span class="text-white-50 small" v-if="selectedItem.vi_do && selectedItem.kinh_do">
                <i class="bx bx-map-pin me-1"></i>Tọa độ GPS: {{ selectedItem.vi_do }}, {{ selectedItem.kinh_do }}
              </span>
            </div>
            <h3 class="fw-bold text-white mb-3">{{ selectedItem.ten_nha_tho }}</h3>
            <p class="text-white-50 small mb-4"><i class="bx bx-map text-warning me-1"></i>{{ selectedItem.dia_chi }}</p>
            
            <h6 class="fw-bold text-white border-bottom border-white/5 pb-2"><i class="bx bx-file-blank text-gold me-1"></i>Tài liệu / Lịch sử lưu truyền:</h6>
            <p class="text-white-50 font-14 lh-lg mb-0 text-justify" style="white-space: pre-line;">
              {{ selectedItem.mo_ta || 'Không có tư liệu lịch sử nào được ghi chép lại.' }}
            </p>
          </div>
          <div class="modal-footer border-top border-white/10 p-4">
            <a v-if="selectedItem?.vi_do && selectedItem?.kinh_do" 
               :href="`https://www.google.com/maps/search/?api=1&query=${selectedItem.vi_do},${selectedItem.kinh_do}`" 
               target="_blank" 
               class="btn btn-warning px-4 radius-10 fw-bold me-auto">
              <i class="bx bx-navigation me-1"></i> Bản Đồ Google Maps
            </a>
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
  name: 'ClientNhaThoHo',
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
        const title = (item.ten_nha_tho || '').toLowerCase();
        const address = (item.dia_chi || '').toLowerCase();
        const desc = (item.mo_ta || '').toLowerCase();
        return title.includes(q) || address.includes(q) || desc.includes(q);
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
      axios.get('http://127.0.0.1:8000/api/nha-tho-ho/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.listData = res.data.data || [];
          } else {
            toastr.error(res.data.message || 'Không thể tải danh sách nhà thờ họ.');
          }
        })
        .catch(err => {
          console.error(err);
          toastr.error('Lỗi kết nối máy chủ khi tải danh sách nhà thờ.');
        })
        .finally(() => {
          this.isLoading = false;
        });
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
.text-gold { color: #ffd700 !important; }

.location-badge {
  background: rgba(243, 156, 18, 0.15);
  color: #f39c12;
  border: 1px solid rgba(243, 156, 18, 0.25);
  font-size: 0.8rem;
  font-weight: 700;
}

.event-desc {
  line-height: 1.6;
  max-height: 120px;
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
