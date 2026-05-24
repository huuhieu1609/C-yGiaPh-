<template>
  <div class="container-fluid px-0">
    <div class="row g-4">
      <div class="col-md-7">
        <div class="card genealogy-main-card border-0 shadow-sm radius-16">
          <div class="card-header bg-transparent border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0 theme-text-main">
              <i class="bx bx-calendar-event me-2 text-warning"></i>Quản Lý Sự Kiện Dòng Họ
            </h5>
            <button class="btn btn-sm btn-gradient-orange radius-30 px-3 fw-bold shadow-sm" @click="openAddModal">
              <i class="bx bx-plus-circle me-1"></i> Tạo Sự Kiện Mới
            </button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive table-container-premium mx-4 mb-4">
              <table class="table align-middle mb-0 text-nowrap table-hover">
                <thead>
                  <tr class="text-secondary text-uppercase small">
                    <th class="ps-4">Sự kiện</th>
                    <th>Loại</th>
                    <th>Thời gian</th>
                    <th>Địa điểm</th>
                    <th class="pe-4 text-end">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="events.length === 0">
                    <td colspan="5" class="text-center py-5 text-muted bg-transparent fw-medium">
                      <i class="bx bx-calendar d-block display-4 opacity-25 mb-2 text-warning"></i>
                      Chưa có sự kiện nào được khởi tạo trong dòng họ.
                    </td>
                  </tr>
                  <tr v-for="ev in events" :key="ev.id" class="table-row-premium" :class="{'row-active': selectedEventId === ev.id}" @click="selectEvent(ev)">
                    <td class="ps-4 bg-transparent">
                      <div>
                        <strong class="theme-text-main d-block font-bold mb-1">{{ ev.tieu_de }}</strong>
                        <small class="text-secondary text-truncate d-inline-block" style="max-width: 220px;">{{ ev.noi_dung }}</small>
                      </div>
                    </td>
                    <td class="bg-transparent">
                      <span class="badge badge-premium-type" :class="evTypeBadgeClass(ev.loai)">
                        {{ ev.loai }}
                      </span>
                    </td>
                    <td class="bg-transparent text-secondary font-medium small">{{ fmtDateTime(ev.ngay_to_chuc) }}</td>
                    <td class="bg-transparent theme-text-main font-medium small">{{ ev.dia_diem || 'Không rõ' }}</td>
                    <td class="pe-4 text-end bg-transparent" @click.stopPropagation>
                      <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-sm btn-action-edit" @click="openEditModal(ev)" title="Sửa">
                          <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-action-delete" @click="handleDelete(ev.id)" title="Xóa">
                          <i class="bx bx-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="card genealogy-main-card border-0 shadow-sm radius-16 h-100">
          <div class="card-header bg-transparent border-0 py-4 px-4">
            <h5 class="fw-bold mb-0 theme-text-main">
              <i class="bx bx-check-shield me-2 text-warning"></i>Danh Sách Đăng Ký / Điểm Danh
            </h5>
          </div>
          <div class="card-body p-4 pt-0">
            <div v-if="!selectedEventId" class="text-center py-5 text-muted">
              <i class="bx bx-pointer d-block display-4 opacity-25 mb-2 text-warning"></i>
              Chọn một sự kiện bên hành lang trái để xem danh sách đăng ký.
            </div>
            <div v-else>
              <div class="event-summary mb-4 p-3 rounded-4 bg-adaptive-input border">
                <h6 class="fw-bold theme-text-main mb-2 font-bold">{{ selectedEventName }}</h6>
                <span class="badge bg-orange-premium">Tổng: {{ participants.length }} thành viên</span>
              </div>

              <div class="mb-3 d-flex gap-2">
                <input type="text" class="form-control premium-input" v-model="searchQuery" placeholder="Tìm thành viên...">
                <select class="form-select premium-select w-auto" v-model="filterDoi">
                  <option value="">Mọi đời</option>
                  <option v-for="d in maxDoi" :key="d" :value="d">Đời thứ {{ d }}</option>
                </select>
              </div>

              <div class="participants-list-wrapper border-adaptive p-2" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm align-middle mb-0">
                  <thead>
                    <tr class="small text-secondary">
                      <th class="ps-2">Thành viên</th>
                      <th>Giới tính</th>
                      <th class="text-center">Đời</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="filteredParticipants.length === 0">
                      <td colspan="3" class="text-center py-4 text-muted bg-transparent small fw-medium">
                        Chưa có thành viên nào đăng ký tham dự.
                      </td>
                    </tr>
                    <tr v-for="p in filteredParticipants" :key="p.id" class="table-row-premium">
                      <td class="bg-transparent ps-2">
                        <div class="d-flex align-items-center gap-2">
                          <img :src="p.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(p.ho_ten)}&background=f97316&color=fff&size=30`" class="rounded-circle border border-2 border-white shadow-sm" width="26" height="26" />
                          <strong class="theme-text-main small font-bold">{{ p.ho_ten }}</strong>
                        </div>
                      </td>
                      <td class="bg-transparent"><span class="small text-secondary font-medium">{{ p.gioi_tinh }}</span></td>
                      <td class="bg-transparent text-center"><span class="badge badge-generation font-monospace">Đời {{ p.doi_thu }}</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="eventCrudModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content radius-24 shadow-lg border-0 bg-adaptive-card overflow-hidden">
          <div class="modal-header border-0 bg-dark-premium text-white p-4">
            <h5 class="modal-title fw-bold text-warning">
              <i class="bx bx-calendar-event me-2"></i>{{ isEditing ? 'Cập Nhật Thiết Lập Sự Kiện' : 'Khởi Tạo Sự Kiện Dòng Họ Mới' }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 text-start">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Tiêu đề sự kiện</label>
                <input type="text" class="form-control premium-input" v-model="form.tieu_de" placeholder="Ví dụ: Lễ Tế Tổ Mùa Xuân 2026">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Phân loại danh mục</label>
                <select class="form-select premium-select" v-model="form.loai">
                  <option value="Giỗ tổ">Giỗ tổ</option>
                  <option value="Họp họ">Họp họ</option>
                  <option value="Cưới hỏi">Cưới hỏi</option>
                  <option value="Tang lễ">Tang lễ</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Thời gian tổ chức</label>
                <input type="datetime-local" class="form-control premium-input" v-model="form.ngay_to_chuc">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Địa điểm diễn ra</label>
                <input type="text" class="form-control premium-input" v-model="form.dia_diem" placeholder="Ví dụ: Nhà thờ tổ hoặc Khách sạn A">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Nội dung chi tiết / Thông báo</label>
                <textarea class="form-control premium-textarea" rows="4" v-model="form.noi_dung" placeholder="Mô tả kế hoạch, đóng góp công đức lễ lạt, chương trình nghị sự..."></textarea>
              </div>
              <div class="col-12">
                <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Liên kết Chi Nhánh</label>
                <select class="form-select premium-select" v-model="form.chi_nhanh_id">
                  <option :value="null">-- Sự kiện công cộng (Toàn hệ) --</option>
                  <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 p-4 justify-content-end gap-2">
            <button class="btn btn-light radius-30 px-4 fw-medium" data-bs-dismiss="modal">Hủy</button>
            <button class="btn btn-gradient-orange text-white px-4 radius-30 fw-bold border-0 shadow-sm" @click="saveEvent">
              {{ isEditing ? 'Cập Nhật' : 'Tạo Sự Kiện' }}
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
  name: 'QuanLySuKien',
  data() {
    return {
      events: [],
      participants: [],
      selectedEventId: null,
      selectedEventName: '',
      form: {
        id: null,
        tieu_de: '',
        noi_dung: '',
        ngay_to_chuc: '',
        dia_diem: '',
        loai: 'Giỗ tổ'
      },
      listChiNhanh: [],
      searchQuery: '',
      filterDoi: '',
      isEditing: false,
      modal: null,
      isLoading: false
    };
  },
  computed: {
    filteredParticipants() {
      let list = this.participants;
      if (this.searchQuery) {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(p => p.ho_ten.toLowerCase().includes(q));
      }
      if (this.filterDoi) {
        list = list.filter(p => p.doi_thu == this.filterDoi);
      }
      return list;
    },
    maxDoi() {
      if (!this.participants.length) return 0;
      return Math.max(...this.participants.map(p => p.doi_thu));
    }
  },
  mounted() {
    this.loadEvents();
    this.loadChiNhanh();
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(document.getElementById('eventCrudModal'));
    }
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadEvents() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/su-kien/get-data', this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.events = res.data.data;
          if (this.events.length > 0 && !this.selectedEventId) {
            this.selectEvent(this.events[0]);
          }
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể lấy dữ liệu sự kiện.');
      })
      .finally(() => {
        this.isLoading = false;
      });
    },
    loadChiNhanh() {
      axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.listChiNhanh = res.data.data;
          if (!this.form.chi_nhanh_id && this.listChiNhanh.length) {
            this.form.chi_nhanh_id = this.listChiNhanh[0].id;
          }
        }
      })
      .catch(() => {});
    },
    selectEvent(ev) {
      this.selectedEventId = ev.id;
      this.selectedEventName = ev.tieu_de;
      this.loadParticipants(ev.id);
    },
    loadParticipants(suKienId) {
      axios.get(`http://127.0.0.1:8000/api/su-kien/get-participants/${suKienId}`, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.participants = res.data.data;
        }
      });
    },
    openAddModal() {
      this.isEditing = false;
      this.form = {
        id: null, tieu_de: '', noi_dung: '', ngay_to_chuc: '', dia_diem: '', loai: 'Giỗ tổ', chi_nhanh_id: (this.listChiNhanh[0] && this.listChiNhanh[0].id) || null
      };
      this.modal.show();
    },
    openEditModal(ev) {
      this.isEditing = true;
      this.form = {
        id: ev.id,
        tieu_de: ev.tieu_de,
        noi_dung: ev.noi_dung || '',
        ngay_to_chuc: ev.ngay_to_chuc ? ev.ngay_to_chuc.substring(0, 16) : '',
        dia_diem: ev.dia_diem || '',
        loai: ev.loai,
        chi_nhanh_id: ev.chi_nhanh_id || null
      };
      this.modal.show();
    },
    saveEvent() {
      if (!this.form.tieu_de || !this.form.ngay_to_chuc) {
        toastr.warning('Vui lòng điền tiêu đề và thời gian tổ chức sự kiện!');
        return;
      }
      const url = this.isEditing 
        ? 'http://127.0.0.1:8000/api/su-kien/update'
        : 'http://127.0.0.1:8000/api/su-kien/create';

      axios.post(url, this.form, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.modal.hide();
          this.loadEvents();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Lỗi khi lưu sự kiện.');
      });
    },
    handleDelete(id) {
      if (!confirm('Bạn có chắc chắn muốn xóa sự kiện này không?')) return;
      axios.post('http://127.0.0.1:8000/api/su-kien/delete', { id }, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          if (this.selectedEventId === id) {
            this.selectedEventId = null;
            this.selectedEventName = '';
            this.participants = [];
          }
          this.loadEvents();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể xóa sự kiện.');
      });
    },
    evTypeBadgeClass(loai) {
      if (loai === 'Giỗ tổ') return 'badge-gioto';
      if (loai === 'Họp họ') return 'badge-hopho';
      if (loai === 'Cưới hỏi') return 'badge-cuoihoi';
      return 'badge-tangle';
    }
  }
};
</script>

