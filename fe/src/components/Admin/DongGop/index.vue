<template>
  <div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
      <div>
        <h1 class="h3 fw-bold text-gradient mb-1" id="page-title">
          <i class="bx bx-gift me-2 text-primary animate-pulse"></i>Hệ Thống Quản Lý Đóng Góp Công Đức
        </h1>
        <p class="text-muted small mb-0">Quản trị toàn bộ các khoản đóng góp quỹ dòng họ, kiểm duyệt giao dịch chuyển khoản SePay và ghi nhận thủ công cho tất cả chi nhánh.</p>
      </div>
      <div>
        <button class="btn btn-primary-gradient radius-12 px-4 py-2-5 shadow-sm fw-bold border-0 d-flex align-items-center gap-2 btn-hover-scale"
                id="btn-open-modal"
                @click="openCreateModal">
          <i class="bx bx-plus-circle fs-5"></i> Ghi Nhận Công Đức
        </button>
      </div>
    </div>
    <div class="row g-4 mb-4">
      <!-- Widget 1: Total Approved Fund -->
      <div class="col-12 col-md-3">
        <div class="stat-card p-4 radius-20 shadow-sm border-0 d-flex justify-content-between align-items-center bg-card-glow-purple">
          <div class="lh-1">
            <span class="text-uppercase text-muted-custom fw-semibold font-xs tracking-wider d-block mb-2">Tổng Quỹ Công Đức</span>
            <span class="fs-3 fw-extrabold text-main d-block mb-1">{{ formatMoney(totalApprovedAmount) }} VNĐ</span>
            <span class="badge badge-success-subtle radius-8 font-xs py-1 px-2 d-inline-flex align-items-center gap-1">
              <i class="bx bx-wallet"></i> Quỹ đã duyệt
            </span>
          </div>
          <div class="card-icon-wrap rounded-circle bg-purple-subtle d-flex align-items-center justify-content-center text-purple">
            <i class="bx bx-wallet fs-3"></i>
          </div>
        </div>
      </div>

      <!-- Widget 2: Cash Fund -->
      <div class="col-12 col-md-3">
        <div class="stat-card p-4 radius-20 shadow-sm border-0 d-flex justify-content-between align-items-center bg-card-glow-teal">
          <div class="lh-1">
            <span class="text-uppercase text-muted-custom fw-semibold font-xs tracking-wider d-block mb-2">Quỹ Tiền Mặt</span>
            <span class="fs-3 fw-extrabold text-main d-block mb-1">{{ formatMoney(totalCashAmount) }} VNĐ</span>
            <span class="badge badge-teal-subtle radius-8 font-xs py-1 px-2 d-inline-flex align-items-center gap-1">
              <i class="bx bx-money-withdraw"></i> Đóng góp trực tiếp
            </span>
          </div>
          <div class="card-icon-wrap rounded-circle bg-teal-subtle d-flex align-items-center justify-content-center text-teal">
            <i class="bx bx-money fs-3"></i>
          </div>
        </div>
      </div>

      <!-- Widget 3: Bank / QR Fund -->
      <div class="col-12 col-md-3">
        <div class="stat-card p-4 radius-20 shadow-sm border-0 d-flex justify-content-between align-items-center bg-card-glow-blue">
          <div class="lh-1">
            <span class="text-uppercase text-muted-custom fw-semibold font-xs tracking-wider d-block mb-2">Quỹ Chuyển Khoản</span>
            <span class="fs-3 fw-extrabold text-main d-block mb-1">{{ formatMoney(totalBankAmount) }} VNĐ</span>
            <span class="badge badge-blue-subtle radius-8 font-xs py-1 px-2 d-inline-flex align-items-center gap-1">
              <i class="bx bx-qr-scan"></i> Tự động qua SePay
            </span>
          </div>
          <div class="card-icon-wrap rounded-circle bg-blue-subtle d-flex align-items-center justify-content-center text-blue">
            <i class="bx bx-transfer fs-3"></i>
          </div>
        </div>
      </div>

      <!-- Widget 4: Pending Approvals -->
      <div class="col-12 col-md-3">
        <div class="stat-card p-4 radius-20 shadow-sm border-0 d-flex justify-content-between align-items-center bg-card-glow-orange">
          <div class="lh-1">
            <span class="text-uppercase text-muted-custom fw-semibold font-xs tracking-wider d-block mb-2">Đang Chờ Duyệt</span>
            <span class="fs-3 fw-extrabold text-main d-block mb-1">{{ totalPendingCount }} Khoản</span>
            <span class="badge badge-orange-subtle radius-8 font-xs py-1 px-2 d-inline-flex align-items-center gap-1">
              <i class="bx bx-time-five"></i> Cần phê duyệt
            </span>
          </div>
          <div class="card-icon-wrap rounded-circle bg-orange-subtle d-flex align-items-center justify-content-center text-orange">
            <i class="bx bx-time fs-3"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Data List Table -->
    <div class="card shadow-sm border-0 radius-20 bg-card overflow-hidden">
      <div class="card-header bg-transparent border-0 py-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="mb-0 fw-bold text-main d-flex align-items-center gap-2">
          <i class="bx bx-list-ul text-primary"></i> Danh Sách Ghi Nhận Công Đức & Đóng Góp
        </h5>
        
        <!-- Filter and Search -->
        <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 flex-grow-1 max-w-lg-100 justify-content-md-end">
          <!-- Clan / Branch Filter -->
          <div class="branch-filter-wrap">
            <select class="form-select radius-12 border py-2 shadow-none-focus bg-transparent text-muted-custom" 
                    v-model="filterBranchId" 
                    id="filter-branch">
              <option :value="null">-- Lọc theo Chi Nhánh / Dòng Họ --</option>
              <option v-for="branch in listBranches" :key="branch.id" :value="branch.id">
                {{ branch.ten_chi }}
              </option>
            </select>
          </div>

          <!-- Search Box -->
          <div class="search-wrap flex-grow-1">
            <div class="input-group radius-12 border overflow-hidden search-input-group shadow-none-focus">
              <span class="input-group-text bg-transparent border-0 pe-1 text-muted">
                <i class="bx bx-search fs-5"></i>
              </span>
              <input type="text" 
                     class="form-control border-0 bg-transparent px-2 py-2 fs-6 shadow-none" 
                     placeholder="Tìm kiếm đóng góp..." 
                     v-model="searchQuery" 
                     id="search-input" />
              <button v-if="searchQuery" 
                      @click="searchQuery = ''" 
                      type="button" 
                      class="btn bg-transparent border-0 text-muted px-2 py-0">
                <i class="bx bx-x fs-5"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 custom-modern-table" id="donations-table">
            <thead>
              <tr>
                <th class="ps-4 text-muted-custom py-3 font-xs text-uppercase tracking-wider" width="5%">#</th>
                <th class="text-muted-custom py-3 font-xs text-uppercase tracking-wider" width="15%">Chi Nhánh</th>
                <th class="text-muted-custom py-3 font-xs text-uppercase tracking-wider" width="20%">Thành Viên</th>
                <th class="text-muted-custom py-3 font-xs text-uppercase tracking-wider" width="13%">Số Tiền</th>
                <th class="text-muted-custom py-3 font-xs text-uppercase tracking-wider" width="20%">Nội Dung Chi Tiết</th>
                <th class="text-muted-custom py-3 font-xs text-uppercase tracking-wider text-center" width="10%">Hình Thức</th>
                <th class="text-muted-custom py-3 font-xs text-uppercase tracking-wider text-center" width="10%">Trạng Thái</th>
                <th class="text-muted-custom py-3 font-xs text-uppercase tracking-wider" width="12%">Thời Gian</th>
                <th class="pe-4 text-muted-custom py-3 font-xs text-uppercase tracking-wider text-end" width="10%">Hành Động</th>
              </tr>
            </thead>
            <tbody>
              <!-- Spinner Loading State -->
              <tr v-if="isLoading">
                <td colspan="9" class="text-center py-5 text-muted bg-transparent">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <span class="d-block mt-2 font-medium">Đang tải dữ liệu...</span>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-else-if="filteredList.length === 0">
                <td colspan="9" class="text-center py-5 text-muted bg-transparent">
                  <i class="bx bx-file-blank fs-1 mb-2 text-muted animate-float d-block"></i>
                  <span class="font-medium text-main">Không tìm thấy khoản đóng góp nào</span>
                  <span class="d-block small text-muted-custom mt-1">Vui lòng chọn bộ lọc khác hoặc ghi nhận mới.</span>
                </td>
              </tr>

              <!-- Items Rows -->
              <tr v-for="(item, index) in filteredList" :key="item.id" class="table-row-hover">
                <td class="ps-4 fw-bold text-muted-custom font-sm">{{ index + 1 }}</td>
                <td>
                  <span class="badge badge-purple-subtle radius-8 font-xs py-1 px-2.5 fw-semibold">
                    {{ item.nguoi_dung?.chi_nhanh?.ten_chi || 'Không xác định' }}
                  </span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-3">
                    <div class="avatar-container position-relative">
                      <img v-if="item.nguoi_dung?.avatar && item.nguoi_dung?.avatar !== 'https://dzfullstack.com/assets/images/logo-1.png'" 
                           :src="item.nguoi_dung?.avatar" 
                           alt="Avatar" 
                           class="avatar-img rounded-circle border shadow-sm" />
                      <div v-else class="avatar-fallback rounded-circle bg-purple-subtle text-purple fw-bold d-flex align-items-center justify-content-center border border-purple-subtle shadow-sm">
                        {{ getFallbackInitial(item.nguoi_dung?.ho_ten || 'Hệ thống') }}
                      </div>
                    </div>
                    <div class="lh-1">
                      <strong class="text-main font-sm d-block mb-1">{{ item.nguoi_dung?.ho_ten || 'Thành viên ẩn danh' }}</strong>
                      <span class="text-muted-custom font-xs">{{ item.nguoi_dung?.email || 'N/A' }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="fw-extrabold text-success font-sm">
                    {{ formatMoney(getAmountFromContent(item.noi_dung)) }} VNĐ
                  </span>
                </td>
                <td class="max-w-xs text-truncate-custom font-sm text-muted-custom">{{ cleanContent(item.noi_dung) }}</td>
                <td class="text-center">
                  <span v-if="getPaymentType(item.noi_dung) === 'Tiền mặt'" 
                        class="badge badge-teal-subtle radius-8 font-xs py-1.5 px-2.5 fw-bold d-inline-flex align-items-center gap-1 shadow-sm-hover animate-all">
                    <i class="bx bx-money fs-6"></i> Tiền mặt
                  </span>
                  <span v-else 
                        class="badge badge-blue-subtle radius-8 font-xs py-1.5 px-2.5 fw-bold d-inline-flex align-items-center gap-1 shadow-sm-hover animate-all">
                    <i class="bx bx-qr-scan fs-6"></i> Chuyển khoản
                  </span>
                </td>
                <td class="text-center">
                  <button @click="changeStatus(item.id)"
                          :class="item.trang_thai === 'Đã duyệt' ? 'badge badge-success-btn' : 'badge badge-warning-btn text-dark'"
                          class="btn-badge py-1.5 px-2.5 border-0 radius-8 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                    <span class="dot-indicator" :class="item.trang_thai === 'Đã duyệt' ? 'bg-white' : 'bg-dark'"></span>
                    {{ item.trang_thai }}
                  </button>
                </td>
                <td class="font-sm text-muted-custom">
                  {{ formatDate(item.created_at) }}
                </td>
                <td class="pe-4 text-end">
                  <div class="d-inline-flex gap-2">
                    <button class="btn btn-sm btn-icon-glass text-primary radius-10 btn-hover-scale"
                            @click="editItem(item)"
                            title="Chỉnh sửa chi tiết">
                      <i class="bx bx-edit-alt"></i>
                    </button>
                    <button class="btn btn-sm btn-icon-glass text-danger radius-10 btn-hover-scale"
                            @click="deleteItem(item.id)"
                            title="Xóa bản ghi">
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

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="contributionModal" tabindex="-1" aria-hidden="true" ref="modalElement">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 radius-24 shadow-lg overflow-hidden bg-card">
          <!-- Modal Header -->
          <div class="modal-header py-4 px-4 border-0 d-flex align-items-center justify-content-between modal-header-purple">
            <h5 class="modal-title fw-extrabold text-white d-flex align-items-center gap-2">
              <i class="bx bx-gift animate-float"></i>
              {{ isEditing ? 'Cập Nhật Bản Ghi Công Đức' : 'Ghi Nhận Đóng Góp Quỹ Mới' }}
            </h5>
            <button type="button" class="btn-close-custom bg-transparent border-0 text-white opacity-75 hover-opacity-100" data-bs-dismiss="modal" aria-label="Close" @click="closeModal">
              <i class="bx bx-x fs-3"></i>
            </button>
          </div>

          <!-- Modal Body -->
          <form @submit.prevent="saveData">
            <div class="modal-body p-4">
              <!-- Select Branch -->
              <div class="mb-4">
                <label class="form-label fw-bold text-main font-sm mb-2" for="modal-branch">Chọn Chi Nhánh / Dòng Họ <span class="text-danger">*</span></label>
                <select class="form-select radius-12 border-2 py-2-5 shadow-none-focus"
                        id="modal-branch"
                        v-model="tempBranchId"
                        @change="onBranchChange"
                        required>
                  <option :value="null">-- Chọn chi nhánh dòng họ để lấy thành viên --</option>
                  <option v-for="branch in listBranches" :key="branch.id" :value="branch.id">
                    {{ branch.ten_chi }}
                  </option>
                </select>
              </div>

              <!-- Select Contributor -->
              <div class="mb-4">
                <label class="form-label fw-bold text-main font-sm mb-2" for="select-user">Người Đóng Góp <span class="text-danger">*</span></label>
                <select class="form-select radius-12 border-2 py-2-5 shadow-none-focus"
                        id="select-user"
                        v-model="formData.nguoi_dung_id"
                        :disabled="!tempBranchId"
                        required>
                  <option :value="null">-- {{ tempBranchId ? 'Chọn thành viên trong chi nhánh' : 'Vui lòng chọn chi nhánh trước' }} --</option>
                  <option v-for="user in listMembers" :key="user.id" :value="user.id">
                    {{ user.ho_ten }} ({{ user.email || 'Không có email' }})
                  </option>
                </select>
              </div>

              <!-- Select Payment Type -->
              <div class="mb-4">
                <label class="form-label fw-bold text-main font-sm mb-2" for="select-type">Hình Thức Đóng Góp <span class="text-danger">*</span></label>
                <select class="form-select radius-12 border-2 py-2-5 shadow-none-focus"
                        id="select-type"
                        v-model="tempType"
                        required>
                  <option value="Tiền mặt">Tiền mặt (Ủng hộ trực tiếp)</option>
                  <option value="Chuyển khoản">Chuyển khoản (SePay / QR)</option>
                </select>
              </div>

              <!-- Input Amount -->
              <div class="mb-4">
                <label class="form-label fw-bold text-main font-sm mb-2" for="input-amount">Số Tiền (VNĐ) <span class="text-danger">*</span></label>
                <div class="input-group radius-12 border-2 overflow-hidden shadow-none-focus-wrap">
                  <span class="input-group-text bg-light border-0 fw-bold text-muted">VNĐ</span>
                  <input type="number" 
                         id="input-amount"
                         class="form-control border-0 py-2-5 shadow-none" 
                         placeholder="Nhập số tiền đóng góp (ví dụ: 500000)" 
                         v-model.number="tempAmount"
                         min="1000"
                         required />
                </div>
                <div class="small text-primary mt-1 fw-semibold font-xs" v-if="tempAmount">
                  Bằng chữ: {{ formatAmountInWords(tempAmount) }}
                </div>
              </div>

              <!-- Select Status -->
              <div class="mb-4">
                <label class="form-label fw-bold text-main font-sm mb-2" for="select-status">Trạng Thái Phê Duyệt <span class="text-danger">*</span></label>
                <select class="form-select radius-12 border-2 py-2-5 shadow-none-focus"
                        id="select-status"
                        v-model="formData.trang_thai"
                        required>
                  <option value="Đã duyệt">Đã duyệt (Approved)</option>
                  <option value="Chờ duyệt">Chờ duyệt (Pending)</option>
                </select>
              </div>

              <!-- Notes Textarea -->
              <div class="mb-4">
                <label class="form-label fw-bold text-main font-sm mb-2" for="input-notes">Mục Đích / Ghi Chú Đóng Góp <span class="text-danger">*</span></label>
                <textarea rows="4" 
                          id="input-notes"
                          class="form-control radius-12 border-2 py-2-5 shadow-none-focus"
                          placeholder="Nhập lý lý do đóng tiền (ví dụ: Đóng góp tiền mặt hỗ trợ ngày giỗ tổ...)" 
                          v-model="tempNotes" 
                          required></textarea>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer py-3 px-4 border-0 bg-light d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-light radius-12 px-4 py-2 fw-semibold" data-bs-dismiss="modal" @click="closeModal">Đóng</button>
              <button type="submit" 
                      class="btn btn-primary-gradient radius-12 px-4 py-2 border-0 fw-bold shadow-sm-hover d-flex align-items-center gap-1 btn-hover-scale"
                      id="btn-save">
                <i class="bx bx-check-shield fs-5"></i>
                {{ isEditing ? 'Cập Nhật' : 'Ghi Nhận' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
  name: 'AdminQuanLyDongGop',
  data() {
    return {
      listData: [],
      listBranches: [],
      listMembers: [],
      formData: {
        id: null,
        nguoi_dung_id: null,
        noi_dung: '',
        trang_thai: 'Đã duyệt'
      },
      filterBranchId: null,
      tempBranchId: null,
      tempAmount: null,
      tempType: 'Tiền mặt',
      tempNotes: '',
      isEditing: false,
      isLoading: false,
      searchQuery: '',
      bsModal: null
    };
  },
  computed: {
    // Lọc danh sách đóng góp theo tìm kiếm từ khóa và chi nhánh chọn
    filteredList() {
      let data = this.listData;
      
      if (this.filterBranchId) {
        data = data.filter(item => item.nguoi_dung?.chi_nhanh_id === this.filterBranchId);
      }

      if (!this.searchQuery) return data;
      const q = this.searchQuery.toLowerCase();
      return data.filter(item => {
        const userName = (item.nguoi_dung?.ho_ten || 'Hệ thống').toLowerCase();
        const email = (item.nguoi_dung?.email || '').toLowerCase();
        const content = (item.noi_dung || '').toLowerCase();
        const branchName = (item.nguoi_dung?.chi_nhanh?.ten_chi || '').toLowerCase();
        return userName.includes(q) || email.includes(q) || content.includes(q) || branchName.includes(q);
      });
    },

    // Tổng số tiền đóng góp đã duyệt (được trích xuất từ cột nội dung bằng regex)
    totalApprovedAmount() {
      return this.listData
        .reduce((sum, item) => sum + (item.trang_thai === 'Đã duyệt' ? this.getAmountFromContent(item.noi_dung) : 0), 0);
    },

    // Tổng số tiền đóng góp qua Tiền mặt
    totalCashAmount() {
      return this.listData
        .reduce((sum, item) => sum + (item.trang_thai === 'Đã duyệt' && this.getPaymentType(item.noi_dung) === 'Tiền mặt' ? this.getAmountFromContent(item.noi_dung) : 0), 0);
    },

    // Tổng số tiền đóng góp qua Chuyển khoản (SePay)
    totalBankAmount() {
      return this.listData
        .reduce((sum, item) => sum + (item.trang_thai === 'Đã duyệt' && this.getPaymentType(item.noi_dung) === 'Chuyển khoản' ? this.getAmountFromContent(item.noi_dung) : 0), 0);
    },

    // Tổng số lượng giao dịch chờ phê duyệt
    totalPendingCount() {
      return this.listData.filter(item => item.trang_thai === 'Chờ duyệt').length;
    }
  },
  mounted() {
    this.loadData();
    this.loadBranches();
    
    // Khởi tạo Bootstrap Modal
    const modalEl = document.getElementById('contributionModal');
    if (modalEl && window.bootstrap) {
      this.bsModal = new window.bootstrap.Modal(modalEl);
    }
  },
  methods: {
    // Tải toàn bộ danh sách đóng góp
    loadData() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/dong-gop/get-data', {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token')
        }
      })
      .then(res => {
        if (res.data.status) {
          this.listData = res.data.data || [];
        }
      })
      .catch(err => {
        console.error(err);
        toastr.error('Không thể tải dữ liệu đóng góp từ máy chủ!');
      })
      .finally(() => {
        this.isLoading = false;
      });
    },

    // Tải tất cả chi nhánh
    loadBranches() {
      axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token')
        }
      })
      .then(res => {
        if (res.data.status) {
          this.listBranches = res.data.data || [];
        }
      })
      .catch(err => console.error(err));
    },

    // Tải thành viên thuộc chi nhánh được chọn trong modal
    onBranchChange() {
      this.listMembers = [];
      this.formData.nguoi_dung_id = null;
      if (!this.tempBranchId) return;

      axios.get(`http://127.0.0.1:8000/api/thanh-vien/chi-nhanh/${this.tempBranchId}`, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token')
        }
      })
      .then(res => {
        if (res.data.status) {
          this.listMembers = res.data.data || [];
        }
      })
      .catch(err => {
        console.error(err);
        toastr.error('Không thể tải danh sách thành viên thuộc chi nhánh này!');
      });
    },

    // Mở modal thêm mới
    openCreateModal() {
      this.isEditing = false;
      this.resetForm();
      if (this.bsModal) {
        this.bsModal.show();
      }
    },

    // Mở modal chỉnh sửa và tự động trích xuất các thông tin từ `noi_dung`
    async editItem(item) {
      this.isEditing = true;
      this.tempBranchId = item.nguoi_dung?.chi_nhanh_id || null;

      // 1. Tải danh sách thành viên thuộc chi nhánh tương ứng trước
      if (this.tempBranchId) {
        try {
          const res = await axios.get(`http://127.0.0.1:8000/api/thanh-vien/chi-nhanh/${this.tempBranchId}`, {
            headers: { Authorization: 'Bearer ' + localStorage.getItem('access_token') }
          });
          if (res.data.status) {
            this.listMembers = res.data.data || [];
          }
        } catch (e) {
          console.error(e);
        }
      }

      // 2. Tìm thành viên gia phả tương ứng dựa theo email hoặc tên
      const matchedMember = this.listMembers.find(u => {
        if (item.nguoi_dung?.email) {
          return u.email === item.nguoi_dung.email;
        }
        return u.ho_ten === item.nguoi_dung?.ho_ten;
      });

      this.formData = {
        id: item.id,
        nguoi_dung_id: matchedMember ? matchedMember.id : null,
        noi_dung: item.noi_dung,
        trang_thai: item.trang_thai || 'Đã duyệt'
      };
      
      // Trích xuất thông tin
      this.tempAmount = this.getAmountFromContent(item.noi_dung) || null;
      this.tempType = this.getPaymentType(item.noi_dung);
      this.tempNotes = this.cleanContent(item.noi_dung);
      
      if (this.bsModal) {
        this.bsModal.show();
      }
    },

    // Lưu dữ liệu (Thêm mới hoặc cập nhật đóng góp)
    saveData() {
      if (!this.tempBranchId) {
        toastr.warning('Vui lòng chọn chi nhánh dòng họ!');
        return;
      }
      if (!this.formData.nguoi_dung_id) {
        toastr.warning('Vui lòng chọn người đóng góp!');
        return;
      }
      if (!this.tempAmount || this.tempAmount < 1000) {
        toastr.warning('Vui lòng nhập số tiền đóng góp hợp lệ (từ 1.000đ)!');
        return;
      }
      if (!this.tempNotes.trim()) {
        toastr.warning('Vui lòng nhập mục đích đóng góp!');
        return;
      }

      // Tự động lắp ráp noi_dung theo quy tắc để regex client trích xuất chính xác
      const targetUser = this.listMembers.find(u => u.id === this.formData.nguoi_dung_id);
      const userCode = targetUser ? targetUser.ho_ten.split(' ').pop().toUpperCase() + targetUser.id : 'MEMBER';
      this.formData.noi_dung = `DONGGOP ${userCode} | Số tiền: ${this.formatMoney(this.tempAmount)} VNĐ | Hình thức: ${this.tempType} | Ghi chú: ${this.tempNotes.trim()}`;

      const payload = {
        id: this.formData.id,
        thanh_vien_id: this.formData.nguoi_dung_id, // Gửi thanh_vien_id để tự động tìm/tạo NguoiDung
        noi_dung: this.formData.noi_dung,
        trang_thai: this.formData.trang_thai
      };

      const url = this.isEditing 
        ? 'http://127.0.0.1:8000/api/dong-gop/update'
        : 'http://127.0.0.1:8000/api/dong-gop/create';

      axios.post(url, payload, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token')
        }
      })
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.loadData();
          this.closeModal();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error('Đã xảy ra lỗi trong quá trình lưu dữ liệu!');
        console.error(err);
      });
    },

    // Xóa đóng góp
    deleteItem(id) {
      if (confirm('Bạn có chắc chắn muốn xóa bản ghi đóng góp này? Hành động này không thể hoàn tác.')) {
        axios.post('http://127.0.0.1:8000/api/dong-gop/delete', { id }, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        })
        .then(res => {
          if (res.data.status) {
            toastr.success(res.data.message);
            this.loadData();
          } else {
            toastr.error(res.data.message);
          }
        })
        .catch(err => {
          toastr.error('Lỗi khi thực hiện xóa đóng góp!');
          console.error(err);
        });
      }
    },

    // Duyệt Nhanh 1-Click
    changeStatus(id) {
      axios.post('http://127.0.0.1:8000/api/dong-gop/status', { id }, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token')
        }
      })
      .then(res => {
        if (res.data.status) {
          toastr.success('Cập nhật trạng thái thành công!');
          this.loadData();
        }
      })
      .catch(err => {
        toastr.error('Lỗi khi cập nhật trạng thái đóng góp!');
        console.error(err);
      });
    },

    // Đóng Modal và Reset
    closeModal() {
      if (this.bsModal) {
        this.bsModal.hide();
      }
      this.resetForm();
    },

    resetForm() {
      this.isEditing = false;
      this.tempBranchId = null;
      this.listMembers = [];
      this.formData = {
        id: null,
        nguoi_dung_id: null,
        noi_dung: '',
        trang_thai: 'Đã duyệt'
      };
      this.tempAmount = null;
      this.tempType = 'Tiền mặt';
      this.tempNotes = '';
    },

    // Regex thông minh trích xuất số tiền từ noi_dung
    getAmountFromContent(content) {
      if (!content) return 0;
      
      // Pattern 1: Tìm 'Số tiền: ...'
      const matchAmountPrefix = content.match(/Số tiền:\s*([\d\.,\s]+)/i);
      if (matchAmountPrefix) {
        const clean = matchAmountPrefix[1].replace(/[\.,\s]/g, '');
        const val = parseFloat(clean);
        if (!isNaN(val)) return val;
      }
      
      // Pattern 2: Tìm chữ số liền kề chữ VNĐ/đ/vnd/đồng
      const matchVndSuffix = content.match(/([\d\.,\s]+)\s*(VNĐ|đ|vnd|đồng)/i);
      if (matchVndSuffix) {
        const clean = matchVndSuffix[1].replace(/[\.,\s]/g, '');
        const val = parseFloat(clean);
        if (!isNaN(val)) return val;
      }

      // Pattern 3: Tìm số có giá trị > 1000 bất kì
      const numbers = content.replace(/[^0-9]/g, ' ').split(/\s+/);
      for (const num of numbers) {
        if (num.length >= 4) {
          const val = parseFloat(num);
          if (!isNaN(val) && val >= 1000) return val;
        }
      }

      return 0;
    },

    // Trích xuất hình thức đóng tiền (Tiền mặt / Chuyển khoản) từ nội dung chuỗi
    getPaymentType(content) {
      if (!content) return 'Tiền mặt';
      if (content.toLowerCase().includes('tiền mặt')) return 'Tiền mặt';
      if (content.toLowerCase().includes('chuyển khoản') || content.toLowerCase().includes('qr công đức') || content.toLowerCase().includes('sepay')) return 'Chuyển khoản';
      return 'Chuyển khoản';
    },

    // Tinh giản nội dung để loại bỏ phần mã giao dịch và số tiền, chỉ hiển thị ghi chú cốt lõi
    cleanContent(content) {
      if (!content) return '';
      const parts = content.split('|');
      for (let part of parts) {
        if (part.toLowerCase().includes('ghi chú:') || part.toLowerCase().includes('nội dung:')) {
          return part.replace(/ghi chú:\s*/i, '').replace(/nội dung:\s*/i, '').trim();
        }
      }
      if (parts.length >= 3) {
        return parts.slice(2).join('|').replace(/Hình thức:\s*[\S ]+\s*/i, '').trim();
      }
      return content.replace(/DONGGOP\s+\S+\s*/i, '').trim();
    },

    // Định dạng tiền tệ đẹp mắt
    formatMoney(value) {
      if (!value) return '0';
      return value.toLocaleString('vi-VN');
    },

    // Định dạng ngày giờ
    formatDate(dateStr) {
      if (!dateStr) return '';
      try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('vi-VN', {
          year: 'numeric',
          month: '2-digit',
          day: '2-digit',
          hour: '2-digit',
          minute: '2-digit'
        });
      } catch (e) {
        return dateStr;
      }
    },

    // Viết chữ cái đại diện khi không có avatar
    getFallbackInitial(name) {
      if (!name) return '?';
      return name.charAt(0).toUpperCase();
    },

    // Đọc số tiền ra chữ tiếng Việt
    formatAmountInWords(amount) {
      if (!amount || isNaN(amount)) return '';
      
      const units = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
      const tens = ['', 'mười', 'hai mươi', 'ba mươi', 'bốn mươi', 'năm mươi', 'sáu mươi', 'bảy mươi', 'tám mươi', 'chín mươi'];
      
      const readGroup = (numStr) => {
        let text = '';
        const hundreds = parseInt(numStr.charAt(0));
        const tenDigit = parseInt(numStr.charAt(1));
        const unitDigit = parseInt(numStr.charAt(2));
        
        if (hundreds > 0) {
          text += units[hundreds] + ' trăm ';
        } else if (hundreds === 0 && (tenDigit > 0 || unitDigit > 0)) {
          text += 'không trăm ';
        }
        
        if (tenDigit > 0) {
          text += tens[tenDigit] + ' ';
        } else if (tenDigit === 0 && unitDigit > 0 && hundreds >= 0) {
          text += 'lẻ ';
        }
        
        if (unitDigit > 0) {
          if (unitDigit === 5 && tenDigit > 0) {
            text += 'lăm';
          } else if (unitDigit === 1 && tenDigit > 1) {
            text += 'mốt';
          } else {
            text += units[unitDigit];
          }
        }
        return text.trim();
      };
      
      let str = amount.toString();
      while (str.length < 12) {
        str = '0' + str;
      }
      
      const billions = str.substring(0, 3);
      const millions = str.substring(3, 6);
      const thousands = str.substring(6, 9);
      const ones = str.substring(9, 12);
      
      let outText = '';
      if (parseInt(billions) > 0) outText += readGroup(billions) + ' tỷ ';
      if (parseInt(millions) > 0) outText += readGroup(millions) + ' triệu ';
      if (parseInt(thousands) > 0) outText += readGroup(thousands) + ' nghìn ';
      if (parseInt(ones) > 0) outText += readGroup(ones) + ' ';
      
      outText = outText.trim();
      if (!outText) return 'Không đồng';
      
      return outText.charAt(0).toUpperCase() + outText.slice(1) + ' đồng chẵn';
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

/* Typography & Layout Reset */
div, h1, h5, p, table, button, label, select, input {
  font-family: 'Outfit', sans-serif;
}

/* Luxury Purple Theme & Gradient */
.text-gradient {
  background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 50%, #d946ef 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.btn-primary-gradient {
  background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
  color: #ffffff;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.btn-primary-gradient:hover {
  background: linear-gradient(135deg, #5c52f9 0%, #9a6ffa 100%);
  color: #ffffff;
  box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3);
}

.modal-header-purple {
  background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
}

/* Card Glow Designs */
.stat-card {
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  border: 1px solid rgba(0, 0, 0, 0.03);
}
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.06) !important;
}

