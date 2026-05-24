<template>
  <div class="gpl-wrapper">
    <!-- Ambient Background -->
    <div class="gpl-bg"></div>
    <div class="gpl-blob gpl-blob-1"></div>
    <div class="gpl-blob gpl-blob-2"></div>
    <div class="gpl-blob gpl-blob-3"></div>

    <div class="gpl-content">
      <!-- Page Header -->
      <div class="gpl-header">
        <span class="gpl-kicker">DI SẢN GIA TỘC</span>
        <h1 class="gpl-title">Cây Gia Phả Của Tôi</h1>
        <p class="gpl-subtitle">
          Danh sách các gia phả bạn đã được ủy thác quyền truy cập
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="gpl-loading">
        <div class="gpl-spinner"></div>
        <p>Đang tải dữ liệu...</p>
      </div>

      <!-- Empty State -->
      <div v-else-if="listChiNhanh.length === 0" class="gpl-empty">
        <div class="gpl-empty-icon">
          <i class="bx bx-git-repo-forked"></i>
        </div>
        <h3>Chưa có quyền truy cập</h3>
        <p>
          Bạn chưa được cấp quyền vào nhánh gia phả nào.<br>
          Vui lòng liên hệ quản lý dòng họ để được cấp quyền.
        </p>
      </div>

      <!-- Branch Cards Grid -->
      <div v-else class="gpl-grid">
        <div
          v-for="(cn, idx) in listChiNhanh"
          :key="cn.id"
          class="gpl-card"
          :style="{ '--delay': idx * 0.08 + 's' }"
        >
          <!-- Card Glow Accent -->
          <div class="gpl-card-glow" :class="'glow-' + ((idx % 5) + 1)"></div>

          <!-- Card Header -->
          <div class="gpl-card-header">
            <div class="gpl-card-icon" :class="'icon-' + ((idx % 5) + 1)">
              <i class="bx bx-git-branch"></i>
            </div>
            <div class="gpl-card-badge">
              <i class="bx bxs-lock-open-alt"></i> Đã cấp quyền
            </div>
          </div>

          <!-- Card Body -->
          <div class="gpl-card-body">
            <h3 class="gpl-card-name">{{ cn.ten_chi }}</h3>
            <p class="gpl-card-desc" v-if="cn.mo_ta">{{ cn.mo_ta }}</p>
            <p class="gpl-card-desc gpl-card-desc--placeholder" v-else>
              Gia phả dòng họ được lưu truyền qua nhiều thế hệ.
            </p>

            <!-- Meta info -->
            <div class="gpl-card-meta">
              <span class="gpl-meta-item">
                <i class="bx bx-user-circle"></i>
                {{ cn.so_thanh_vien || '—' }} thành viên
              </span>
              <span class="gpl-meta-item">
                <i class="bx bx-calendar"></i>
                {{ formatDate(cn.created_at) }}
              </span>
            </div>
          </div>

          <!-- Card Footer -->
          <div class="gpl-card-footer">
            <router-link
              :to="{ name: 'client-cay-gia-pha', params: { chiNhanhId: cn.id }, query: { ten: cn.ten_chi } }"
              class="gpl-view-btn"
            >
              <i class="bx bx-sitemap"></i>
              Xem Cây Gia Phả
              <i class="bx bx-right-arrow-alt gpl-btn-arrow"></i>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'GiaPhaList',
  data() {
    return {
      listChiNhanh: [],
      isLoading: true,
    };
  },
  mounted() {
    this.loadChiNhanh();
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadChiNhanh() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            // Lấy thêm số thành viên cho mỗi nhánh
            this.listChiNhanh = res.data.data;
            this.loadMemberCounts();
          }
        })
        .catch(e => console.error(e))
        .finally(() => { this.isLoading = false; });
    },
    loadMemberCounts() {
      // Lấy tất cả thành viên để đếm theo chi nhánh
      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            const members = res.data.data;
            this.listChiNhanh = this.listChiNhanh.map(cn => ({
              ...cn,
              so_thanh_vien: members.filter(m => m.chi_nhanh_id == cn.id).length
            }));
          }
        })
        .catch(() => {});
    },
    formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
    },
  },
};
</script>