<style scoped>
/* ─── HỆ THỐNG PHÔNG NỀN VÀ KHUNG CARD THÍCH ỨNG CHUẨN ─── */
.genealogy-main-card {
  background: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 16px !important;
}
.theme-text-main { color: var(--text-main) !important; }
.font-bold { font-weight: 700; }
.font-medium { font-weight: 500; }
.bg-adaptive-input { background: var(--input-bg) !important; border: 1px solid var(--border-color) !important; }
.bg-adaptive-card { background: var(--card-bg) !important; }
.border-adaptive { border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }

/* Thẻ Summary và Ô Tìm kiếm viên thuốc */
.event-summary {
  background: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 14px !important;
}

.premium-input, .premium-select {
  border-radius: 30px !important;
  border: 1px solid var(--border-color) !important;
  padding: 8px 16px !important;
  font-size: 14px;
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  box-shadow: none !important;
  transition: all 0.2s ease;
}
.premium-input:focus, .premium-select:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg) !important;
}

/* KHUNG BẢNG PREMIUM ADAPTIVE */
.table-container-premium {
  border: 1px solid var(--border-color);
  border-radius: 12px; overflow: hidden;
}
.table thead th {
  background-color: var(--input-bg) !important;
  color: var(--text-sub) !important;
  font-weight: 600; font-size: 12px;
  border-bottom: 1px solid var(--border-color) !important;
  padding: 14px 16px !important;
}
.table-row-premium {
  border-bottom: 1px solid var(--border-color) !important;
  transition: background-color 0.2s ease;
  cursor: pointer;
}
.table-row-premium:hover { background-color: var(--input-bg) !important; }

