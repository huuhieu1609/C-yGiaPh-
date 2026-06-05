<template>
  <div class="container-fluid px-0">
    <div class="row g-4">
      <div class="col-12">
        <div class="card genealogy-main-card border-0 shadow-sm radius-16">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3 theme-text-main">
              <i class="bx bx-cog me-2 text-warning"></i>Cấu Hình Tự Động Phê Duyệt
            </h5>
            <p class="text-secondary small mb-4">Khi kích hoạt, mọi đề xuất chỉnh sửa hoặc thêm thành viên từ khách hàng thuộc chi nhánh tương ứng sẽ tự động được phê duyệt và áp dụng trực tiếp lên cây gia phả tức thì.</p>
            <div class="row g-3">
              <div v-for="branch in listChiNhanh" :key="branch.id" class="col-md-6 col-lg-4">
                <div class="branch-config-card p-3 rounded-4 d-flex justify-content-between align-items-center bg-adaptive-input">
                  <div>
                    <h6 class="fw-bold theme-text-main mb-1">{{ branch.ten_chi }}</h6>
                    <span class="badge" :class="branch.is_auto_approve ? 'bg-success-premium' : 'bg-secondary-premium'">
                      {{ branch.is_auto_approve ? 'Đang bật tự động' : 'Duyệt thủ công' }}
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

      <div class="col-12">
        <div class="card genealogy-main-card border-0 shadow-sm radius-16">
          <div class="card-header bg-transparent border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="fw-bold mb-0 theme-text-main">
              <i class="bx bx-git-pull-request me-2 text-warning"></i>Quản Lý Kiểm Duyệt Đề Xuất Phả Hệ
            </h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <!-- Select Tree/Branch Filter -->
              <div style="width: 170px;">
                <select class="form-select premium-select fw-bold py-1 px-3 shadow-none border-2" v-model="selectedChiNhanhId">
                  <option value="all">-- Tất cả các cây --</option>
                  <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                </select>
              </div>
              <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="refreshData" :disabled="isLoading" title="Làm mới dữ liệu">
                <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
              </button>
              <div class="d-flex gap-2 project-filter-group">
                <button class="btn btn-sm btn-filter" :class="{active: filterStatus === 'all'}" @click="filterStatus = 'all'">Tất cả</button>
                <button class="btn btn-sm btn-filter" :class="{active: filterStatus === 'pending'}" @click="filterStatus = 'pending'">Chờ duyệt</button>
                <button class="btn btn-sm btn-filter" :class="{active: filterStatus === 'approved'}" @click="filterStatus = 'approved'">Đã duyệt</button>
                <button class="btn btn-sm btn-filter" :class="{active: filterStatus === 'rejected'}" @click="filterStatus = 'rejected'">Từ chối</button>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive table-container-premium mx-4 mb-4">
              <table class="table align-middle mb-0 text-nowrap">
                <thead>
                  <tr class="text-secondary text-uppercase small">
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
                    <td colspan="7" class="text-center py-5 text-muted bg-transparent fw-medium">
                      <i class="bx bx-box d-block display-4 opacity-25 mb-2 text-warning"></i>
                      Không có đề xuất nào phù hợp trong danh sách.
                    </td>
                  </tr>
                  <tr v-for="item in filteredProposals" :key="item.id" class="table-row-premium">
                    <td class="ps-4 bg-transparent">
                      <strong class="theme-text-main d-block font-bold mb-1">{{ item.proposed_by ? item.proposed_by.ho_ten : 'Khách vãng lai' }}</strong>
                      <span class="badge bg-light text-dark font-9 py-0.5 px-2" style="border: 1px solid rgba(0,0,0,0.1); border-radius: 4px !important;">
                        <i class="bx bx-git-branch text-warning"></i> {{ getProposalBranchName(item) }}
                      </span>
                    </td>
                    <td class="bg-transparent">
                      <span class="badge badge-premium-type" :class="typeBadgeClass(item.type)">
                        {{ typeLabel(item.type) }}
                      </span>
                    </td>
                    <td class="bg-transparent">
                      <div class="d-flex align-items-center gap-2" v-if="item.thanh_vien">
                        <img :src="item.thanh_vien.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.thanh_vien.ho_ten)}&background=f97316&color=fff&size=30`" class="rounded-circle border border-2 border-white shadow-sm" width="30" height="30" />
                        <div>
                          <span class="fw-bold theme-text-main d-block lh-1 mb-1">{{ item.thanh_vien.ho_ten }}</span>
                          <small class="text-secondary small">(Đời {{ item.thanh_vien.doi_thu }})</small>
                        </div>
                      </div>
                      <span class="text-secondary small" v-else>Không rõ</span>
                    </td>
                    <td class="bg-transparent font-monospace small theme-text-main fw-semibold">
                      {{ item.data.ho_ten }} ({{ item.data.gioi_tinh }}, Đời {{ item.data.doi_thu }})
                    </td>
                    <td class="bg-transparent text-secondary font-medium small">{{ fmtDateTime(item.created_at) }}</td>
                    <td class="bg-transparent">
                      <span class="badge badge-status" :class="statusBadgeClass(item.status)">
                        {{ statusLabel(item.status) }}
                      </span>
                    </td>
                    <td class="pe-4 text-end bg-transparent">
                      <button class="btn btn-sm btn-action-edit px-3" @click="viewProposal(item)">
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

    <div class="modal fade" id="compareProposalModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content radius-24 shadow-lg border-0 bg-adaptive-card overflow-hidden">
          <div class="modal-header border-0 bg-dark-premium text-white p-4">
            <h5 class="modal-title fw-bold text-warning">
              <i class="bx bx-git-compare me-2"></i>So Sánh Chi Tiết Đề Xuất Thay Đổi
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <!-- Proposal Type Banner -->
            <div class="alert border-0 radius-12 p-3 text-start mb-4 d-flex align-items-center gap-2" style="background: rgba(249, 115, 22, 0.08); border: 1px solid rgba(249, 115, 22, 0.15) !important;">
              <i class="bx bx-info-circle fs-4 text-warning"></i>
              <div>
                <span class="fw-bold text-dark theme-text-main">Mục tiêu đề xuất:</span>
                <span class="text-secondary ms-1">
                  <template v-if="currentProposal.type === 'edit'">Chỉnh sửa thông tin thành viên <strong>{{ currentProposal.thanh_vien ? currentProposal.thanh_vien.ho_ten : '' }}</strong></template>
                  <template v-else-if="currentProposal.type === 'add_child'">Thêm con mới cho cha/mẹ là <strong>{{ currentProposal.thanh_vien ? currentProposal.thanh_vien.ho_ten : '' }}</strong></template>
                  <template v-else-if="currentProposal.type === 'add_spouse'">Thêm phối ngẫu mới cho <strong>{{ currentProposal.thanh_vien ? currentProposal.thanh_vien.ho_ten : '' }}</strong></template>
                  <template v-else-if="currentProposal.type === 'delete'">Yêu cầu xóa thành viên <strong>{{ currentProposal.thanh_vien ? currentProposal.thanh_vien.ho_ten : '' }}</strong> khỏi phả hệ</template>
                  <template v-else>Thêm phối ngẫu mới cho <strong>{{ currentProposal.thanh_vien ? currentProposal.thanh_vien.ho_ten : '' }}</strong></template>
                </span>
              </div>
            </div>

            <div class="row g-4 mb-4">
              <div class="col-md-6 border-adaptive-end text-start">
                <h6 class="fw-bold mb-3 text-secondary text-uppercase small font-bold">
                  <i class="bx bx-user-voice me-1 text-warning"></i>
                  Thông Tin Người Đề Xuất
                </h6>
                
                <div v-if="!currentProposal.proposed_by" class="alert alert-premium-info p-4 text-center text-muted radius-12">
                  <i class="bx bx-user d-block display-5 opacity-25 mb-2 text-warning"></i>
                  Khách vãng lai gửi đề xuất.
                </div>
                <div v-else class="current-info-card p-4 rounded-4 bg-adaptive-input text-start">
                  <div class="d-flex align-items-center gap-3 mb-4">
                    <img :src="currentProposal.proposed_by.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(currentProposal.proposed_by.ho_ten)}&background=6b7280&color=fff&size=80`" class="rounded-circle border border-3 border-white shadow-sm" width="60" height="60" style="object-fit:cover" />
                    <div>
                      <h5 class="fw-bold theme-text-main mb-1">{{ currentProposal.proposed_by.ho_ten }}</h5>
                      <span class="badge bg-secondary-premium">
                        {{ currentProposal.proposed_by.vai_tro || 'Thành viên' }}
                      </span>
                    </div>
                  </div>
                  <div class="row g-3">
                    <div class="col-12"><span class="text-secondary d-block small mb-1">Email liên hệ:</span><strong class="theme-text-main font-13">{{ currentProposal.proposed_by.email }}</strong></div>
                    <div class="col-6"><span class="text-secondary d-block small mb-1">Số điện thoại:</span><strong class="theme-text-main">{{ currentProposal.proposed_by.so_dien_thoai || 'Chưa cập nhật' }}</strong></div>
                    <div class="col-6"><span class="text-secondary d-block small mb-1">Trạng thái tài khoản:</span><strong class="theme-text-main text-success">Hoạt động</strong></div>
                    <div class="col-12 pt-2 border-top border-opacity-10" v-if="currentProposal.proposed_by.chi_nhanh">
                      <span class="text-secondary d-block small mb-1">Chi nhánh quản lý:</span>
                      <strong class="theme-text-main small">{{ currentProposal.proposed_by.chi_nhanh.ten_chi }}</strong>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <h6 class="fw-bold mb-3 text-warning text-uppercase small font-bold"><i class="bx bx-message-square-edit me-1"></i>Dữ Liệu Đề Xuất Thay Đổi</h6>
                <div class="proposed-info-card p-4 rounded-4 bg-adaptive-input text-start" v-if="currentProposal.data">
                  <!-- Case 1: Member Delete Proposal -->
                  <div v-if="currentProposal.type === 'delete'" class="delete-request-widget text-center py-2">
                    <div class="text-danger mb-3">
                      <i class="bx bx-trash display-4" style="color: #dc3545 !important;"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-danger">Đề xuất gỡ bỏ</h5>
                    <span class="badge bg-danger mb-3 px-3 py-1.5 radius-8" style="background-color: #dc3545 !important;">HÀNH ĐỘNG: XÓA CỨNG</span>
                    
                    <div class="p-3 rounded-3 text-start mt-2 border border-danger border-opacity-20" style="background: rgba(239, 68, 68, 0.05); border-color: rgba(220, 53, 69, 0.2) !important;">
                      <span class="text-danger d-block small mb-1 fw-bold"><i class="bx bx-info-circle me-1"></i>Lý do đề xuất:</span>
                      <p class="mb-0 theme-text-main fw-medium text-danger small">{{ currentProposal.data.ly_do_xoa }}</p>
                    </div>
                  </div>

                  <!-- Case 2: Other Proposals (edit, add_child, add_spouse) -->
                  <div v-else>
                    <div class="d-flex align-items-center gap-3 mb-4">
                      <img :src="currentProposal.data.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(currentProposal.data.ho_ten)}&background=f97316&color=fff&size=80`" class="rounded-circle border border-3 border-white shadow-sm" width="60" height="60" style="object-fit:cover" />
                      <div>
                        <h5 class="fw-bold mb-1 theme-text-main" :class="isDiff('ho_ten')">
                          {{ currentProposal.data.ho_ten }}
                          <span v-if="hasDiff('ho_ten')" class="text-muted font-11 fw-normal d-block mt-0.5">(Cũ: {{ getOldValue('ho_ten') }})</span>
                        </h5>
                        <span class="badge bg-orange-premium" :class="isDiff('doi_thu')">
                          Đời thứ {{ currentProposal.data.doi_thu }}
                          <span v-if="hasDiff('doi_thu')" class="font-normal ms-1">(Cũ: {{ getOldValue('doi_thu') }})</span>
                        </span>
                      </div>
                    </div>
                    <div class="row g-3">
                      <div class="col-6">
                        <span class="text-secondary d-block small mb-1">Giới tính:</span>
                        <strong class="theme-text-main" :class="isDiff('gioi_tinh')">
                          {{ currentProposal.data.gioi_tinh }}
                          <span v-if="hasDiff('gioi_tinh')" class="text-muted font-11 fw-normal d-block">(Cũ: {{ getOldValue('gioi_tinh') }})</span>
                        </strong>
                      </div>
                      <div class="col-6">
                        <span class="text-secondary d-block small mb-1">Trạng thái:</span>
                        <strong class="theme-text-main" :class="isDiff('trang_thai')">
                          {{ currentProposal.data.trang_thai }}
                          <span v-if="hasDiff('trang_thai')" class="text-muted font-11 fw-normal d-block">(Cũ: {{ getOldValue('trang_thai') }})</span>
                        </strong>
                      </div>
                      <div class="col-6">
                        <span class="text-secondary d-block small mb-1">Ngày sinh:</span>
                        <strong class="theme-text-main" :class="isDiff('ngay_sinh')">
                          {{ formatDate(currentProposal.data.ngay_sinh) }}
                          <span v-if="hasDiff('ngay_sinh')" class="text-muted font-11 fw-normal d-block">(Cũ: {{ getOldValue('ngay_sinh') }})</span>
                        </strong>
                      </div>
                      <div class="col-6" v-if="currentProposal.data.trang_thai === 'Đã mất' || hasDiff('ngay_mat')">
                        <span class="text-secondary d-block small mb-1">Ngày mất:</span>
                        <strong class="theme-text-main" :class="isDiff('ngay_mat')">
                          {{ formatDate(currentProposal.data.ngay_mat) }}
                          <span v-if="hasDiff('ngay_mat')" class="text-muted font-11 fw-normal d-block">(Cũ: {{ getOldValue('ngay_mat') }})</span>
                        </strong>
                      </div>
                      <div class="col-6">
                        <span class="text-secondary d-block small mb-1">Hôn nhân:</span>
                        <strong class="theme-text-main" :class="isDiff('tinh_trang_hon_nhan')">
                          {{ currentProposal.data.tinh_trang_hon_nhan || 'Chưa rõ' }}
                          <span v-if="hasDiff('tinh_trang_hon_nhan')" class="text-muted font-11 fw-normal d-block">(Cũ: {{ getOldValue('tinh_trang_hon_nhan') || 'Chưa rõ' }})</span>
                        </strong>
                      </div>
                      <div class="col-6">
                        <span class="text-secondary d-block small mb-1">Số điện thoại:</span>
                        <strong class="theme-text-main" :class="isDiff('so_dien_thoai')">
                          {{ currentProposal.data.so_dien_thoai || 'Chưa có' }}
                          <span v-if="hasDiff('so_dien_thoai')" class="text-muted font-11 fw-normal d-block">(Cũ: {{ getOldValue('so_dien_thoai') || 'Chưa có' }})</span>
                        </strong>
                      </div>
                      <div class="col-6">
                        <span class="text-secondary d-block small mb-1">Email:</span>
                        <strong class="theme-text-main" :class="isDiff('email')">
                          {{ currentProposal.data.email || 'Chưa có' }}
                          <span v-if="hasDiff('email')" class="text-muted font-11 fw-normal d-block">(Cũ: {{ getOldValue('email') || 'Chưa có' }})</span>
                        </strong>
                      </div>
                      <div class="col-6">
                        <span class="text-secondary d-block small mb-1">Nghề nghiệp:</span>
                        <strong class="theme-text-main" :class="isDiff('nghe_nghiep')">
                          {{ currentProposal.data.nghe_nghiep || 'Chưa có' }}
                          <span v-if="hasDiff('nghe_nghiep')" class="text-muted font-11 fw-normal d-block">(Cũ: {{ getOldValue('nghe_nghiep') || 'Chưa có' }})</span>
                        </strong>
                      </div>
                      <div class="col-12">
                        <span class="text-secondary d-block small mb-1">Địa chỉ:</span>
                        <strong class="theme-text-main" :class="isDiff('dia_chi')">
                          {{ currentProposal.data.dia_chi || 'Chưa có' }}
                          <span v-if="hasDiff('dia_chi')" class="text-muted font-11 fw-normal d-block">(Cũ: {{ getOldValue('dia_chi') || 'Chưa có' }})</span>
                        </strong>
                      </div>
                      <div class="col-12 pt-2 border-top border-opacity-10" v-if="currentProposal.data.ghi_chu">
                        <span class="text-secondary d-block small mb-1">Ghi chú đề xuất:</span>
                        <p class="mb-0 text-secondary small" :class="isDiff('ghi_chu')">
                          {{ currentProposal.data.ghi_chu }}
                          <span v-if="hasDiff('ghi_chu')" class="text-muted font-11 fw-normal d-block mt-1">(Cũ: {{ getOldValue('ghi_chu') || 'Không có' }})</span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-2 text-start px-1" v-if="currentProposal.status === 'pending'">
              <label class="form-label fw-bold text-secondary small text-uppercase font-bold">Ghi chú phản hồi / Lý do từ chối (nếu có)</label>
              <textarea class="form-control premium-textarea" rows="3" v-model="responseNote" placeholder="Ví dụ: Đã đối chiếu thông tin chính xác, hoặc Từ chối do trùng lặp thành viên..."></textarea>
            </div>
            
            <div class="p-3 rounded-4 border bg-success-premium text-start" v-else>
              <h6 class="fw-bold mb-1 text-success"><i class="bx bx-check-circle me-1"></i>Hệ thống đã xử lý đề xuất này:</h6>
              <p class="mb-0 text-secondary small">Người thực hiện: {{ currentProposal.approver ? currentProposal.approver.ho_ten : 'Hệ thống tự động' }}</p>
              <p class="mb-0 text-secondary small" v-if="currentProposal.note">Nội dung phản hồi: {{ currentProposal.note }}</p>
            </div>
          </div>
          <div class="modal-footer border-0 p-4 justify-content-end gap-2">
            <button class="btn btn-light radius-30 px-4 fw-medium" data-bs-dismiss="modal">Đóng</button>
            <template v-if="currentProposal.status === 'pending'">
              <button class="btn btn-action-delete radius-30 px-4 fw-bold border-1" @click="rejectProposal">
                <i class="bx bx-x-circle me-1"></i> Từ Chối
              </button>
              <button class="btn btn-gradient-orange radius-30 px-4 fw-bold text-white border-0" @click="approveProposal">
                <i class="bx bx-check-circle me-1"></i> Phê Duyệt
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
      selectedChiNhanhId: 'all',
      modal: null,
      isLoading: false
    };
  },
  computed: {
    filteredProposals() {
      let list = this.proposals;
      if (this.filterStatus !== 'all') {
        list = list.filter(p => p.status === this.filterStatus);
      }
      if (this.selectedChiNhanhId !== 'all') {
        list = list.filter(p => {
          const branchId = p.thanh_vien?.chi_nhanh_id || p.data?.chi_nhanh_id || p.proposed_by?.chi_nhanh_id;
          return branchId == this.selectedChiNhanhId;
        });
      }
      return list;
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
    refreshData() {
      this.isLoading = true;
      Promise.all([
        axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders()),
        axios.get('http://127.0.0.1:8000/api/de-xuat/get-data', this.getHeaders())
      ]).then(([resC, resP]) => {
        if (resC.data.status) this.listChiNhanh = resC.data.data;
        if (resP.data.status) this.proposals = resP.data.data;
        toastr.success('Dữ liệu đề xuất đã được làm mới!');
      }).catch(err => {
        toastr.error('Lỗi khi tải lại dữ liệu đề xuất.');
      }).finally(() => {
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
        return 'text-success-premium'; 
      }
      let valOld = this.currentProposal.thanh_vien[field];
      let valNew = this.currentProposal.data[field];
      if (field === 'ngay_sinh' || field === 'ngay_mat') {
        valOld = valOld ? valOld.substring(0, 10) : null;
        valNew = valNew ? valNew.substring(0, 10) : null;
      }
      return valOld != valNew ? 'diff-highlight-danger font-bold' : '';
    },
    hasDiff(field) {
      if (this.currentProposal.type !== 'edit' || !this.currentProposal.thanh_vien || !this.currentProposal.data) {
        return false;
      }
      let valOld = this.currentProposal.thanh_vien[field];
      let valNew = this.currentProposal.data[field];
      if (field === 'ngay_sinh' || field === 'ngay_mat') {
        valOld = valOld ? valOld.substring(0, 10) : null;
        valNew = valNew ? valNew.substring(0, 10) : null;
      }
      return valOld != valNew;
    },
    getOldValue(field) {
      if (!this.currentProposal.thanh_vien) return null;
      let val = this.currentProposal.thanh_vien[field];
      if (field === 'ngay_sinh' || field === 'ngay_mat') {
        return this.formatDate(val);
      }
      return val;
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
      if (t === 'add_spouse') return 'Thêm vợ/chồng';
      if (t === 'delete') return 'Yêu cầu Xóa';
      return t;
    },
    typeBadgeClass(t) {
      if (t === 'edit') return 'badge-edit-type';
      if (t === 'add_child') return 'badge-child-type';
      if (t === 'delete') return 'badge-delete-type';
      return 'badge-spouse-type';
    },
    statusLabel(s) {
      if (s === 'pending') return 'Chờ duyệt';
      if (s === 'approved') return 'Đã duyệt';
      return 'Từ chối';
    },
    statusBadgeClass(s) {
      if (s === 'pending') return 'bg-warning-premium';
      if (s === 'approved') return 'bg-success-premium';
      return 'bg-danger-premium';
    },
    getProposalBranchName(item) {
      const branchId = item.thanh_vien?.chi_nhanh_id || item.data?.chi_nhanh_id || item.proposed_by?.chi_nhanh_id;
      if (!branchId) return 'Không rõ';
      const branch = this.listChiNhanh.find(c => c.id === branchId);
      return branch ? branch.ten_chi : 'Không rõ';
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
.border-adaptive-end { border-right: 1px solid var(--border-color); }

/* Thẻ Chi Nhánh Tự Động Duyệt */
.branch-config-card {
  border-radius: 16px !important;
  transition: all 0.25s ease;
}
.branch-config-card:hover {
  transform: translateY(-3px);
  border-color: rgba(249, 115, 22, 0.35) !important;
}
.form-switch .form-check-input:checked {
  background-color: #f97316 !important;
  border-color: #f97316 !important;
}

/* NÚT LỌC VIÊN THUỐC (FILTER BUTTONS) */
.btn-filter {
  background: var(--input-bg);
  color: var(--text-sub) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 30px !important;
  padding: 6px 16px !important;
  font-size: 13px; font-weight: 600;
  transition: all 0.2s ease;
}
.btn-filter:hover { transform: translateY(-1px); color: var(--text-main) !important; }
.btn-filter.active {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
  border-color: transparent !important; color: white !important;
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
}
.table-row-premium:hover { background-color: var(--input-bg) !important; }

/* ─── HỆ THỐNG BADGE CAO CẤP MỜ PASTEL ─── */
.badge { padding: 6px 14px !important; font-size: 11px !important; font-weight: 700 !important; border-radius: 30px !important; }

/* Trạng thái duyệt */
.bg-warning-premium { background-color: rgba(245, 158, 11, 0.08) !important; color: #f59e0b !important; border: 1px solid rgba(245, 158, 11, 0.15); }
.bg-success-premium { background-color: rgba(16, 185, 129, 0.08) !important; color: #10b981 !important; border: 1px solid rgba(16, 185, 129, 0.15); }
.bg-danger-premium { background-color: rgba(239, 68, 68, 0.08) !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.15); }
.bg-secondary-premium { background-color: rgba(156, 163, 175, 0.08) !important; color: #9ca3af !important; border: 1px solid rgba(156, 163, 175, 0.15); }

/* Loại đề xuất */
.badge-premium-type { border-radius: 6px !important; font-size: 11.5px !important; }
.badge-edit-type { background-color: rgba(59, 130, 246, 0.08) !important; color: #3b82f6 !important; border: 1px solid rgba(59, 130, 246, 0.15); }
.badge-child-type { background-color: rgba(249, 115, 22, 0.08) !important; color: #f97316 !important; border: 1px solid rgba(249, 115, 22, 0.15); }
.badge-spouse-type { background-color: rgba(219, 39, 119, 0.08) !important; color: #db2777 !important; border: 1px solid rgba(219, 39, 119, 0.15); }
.badge-delete-type { background-color: rgba(239, 68, 68, 0.08) !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.15); }

/* NÚT TƯƠNG TÁC CHỨC NĂNG */
.btn-action-edit, .btn-action-delete {
  background: transparent !important; padding: 6px 14px !important; font-size: 13px; font-weight: 600; border-radius: 8px !important; transition: all 0.2s ease;
}
.btn-action-edit { border: 1px solid rgba(249, 115, 22, 0.3) !important; color: #f97316 !important; }
.btn-action-edit:hover { background: #f97316 !important; color: white !important; }

.btn-action-delete { border: 1px solid rgba(239, 68, 68, 0.3) !important; color: #ef4444 !important; background: transparent !important; }
.btn-action-delete:hover { background: #ef4444 !important; color: white !important; border-color: transparent !important; }

.btn-gradient-orange {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important; color: white !important; border: none;
}
.radius-30 { border-radius: 30px !important; }
.radius-24 { border-radius: 24px !important; }

/* MODAL CONTAINER HỆ THỐNG */
.bg-dark-premium { background: linear-gradient(145deg, #1a1c2e 0%, #141625 100%) !important; border-bottom: 1px solid var(--border-color); }
.alert-premium-info { background: var(--input-bg) !important; border: 1px solid var(--border-color) !important; }

.premium-textarea {
  border-radius: 14px !important; border: 1px solid var(--border-color) !important;
  padding: 12px 14px !important; font-size: 14px; background-color: var(--input-bg) !important; color: var(--text-main) !important; box-shadow: none !important;
}
.premium-textarea:focus { border-color: #f97316 !important; background-color: var(--card-bg) !important; }

/* ĐIỂM SÁNG CHÊNH LỆCH DỮ LIỆU (DIFF CORES) */
.text-success-premium { color: #10b981 !important; font-weight: 600; }
.diff-highlight-danger {
  background-color: rgba(239, 68, 68, 0.08) !important; color: #ef4444 !important;
  border: 1px solid rgba(239, 68, 68, 0.18); padding: 2px 8px; border-radius: 6px;
}

.btn-refresh-premium {
  background: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
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
  border-color: #f97316 !important;
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
}
.btn-refresh-premium:active {
  transform: scale(0.95);
}
</style>