<style>
/* ─── WRAPPER & BACKGROUND ─── */
.gpl-wrapper {
  min-height: 100vh;
  background: #080d18;
  position: relative;
  overflow: hidden;
  font-family: 'Inter', 'Segoe UI', sans-serif;
}

.gpl-bg {
  position: absolute;
  inset: 0;
  background: radial-gradient(ellipse at 20% 10%, rgba(79, 70, 229, 0.12) 0%, transparent 60%),
              radial-gradient(ellipse at 80% 80%, rgba(219, 39, 119, 0.1) 0%, transparent 60%);
  z-index: 0;
}

.gpl-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(100px);
  opacity: 0.18;
  z-index: 0;
  animation: blobFloat 8s ease-in-out infinite alternate;
}
.gpl-blob-1 { width: 500px; height: 500px; background: #4f46e5; top: -100px; left: -150px; animation-delay: 0s; }
.gpl-blob-2 { width: 400px; height: 400px; background: #db2777; bottom: -80px; right: -100px; animation-delay: 3s; }
.gpl-blob-3 { width: 300px; height: 300px; background: #0d9488; top: 40%; left: 40%; animation-delay: 6s; }

@keyframes blobFloat {
  from { transform: translate(0, 0) scale(1); }
  to   { transform: translate(30px, -30px) scale(1.08); }
}

/* ─── CONTENT ─── */
.gpl-content {
  position: relative;
  z-index: 2;
  max-width: 1300px;
  margin: 0 auto;
  padding: 60px 24px 80px;
}

/* ─── HEADER ─── */
.gpl-header {
  text-align: center;
  margin-bottom: 56px;
}

.gpl-kicker {
  display: inline-block;
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.25em;
  color: #f59e0b;
  text-transform: uppercase;
  margin-bottom: 12px;
  padding: 5px 18px;
  border: 1px solid rgba(245, 158, 11, 0.3);
  border-radius: 30px;
  background: rgba(245, 158, 11, 0.08);
}

.gpl-title {
  font-size: clamp(2rem, 5vw, 3.2rem);
  font-weight: 800;
  background: linear-gradient(135deg, #ffffff 0%, #a5b4fc 40%, #db2777 100%);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  margin-bottom: 12px;
  line-height: 1.2;
}

.gpl-subtitle {
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.45);
  max-width: 500px;
  margin: 0 auto;
}

/* ─── LOADING ─── */
.gpl-loading {
  text-align: center;
  padding: 80px 0;
  color: rgba(255,255,255,0.4);
}
.gpl-spinner {
  width: 52px;
  height: 52px;
  border: 3px solid rgba(255,255,255,0.1);
  border-top-color: #db2777;
  border-radius: 50%;
  animation: gplSpin 0.8s linear infinite;
  margin: 0 auto 20px;
}
@keyframes gplSpin { to { transform: rotate(360deg); } }

/* ─── EMPTY STATE ─── */
.gpl-empty {
  text-align: center;
  padding: 80px 20px;
  color: rgba(255,255,255,0.5);
}
.gpl-empty-icon {
  width: 100px;
  height: 100px;
  border-radius: 28px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 28px;
  font-size: 3rem;
}
.gpl-empty h3 {
  font-size: 1.4rem;
  font-weight: 700;
  color: rgba(255,255,255,0.7);
  margin-bottom: 12px;
}
.gpl-empty p {
  font-size: 0.95rem;
  line-height: 1.7;
}

/* ─── GRID ─── */
.gpl-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 28px;
}

/* ─── CARD ─── */
.gpl-card {
  background: rgba(255, 255, 255, 0.035);
  border: 1px solid rgba(255, 255, 255, 0.09);
  border-radius: 24px;
  padding: 28px;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  gap: 0;
  backdrop-filter: blur(12px);
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1),
              border-color 0.35s ease,
              box-shadow 0.35s ease;
  animation: cardFadeIn 0.6s ease both;
  animation-delay: var(--delay, 0s);
}
@keyframes cardFadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to   { opacity: 1; transform: translateY(0); }
}
.gpl-card:hover {
  transform: translateY(-6px);
  border-color: rgba(255,255,255,0.18);
  box-shadow: 0 30px 60px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,255,255,0.06);
}

