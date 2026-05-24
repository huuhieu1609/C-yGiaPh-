<template>
  <div class="timeline-container">
    <!-- Premium Header Area -->
    <div class="timeline-hero">
      <div class="hero-overlay"></div>
      <div class="hero-content text-center text-white">
        <span class="golden-tag"> Heritage Archivist </span>
        <h1 class="hero-title">Dòng Lịch Sử Hào Hùng</h1>
        <p class="hero-subtitle">Niên đại truyền thừa và các mốc son chói lọi của gia tộc</p>
      </div>
    </div>

    <!-- Main Timeline Layout -->
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-10 position-relative">
          
          <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border text-orange" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Đang kết nối lịch sử gia tộc...</p>
          </div>

          <div v-else-if="sortedMembers.length === 0" class="text-center py-5 text-muted">
            <i class="bx bx-history fs-1 mb-2 opacity-50"></i>
            <p>Chưa có dữ liệu thành viên trong dòng họ.</p>
          </div>

          <div v-else class="timeline-axis-wrapper">
            <!-- Center Vertical Line -->
            <div class="timeline-axis-line"></div>

            <!-- Loop Chronological Members -->
            <div 
              v-for="(member, index) in sortedMembers" 
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
                    
                    <!-- Ancestor Avatar -->
                    <img 
                      :src="member.avatar || 'https://ui-avatars.com/api/?name=' + member.ho_ten + '&background=f97316&color=fff'" 
                      :alt="member.ho_ten" 
                      class="ancestor-avatar flex-shrink-0 shadow-sm border border-2 border-white"
                    />

                    <!-- Ancestor Info Details -->
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
                      
                      <!-- Years Lifespan -->
                      <p class="lifespan-text font-bold mb-2">
                        <i class="bx bx-calendar"></i>
                        {{ formatYear(member.ngay_sinh) }} - {{ member.trang_thai === 'Đã mất' ? formatYear(member.ngay_mat) : 'Nay' }}
                      </p>

                      <!-- Career -->
                      <p v-if="member.nghe_nghiep" class="career-text font-medium text-secondary mb-2">
                        <i class="bx bx-briefcase"></i> Nghe nghiệp: {{ member.nghe_nghiep }}
                      </p>

                      <hr class="my-2 border-dashed" />

                      <!-- Biography Milestones -->
                      <p class="biography-text text-muted mb-0">
                        {{ member.ghi_chu || 'Tiểu sử chưa được bổ sung chi tiết.' }}
                      </p>
                    </div>

                  </div>
                </div>
              </div>

              <!-- Offset Column to balance layout on large screens -->
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
  name: 'ClientTimeline',
  data() {
    return {
      members: [],
      isLoading: true
    }
  },
  computed: {
    sortedMembers() {
      // Sort chronologically by generation (doi_thu) then by birth year
      return [...this.members].sort((a, b) => {
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
    this.loadTimelineData();
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadTimelineData() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.members = res.data.data;
          }
        })
        .catch(err => {
          console.error("Lỗi load timeline:", err);
          toastr.error("Không thể kết nối lịch sử tộc họ.");
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

.timeline-container {
  min-height: 100vh;
  background-color: var(--app-bg, #f8fafc);
  font-family: 'Inter', sans-serif;
  color: var(--text-main, #1e293b);
  padding-bottom: 50px;
}

/* HERO AREA WITH PARALLAX VIBES */
.timeline-hero {
  height: 380px;
  background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.75)), url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1200');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, transparent 70%);
}

.hero-content {
  z-index: 2;
  max-width: 650px;
  padding: 20px;
}

.golden-tag {
  color: #d4af37;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 2px;
  border: 1px solid rgba(212, 175, 55, 0.3);
  padding: 6px 14px;
  border-radius: 30px;
  display: inline-block;
  margin-bottom: 16px;
}

.hero-title {
  font-family: 'Playfair Display', serif;
  font-weight: 700;
  font-size: 40px;
  margin-bottom: 12px;
}

.hero-subtitle {
  font-size: 15px;
  opacity: 0.85;
}

/* TIMELINE GRID SYSTEM */
.timeline-axis-wrapper {
  position: relative;
  padding: 20px 0;
}

/* Center axis vertical line */
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

/* Row wrapper layout */
.timeline-row {
  z-index: 2;
}

.timeline-card-col {
  width: calc(50% - 40px);
}

.timeline-offset-col {
  width: calc(50% - 40px);
}

/* Center bullet point on the vertical line */
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

/* Premium biography card styling */
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

/* RESPONSIVE LAYOUT FOR MOBILE */
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
    flex-direction: row !important; /* Force left-aligned always */
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
