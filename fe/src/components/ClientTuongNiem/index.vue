<template>
  <div class="tuong-niem-container">
    <div class="container py-5 mt-5">
      
      <!-- Top Section Heading -->
      <div class="section-heading text-center mb-5 pt-4">
        <span class="subtitle-gradient d-block text-uppercase mb-2 tracking-widest font-xs fw-bold">DI SẢN DÒNG HỌ</span>
        <h2 class="text-gradient fw-bold display-5 mb-2 font-serif">Dòng Lịch Sử Gia Tộc</h2>
        <p class="text-white-50 max-w-600 mx-auto font-sm">Ôn lại niên trình, tiểu sử và những cột mốc vẻ vang của các thế hệ tổ tiên đã dựng xây cơ nghiệp.</p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="text-center py-5">
        <div class="spinner-border text-warning" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-white-50 mt-3 font-sm">Đang tải dòng lịch sử gia tộc...</p>
      </div>

      <div v-else-if="allMembers.length === 0" class="text-center py-5 text-white-50">
        <i class="bx bx-history fs-1 opacity-25 mb-2"></i>
        <p class="mb-0 font-sm">Chưa có dữ liệu thành viên dòng họ để kiến tạo dòng lịch sử.</p>
      </div>

      <!-- Visual Family Timeline -->
      <div v-else class="row justify-content-center animate-fade-in">
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

export default {
  name: 'ClientTimelineOnly',
  data() {
    return {
      allMembers: [], // Loads all members to construct the integrated Timeline
      isLoading: true
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
          }
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    formatYear(dateString) {
      if (!dateString) return '???';
      return new Date(dateString).getFullYear();
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Inter:wght@400;500;600;700&display=swap');

.tuong-niem-container {
  min-height: 100vh;
  background-image: linear-gradient(rgba(15, 23, 42, 0.96), rgba(15, 23, 42, 0.96)), url('https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&q=80&w=1200');
  background-size: cover;
  background-attachment: fixed;
  font-family: 'Inter', sans-serif;
  color: #f8fafc;
  padding-bottom: 50px;
}

.font-xs { font-size: 12px; }
.font-sm { font-size: 0.95px; }

/* FADE IN TRANSITION */
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.subtitle-gradient {
  font-size: 0.8rem;
  font-weight: 800;
  letter-spacing: 0.22em;
  color: #d4af37;
}

.text-gradient {
  background: linear-gradient(135deg, #ffd700, #ff8c00);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
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
  background: #1e293b;
  border: 4px solid #d4af37;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  color: #d4af37;
  z-index: 5;
  transition: all 0.3s;
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
  background: #1e293b !important;
  border: 1px solid rgba(255,255,255,0.05) !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
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
  color: #ffffff;
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
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 6px;
}

.biography-text {
  font-size: 13px;
  line-height: 1.6;
  color: #cbd5e1;
}

.border-dashed {
  border-color: rgba(255,255,255,0.08);
}

.font-serif { font-family: 'Playfair Display', serif !important; }
.tracking-widest { letter-spacing: 0.22em !important; }
.max-w-600 { max-width: 600px; }

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
