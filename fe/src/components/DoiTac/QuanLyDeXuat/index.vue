<template>
  <div class="container-fluid py-4">
    <div class="row">
      <!-- Cấu hình tự động duyệt (Auto-approve) -->
      <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm radius-15">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3 text-dark">
              <i class="bx bx-cog section-icon"></i> Cấu Hình Tự Động Phê Duyệt
            </h5>
            <p class="text-muted small">Khi kích hoạt, mọi đề xuất chỉnh sửa hoặc thêm thành viên từ khách hàng thuộc chi nhánh tương ứng sẽ tự động được phê duyệt và áp dụng trực tiếp lên cây gia phả tức thì.</p>
            <div class="row g-3 mt-1">
              <div v-for="branch in listChiNhanh" :key="branch.id" class="col-md-6 col-lg-4">
                <div class="branch-config-card p-3 rounded-3 border d-flex justify-content-between align-items-center bg-light">
                  <div>
                    <h6 class="fw-bold text-dark mb-1">{{ branch.ten_chi }}</h6>
                    <span class="badge" :class="branch.is_auto_approve ? 'bg-success' : 'bg-secondary'">
                      {{ branch.is_auto_approve ? 'Đang bật Tự động duyệt' : 'Duyệt thủ công' }}
                    </span>
                  </div>
                  <div class="form-check form-switch fs-4">
                    <input class="form-check-input cursor-pointer" type="checkbox" role="switch" :checked="branch.is_auto_approve" @change="toggleAutoApprove(branch)">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Danh sách đề xuất cần duyệt -->
      <div class="col-12">
        <div class="card border-0 shadow-sm radius-15">
          <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0 text-dark">
              <i class="bx bx-git-pull-request me-2 proposal-icon"></i> Quản Lý Kiểm Duyệt Đề Xuất Phả Hệ
            </h5>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary radius-10" :class="{active: filterStatus === 'all'}" @click="filterStatus = 'all'">Tất cả</button>
              <button class="btn btn-outline-warning radius-10" :class="{active: filterStatus === 'pending'}" @click="filterStatus = 'pending'">Chờ duyệt</button>
              <button class="btn btn-outline-success radius-10" :class="{active: filterStatus === 'approved'}" @click="filterStatus = 'approved'">Đã duyệt</button>
              <button class="btn btn-outline-danger radius-10" :class="{active: filterStatus === 'rejected'}" @click="filterStatus = 'rejected'">Từ chối</button>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0 table-hover">
                <thead class="table-light text-uppercase small text-muted">
                  <tr>
                    <th class="ps-4">Thành viên đề xuất</th>
                    <th>Loại đề xuất</th>
                    <th>Đối tượng ảnh hưởng</th>
                    <th>Nội dung đề xuất mới</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th class="pe-4 text-end">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="filteredProposals.length === 0">
                    <td colspan="7" class="text-center py-5 text-muted">
                      <i class="bx bx-box d-block fs-1 opacity-50 mb-2"></i>
                      Không có đề xuất nào phù hợp
                    </td>
                  </tr>
                  <tr v-for="item in filteredProposals" :key="item.id">
                    <td class="ps-4">
                      <div class="d-flex align-items-center gap-2">
                        <strong class="text-dark">{{ item.proposed_by ? item.proposed_by.ho_ten : 'Khách vãng lai' }}</strong>
                      </div>
                    </td>
                    <td>
                      <span class="badge px-2.5 py-1.5 radius-8" :class="typeBadgeClass(item.type)">
                        {{ typeLabel(item.type) }}
                      </span>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-2" v-if="item.thanh_vien">
                        <img :src="item.thanh_vien.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.thanh_vien.ho_ten)}&background=d4af37&color=fff&size=30`" class="rounded-circle" width="30" height="30" />
                        <div>
                          <strong class="text-dark">{{ item.thanh_vien.ho_ten }}</strong>
                          <span class="text-muted small"> (Đời {{ item.thanh_vien.doi_thu }})</span>
                        </div>
                      </div>
                      <span class="text-muted small" v-else>Không rõ</span>
                    </td>
                    <td>
                      <span class="text-dark font-monospace small">
                        {{ item.data.ho_ten }} ({{ item.data.gioi_tinh }}, Đời {{ item.data.doi_thu }})
                      </span>
                    </td>
                    <td>{{ fmtDateTime(item.created_at) }}</td>
                    <td>
                      <span class="badge px-3 py-1.5 radius-8" :class="statusBadgeClass(item.status)">
                        {{ statusLabel(item.status) }}
                      </span>
                    </td>
                    <td class="pe-4 text-end">
                      <button class="btn btn-primary btn-sm radius-8 px-3" @click="viewProposal(item)">
                        <i class="bx bx-show-alt me-1"></i> So Sánh / Duyệt
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal So Sánh Diff & Duyệt Đề Xuất -->
    <div class="modal fade" id="compareProposalModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content radius-15 shadow-lg border-0">
          <div class="modal-header border-0 bg-dark text-white p-4" style="border-radius:15px 15px 0 0">
            <h5 class="modal-title fw-bold" style="color:#d4af37">
              <i class="bx bx-git-compare me-2"></i>So Sánh Chi Tiết Đề Xuất
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row g-4 mb-4">
              <!-- Cột bên trái: Dữ liệu hiện tại -->
              <div class="col-md-6 border-end">
                <h5 class="fw-bold mb-3 text-muted"><i class="bx bx-history me-1"></i>Thông Tin Hiện Tại</h5>
                
                <div v-if="!currentProposal.thanh_vien || currentProposal.type !== 'edit'" class="alert alert-light border p-4 text-center text-muted radius-10">
                  <i class="bx bx-plus-circle d-block fs-1 opacity-50 mb-2"></i>
                  {{ currentProposal.type === 'add_child' ? 'Đề xuất thêm CON MỚI (chưa có thông tin hiện tại)' : 'Đề xuất thêm PHỐI NGẪU MỚI (chưa có thông tin hiện tại)' }}
                </div>
                <div v-else class="current-info-card p-3 rounded-3 border bg-light text-start">
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <img :src="currentProposal.thanh_vien.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(currentProposal.thanh_vien.ho_ten)}&background=d4af37&color=fff&size=80`" class="rounded-circle border border-warning" width="60" height="60" style="object-fit:cover" />
                    <div>
                      <h5 class="fw-bold text-dark mb-1">{{ currentProposal.thanh_vien.ho_ten }}</h5>
                      <span class="badge bg-secondary">Đời thứ {{ currentProposal.thanh_vien.doi_thu }}</span>
                    </div>
                  </div>
                  <div class="row g-3">
                    <div class="col-6"><span class="text-muted d-block small">Giới tính:</span><strong>{{ currentProposal.thanh_vien.gioi_tinh }}</strong></div>
                    <div class="col-6"><span class="text-muted d-block small">Trạng thái:</span><strong>{{ currentProposal.thanh_vien.trang_thai }}</strong></div>
                    <div class="col-6"><span class="text-muted d-block small">Ngày sinh:</span><strong>{{ formatDate(currentProposal.thanh_vien.ngay_sinh) }}</strong></div>
                    <div class="col-6" v-if="currentProposal.thanh_vien.trang_thai === 'Đã mất'"><span class="text-muted d-block small">Ngày mất:</span><strong>{{ formatDate(currentProposal.thanh_vien.ngay_mat) }}</strong></div>
                    <div class="col-12" v-if="currentProposal.thanh_vien.ghi_chu"><span class="text-muted d-block small">Ghi chú:</span><p class="mb-0 text-muted small">{{ currentProposal.thanh_vien.ghi_chu }}</p></div>
                  </div>
                </div>
              </div>

              <!-- Cột bên phải: Dữ liệu đề xuất mới -->
              <div class="col-md-6">
                <h5 class="fw-bold mb-3 text-primary"><i class="bx bx-message-square-edit me-1"></i>Thông Tin Đề Xuất Mới</h5>
                <div class="proposed-info-card p-3 rounded-3 border bg-light-primary text-start" v-if="currentProposal.data">
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <img :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(currentProposal.data.ho_ten)}&background=3b82f6&color=fff&size=80`" class="rounded-circle border border-primary" width="60" height="60" style="object-fit:cover" />
                    <div>
                      <h5 class="fw-bold text-primary mb-1" :class="isDiff('ho_ten')">{{ currentProposal.data.ho_ten }}</h5>
                      <span class="badge bg-primary" :class="isDiff('doi_thu')">Đời thứ {{ currentProposal.data.doi_thu }}</span>
                    </div>
                  </div>
                  <div class="row g-3">
                    <div class="col-6"><span class="text-muted d-block small">Giới tính:</span><strong :class="isDiff('gioi_tinh')">{{ currentProposal.data.gioi_tinh }}</strong></div>
                    <div class="col-6"><span class="text-muted d-block small">Trạng thái:</span><strong :class="isDiff('trang_thai')">{{ currentProposal.data.trang_thai }}</strong></div>
                    <div class="col-6"><span class="text-muted d-block small">Ngày sinh:</span><strong :class="isDiff('ngay_sinh')">{{ formatDate(currentProposal.data.ngay_sinh) }}</strong></div>
                    <div class="col-6" v-if="currentProposal.data.trang_thai === 'Đã mất'"><span class="text-muted d-block small">Ngày mất:</span><strong :class="isDiff('ngay_mat')">{{ formatDate(currentProposal.data.ngay_mat) }}</strong></div>
                    <div class="col-12" v-if="currentProposal.data.ghi_chu"><span class="text-muted d-block small">Ghi chú:</span><p class="mb-0 text-dark small" :class="isDiff('ghi_chu')">{{ currentProposal.data.ghi_chu }}</p></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Trường nhập Ghi chú/Lý do từ chối -->
            <div class="mb-3 text-start" v-if="currentProposal.status === 'pending'">
              <label class="form-label fw-bold text-dark">Ghi chú phản hồi / Lý do từ chối (nếu có)</label>
              <textarea class="form-control radius-10 border-2" rows="3" v-model="responseNote" placeholder="Ví dụ: Đã xác minh chính xác, hoặc Từ chối do nhập sai ngày sinh..."></textarea>
            </div>
            
            <div class="p-3 rounded-3 border bg-success/5 text-success border-success/20 text-start" v-else>
              <h6 class="fw-bold mb-1"><i class="bx bx-check-circle me-1"></i>Đã xử lý đề xuất:</h6>
              <p class="mb-0 text-muted small">Người xử lý: {{ currentProposal.approver ? currentProposal.approver.ho_ten : 'Hệ thống' }}</p>
              <p class="mb-0 text-muted small" v-if="currentProposal.note">Phản hồi: {{ currentProposal.note }}</p>
            </div>
          </div>
          <div class="modal-footer border-0 p-4 justify-content-end gap-2">
            <button class="btn btn-outline-secondary radius-10 px-4" data-bs-dismiss="modal">Đóng</button>
            <template v-if="currentProposal.status === 'pending'">
              <button class="btn btn-danger radius-10 px-4 fw-bold" @click="rejectProposal">
                <i class="bx bx-x-circle me-1"></i> Từ Chối Đề Xuất
              </button>
              <button class="btn btn-success radius-10 px-4 fw-bold" @click="approveProposal">
                <i class="bx bx-check-circle me-1"></i> Phê Duyệt Đề Xuất
              </button>
            </template>
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
  name: 'QuanLyDeXuat',
  data() {
    return {
      listChiNhanh: [],
      proposals: [],
      currentProposal: {},
      responseNote: '',
      filterStatus: 'all',
      modal: null,
      isLoading: false
    };
  },
  computed: {
    filteredProposals() {
      if (this.filterStatus === 'all') return this.proposals;
      return this.proposals.filter(p => p.status === this.filterStatus);
    }
  },
  mounted() {
    this.loadChiNhanh();
    this.loadProposals();
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(document.getElementById('compareProposalModal'));
    }
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadChiNhanh() {
      axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.listChiNhanh = res.data.data;
        }
      });
    },
    loadProposals() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/de-xuat/get-data', this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.proposals = res.data.data;
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể lấy dữ liệu danh sách đề xuất.');
      })
      .finally(() => {
        this.isLoading = false;
      });
    },
    toggleAutoApprove(branch) {
      axios.post('http://127.0.0.1:8000/api/de-xuat/toggle-auto-approve', {
        chi_nhanh_id: branch.id
      }, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          branch.is_auto_approve = res.data.is_auto_approve;
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Lỗi cấu hình tự động duyệt.');
      });
    },
    viewProposal(item) {
      this.currentProposal = item;
      this.responseNote = '';
      this.modal.show();
    },
    isDiff(field) {
      if (this.currentProposal.type !== 'edit' || !this.currentProposal.thanh_vien || !this.currentProposal.data) {
        return 'text-success'; // Đề xuất mới hoàn toàn, coi như diff dương
      }
      
      let valOld = this.currentProposal.thanh_vien[field];
      let valNew = this.currentProposal.data[field];
      
      // So sánh ngày
      if (field === 'ngay_sinh' || field === 'ngay_mat') {
        valOld = valOld ? valOld.substring(0, 10) : null;
        valNew = valNew ? valNew.substring(0, 10) : null;
      }
      
      return valOld != valNew ? 'text-danger bg-warning/10 p-1 rounded font-bold' : '';
    },
    approveProposal() {
      axios.post('http://127.0.0.1:8000/api/de-xuat/approve', {
        id: this.currentProposal.id,
        note: this.responseNote || 'Đã phê duyệt thủ công'
      }, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.modal.hide();
          this.loadProposals();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể phê duyệt đề xuất.');
      });
    },
    rejectProposal() {
      if (!this.responseNote) {
        toastr.warning('Vui lòng nhập lý do từ chối đề xuất!');
        return;
      }
      axios.post('http://127.0.0.1:8000/api/de-xuat/reject', {
        id: this.currentProposal.id,
        note: this.responseNote
      }, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.modal.hide();
          this.loadProposals();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể từ chối đề xuất.');
      });
    },
    fmtDateTime(d) {
      if (!d) return '';
      const dt = new Date(d);
      return dt.toLocaleDateString('vi-VN') + ' ' + dt.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    },
    formatDate(d) {
      if (!d) return 'Không rõ';
      return new Date(d).toLocaleDateString('vi-VN');
    },
    typeLabel(t) {
      if (t === 'edit') return 'Chỉnh sửa';
      if (t === 'add_child') return 'Thêm con';
      return 'Thêm vợ/chồng';
    },
    typeBadgeClass(t) {
      if (t === 'edit') return 'bg-info/20 text-info border border-info/30';
      if (t === 'add_child') return 'bg-primary/20 text-primary border border-primary/30';
      return 'bg-pink-badge text-pink-custom border border-pink/30';
    },
    statusLabel(s) {
      if (s === 'pending') return 'Chờ duyệt';
      if (s === 'approved') return 'Đã duyệt';
      return 'Từ chối';
    },
    statusBadgeClass(s) {
      if (s === 'pending') return 'bg-warning text-dark';
      if (s === 'approved') return 'bg-success text-white';
      return 'bg-danger text-white';
    }
  }
};
</script>

<style scoped>
.section-icon{
    color: #f97316 !important;
    font-size: 22px;
}
.proposal-icon{
    background: linear-gradient(135deg, #f97316, #fb923c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 22px;
}
/* =========================
   TỔNG THỂ THEO STYLE WEB
========================= */
.container-fluid {
    padding: 24px 10px;
}

.card {
    border: none !important;
    border-radius: 24px !important;
    background: #ffffff !important;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04) !important;
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

/* =========================
   TEXT
========================= */
.text-dark {
    color: #1e293b !important;
}

.text-muted {
    color: #94a3b8 !important;
}

h5,
h6 {
    letter-spacing: -0.3px;
}

/* =========================
   CARD AUTO APPROVE
========================= */
.branch-config-card {
    background: #f8fafc !important;
    border: 1px solid #edf2f7 !important;
    border-radius: 20px !important;
    transition: all 0.25s ease;
    min-height: 92px;
}

.branch-config-card:hover {
    transform: translateY(-3px);
    border-color: rgba(249, 115, 22, 0.35) !important;
    box-shadow: 0 10px 25px rgba(249, 115, 22, 0.08);
}

.branch-config-card h6 {
    font-size: 15px;
    margin-bottom: 8px !important;
}

/* =========================
   SWITCH
========================= */
.form-switch .form-check-input {
    width: 52px;
    height: 28px;
    cursor: pointer;
    border: none;
    background-color: #d1d5db;
    box-shadow: none !important;
}

.form-switch .form-check-input:checked {
    background-color: #f97316;
    border-color: #f97316;
}

/* =========================
   FILTER BUTTONS
========================= */
.btn-outline-secondary,
.btn-outline-warning,
.btn-outline-success,
.btn-outline-danger {
    border-radius: 12px !important;
    padding: 9px 18px !important;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid #e2e8f0 !important;
    background: white !important;
    color: #64748b !important;
    transition: all 0.25s ease;
}

.btn-outline-secondary:hover,
.btn-outline-warning:hover,
.btn-outline-success:hover,
.btn-outline-danger:hover {
    transform: translateY(-1px);
}

.btn-outline-secondary.active {
    background: linear-gradient(135deg, #f97316 0%, #fb923c 100%) !important;
    border-color: transparent !important;
    color: white !important;
}

.btn-outline-warning.active {
    background: #f59e0b !important;
    border-color: transparent !important;
    color: white !important;
}

.btn-outline-success.active {
    background: #10b981 !important;
    border-color: transparent !important;
    color: white !important;
}

.btn-outline-danger.active {
    background: #ef4444 !important;
    border-color: transparent !important;
    color: white !important;
}

/* =========================
   TABLE
========================= */
.table-responsive {
    border-radius: 0 0 24px 24px;
}

.table {
    margin-bottom: 0 !important;
}

.table thead th {
    background: #f8fafc !important;
    color: #64748b !important;
    font-size: 12px;
    font-weight: 700;
    padding: 18px 16px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    white-space: nowrap;
}

.table tbody td {
    padding: 18px 16px !important;
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

/* =========================
   BADGE
========================= */
.badge {
    padding: 7px 14px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    border-radius: 999px !important;
    letter-spacing: 0.3px;
}

.bg-success {
    background: rgba(16, 185, 129, 0.12) !important;
    color: #059669 !important;
}

.bg-secondary {
    background: rgba(148, 163, 184, 0.15) !important;
    color: #64748b !important;
}

.bg-warning {
    background: rgba(245, 158, 11, 0.15) !important;
    color: #d97706 !important;
}

.bg-danger {
    background: rgba(239, 68, 68, 0.12) !important;
    color: #dc2626 !important;
}

.bg-primary {
    background: rgba(59, 130, 246, 0.12) !important;
    color: #2563eb !important;
}

/* =========================
   BUTTON ACTION
========================= */
.btn-primary {
    background: linear-gradient(135deg, #f97316 0%, #fb923c 100%) !important;
    border: none !important;
    border-radius: 12px !important;
    padding: 10px 18px !important;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 6px 16px rgba(249, 115, 22, 0.2);
    transition: all 0.25s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(249, 115, 22, 0.28);
}

/* =========================
   MODAL
========================= */
.modal-content {
    border: none !important;
    border-radius: 28px !important;
    overflow: hidden;
    background: #ffffff !important;
}

.modal-header {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
    padding: 24px !important;
}

.modal-title {
    color: white !important;
    font-size: 22px;
}

.modal-body {
    background: #f8fafc;
}

.current-info-card,
.proposed-info-card {
    background: white !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    padding: 20px !important;
    height: 100%;
}

.current-info-card:hover,
.proposed-info-card:hover {
    border-color: rgba(249, 115, 22, 0.25) !important;
}

/* =========================
   TEXTAREA
========================= */
textarea.form-control {
    border-radius: 16px !important;
    border: 1px solid #e2e8f0 !important;
    padding: 14px !important;
    font-size: 14px;
    box-shadow: none !important;
}

textarea.form-control:focus {
    border-color: #fb923c !important;
    box-shadow: 0 0 0 4px rgba(251, 146, 60, 0.12) !important;
}

/* =========================
   DIFF HIGHLIGHT
========================= */
.text-danger.bg-warning\/10 {
    background: rgba(249, 115, 22, 0.12) !important;
    color: #ea580c !important;
    border-radius: 8px;
    padding: 3px 8px;
    font-weight: 700;
}

/* =========================
   EMPTY STATE
========================= */
.table tbody tr td.text-center {
    padding: 60px 20px !important;
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 768px) {
    .card-body {
        padding: 18px !important;
    }

    .table thead {
        display: none;
    }

    .table tbody tr {
        display: block;
        margin-bottom: 16px;
        border-radius: 18px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px !important;
        border-bottom: 1px solid #f1f5f9;
    }

    .table tbody td::before {
        content: attr(data-label);
        font-weight: 700;
        color: #64748b;
        margin-right: 12px;
    }

    .modal-dialog {
        margin: 12px;
    }
}
</style>