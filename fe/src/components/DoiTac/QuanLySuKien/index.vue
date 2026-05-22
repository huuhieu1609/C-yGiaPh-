<template>
  <div class="container-fluid py-4">
    <div class="row">
      <!-- Danh sách sự kiện dòng tộc -->
      <div class="col-md-7">
        <div class="card border-0 shadow-sm radius-15">
          <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bx bx-calendar-event me-2 text-primary"></i>Quản Lý Sự Kiện Dòng Họ
            </h5>
            <button class="btn btn-primary radius-10 px-3" @click="openAddModal">
              <i class="bx bx-plus-circle me-1"></i> Tạo Sự Kiện Mới
            </button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0 table-hover">
                <thead class="table-light text-uppercase small text-muted">
                  <tr>
                    <th class="ps-4">Sự kiện</th>
                    <th>Loại</th>
                    <th>Thời gian</th>
                    <th>Địa điểm</th>
                    <th class="pe-4 text-end">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="events.length === 0">
                    <td colspan="5" class="text-center py-5 text-muted">
                      <i class="bx bx-calendar d-block fs-1 opacity-50 mb-2"></i>
                      Chưa có sự kiện nào được tạo
                    </td>
                  </tr>
                  <tr v-for="ev in events" :key="ev.id" class="cursor-pointer" @click="selectEvent(ev)" :class="{'table-primary': selectedEventId === ev.id}">
                    <td class="ps-4">
                      <div>
                        <strong class="text-dark d-block">{{ ev.tieu_de }}</strong>
                        <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ ev.noi_dung }}</small>
                      </div>
                    </td>
                    <td>
                      <span class="badge px-2.5 py-1.5 radius-8" :class="evTypeBadgeClass(ev.loai)">
                        {{ ev.loai }}
                      </span>
                    </td>
                    <td>{{ fmtDateTime(ev.ngay_to_chuc) }}</td>
                    <td>{{ ev.dia_diem || 'Không rõ' }}</td>
                    <td class="pe-4 text-end" @click.stopPropagation>
                      <button class="btn btn-outline-info btn-sm radius-8 me-1" @click="openEditModal(ev)" title="Sửa">
                        <i class="bx bx-edit-alt"></i>
                      </button>
                      <button class="btn btn-outline-danger btn-sm radius-8" @click="handleDelete(ev.id)" title="Xóa">
                        <i class="bx bx-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Danh sách điểm danh / đăng ký tham gia sự kiện -->
      <div class="col-md-5">
        <div class="card border-0 shadow-sm radius-15">
          <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bx bx-check-shield me-2 text-primary"></i>Danh Sách Đăng Ký / Điểm Danh
            </h5>
          </div>
          <div class="card-body p-4 pt-0">
            <div v-if="!selectedEventId" class="text-center py-5 text-muted">
              <i class="bx bx-pointer d-block fs-1 opacity-50 mb-2"></i>
              Chọn một sự kiện bên trái để xem danh sách đăng ký
            </div>
            <div v-else>
              <div class="event-summary mb-4 p-3 rounded-3 bg-light border">
                <h6 class="fw-bold text-dark mb-1">{{ selectedEventName }}</h6>
                <span class="badge bg-primary me-2">Tổng: {{ participants.length }} thành viên</span>
              </div>

              <div class="mb-3 d-flex gap-2">
                <input type="text" class="form-control radius-8" v-model="searchQuery" placeholder="Tìm thành viên...">
                <select class="form-select radius-8 w-auto" v-model="filterDoi">
                  <option value="">Mọi đời</option>
                  <option v-for="d in maxDoi" :key="d" :value="d">Đời thứ {{ d }}</option>
                </select>
              </div>

              <div class="participants-list-wrapper border rounded-3 p-2 bg-light" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm align-middle mb-0">
                  <thead>
                    <tr class="small text-muted">
                      <th>Thành viên</th>
                      <th>Giới tính</th>
                      <th>Đời</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="filteredParticipants.length === 0">
                      <td colspan="3" class="text-center py-4 text-muted small">
                        Chưa có thành viên nào đăng ký
                      </td>
                    </tr>
                    <tr v-for="p in filteredParticipants" :key="p.id">
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <img :src="p.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(p.ho_ten)}&background=d4af37&color=fff&size=30`" class="rounded-circle" width="24" height="24" />
                          <strong class="text-dark small">{{ p.ho_ten }}</strong>
                        </div>
                      </td>
                      <td><span class="small">{{ p.gioi_tinh }}</span></td>
                      <td><span class="badge bg-secondary font-monospace">{{ p.doi_thu }}</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal CRUD Sự Kiện -->
    <div class="modal fade" id="eventCrudModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content radius-15 shadow-lg border-0">
          <div class="modal-header border-0 bg-dark text-white p-4" style="border-radius:15px 15px 0 0">
            <h5 class="modal-title fw-bold" style="color:#d4af37">
              <i class="bx bx-calendar-event me-2"></i>{{ isEditing ? 'Cập Nhật Sự Kiện Dòng Họ' : 'Thêm Sự Kiện Dòng Họ Mới' }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 text-start">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-bold text-dark">Tiêu đề sự kiện</label>
                <input type="text" class="form-control radius-8 border-2 shadow-none" v-model="form.tieu_de" placeholder="Ví dụ: Lễ Tế Tổ Mùa Xuân 2026">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-dark">Phân loại</label>
                <select class="form-select radius-8 border-2 shadow-none" v-model="form.loai">
                  <option value="Giỗ tổ">Giỗ tổ</option>
                  <option value="Họp họ">Họp họ</option>
                  <option value="Cưới hỏi">Cưới hỏi</option>
                  <option value="Tang lễ">Tang lễ</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-dark">Thời gian tổ chức</label>
                <input type="datetime-local" class="form-control radius-8 border-2 shadow-none" v-model="form.ngay_to_chuc">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold text-dark">Địa điểm</label>
                <input type="text" class="form-control radius-8 border-2 shadow-none" v-model="form.dia_diem" placeholder="Ví dụ: Nhà thờ tổ hoặc Khách sạn A">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold text-dark">Nội dung chi tiết / Thông báo</label>
                <textarea class="form-control radius-8 border-2 shadow-none" rows="4" v-model="form.noi_dung" placeholder="Mô tả kế hoạch, đóng góp công đức lễ lạt, chương trình nghị sự..."></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 p-4">
            <button class="btn btn-light px-4 radius-10" data-bs-dismiss="modal">Hủy</button>
            <button class="btn btn-primary px-4 radius-10 fw-bold" @click="saveEvent">
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
        id: null,
        tieu_de: '',
        noi_dung: '',
        ngay_to_chuc: '',
        dia_diem: '',
        loai: 'Giỗ tổ'
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
        loai: ev.loai
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
    fmtDateTime(d) {
      if (!d) return '';
      const dt = new Date(d);
      return dt.toLocaleDateString('vi-VN') + ' ' + dt.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    },
    evTypeBadgeClass(loai) {
      if (loai === 'Giỗ tổ') return 'bg-warning text-dark';
      if (loai === 'Họp họ') return 'bg-primary text-white';
      if (loai === 'Cưới hỏi') return 'bg-success text-white';
      return 'bg-secondary text-white';
    }
  }
};
</script>
<style scoped>
/* =========================
   TỔNG THỂ
========================= */
.container-fluid {
    padding: 24px 10px;
}