/* Card Glow */
.gpl-card-glow {
  position: absolute;
  top: -60px;
  right: -60px;
  width: 200px;
  height: 200px;
  border-radius: 50%;
  filter: blur(60px);
  opacity: 0.15;
  pointer-events: none;
  transition: opacity 0.3s;
}
.gpl-card:hover .gpl-card-glow { opacity: 0.25; }
.glow-1 { background: #4f46e5; }
.glow-2 { background: #db2777; }
.glow-3 { background: #0d9488; }
.glow-4 { background: #d97706; }
.glow-5 { background: #7c3aed; }

/* Card Header */
.gpl-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}
.gpl-card-icon {
  width: 52px;
  height: 52px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.6rem;
  color: #ffffff;
}
.icon-1 { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
.icon-2 { background: linear-gradient(135deg, #db2777, #f97316); }
.icon-3 { background: linear-gradient(135deg, #0d9488, #0891b2); }
.icon-4 { background: linear-gradient(135deg, #d97706, #dc2626); }
.icon-5 { background: linear-gradient(135deg, #7c3aed, #db2777); }

.gpl-card-badge {
  font-size: 11px;
  font-weight: 700;
  color: #10b981;
  background: rgba(16, 185, 129, 0.12);
  border: 1px solid rgba(16, 185, 129, 0.25);
  border-radius: 20px;
  padding: 4px 12px;
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Card Body */
.gpl-card-body {
  flex: 1;
  margin-bottom: 24px;
}
.gpl-card-name {
  font-size: 1.3rem;
  font-weight: 800;
  color: #f1f5f9;
  margin-bottom: 8px;
  line-height: 1.3;
}
.gpl-card-desc {
  font-size: 0.875rem;
  color: rgba(255,255,255,0.45);
  line-height: 1.65;
  margin-bottom: 20px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.gpl-card-desc--placeholder {
  font-style: italic;
}

/* Meta */
.gpl-card-meta {
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
}
.gpl-meta-item {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
  color: rgba(255,255,255,0.4);
  font-weight: 500;
}
.gpl-meta-item i { font-size: 14px; }

/* Card Footer — View Button */
.gpl-card-footer {
  border-top: 1px solid rgba(255,255,255,0.06);
  padding-top: 20px;
}
.gpl-view-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 13px 20px;
  border-radius: 14px;
  background: linear-gradient(135deg, rgba(79, 70, 229, 0.2), rgba(219, 39, 119, 0.2));
  border: 1px solid rgba(255,255,255,0.12);
  color: #e2e8f0;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  letter-spacing: 0.02em;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}
.gpl-view-btn::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #4f46e5, #db2777);
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 0;
}
.gpl-view-btn:hover::before { opacity: 1; }
.gpl-view-btn i, .gpl-view-btn span { position: relative; z-index: 1; }
.gpl-view-btn:hover {
  color: #ffffff;
  border-color: transparent;
  transform: none;
  box-shadow: 0 8px 24px rgba(79, 70, 229, 0.35);
}
.gpl-btn-arrow {
  font-size: 1.1rem;
  transition: transform 0.3s ease;
  position: relative;
  z-index: 1;
}
.gpl-view-btn:hover .gpl-btn-arrow {
  transform: translateX(4px);
}
</style>
