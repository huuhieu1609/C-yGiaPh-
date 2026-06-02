<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="container content-relative pt-5 pb-5">
      <div class="glass-card shadow-2xl rounded-3xl overflow-hidden mt-5 p-4 p-md-5">
        <div class="checkout-header text-center mb-5 position-relative">
          <span class="subtitle-gradient d-block text-uppercase mb-2">ĐÓNG GÓP DI SẢN</span>
          <h2 class="text-gradient fw-bold mb-2">Lịch Sử Đề Xuất Phả Hệ</h2>
          <p class="text-white-50">Xem trạng thái và tiến trình các đóng góp, đề xuất cập nhật thông tin dòng tộc của bạn.</p>
          <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center position-absolute end-0 top-0 mt-2" @click="loadProposals" :disabled="isLoading" title="Làm mới dữ liệu">
            <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
          </button>
        </div>

        <div v-if="isLoading" class="empty-state text-center py-5">
          <div class="ld-ring"></div>
          <p class="text-white-50 mt-3">Đang tải lịch sử đề xuất...</p>
        </div>

        <div v-else-if="!proposals.length" class="empty-state text-center py-5">
          <div class="empty-icon-wrapper mb-3">
            <i class="bx bx-git-pull-request text-white-50" style="font-size: 4rem;"></i>
          </div>
          <p class="text-white-50">Bạn chưa gửi đề xuất chỉnh sửa phả hệ nào.</p>
          <router-link to="/gia-pha" class="btn btn-outline-warning radius-10 mt-3">
            <i class="bx bx-git-repo-forked me-1"></i> Đến Cây Gia Phả để Đề xuất
          </router-link>
        </div>

        <div v-else class="timeline-wrapper">
          <div class="timeline">
            <div v-for="item in proposals" :key="item.id" class="timeline-item">
              <div class="timeline-badge" :class="statusClass(item.status)">
                <i :class="typeIcon(item.type)"></i>
              </div>
              <div class="timeline-panel glass-panel">
                <div class="timeline-heading d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                  <div>
                    <h5 class="timeline-title fw-bold text-white mb-1">{{ typeLabel(item.type) }}</h5>
                    <span class="text-white-50 small">
                      <i class="bx bx-time-five me-1"></i>Gửi lúc: {{ fmtDateTime(item.created_at) }}
                    </span>
                  </div>
                  <span class="badge px-3 py-2 radius-8" :class="statusBadgeClass(item.status)">
                    {{ statusLabel(item.status) }}
                  </span>
                </div>
                <div class="timeline-body text-white-50">
                  <!-- Đối tượng liên quan -->
                  <div class="target-member mb-3 p-3 rounded-2xl bg-white/5 border border-white/10" v-if="item.thanh_vien">
                    <span class="text-white-50 small d-block mb-1">
                      {{ item.type === 'edit' ? 'Thành viên cần chỉnh sửa:' : (item.type === 'add_child' ? 'Đề xuất làm con của:' : 'Đề xuất kết hôn với:') }}
                    </span>
                    <div class="d-flex align-items-center gap-2">
                      <img :src="item.thanh_vien.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.thanh_vien.ho_ten)}&background=d4af37&color=fff&size=50`" class="rounded-circle border border-warning" width="36" height="36" style="object-fit:cover" />
                      <div>
                        <strong class="text-white">{{ item.thanh_vien.ho_ten }}</strong>
                        <span class="text-white-50 small"> (Đời thứ {{ item.thanh_vien.doi_thu }})</span>
                      </div>
                    </div>
                  </div>

                  <!-- Thông tin đề xuất -->
                  <div class="proposal-details p-3 rounded-2xl bg-black/20 border border-white/5">
                    <h6 class="text-gold fw-bold mb-3"><i class="bx bx-file-find me-1"></i>Thông tin đề xuất mới:</h6>
                    <div class="row g-2 text-start">
                      <div class="col-md-6">
                        <span class="d-block small text-white-50">Họ và tên:</span>
                        <strong class="text-white">{{ item.data.ho_ten }}</strong>
                      </div>
                      <div class="col-md-6">
                        <span class="d-block small text-white-50">Giới tính:</span>
                        <strong class="text-white">{{ item.data.gioi_tinh }}</strong>
                      </div>
                      <div class="col-md-6">
                        <span class="d-block small text-white-50">Đời thứ:</span>
                        <strong class="text-white">{{ item.data.doi_thu }}</strong>
                      </div>
                      <div class="col-md-6" v-if="item.data.ngay_sinh">
                        <span class="d-block small text-white-50">Ngày sinh:</span>
                        <strong class="text-white">{{ fmtDate(item.data.ngay_sinh) }}</strong>
                      </div>
                      <div class="col-md-6">
                        <span class="d-block small text-white-50">Trạng thái:</span>
                        <strong :class="item.data.trang_thai === 'Đã mất' ? 'text-danger' : 'text-success'">
                          {{ item.data.trang_thai }}
                        </strong>
                      </div>
                      <div class="col-md-6" v-if="item.data.trang_thai === 'Đã mất' && item.data.ngay_mat">
                        <span class="d-block small text-white-50">Ngày mất:</span>
                        <strong class="text-white">{{ fmtDate(item.data.ngay_mat) }}</strong>
                      </div>
                      <div class="col-12 mt-2" v-if="item.data.ghi_chu">
                        <span class="d-block small text-white-50">Ghi chú / Tiểu sử:</span>
                        <p class="mb-0 text-white-50 opacity-90">{{ item.data.ghi_chu }}</p>
                      </div>
                    </div>
                  </div>

                  <!-- Phản hồi duyệt / từ chối -->
                  <div class="approval-note mt-3 p-3 rounded-2xl border" :class="noteBorderClass(item.status)" v-if="item.note">
                    <span class="d-block small fw-bold mb-1" :class="noteTextClass(item.status)">
                      <i class="bx bx-comment-detail me-1"></i>Ghi chú phản hồi từ Gia tộc:
                    </span>
                    <p class="mb-0 text-white">{{ item.note }}</p>
                  </div>
                </div>
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
  name: 'ClientDeXuat',
  data() {
    return {
      proposals: [],
      isLoading: true
    };
  },
  mounted() {
    this.loadProposals();
  },
  methods: {
    loadProposals() {
      const token = localStorage.getItem('access_token');
      if (!token) {
        toastr.error('Vui lòng đăng nhập để xem lịch sử đề xuất!');
        this.$router.push('/login');
        return;
      }
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/de-xuat/my-proposals', {
        headers: { Authorization: `Bearer ${token}` }
      })
      .then(res => {
        if (res.data.status) {
          this.proposals = res.data.data;
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể lấy dữ liệu lịch sử đề xuất.');
      })
      .finally(() => {
        this.isLoading = false;
      });
    },
    fmtDateTime(d) {
      if (!d) return '';
      const dt = new Date(d);
      return dt.toLocaleDateString('vi-VN') + ' ' + dt.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    },
    fmtDate(d) {
      if (!d) return '';
      return new Date(d).toLocaleDateString('vi-VN');
    },
    typeIcon(t) {
      if (t === 'edit') return 'bx bx-edit-alt';
      if (t === 'add_child') return 'bx bx-plus-circle';
      return 'bx bx-heart';
    },
    typeLabel(t) {
      if (t === 'edit') return 'Đề xuất Chỉnh sửa Thành viên';
      if (t === 'add_child') return 'Đề xuất Thêm Con mới';
      return 'Đề xuất Thêm Vợ/Chồng';
    },
    statusLabel(s) {
      if (s === 'pending') return 'Chờ Duyệt';
      if (s === 'approved') return 'Đã Duyệt';
      return 'Từ Chối';
    },
    statusClass(s) {
      if (s === 'pending') return 'bg-warning text-dark';
      if (s === 'approved') return 'bg-success text-white';
      return 'bg-danger text-white';
    },
    statusBadgeClass(s) {
      if (s === 'pending') return 'bg-warning/20 text-warning border border-warning/30';
      if (s === 'approved') return 'bg-success/20 text-success border border-success/30';
      return 'bg-danger/20 text-danger border border-danger/30';
    },
    noteBorderClass(s) {
      if (s === 'approved') return 'bg-success/5 border-success/20';
      return 'bg-danger/5 border-danger/20';
    },
    noteTextClass(s) {
      if (s === 'approved') return 'text-success';
      return 'text-danger';
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

.radius-10 { border-radius: 10px !important; }
.radius-8 { border-radius: 8px !important; }

/* Timeline CSS */
.timeline-wrapper {
  position: relative;
  padding: 20px 0;
}

.timeline {
  list-style: none;
  padding: 0;
  position: relative;
}

.timeline::before {
  top: 0; bottom: 0;
  position: absolute;
  content: " ";
  width: 3px;
  background-color: rgba(255, 255, 255, 0.1);
  left: 30px;
  margin-left: -1.5px;
}

.timeline-item {
  margin-bottom: 40px;
  position: relative;
}

.timeline-item::before, .timeline-item::after {
  content: " ";
  display: table;
}

.timeline-item::after {
  clear: both;
}

.timeline-badge {
  color: #fff;
  width: 50px; height: 50px;
  line-height: 50px;
  font-size: 1.4rem;
  text-align: center;
  position: absolute;
  top: 0; left: 5px;
  z-index: 10;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 0 15px rgba(0,0,0,0.5);
  border: 2px solid rgba(255,255,255,0.2);
}

.timeline-panel {
  width: calc(100% - 80px);
  float: right;
  border-radius: 20px;
  padding: 25px;
  position: relative;
  transition: all 0.3s ease;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.timeline-panel:hover {
  transform: translateX(5px);
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.15);
  box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

@media (max-width: 768px) {
  .timeline-panel {
    padding: 15px;
  }
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