.card {
    border: none !important;
    border-radius: 24px !important;
    background: #ffffff !important;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04) !important;
    overflow: hidden;
}

.card-header {
    background: transparent !important;
    border-bottom: 1px solid #f1f5f9 !important;
}

.radius-15 {
    border-radius: 24px !important;
}

.radius-10 {
    border-radius: 14px !important;
}

.radius-8 {
    border-radius: 10px !important;
}

.cursor-pointer {
    cursor: pointer;
}

/* =========================
   TEXT
========================= */
.text-dark {
    color: #1e293b !important;
}

.text-muted {
    color: #94a3b8 !important;
}

/* =========================
   ICON
========================= */
.section-icon {
    background: linear-gradient(135deg, #f97316, #fb923c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 24px;
}

/* =========================
   BUTTON
========================= */
.btn-primary {
    background: linear-gradient(135deg, #f97316 0%, #fb923c 100%) !important;
    border: none !important;
    border-radius: 12px !important;
    font-weight: 600;
    box-shadow: 0 8px 18px rgba(249, 115, 22, 0.18);
    transition: all 0.25s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(249, 115, 22, 0.25);
}

.btn-outline-info {
    border-radius: 10px !important;
    border: 1px solid rgba(59, 130, 246, 0.2) !important;
    color: #2563eb !important;
}

.btn-outline-info:hover {
    background: #2563eb !important;
    color: white !important;
}

.btn-outline-danger {
    border-radius: 10px !important;
    border: 1px solid rgba(239, 68, 68, 0.2) !important;
    color: #ef4444 !important;
}

.btn-outline-danger:hover {
    background: #ef4444 !important;
    color: white !important;
}

/* =========================
   TABLE
========================= */
.table {
    margin-bottom: 0 !important;
}

.table thead th {
    background: #f8fafc !important;
    color: #64748b !important;
    font-size: 12px;
    font-weight: 700;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 16px !important;
    white-space: nowrap;
}

.table tbody td {
    padding: 16px !important;
    vertical-align: middle;
    border-color: #f1f5f9 !important;
    color: #334155 !important;
    font-size: 14px;
}

.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background: #fff7ed !important;
}

.table-primary {
    background: rgba(249, 115, 22, 0.08) !important;
}

/* =========================
   BADGE
========================= */
.badge {
    padding: 7px 14px !important;
    border-radius: 999px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
}

.bg-warning {
    background: rgba(245, 158, 11, 0.12) !important;
    color: #d97706 !important;
}

.bg-primary {
    background: rgba(59, 130, 246, 0.12) !important;
    color: #2563eb !important;
}

.bg-success {
    background: rgba(16, 185, 129, 0.12) !important;
    color: #059669 !important;
}

.bg-secondary {
    background: rgba(148, 163, 184, 0.12) !important;
    color: #64748b !important;
}

/* =========================
   EVENT SUMMARY
========================= */
.event-summary {
    background: linear-gradient(135deg, #fff7ed 0%, #ffffff 100%) !important;
    border: 1px solid rgba(249, 115, 22, 0.15) !important;
    border-radius: 18px !important;
}

/* =========================
   SEARCH INPUT
========================= */
.form-control,
.form-select {
    border-radius: 12px !important;
    border: 1px solid #e2e8f0 !important;
    min-height: 45px;
    box-shadow: none !important;
    transition: all 0.2s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: #fb923c !important;
    box-shadow: 0 0 0 4px rgba(251, 146, 60, 0.12) !important;
}

/* =========================
   PARTICIPANTS LIST
========================= */
.participants-list-wrapper {
    background: #f8fafc !important;
    border: 1px solid #edf2f7 !important;
    border-radius: 18px !important;
}

.participants-list-wrapper table tbody tr:hover {
    background: rgba(249, 115, 22, 0.05);
}

/* =========================
   MODAL
========================= */
.modal-content {
    border: none !important;
    border-radius: 28px !important;
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
}

.modal-title {
    color: white !important;
    font-size: 22px;
}

.modal-body {
    background: #f8fafc;
}

.modal-footer {
    background: #ffffff;
}

/* =========================
   TEXTAREA
========================= */
textarea.form-control {
    min-height: 120px;
    resize: none;
}

/* =========================
   SCROLLBAR
========================= */
.participants-list-wrapper::-webkit-scrollbar {
    width: 6px;
}

.participants-list-wrapper::-webkit-scrollbar-thumb {
    background: rgba(249, 115, 22, 0.3);
    border-radius: 20px;
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 768px) {

    .col-md-7,
    .col-md-5 {
        margin-bottom: 20px;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        background: white;
        margin-bottom: 14px;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px !important;
        border-bottom: 1px solid #f1f5f9;
    }

    .modal-dialog {
        margin: 12px;
    }
}
</style>