/* Kích hoạt dòng sự kiện đang chọn */
.table-row-premium.row-active {
  background-color: rgba(249, 115, 22, 0.06) !important;
}
[data-theme="dark"] .table-row-premium.row-active {
  background-color: rgba(249, 115, 22, 0.12) !important;
}

/* ─── HỆ THỐNG BADGE PHÂN LOẠI MỜ PASTEL ─── */
.badge { padding: 6px 14px !important; font-size: 11px !important; font-weight: 700 !important; border-radius: 30px !important; }
.bg-orange-premium { background-color: #f97316 !important; color: white !important; }

/* Badge loại danh mục */
.badge-premium-type { border-radius: 6px !important; font-size: 11px !important; }
.badge-gioto { background-color: rgba(245, 158, 11, 0.08) !important; color: #f59e0b !important; border: 1px solid rgba(245, 158, 11, 0.15); }
.badge-hopho { background-color: rgba(59, 130, 246, 0.08) !important; color: #3b82f6 !important; border: 1px solid rgba(59, 130, 246, 0.15); }
.badge-cuoihoi { background-color: rgba(16, 185, 129, 0.08) !important; color: #10b981 !important; border: 1px solid rgba(16, 185, 129, 0.15); }
.badge-tangle { background-color: rgba(156, 163, 175, 0.08) !important; color: #9ca3af !important; border: 1px solid rgba(156, 163, 175, 0.15); }
.badge-generation { background-color: var(--input-bg) !important; color: var(--text-main) !important; border: 1px solid var(--border-color); }

/* NÚT TƯƠNG TÁC CHỨC NĂNG */
.btn-action-edit, .btn-action-delete {
  background: transparent !important; padding: 5px 10px !important; font-size: 14px; border-radius: 8px !important; transition: all 0.2s ease;
}
.btn-action-edit { border: 1px solid rgba(245, 158, 11, 0.3) !important; color: #f59e0b !important; }
.btn-action-edit:hover { background: #f59e0b !important; color: white !important; }

.btn-action-delete { border: 1px solid rgba(239, 68, 68, 0.3) !important; color: #ef4444 !important; }
.btn-action-delete:hover { background: #ef4444 !important; color: white !important; }

.btn-gradient-orange {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important; color: white !important; border: none;
}
.radius-30 { border-radius: 30px !important; }
.radius-24 { border-radius: 24px !important; }

/* MODAL CONTAINER HỆ THỐNG */
.bg-dark-premium { background: linear-gradient(145deg, #1a1c2e 0%, #141625 100%) !important; border-bottom: 1px solid var(--border-color); }
.premium-textarea {
  border-radius: 14px !important; border: 1px solid var(--border-color) !important;
  padding: 12px 14px !important; font-size: 14px; background-color: var(--input-bg) !important; color: var(--text-main) !important; box-shadow: none !important;
}
.premium-textarea:focus { border-color: #f97316 !important; background-color: var(--card-bg) !important; }
</style>