.bg-card-glow-purple {
  background: linear-gradient(135deg, #ffffff 0%, #faf9ff 100%);
  border-left: 5px solid #8b5cf6 !important;
}
.bg-card-glow-teal {
  background: linear-gradient(135deg, #ffffff 0%, #f5fffd 100%);
  border-left: 5px solid #0d9488 !important;
}
.bg-card-glow-blue {
  background: linear-gradient(135deg, #ffffff 0%, #f5f9ff 100%);
  border-left: 5px solid #3b82f6 !important;
}
.bg-card-glow-orange {
  background: linear-gradient(135deg, #ffffff 0%, #fffbf5 100%);
  border-left: 5px solid #f97316 !important;
}

.bg-card {
  background: #ffffff;
}

/* Dark Mode Support via HTML attribute */
[data-theme='dark'] .bg-card,
[data-theme='dark'] .stat-card {
  background: #1e1e2d !important;
  border-color: rgba(255, 255, 255, 0.05);
}
[data-theme='dark'] .bg-card-glow-purple {
  background: linear-gradient(135deg, #1e1e2d 0%, #25233c 100%);
}
[data-theme='dark'] .bg-card-glow-teal {
  background: linear-gradient(135deg, #1e1e2d 0%, #1c2e2c 100%);
}
[data-theme='dark'] .bg-card-glow-blue {
  background: linear-gradient(135deg, #1e1e2d 0%, #1c263c 100%);
}
[data-theme='dark'] .bg-card-glow-orange {
  background: linear-gradient(135deg, #1e1e2d 0%, #2e241c 100%);
}
[data-theme='dark'] .text-main {
  color: #f3f4f6 !important;
}
[data-theme='dark'] .text-muted-custom {
  color: #9ca3af !important;
}
[data-theme='dark'] .table-row-hover:hover {
  background-color: rgba(255, 255, 255, 0.02) !important;
}
[data-theme='dark'] .input-group-text,
[data-theme='dark'] .modal-footer {
  background-color: #151521 !important;
}
[data-theme='dark'] .custom-modern-table thead th {
  border-bottom-color: rgba(255, 255, 255, 0.08) !important;
  background-color: rgba(255, 255, 255, 0.02) !important;
}
[data-theme='dark'] .custom-modern-table tbody td {
  border-bottom-color: rgba(255, 255, 255, 0.05) !important;
}

/* Helpers styles */
.text-main {
  color: #1f2937;
}

.text-muted-custom {
  color: #6b7280;
}

.radius-24 { border-radius: 24px; }
.radius-20 { border-radius: 20px; }
.radius-12 { border-radius: 12px; }
.radius-10 { border-radius: 10px; }
.radius-8  { border-radius: 8px; }

.font-xs { font-size: 0.75rem; }
.font-sm { font-size: 0.875rem; }
.font-medium { font-size: 0.95rem; }
.font-bold { font-weight: 600; }
.fw-extrabold { font-weight: 800; }

.tracking-wider { letter-spacing: 0.05em; }

/* Badges styling */
.badge-success-subtle { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
.badge-teal-subtle { background-color: rgba(13, 148, 136, 0.1); color: #0d9488; }
.badge-purple-subtle { background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.badge-blue-subtle { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.badge-orange-subtle { background-color: rgba(249, 115, 22, 0.1); color: #f97316; }

.card-icon-wrap {
  width: 48px;
  height: 48px;
  flex-shrink: 0;
}
.bg-purple-subtle { background-color: rgba(139, 92, 246, 0.12); }
.text-purple { color: #8b5cf6; }
.bg-teal-subtle { background-color: rgba(13, 148, 136, 0.12); }
.text-teal { color: #0d9488; }
.bg-blue-subtle { background-color: rgba(59, 130, 246, 0.12); }
.text-blue { color: #3b82f6; }
.bg-orange-subtle { background-color: rgba(249, 115, 22, 0.12); }
.text-orange { color: #f97316; }

/* Filter dropdown select */
.branch-filter-wrap select {
  border-color: rgba(0, 0, 0, 0.08);
  background-color: rgba(0, 0, 0, 0.02);
  min-width: 240px;
}
.branch-filter-wrap select:focus {
  border-color: #8b5cf6 !important;
  background-color: #ffffff;
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15) !important;
}

/* Interactive Search Bar */
.search-wrap {
  max-width: 320px;
}
.search-input-group {
  border-color: rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  background-color: rgba(0, 0, 0, 0.02);
}
.search-input-group:focus-within {
  border-color: #8b5cf6 !important;
  background-color: #ffffff;
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15) !important;
}

/* Modern Datatable styles */
.custom-modern-table {
  border-collapse: collapse;
  width: 100%;
}
.custom-modern-table thead th {
  border-bottom: 2px solid rgba(0, 0, 0, 0.05);
  font-weight: 700;
  background-color: rgba(0, 0, 0, 0.01);
  padding: 14px 16px;
  vertical-align: middle;
}
.custom-modern-table tbody td {
  padding: 14px 16px;
  vertical-align: middle;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}
.table-row-hover {
  transition: background-color 0.2s ease;
}
.table-row-hover:hover {
  background-color: rgba(139, 92, 246, 0.03);
}

/* Avatar layout */
.avatar-container {
  width: 38px;
  height: 38px;
}
.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.avatar-fallback {
  width: 100%;
  height: 100%;
  font-size: 14px;
}

.text-truncate-custom {
  max-width: 250px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  white-space: normal;
}

/* Interactive Status Badges */
.btn-badge {
  cursor: pointer;
  outline: none;
  font-size: 11px;
  transition: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.btn-badge:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08) !important;
}
.btn-badge:active {
  transform: scale(0.95);
}

.badge-success-btn {
  background-color: #10b981;
  color: #ffffff;
}
.badge-warning-btn {
  background-color: #f59e0b;
  color: #1f2937;
}

.dot-indicator {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  display: inline-block;
}

/* Glassmorphism Action buttons */
.btn-icon-glass {
  background-color: rgba(0, 0, 0, 0.03);
  border: 1px solid rgba(0, 0, 0, 0.01);
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  transition: all 0.2s ease;
}
.btn-icon-glass:hover {
  background-color: rgba(139, 92, 246, 0.1);
  border-color: rgba(139, 92, 246, 0.05);
}
.btn-icon-glass.text-danger:hover {
  background-color: rgba(239, 68, 68, 0.1);
}

/* Form Styles */
.form-select, .form-control {
  border-color: rgba(0, 0, 0, 0.08);
  background-color: rgba(0, 0, 0, 0.01);
  transition: all 0.25s ease;
}
.form-select:focus, .form-control:focus {
  border-color: #8b5cf6 !important;
  background-color: #ffffff;
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15) !important;
}

/* Input group custom focus shadow */
.shadow-none-focus-wrap:focus-within {
  border-color: #8b5cf6 !important;
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15) !important;
}

/* Micro-animations */
.animate-all {
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.btn-hover-scale {
  transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.2s ease;
}
.btn-hover-scale:hover {
  transform: scale(1.025);
}
.btn-hover-scale:active {
  transform: scale(0.975);
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.08); }
  100% { transform: scale(1); }
}
.animate-pulse {
  animation: pulse 2s infinite ease-in-out;
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-4px); }
  100% { transform: translateY(0px); }
}
.animate-float {
  animation: float 3s infinite ease-in-out;
}

/* Responsive Grid and layouts */
@media (max-width: 768px) {
  .search-wrap, .branch-filter-wrap {
    max-width: 100% !important;
    width: 100%;
  }
}hadow-none-focus-wrap:focus-within {
  border-color: #8b5cf6 !important;
  box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.15) !important;
}

/* Micro-animations */
.animate-all {
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.btn-hover-scale {
  transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.2s ease;
}
.btn-hover-scale:hover {
  transform: scale(1.025);
}
.btn-hover-scale:active {
  transform: scale(0.975);
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.08); }
  100% { transform: scale(1); }
}
.animate-pulse {
  animation: pulse 2s infinite ease-in-out;
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-4px); }
  100% { transform: translateY(0px); }
}
.animate-float {
  animation: float 3s infinite ease-in-out;
}

/* Responsive Grid and layouts */
@media (max-width: 768px) {
  .search-wrap, .branch-filter-wrap {
    max-width: 100% !important;
    width: 100%;
  }
}
</style>