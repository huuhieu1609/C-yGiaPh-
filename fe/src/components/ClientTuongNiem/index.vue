<template>
  <div class="tuong-niem-container">
    <div class="container py-5 mt-5">
      
      <!-- Top Alert: Upcoming Death Anniversaries (Kỵ Nhật) -->
      <div v-if="upcomingAnniversaries.length > 0" class="row mb-4">
        <div class="col-12">
          <div class="alert-kyp-nhat shadow-sm border border-danger-subtle p-3 rounded-4">
            <div class="d-flex align-items-center gap-3">
              <div class="bell-ring-box bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 animate-bounce">
                <i class="bx bx-bell fs-4"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="alert-title text-danger mb-1 fw-bold">Nhắc nhở Kỵ Nhật (Ngày Giỗ) Dòng Họ Sắp Tới</h6>
                
                <div class="d-flex flex-wrap gap-3 mt-2">
                  <div 
                    v-for="an in upcomingAnniversaries" 
                    :key="an.id" 
                    class="anniversary-badge"
                    :class="{ 'soon': an.days_remaining <= 7 }"
                    @click="onSelectAncestorFromAlert(an.id)"
                  >
                    <img :src="an.avatar || 'https://ui-avatars.com/api/?name=' + an.ho_ten + '&background=f43f5e&color=fff'" class="badge-avt rounded-circle" />
                    <span class="badge-text font-bold">
                      Cụ {{ an.ho_ten }} (Giỗ lần {{ an.years_mat }}): 
                      <span class="countdown-highlight text-danger">Còn {{ an.days_remaining }} ngày</span>
                    </span>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Switcher at the top -->
      <div class="row mb-4 justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="d-flex justify-content-center gap-3 p-2 bg-tab-container rounded-pill border">
            <button 
              class="btn btn-memorial-tab px-4 py-2 fw-bold flex-grow-1" 
              :class="{ active: activeTab === 'altar' }"
              @click="activeTab = 'altar'"
            >
              <i class="bx bx-heart"></i> Dâng Hương & Tri Ân
            </button>
            <button 
              class="btn btn-memorial-tab px-4 py-2 fw-bold flex-grow-1" 
              :class="{ active: activeTab === 'timeline' }"
              @click="activeTab = 'timeline'"
            >
              <i class="bx bx-history"></i> Dòng Lịch Sử Gia Tộc
            </button>
          </div>
        </div>
      </div>

      <!-- Tab Content 1: Sacred Altar & Tributes -->
      <div v-if="activeTab === 'altar'" class="row g-4 animate-fade-in">
        
        <!-- Left Side: Altar & Linh vị (Sacred Altar Space) -->
        <div class="col-lg-5">
          <div class="altar-panel glass-altar shadow-lg rounded-4 p-4 text-center">
            
            <div class="mb-3">
              <span class="altar-sub">Tưởng Niệm Tổ Tiên</span>
              <h4 class="altar-title mt-1 mb-3">Linh Vị Linh Thiêng</h4>
              
              <!-- Select Deceased Ancestor Dropdown -->
              <label class="form-label font-xs text-muted mb-1 d-block">Lựa chọn Tổ tiên tôn kính:</label>
              <select class="form-select border rounded-3 text-center" v-model="selectedAncestorId" @change="onAncestorChange">
                <option v-for="a in deceasedAncestors" :key="a.id" :value="a.id">
                  Cụ: {{ a.ho_ten }} (Thế hệ {{ a.doi_thu }})
                </option>
              </select>
            </div>

            <!-- Incense Linh Vị Card -->
            <div v-if="currentAncestor" class="linh-vi-card border shadow-sm my-4 py-4 rounded-4 position-relative">
              <div class="sacred-lights sacred-left"></div>
              <div class="sacred-lights sacred-right"></div>
              
              <div class="linh-vi-header mb-2 text-danger font-bold uppercase letter-05 font-sm">
                Linh Vị Tổ Tiên
              </div>
              <h2 class="linh-vi-name fw-bold py-1">{{ currentAncestor.ho_ten }}</h2>
              <p class="linh-vi-span font-medium text-muted">
                {{ formatYear(currentAncestor.ngay_sinh) }} - {{ formatYear(currentAncestor.ngay_mat) }}
              </p>

              <!-- Dynamic Incense Pot Canvas -->
              <div class="incense-pot-area position-relative my-4 d-flex justify-content-center align-items-center">
                <!-- Smoke Particles rising from the pot -->
                <div v-if="incenseActive" class="incense-smoke">
                  <div class="smoke-line smoke-1"></div>
                  <div class="smoke-line smoke-2"></div>
                  <div class="smoke-line smoke-3"></div>
                </div>

                <!-- Lit Incense Sticks -->
                <div v-if="incenseActive" class="incense-sticks-wrapper">
                  <div class="stick stick-left"></div>
                  <div class="stick stick-center"></div>
                  <div class="stick stick-right"></div>
                </div>

                <!-- Physical Incense Pot (Bát Hương) -->
                <div class="bat-huong shadow-lg border border-warning">
                  <div class="bat-huong-logo"></div>
                </div>
              </div>

              <!-- Offerings Counter Summary badges -->
              <div class="d-flex justify-content-center gap-2 flex-wrap px-3">
                <span class="badge bg-light text-dark font-xs border"><i class="bx bx-wind text-warning"></i> Nhang: {{ stats.Nhang }}</span>
                <span class="badge bg-light text-dark font-xs border"><i class="bx bx-rose text-danger"></i> Hoa: {{ stats.Hoa }}</span>
                <span class="badge bg-light text-dark font-xs border"><i class="bx bx-sun text-warning"></i> Nến: {{ stats.Nen }}</span>
                <span class="badge bg-light text-dark font-xs border"><i class="bx bx-bowl-rice text-success"></i> Trái cây: {{ stats.TraiCay }}</span>
              </div>
            </div>

            <!-- Interactive Offerings Tray -->
            <div class="offerings-tray border-top pt-4">
              <h6 class="tray-title text-muted mb-3 font-xs font-bold uppercase">Mâm lễ vật dâng kính tổ tiên</h6>
              
              <div class="row g-2 justify-content-center">
                <div class="col-6">
                  <button class="btn btn-tray w-100 py-3 d-flex flex-column align-items-center gap-1 border rounded-3" @click="submitOffering('Nhang')">
                    <i class="bx bx-wind text-warning fs-3"></i>
                    <span class="font-xs font-bold">Thắp Nhang</span>
                  </button>
                </div>
                <div class="col-6">
                  <button class="btn btn-tray w-100 py-3 d-flex flex-column align-items-center gap-1 border rounded-3" @click="submitOffering('Hoa')">
                    <i class="bx bx-rose text-danger fs-3"></i>
                    <span class="font-xs font-bold">Dâng Hoa</span>
                  </button>
                </div>
                <div class="col-6">
                  <button class="btn btn-tray w-100 py-3 d-flex flex-column align-items-center gap-1 border rounded-3" @click="submitOffering('Nen')">
                    <i class="bx bx-sun text-warning fs-3"></i>
                    <span class="font-xs font-bold">Thắp Nến</span>
                  </button>
                </div>
                <div class="col-6">
                  <button class="btn btn-tray w-100 py-3 d-flex flex-column align-items-center gap-1 border rounded-3" @click="submitOffering('TraiCay')">
                    <i class="bx bx-bowl-rice text-success fs-3"></i>
                    <span class="font-xs font-bold">Dâng Mâm Quả</span>
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Right Side: Prayers & Sớ Tri Ân Submission -->
        <div class="col-lg-7">
          <div class="tribute-panel shadow-lg rounded-4 p-4 bg-white d-flex flex-column h-100">
            <h5 class="fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
              <i class="bx bx-book-open text-orange"></i> Hòm Sớ Cầu Nguyện & Tri Ân
            </h5>

            <!-- Tributes ledger list (scrollable) -->
            <div class="tribute-scroll-area flex-grow-1 mb-4">
              <div v-if="tributes.length === 0" class="text-center py-5 text-muted">
                <i class="bx bx-edit fs-1 opacity-25 mb-2"></i>
                <p class="mb-0">Chưa có lời tri ân nào. Hãy thắp hương và dâng lời đầu tiên gửi đến tổ tiên!</p>
              </div>

              <div v-else class="tribute-cards-wrapper">
                <div 
                  v-for="tr in tributes" 
                  :key="tr.id" 
                  class="tribute-card p-3 mb-3 border rounded-3 position-relative"
                >
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                      <strong class="author-name text-dark">{{ tr.ho_ten_nguoi_gui }}</strong>
                      <span class="text-muted font-xxs ms-2">{{ formatDate(tr.created_at) }}</span>
                    </div>
                    <span class="badge rounded-pill bg-light text-dark border d-flex align-items-center gap-1 font-xxs">
                      <i class="bx" :class="getOfferingIcon(tr.loai_le_vat)"></i>
                      {{ getOfferingLabel(tr.loai_le_vat) }}
                    </span>
                  </div>
                  <p class="tribute-msg mb-0 text-secondary">{{ tr.loi_nhan }}</p>
                </div>
              </div>
            </div>

            <!-- Tribute Submission Form -->
            <div class="tribute-submit-form border-top pt-3">
              <h6 class="fw-bold mb-2 font-xs text-muted text-uppercase">Gửi Sớ Cầu Nguyện Lên Bậc Tổ Tiên</h6>
              <div class="mb-3">
                <textarea 
                  class="form-control border rounded-3 p-3 font-sm" 
                  rows="3" 
                  v-model="tributeMessage" 
                  placeholder="Kính thưa Cụ Tổ, con cháu hôm nay thắp hương thành kính cúi mong cụ tổ độ trì cho gia đình luôn bình an, con cháu học hành tấn tới, dòng họ hưng thịnh..."
                ></textarea>
              </div>
              
              <div class="d-flex justify-content-between align-items-center gap-3">
                <div class="select-offering-mini d-flex align-items-center gap-2">
                  <span class="font-xs text-muted">Kèm lễ vật:</span>
                  <select class="form-select form-select-sm border py-1 px-2 font-xs" v-model="selectedOfferingType">
                    <option value="Nhang">Nén nhang lòng</option>
                    <option value="Hoa">Đóa hoa cúc</option>
                    <option value="Nen">Đèn nến ấm</option>
                    <option value="TraiCay">Mâm ngũ quả</option>
                  </select>
                </div>

                <button class="btn btn-action-orange fw-bold px-4 py-2" @click="submitTributeMessage" :disabled="isSubmitting">
                  <i class="bx bx-send"></i> Dâng Sớ Cầu Khấn
                </button>
              </div>
            </div>

          </div>
        </div>

      </div>

      <!-- Tab Content 2: Visual Family Timeline -->
      <div v-else-if="activeTab === 'timeline'" class="row justify-content-center animate-fade-in">
        <div class="col-lg-10 position-relative mt-2">
          
          <div class="timeline-axis-wrapper">
            <div class="timeline-axis-line"></div>

            <div 
              v-for="(member, index) in sortedTimelineMembers" 
              :key="member.id" 
              class="timeline-row d-flex align-items-center position-relative mb-5"
              :class="index % 2 === 0 ? 'flex-row' : 'flex-row-reverse'"
            >
              <!-- Center Axis Bullet Point -->
              <div class="timeline-axis-bullet shadow" :class="{ 'deceased': member.trang_thai === 'Đã mất' }">
                <i class="bx" :class="member.trang_thai === 'Đã mất' ? 'bx-ghost' : 'bx-user'"></i>
              </div>

              <!-- Content Card -->
              <div class="timeline-card-col">
                <div class="timeline-premium-card card border-0 shadow-lg">
                  <div class="card-body p-4 d-flex gap-4 align-items-start flex-column flex-md-row">
                    
                    <img 
                      :src="member.avatar || 'https://ui-avatars.com/api/?name=' + member.ho_ten + '&background=f97316&color=fff'" 
                      :alt="member.ho_ten" 
                      class="ancestor-avatar flex-shrink-0 shadow-sm border border-2 border-white"
                    />

                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <span class="badge generation-badge">
                          Thế hệ thứ {{ member.doi_thu }}
                        </span>
                        <span class="badge status-badge" :class="member.trang_thai === 'Đã mất' ? 'bg-secondary' : 'bg-success'">
                          {{ member.trang_thai }}
                        </span>
                      </div>

                      <h4 class="ancestor-name mb-1">{{ member.ho_ten }}</h4>
                      
                      <p class="lifespan-text font-bold mb-2">
                        <i class="bx bx-calendar"></i>
                        {{ formatYear(member.ngay_sinh) }} - {{ member.trang_thai === 'Đã mất' ? formatYear(member.ngay_mat) : 'Nay' }}
                      </p>

                      <p v-if="member.nghe_nghiep" class="career-text font-medium text-secondary mb-2">
                        <i class="bx bx-briefcase"></i> Nghề nghiệp: {{ member.nghe_nghiep }}
                      </p>

                      <hr class="my-2 border-dashed" />

                      <p class="biography-text text-muted mb-0">
                        {{ member.ghi_chu || 'Tiểu sử chưa được bổ sung chi tiết.' }}
                      </p>
                    </div>

                  </div>
                </div>
              </div>

              <div class="timeline-offset-col hide-on-mobile"></div>
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
  name: 'ClientTuongNiem',
  data() {
    return {
      allMembers: [], // Loads all members to construct the integrated Timeline
      deceasedAncestors: [],
      upcomingAnniversaries: [],
      selectedAncestorId: null,
      currentAncestor: null,
      stats: { Nhang: 0, Hoa: 0, Nen: 0, TraiCay: 0 },
      tributes: [],
      tributeMessage: '',
      selectedOfferingType: 'Nhang',
      incenseActive: false,
      activeTab: 'altar', // altar, timeline
      isLoading: true,
      isSubmitting: false
    }
  },
  computed: {
    sortedTimelineMembers() {
      // Sort chronologically by generation (doi_thu) then by birth year
      return [...this.allMembers].sort((a, b) => {
        if (a.doi_thu !== b.doi_thu) {
          return a.doi_thu - b.doi_thu;
        }
        const yearA = a.ngay_sinh ? new Date(a.ngay_sinh).getFullYear() : 9999;
        const yearB = b.ngay_sinh ? new Date(b.ngay_sinh).getFullYear() : 9999;
        return yearA - yearB;
      });
    }
  },
  mounted() {
    this.loadAllMembersData();
    this.loadUpcomingAnniversaries();
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadAllMembersData() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.allMembers = res.data.data;
            this.deceasedAncestors = this.allMembers.filter(m => m.trang_thai === 'Đã mất');
            
            if (this.deceasedAncestors.length > 0) {
              this.selectedAncestorId = this.deceasedAncestors[0].id;
              this.onAncestorChange();
            }
          }
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    loadUpcomingAnniversaries() {
      axios.get('http://127.0.0.1:8000/api/tuong-niem/sap-toi', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.upcomingAnniversaries = res.data.data;
          }
        });
    },
    onAncestorChange() {
      if (!this.selectedAncestorId) return;
      this.currentAncestor = this.deceasedAncestors.find(a => a.id === this.selectedAncestorId);
      
      axios.get(`http://127.0.0.1:8000/api/tuong-niem/thanh-vien/${this.selectedAncestorId}`, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.stats = res.data.stats;
            this.tributes = res.data.tributes;
            this.incenseActive = this.stats.Nhang > 0;
          }
        });
    },
    onSelectAncestorFromAlert(id) {
      this.activeTab = 'altar';
      this.selectedAncestorId = id;
      this.onAncestorChange();
      
      this.$nextTick(() => {
        const altarCard = document.querySelector('.linh-vi-card');
        if (altarCard) {
          altarCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    },
    submitOffering(type) {
      this.isSubmitting = true;
      const payload = {
        thanh_vien_id: this.selectedAncestorId,
        loai_le_vat: type,
        loi_nhan: null
      };

      axios.post('http://127.0.0.1:8000/api/tuong-niem/create', payload, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(`Bạn đã thành kính dâng ${this.getOfferingLabel(type)} lên tổ tiên!`);
            this.stats[type]++;
            if (type === 'Nhang') {
              this.incenseActive = true;
            }
          }
        })
        .catch(err => {
          console.error("Lỗi dâng hương:", err);
          toastr.error("Có lỗi xảy ra, không thể dâng lễ.");
        })
        .finally(() => {
          this.isSubmitting = false;
        });
    },
    submitTributeMessage() {
      if (!this.tributeMessage.trim()) {
        toastr.warning("Vui lòng ghi sớ khấn nguyện trước khi dâng.");
        return;
      }

      this.isSubmitting = true;
      const payload = {
        thanh_vien_id: this.selectedAncestorId,
        loai_le_vat: this.selectedOfferingType,
        loi_nhan: this.tributeMessage
      };

      axios.post('http://127.0.0.1:8000/api/tuong-niem/create', payload, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success("Sớ khấn nguyện đã được thành kính dâng lên!");
            this.stats[this.selectedOfferingType]++;
            this.tributes.unshift(res.data.data);
            this.tributeMessage = '';
            
            if (this.selectedOfferingType === 'Nhang') {
              this.incenseActive = true;
            }
          }
        })
        .catch(err => {
          console.error("Lỗi gửi sớ:", err);
          toastr.error("Không thể gửi sớ tri ân lên tổ tiên.");
        })
        .finally(() => {
          this.isSubmitting = false;
        });
    },
    formatYear(dateString) {
      if (!dateString) return '???';
      return new Date(dateString).getFullYear();
    },
    formatDate(dateString) {
      if (!dateString) return '';
      return new Date(dateString).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    getOfferingLabel(type) {
      const map = { Nhang: 'Nhang', Hoa: 'Đóa Hoa', Nen: 'Đèn Nến', TraiCay: 'Ngũ Quả' };
      return map[type] || type;
    },
    getOfferingIcon(type) {
      const map = { Nhang: 'bx-wind text-warning', Hoa: 'bx-rose text-danger', Nen: 'bx-sun text-warning', TraiCay: 'bx-bowl-rice text-success' };
      return map[type] || 'bx-tag';
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap');

.tuong-niem-container {
  min-height: 100vh;
  background-image: linear-gradient(rgba(248, 250, 252, 0.96), rgba(248, 250, 252, 0.96)), url('https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&q=80&w=1200');
  background-size: cover;
  background-attachment: fixed;
  font-family: 'Inter', sans-serif;
  color: #1e293b;
  padding-bottom: 50px;
}

[data-theme="dark"] .tuong-niem-container {
  background-image: linear-gradient(rgba(15, 23, 42, 0.96), rgba(15, 23, 42, 0.96)), url('https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&q=80&w=1200');
  color: #f8fafc;
}

.font-xs { font-size: 12px; }
.font-xxs { font-size: 10.5px; }

/* 🌟 TAB TOGGLES */
.bg-tab-container {
  background: var(--input-bg, #f1f5f9);
  border-color: var(--border-color, rgba(0,0,0,0.05)) !important;
}

[data-theme="dark"] .bg-tab-container {
  background: rgba(0, 0, 0, 0.2);
}

.btn-memorial-tab {
  background: transparent;
  border: none;
  color: var(--text-sub, #64748b);
  border-radius: 30px !important;
  font-size: 13.5px;
  padding: 10px 16px;
  transition: all 0.25s ease;
}

[data-theme="dark"] .btn-memorial-tab {
  color: #cbd5e1;
}

.btn-memorial-tab:hover {
  color: #f97316;
}

.btn-memorial-tab.active {
  background: #f97316 !important;
  border-color: #ea580c !important;
  color: #ffffff !important;
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2) !important;
}

/* FADE IN TRANSITION */
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* 🌟 KỴ NHẬT COUNTDOWN BOX */
.alert-kyp-nhat {
  background: rgba(244, 63, 94, 0.05);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

[data-theme="dark"] .alert-kyp-nhat {
  background: rgba(244, 63, 94, 0.12);
}

.bell-ring-box {
  width: 50px;
  height: 50px;
}

.animate-bounce {
  animation: bounceAlert 1s infinite alternate;
}

@keyframes bounceAlert {
  0% { transform: translateY(0); }
  100% { transform: translateY(-4px); }
}

.anniversary-badge {
  background: #ffffff;
  border: 1px solid rgba(244, 63, 94, 0.15);
  border-radius: 30px;
  padding: 6px 16px;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.02);
  transition: all 0.2s;
}

[data-theme="dark"] .anniversary-badge {
  background: #1e293b;
  border-color: rgba(255,255,255,0.08);
}

.anniversary-badge:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(244, 63, 94, 0.12);
}

.anniversary-badge.soon {
  border-color: #f43f5e;
  background: rgba(244, 63, 94, 0.08);
}

.badge-avt {
  width: 24px;
  height: 24px;
  object-fit: cover;
}

/* 🌟 SACRED ALTAR GLASSMORPHISM */
.glass-altar {
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  border: 1px solid rgba(255, 255, 255, 0.4);
}

[data-theme="dark"] .glass-altar {
  background: rgba(30, 41, 59, 0.85);
  border-color: rgba(255,255,255,0.08);
}

.altar-sub {
  color: #d4af37;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
}

.altar-title {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
}

/* LINH VỊ CARD */
.linh-vi-card {
  background: linear-gradient(135deg, rgba(212, 175, 55, 0.06) 0%, rgba(249, 115, 22, 0.06) 100%);
  border-color: rgba(212, 175, 55, 0.25) !important;
  overflow: hidden;
}

.sacred-lights {
  position: absolute;
  top: 15px;
  width: 8px;
  height: 8px;
  background: #f59e0b;
  border-radius: 50%;
  box-shadow: 0 0 10px #f59e0b, 0 0 20px #f59e0b;
  animation: flickerLight 0.8s infinite alternate;
}

.sacred-left { left: 20px; }
.sacred-right { right: 20px; }

@keyframes flickerLight {
  0% { opacity: 0.6; }
  100% { opacity: 1; transform: scale(1.1); }
}

.linh-vi-name {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  color: #1e293b;
}

[data-theme="dark"] .linh-vi-name {
  color: #ffffff;
}

/* INCENSE ANIMATIONS CSS */
.incense-pot-area {
  height: 120px;
}

.bat-huong {
  width: 90px;
  height: 60px;
  background: linear-gradient(135deg, #d4af37 0%, #b5952d 100%);
  border-radius: 8px 8px 30px 30px;
  position: absolute;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

.bat-huong-logo {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 2px solid #fff;
  background: rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
}

.bat-huong-logo::before {
  content: '壽';
  color: #fff;
  font-weight: 700;
  font-size: 14px;
}

.incense-sticks-wrapper {
  position: absolute;
  bottom: 48px;
  z-index: 5;
}

.stick {
  width: 3px;
  height: 50px;
  background: #d97706;
  border-radius: 1px;
  position: absolute;
  bottom: 0;
}

.stick::after {
  content: '';
  position: absolute;
  top: 0;
  left: -1px;
  width: 5px;
  height: 5px;
  background: #ef4444;
  border-radius: 50%;
  box-shadow: 0 0 6px #ef4444, 0 0 12px #ef4444;
}

.stick-left { transform: rotate(-10deg); left: -12px; }
.stick-center { left: -1px; }
.stick-right { transform: rotate(10deg); left: 10px; }

/* Smoke squiggles rising */
.incense-smoke {
  position: absolute;
  bottom: 90px;
  z-index: 3;
}

.smoke-line {
  position: absolute;
  width: 2px;
  height: 45px;
  background: rgba(255, 255, 255, 0.4);
  filter: blur(1.5px);
  animation: riseAndFade 3s infinite ease-out;
  border-radius: 50%;
}

.smoke-1 { left: -8px; animation-delay: 0s; }
.smoke-2 { left: 0px; animation-delay: 1s; }
.smoke-3 { left: 8px; animation-delay: 1.8s; }

@keyframes riseAndFade {
  0% { transform: translateY(0) scaleX(1); opacity: 0; }
  50% { transform: translateY(-30px) scaleX(1.8) translateX(6px); opacity: 0.6; }
  100% { transform: translateY(-75px) scaleX(2.5) translateX(-8px); opacity: 0; }
}

/* OFFERING BUTTONS TRAY */
.btn-tray {
  background: var(--input-bg, #ffffff);
  color: var(--text-main);
  transition: all 0.25s ease;
}

[data-theme="dark"] .btn-tray {
  background: rgba(255,255,255,0.03);
  border-color: rgba(255,255,255,0.05);
}

.btn-tray:hover {
  transform: translateY(-3px);
  border-color: #f97316 !important;
  box-shadow: 0 8px 18px rgba(249, 115, 22, 0.08);
}

/* 🌟 TRIBUTE SCROLL AREA */
.tribute-panel {
  background: #ffffff;
  border: 1px solid var(--border-color, rgba(0,0,0,0.05));
  max-height: 700px;
}

[data-theme="dark"] .tribute-panel {
  background: #1e293b;
  border-color: rgba(255,255,255,0.05);
}

.tribute-scroll-area {
  overflow-y: auto;
  padding-right: 6px;
  margin-right: -6px;
  max-height: 400px;
  min-height: 300px;
}

.tribute-scroll-area::-webkit-scrollbar {
  width: 4px;
}

.tribute-scroll-area::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.05);
  border-radius: 10px;
}

.tribute-card {
  background: rgba(248, 250, 252, 0.5);
  border-color: rgba(0,0,0,0.04) !important;
  transition: background 0.2s;
}

[data-theme="dark"] .tribute-card {
  background: rgba(255,255,255,0.02);
  border-color: rgba(255,255,255,0.04) !important;
}

.tribute-card:hover {
  background: rgba(248, 250, 252, 0.9);
}

[data-theme="dark"] .tribute-card:hover {
  background: rgba(255,255,255,0.04);
}

.author-name {
  font-size: 13.5px;
}

.tribute-msg {
  font-size: 13px;
  line-height: 1.5;
  font-style: italic;
}

/* FORM SUBMIT */
.tribute-submit-form textarea {
  color: var(--text-main);
  background-color: var(--card-bg);
}

.tribute-submit-form textarea:focus {
  border-color: #f97316 !important;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1) !important;
}

.btn-action-orange {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
  color: #ffffff !important;
  border: none !important;
  border-radius: 10px !important;
  box-shadow: 0 4px 10px rgba(244, 63, 94, 0.2) !important;
}

.btn-action-orange:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 15px rgba(244, 63, 94, 0.3) !important;
}

.select-offering-mini select {
  border-radius: 6px;
  background-color: var(--card-bg);
  color: var(--text-main);
}

/* =========================================================================
   🌟 TIMELINE STYLES INTEGRATION
   ========================================================================= */
.timeline-axis-wrapper {
  position: relative;
  padding: 20px 0;
  animation: fadeIn 0.4s ease-out;
}

.timeline-axis-line {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 50%;
  width: 3px;
  background: linear-gradient(to bottom, #d4af37, #f97316, transparent);
  transform: translateX(-50%);
  z-index: 1;
}

.timeline-row {
  z-index: 2;
}

.timeline-card-col {
  width: calc(50% - 40px);
}

.timeline-offset-col {
  width: calc(50% - 40px);
}

.timeline-axis-bullet {
  position: absolute;
  left: 50%;
  transform: translate(-50%, -50%);
  top: 50%;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #ffffff;
  border: 4px solid #d4af37;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  color: #d4af37;
  z-index: 5;
  transition: all 0.3s;
}

[data-theme="dark"] .timeline-axis-bullet {
  background: #1e293b;
}

.timeline-axis-bullet.deceased {
  border-color: #94a3b8;
  color: #64748b;
}

.timeline-row:hover .timeline-axis-bullet {
  transform: translate(-50%, -50%) scale(1.15);
  box-shadow: 0 0 15px rgba(212, 175, 55, 0.4);
}

.timeline-premium-card {
  border-radius: 20px !important;
  background: rgba(255, 255, 255, 0.95) !important;
  border: 1px solid rgba(0,0,0,0.03) !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

[data-theme="dark"] .timeline-premium-card {
  background: #1e293b !important;
  border-color: rgba(255,255,255,0.05) !important;
}

.timeline-premium-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(212, 175, 55, 0.08) !important;
}

.ancestor-avatar {
  width: 100px;
  height: 100px;
  border-radius: 16px;
  object-fit: cover;
}

.generation-badge {
  background-color: rgba(212, 175, 55, 0.1) !important;
  color: #b5952d !important;
  font-weight: 700;
  font-size: 11px;
}

.status-badge {
  font-size: 10px;
  font-weight: 700;
}

.ancestor-name {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  color: var(--text-main);
  font-size: 20px;
}

.lifespan-text {
  font-size: 12.5px;
  color: #f97316;
  display: flex;
  align-items: center;
  gap: 6px;
}

.career-text {
  font-size: 12.5px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.biography-text {
  font-size: 13px;
  line-height: 1.6;
}

.border-dashed {
  border-top: 1px dashed rgba(0,0,0,0.08);
}

[data-theme="dark"] .border-dashed {
  border-color: rgba(255,255,255,0.08);
}

@media (max-width: 991px) {
  .timeline-axis-line {
    left: 20px;
    transform: none;
  }
  
  .timeline-axis-bullet {
    left: 20px;
    transform: translate(-50%, -50%);
  }
  
  .timeline-row {
    flex-direction: row !important;
    padding-left: 45px;
  }
  
  .timeline-card-col {
    width: 100%;
  }
  
  .hide-on-mobile {
    display: none !important;
  }
}
</style>
