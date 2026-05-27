<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="container content-relative pt-5 pb-5">
      <div class="glass-card shadow-2xl rounded-3xl overflow-hidden mt-5 p-4 p-md-5">
        <div class="checkout-header text-center mb-5">
          <span class="subtitle-gradient d-block text-uppercase mb-2">KẾT NỐI DÒNG TỘC</span>
          <h2 class="text-gradient fw-bold mb-2">Sự Kiện Dòng Họ</h2>
          <p class="text-white-50">Lịch trình các hoạt động tế lễ, họp dòng họ, giỗ tổ và các sự kiện chung trong gia tộc.</p>
        </div>

        <div v-if="isLoading" class="empty-state text-center py-5">
          <div class="ld-ring"></div>
          <p class="text-white-50 mt-3">Đang tải danh sách sự kiện...</p>
        </div>

        <div v-else-if="!events.length" class="empty-state text-center py-5">
          <i class="bx bx-calendar-event text-white-50" style="font-size: 4rem;"></i>
          <p class="text-white-50 mt-3">Chưa có sự kiện nào được lên lịch.</p>
        </div>

        <div v-else class="row g-4">
          <div v-for="ev in events" :key="ev.id" class="col-lg-6">
            <div class="event-card glass-panel h-100 d-flex flex-column justify-content-between">
              <div>
                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                  <span class="badge px-3 py-2 radius-8" :class="evTypeClass(ev.loai)">
                    <i class="bx bx-tag-alt me-1"></i>{{ ev.loai }}
                  </span>
                  <div class="countdown-badge radius-8 px-2 py-1" v-if="getCountdown(ev.ngay_to_chuc)">
                    <i class="bx bx-time-five me-1"></i>Còn {{ getCountdown(ev.ngay_to_chuc) }}
                  </div>
                  <div class="countdown-badge passed radius-8 px-2 py-1" v-else>
                    <i class="bx bx-calendar-check me-1"></i>Đã diễn ra
                  </div>
                </div>

                <h4 class="text-white fw-bold mb-3">{{ ev.tieu_de }}</h4>
                <div class="event-info mb-3">
                  <div class="info-item mb-2 text-white-50">
                    <i class="bx bx-calendar text-gold me-2"></i>
                    <span>Thời gian: <strong>{{ fmtDateTime(ev.ngay_to_chuc) }}</strong></span>
                  </div>
                  <div class="info-item mb-2 text-white-50" v-if="ev.dia_diem">
                    <i class="bx bx-map text-gold me-2"></i>
                    <span>Địa điểm: <strong>{{ ev.dia_diem }}</strong></span>
                  </div>
                </div>

                <p class="text-white-50 opacity-90 event-desc mb-4" v-if="ev.noi_dung">{{ ev.noi_dung }}</p>
              </div>

              <div>
                <hr class="border-white/10 my-3" />

                <!-- Đã đăng ký -->
                <div class="mb-3" v-if="participantsMap[ev.id] && participantsMap[ev.id].length">
                  <span class="text-white-50 small d-block mb-2">Thành viên tham gia ({{ participantsMap[ev.id].length }}):</span>
                  <div class="d-flex flex-wrap gap-2">
                    <span v-for="p in participantsMap[ev.id].slice(0, 5)" :key="p.id" class="badge bg-white/5 border border-white/10 text-white rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                      <img :src="p.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(p.ho_ten)}&background=d4af37&color=fff&size=30`" class="rounded-circle" width="18" height="18" />
                      {{ p.ho_ten }}
                      <i class="bx bx-x text-danger cursor-pointer ms-1" style="font-size: 1rem;" @click="unregister(ev.id, p.id)" title="Hủy đăng ký"></i>
                    </span>
                    <span v-if="participantsMap[ev.id].length > 5" class="badge bg-gold/20 text-gold border border-gold/30 rounded-pill px-3 py-2">
                      +{{ participantsMap[ev.id].length - 5 }} người khác
                    </span>
                  </div>
                </div>

                <div class="d-flex gap-2">
                  <button class="btn btn-warning w-100 radius-10 fw-bold py-2.5" @click="openRegisterModal(ev)">
                    <i class="bx bx-check-double me-1"></i> Đăng Ký Tham Gia
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Đăng Ký Sự Kiện -->
    <div class="modal fade" id="registerEventModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content radius-15 shadow-lg border-0 bg-dark-custom text-white">
          <div class="modal-header border-bottom border-white/10 p-4">
            <h5 class="modal-title fw-bold" style="color:#d4af37">
              <i class="bx bx-calendar-check me-2"></i>Đăng Ký Tham Gia Sự Kiện
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <h6 class="text-white fw-bold mb-1">{{ currentEvent.tieu_de }}</h6>
            <span class="text-white-50 small d-block mb-4">
              <i class="bx bx-calendar me-1"></i>{{ fmtDateTime(currentEvent.ngay_to_chuc) }}
            </span>

            <label class="form-label fw-bold mb-2">Chọn thành viên gia đình tham gia:</label>
            <div class="search-box mb-3">
              <input type="text" class="form-control radius-8 border-2 bg-white/5 border-white/10 text-white shadow-none" v-model="searchQuery" placeholder="Tìm kiếm thành viên...">
            </div>

            <div class="member-list-wrapper rounded-2xl p-2 bg-black/20 border border-white/5" style="max-height: 250px; overflow-y: auto;">
              <div v-if="filteredMembers.length === 0" class="text-center text-white-50 py-3">
                Không tìm thấy thành viên phù hợp
              </div>
              <div v-else v-for="m in filteredMembers" :key="m.id" class="form-check p-2 border-bottom border-white/5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <input class="form-check-input ms-0 me-2" type="checkbox" :value="m.id" v-model="selectedMemberIds" :id="`chk_${m.id}`" :disabled="isAlreadyRegistered(m.id)">
                  <label class="form-check-label text-white d-flex align-items-center gap-2" :for="`chk_${m.id}`">
                    <img :src="m.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(m.ho_ten)}&background=d4af37&color=fff&size=30`" class="rounded-circle" width="24" height="24" />
                    <div>
                      <strong>{{ m.ho_ten }}</strong>
                      <span class="text-white-50 small"> (Đời {{ m.doi_thu }})</span>
                    </div>
                  </label>
                </div>
                <span class="badge bg-success/20 text-success border border-success/30 radius-8 text-xs px-2 py-1" v-if="isAlreadyRegistered(m.id)">
                  Đã đăng ký
                </span>
              </div>
            </div>
          </div>
          <div class="modal-footer border-top border-white/10 p-4">
            <button class="btn btn-light px-4 radius-10" data-bs-dismiss="modal">Đóng</button>
            <button class="btn btn-warning px-4 radius-10 fw-bold" @click="submitRegister" :disabled="selectedMemberIds.length === 0">
              Xác Nhận Đăng Ký
            </button>
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
  name: 'ClientSuKien',
  data() {
    return {
      events: [],
      allMembers: [],
      participantsMap: {}, // mapping event_id => array of participants
      isLoading: true,
      currentEvent: {},
      selectedMemberIds: [],
      searchQuery: '',
      modal: null,
      countdownInterval: null,
      currentTime: new Date().getTime()
    };
  },
  computed: {
    filteredMembers() {
      if (!this.searchQuery) return this.allMembers;
      const q = this.searchQuery.toLowerCase();
      return this.allMembers.filter(m => m.ho_ten.toLowerCase().includes(q));
    }
  },
  mounted() {
    this.loadData();
    this.loadMembers();
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(document.getElementById('registerEventModal'));
    }
    this.countdownInterval = setInterval(() => {
      this.currentTime = new Date().getTime();
    }, 1000);
  },
  beforeUnmount() {
    if (this.countdownInterval) {
      clearInterval(this.countdownInterval);
    }
  },
  methods: {
    getHeaders() {
      const token = localStorage.getItem('access_token');
      return token ? { headers: { Authorization: `Bearer ${token}` } } : {};
    },
    loadData() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/su-kien/get-data', this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.events = res.data.data;
          this.events.forEach(ev => {
            this.loadParticipants(ev.id);
          });
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể tải danh sách sự kiện.');
      })
      .finally(() => {
        this.isLoading = false;
      });
    },
    loadMembers() {
      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.allMembers = res.data.data;
        }
      });
    },
    loadParticipants(suKienId) {
      axios.get(`http://127.0.0.1:8000/api/su-kien/get-participants/${suKienId}`, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.participantsMap[suKienId] = res.data.data;
        }
      });
    },
    openRegisterModal(ev) {
      const token = localStorage.getItem('access_token');
      if (!token) {
        toastr.error('Vui lòng đăng nhập để đăng ký sự kiện!');
        this.$router.push('/login');
        return;
      }
      this.currentEvent = ev;
      this.selectedMemberIds = [];
      this.searchQuery = '';
      this.modal.show();
    },
    isAlreadyRegistered(memberId) {
      const parts = this.participantsMap[this.currentEvent.id] || [];
      return parts.some(p => p.id === memberId);
    },
    submitRegister() {
      const token = localStorage.getItem('access_token');
      if (!token) {
        toastr.error('Vui lòng đăng nhập!');
        return;
      }
      axios.post('http://127.0.0.1:8000/api/su-kien/register', {
        su_kien_id: this.currentEvent.id,
        thanh_vien_ids: this.selectedMemberIds
      }, {
        headers: { Authorization: `Bearer ${token}` }
      })
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.modal.hide();
          this.loadParticipants(this.currentEvent.id);
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể đăng ký sự kiện.');
      });
    },
    unregister(suKienId, memberId) {
      const token = localStorage.getItem('access_token');
      if (!token) {
        toastr.error('Vui lòng đăng nhập để hủy đăng ký!');
        return;
      }
      if (!confirm('Bạn có chắc chắn muốn hủy đăng ký cho thành viên này không?')) return;

      axios.post('http://127.0.0.1:8000/api/su-kien/unregister', {
        su_kien_id: suKienId,
        thanh_vien_id: memberId
      }, {
        headers: { Authorization: `Bearer ${token}` }
      })
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.loadParticipants(suKienId);
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể hủy đăng ký.');
      });
    },
    fmtDateTime(d) {
      if (!d) return '';
      const dt = new Date(d);
      return dt.toLocaleDateString('vi-VN') + ' ' + dt.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    },
    evTypeClass(type) {
      if (type === 'Giỗ tổ') return 'bg-gold-badge text-gold border border-gold/30';
      if (type === 'Họp họ') return 'bg-blue-badge text-blue-custom border border-blue/30';
      if (type === 'Cưới hỏi') return 'bg-pink-badge text-pink-custom border border-pink/30';
      return 'bg-gray-badge text-gray-custom border border-gray/30';
    },
    getCountdown(targetDateStr) {
      if (!targetDateStr) return '';
      const targetTime = new Date(targetDateStr).getTime();
      const diff = targetTime - this.currentTime;
      if (diff <= 0) return '';
      
      const days = Math.floor(diff / (1000 * 60 * 60 * 24));
      const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      
      if (days > 0) {
        return `${days} ngày ${hours}h`;
      }
      return `${hours}h ${minutes}m`;
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

.text-gold { color: #ffd700; }

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

/* Event Card CSS */
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

/* Badge classes */
.bg-gold-badge { background: rgba(212, 175, 55, 0.15); }
.bg-blue-badge { background: rgba(59, 130, 246, 0.15); }
.bg-pink-badge { background: rgba(236, 72, 153, 0.15); }
.bg-gray-badge { background: rgba(156, 163, 175, 0.15); }

.text-gold { color: #ffd700 !important; }
.text-blue-custom { color: #3b82f6 !important; }
.text-pink-custom { color: #ec4899 !important; }
.text-gray-custom { color: #9ca3af !important; }

.countdown-badge {
  background: rgba(16, 185, 129, 0.15);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.3);
  font-size: 0.85rem;
  font-weight: 700;
}

.countdown-badge.passed {
  background: rgba(156, 163, 175, 0.15);
  color: #9ca3af;
  border: 1px solid rgba(156, 163, 175, 0.3);
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

.form-check-input:checked {
  background-color: #ffd700;
  border-color: #ffd700;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
