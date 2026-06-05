<template>
  <div class="qltkh-wrapper">

    <!-- ========== STATISTICS CARDS ========== -->
    <div class="stats-grid mb-4">
      <div class="stat-card" v-for="s in statCards" :key="s.key">
        <div class="stat-icon" :style="{ background: s.bg }"><i :class="s.icon"></i></div>
        <div class="stat-body">
          <div class="stat-value">{{ stats[s.key] ?? '—' }}</div>
          <div class="stat-label">{{ s.label }}</div>
        </div>
      </div>
    </div>

    <!-- ========== MAIN PANEL ========== -->
    <div class="main-panel">

      <!-- Panel Header -->
      <div class="panel-header">
        <div class="panel-title-wrap">
          <i class="bx bx-user-check panel-icon"></i>
          <h5 class="panel-title">Quản Lý Tài Khoản</h5>
          <span class="live-badge"><span class="live-dot"></span>Live</span>
        </div>
        <div class="panel-actions">
          <div class="filter-group">
            <button v-for="f in filters" :key="f.value"
              class="filter-pill" :class="{ active: activeFilter === f.value }"
              @click="setFilter(f.value)">{{ f.label }}</button>
          </div>
          <button class="btn-icon-round" @click="exportCSV" title="Xuất CSV">
            <i class="bx bx-export"></i>
          </button>
          <button class="btn-icon-round" @click="loadAll" :disabled="isLoading" title="Làm mới">
            <i class="bx bx-sync" :class="{ 'bx-spin': isLoading }"></i>
          </button>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="toolbar">
        <div class="search-wrap">
          <i class="bx bx-search search-ico"></i>
          <input class="search-input" type="text" placeholder="Tìm theo tên, email, số điện thoại..."
            v-model="searchQuery" @input="debouncedSearch" />
          <button class="search-clear" v-if="searchQuery" @click="searchQuery=''; loadAccounts()">
            <i class="bx bx-x"></i>
          </button>
        </div>
        <div class="sort-wrap">
          <select class="sort-select" v-model="sortBy" @change="loadAccounts">
            <option value="created_at">Mới nhất</option>
            <option value="ho_ten">Tên A→Z</option>
            <option value="vai_tro">Vai trò</option>
            <option value="trang_thai">Trạng thái</option>
          </select>
          <button class="sort-dir" @click="toggleSortDir" :title="sortOrder==='desc'?'Giảm dần':'Tăng dần'">
            <i class="bx" :class="sortOrder==='desc'?'bx-sort-down':'bx-sort-up'"></i>
          </button>
        </div>
        <div class="per-page-wrap">
          <select class="sort-select" v-model="perPage" @change="loadAccounts">
            <option :value="10">10 / trang</option>
            <option :value="25">25 / trang</option>
            <option :value="50">50 / trang</option>
          </select>
        </div>
        <span class="result-badge"><i class="bx bx-info-circle me-1"></i>{{ pagination.total ?? 0 }} tài khoản</span>
      </div>

      <!-- Bulk Action Bar -->
      <transition name="slide-down">
        <div class="bulk-bar" v-if="selectedIds.length > 0">
          <span class="bulk-count"><i class="bx bx-checkbox-checked me-1"></i>Đã chọn <strong>{{ selectedIds.length }}</strong> tài khoản</span>
          <div class="bulk-actions">
            <button class="bulk-btn bulk-lock" @click="doBulk('lock')" :disabled="isBulkProcessing">
              <i class="bx bx-lock me-1"></i>Khóa
            </button>
            <button class="bulk-btn bulk-unlock" @click="doBulk('unlock')" :disabled="isBulkProcessing">
              <i class="bx bx-lock-open me-1"></i>Mở khóa
            </button>
            <button class="bulk-btn bulk-delete" @click="doBulk('delete')" :disabled="isBulkProcessing">
              <i class="bx bx-trash me-1"></i>Xóa
            </button>
            <button class="bulk-btn bulk-restore" @click="doBulk('restore')" :disabled="isBulkProcessing">
              <i class="bx bx-refresh me-1"></i>Khôi phục
            </button>
            <button class="bulk-btn bulk-cancel" @click="selectedIds = []">
              <i class="bx bx-x me-1"></i>Bỏ chọn
            </button>
          </div>
          <div class="bulk-spinner" v-if="isBulkProcessing">
            <i class="bx bx-loader-alt bx-spin"></i> Đang xử lý...
          </div>
        </div>
      </transition>

      <!-- TABLE -->
      <div class="table-wrap">
        <table class="acc-table">
          <thead>
            <tr>
              <th style="width:42px" class="text-center">
                <input type="checkbox" class="tbl-check" :checked="allSelected" @change="toggleSelectAll" :indeterminate.prop="someSelected" />
              </th>
              <th style="width:42px" class="text-center">#</th>
              <th style="min-width:200px">Tài khoản</th>
              <th style="min-width:170px">Email</th>
              <th style="width:110px">SĐT</th>
              <th style="width:100px" class="text-center">Vai trò</th>
              <th style="width:110px" class="text-center">Loại TK</th>
              <th style="width:135px">Gói & Hạn</th>
              <th style="width:105px" class="text-center">Trạng thái</th>
              <th style="width:120px" class="text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading" v-for="i in perPage" :key="'sk'+i" class="skeleton-row">
              <td colspan="10"><div class="skeleton-bar"></div></td>
            </tr>
            <tr v-else-if="accounts.length === 0">
              <td colspan="10" class="empty-cell">
                <i class="bx bx-user-x fs-1 d-block mb-2 opacity-40"></i>
                <p class="mb-1 fw-semibold">Không tìm thấy tài khoản nào phù hợp.</p>
                <span class="small text-secondary">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm.</span>
              </td>
            </tr>
            <tr v-else v-for="(acc, idx) in accounts" :key="acc.id"
              class="acc-row" :class="{ 'row-deleted': acc.deleted_at, 'row-selected': selectedIds.includes(acc.id) }">
              <td class="text-center">
                <input type="checkbox" class="tbl-check" :checked="selectedIds.includes(acc.id)"
                  @change="toggleSelect(acc.id)" />
              </td>
              <td class="text-center text-muted small fw-bold">{{ (pagination.from || 1) + idx }}</td>
              <td>
                <div class="acc-identity">
                  <div class="acc-avatar" :style="{ background: avatarColor(acc.ho_ten) }">{{ initials(acc.ho_ten) }}</div>
                  <div class="acc-info">
                    <span class="acc-name">{{ acc.ho_ten }}</span>
                    <span class="acc-date">{{ formatDateShort(acc.created_at) }}</span>
                  </div>
                </div>
              </td>
              <td class="small text-secondary text-break">{{ acc.email }}</td>
              <td class="small">{{ acc.so_dien_thoai || '—' }}</td>
              <td class="text-center">
                <span class="badge-role" :class="roleClass(acc)">{{ roleLabel(acc) }}</span>
              </td>
              <td class="text-center">
                <span class="badge-type" :class="typeClass(acc)">{{ typeLabel(acc) }}</span>
              </td>
              <td>
                <template v-if="acc.doi_tac && acc.doi_tac.trang_thai === 'APPROVED'">
                  <div class="pkg-info">
                    <span class="pkg-name">{{ acc.doi_tac.ten_goi }}</span>
                    <span class="pkg-expire" :class="{ 'expired': isExpired(acc.doi_tac.ngay_ket_thuc) }">
                      <i class="bx bx-calendar me-1"></i>{{ formatDateShort(acc.doi_tac.ngay_ket_thuc) }}
                    </span>
                  </div>
                </template>
                <span v-else class="text-muted small">—</span>
              </td>
              <td class="text-center">
                <span class="badge-status" :class="statusClass(acc)">
                  <span class="status-dot" :class="statusDotClass(acc)"></span>
                  {{ statusLabel(acc) }}
                </span>
              </td>
              <td class="text-center">
                <div class="actions-cell">
                  <button class="act-btn act-view" @click="openDetail(acc)" title="Chi tiết">
                    <i class="bx bx-show"></i>
                  </button>
                  <button class="act-btn act-edit" @click="openEdit(acc)" title="Chỉnh sửa" :disabled="!!acc.deleted_at">
                    <i class="bx bx-edit"></i>
                  </button>
                  <div class="act-more-wrap" v-click-outside="() => closeMenu(acc.id)">
                    <button class="act-btn act-more" @click.stop="toggleMenu(acc.id)" title="Thêm">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu-custom" :class="{ show: openMenuId === acc.id }">
                      <template v-if="!acc.deleted_at">
                        <button class="dd-item" @click="openResetPwd(acc); closeMenu(acc.id)">
                          <i class="bx bx-key me-2"></i>Reset mật khẩu
                        </button>
                        <button class="dd-item" v-if="acc.trang_thai !== 'Khóa'" @click="doLock(acc); closeMenu(acc.id)">
                          <i class="bx bx-lock me-2"></i>Khóa tài khoản
                        </button>
                        <button class="dd-item" v-else @click="doUnlock(acc); closeMenu(acc.id)">
                          <i class="bx bx-lock-open me-2"></i>Mở khóa
                        </button>
                        <button class="dd-item" v-if="acc.is_doi_tac != 1" @click="openUpgrade(acc); closeMenu(acc.id)">
                          <i class="bx bx-crown me-2 text-warning"></i>Cấp đối tác
                        </button>
                        <template v-else>
                          <button class="dd-item" @click="openUpgrade(acc); closeMenu(acc.id)">
                            <i class="bx bx-crown me-2 text-warning"></i>Gia hạn / Nâng cấp
                          </button>
                          <button class="dd-item" @click="doRemovePartner(acc); closeMenu(acc.id)">
                            <i class="bx bx-user-minus me-2 text-danger"></i>Hủy đối tác
                          </button>
                        </template>
                        <div class="dd-divider"></div>
                        <button class="dd-item danger" @click="doDelete(acc); closeMenu(acc.id)">
                          <i class="bx bx-trash me-2"></i>Xóa tài khoản
                        </button>
                      </template>
                      <template v-else>
                        <button class="dd-item success" @click="doRestore(acc); closeMenu(acc.id)">
                          <i class="bx bx-refresh me-2"></i>Khôi phục
                        </button>
                      </template>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-bar" v-if="pagination.last_page > 1">
        <button class="pg-btn" :disabled="pagination.current_page <= 1" @click="goPage(pagination.current_page - 1)">
          <i class="bx bx-chevron-left"></i>
        </button>
        <template v-for="p in pageNumbers" :key="p">
          <span v-if="p === '...'" class="pg-dots">…</span>
          <button v-else class="pg-btn" :class="{ 'pg-active': p === pagination.current_page }" @click="goPage(p)">{{ p }}</button>
        </template>
        <button class="pg-btn" :disabled="pagination.current_page >= pagination.last_page" @click="goPage(pagination.current_page + 1)">
          <i class="bx bx-chevron-right"></i>
        </button>
        <span class="pg-info">Trang {{ pagination.current_page }}/{{ pagination.last_page }}</span>
      </div>
    </div>

    <!-- ===== MODAL: CHI TIẾT ===== -->
    <transition name="modal-fade">
      <div class="modal-overlay" v-if="modal.detail" @click.self="modal.detail = false">
        <div class="modal-box modal-lg">
          <div class="modal-hdr">
            <span><i class="bx bx-user-detail me-2"></i>Chi Tiết Tài Khoản</span>
            <button class="modal-close" @click="modal.detail = false"><i class="bx bx-x"></i></button>
          </div>
          <div class="modal-body-custom" v-if="detailData">
            <div class="detail-hero">
              <div class="detail-avatar" :style="{ background: avatarColor(detailData.account.ho_ten) }">
                {{ initials(detailData.account.ho_ten) }}
              </div>
              <div>
                <h5 class="mb-1 fw-bold">{{ detailData.account.ho_ten }}</h5>
                <span class="text-muted small">{{ detailData.account.email }}</span><br>
                <span class="badge-role mt-1 d-inline-block" :class="roleClass(detailData.account)">{{ roleLabel(detailData.account) }}</span>
                <span class="badge-type ms-2 d-inline-block" :class="typeClass(detailData.account)">{{ typeLabel(detailData.account) }}</span>
              </div>
            </div>
            <div class="detail-grid">
              <div class="detail-item"><label>Số điện thoại</label><span>{{ detailData.account.so_dien_thoai || '—' }}</span></div>
              <div class="detail-item"><label>Trạng thái</label><span>{{ detailData.account.trang_thai }}</span></div>
              <div class="detail-item"><label>Ngày tạo</label><span>{{ formatDate(detailData.account.created_at) }}</span></div>
              <div class="detail-item" v-if="detailData.account.doi_tac"><label>Gói hiện tại</label><span>{{ detailData.account.doi_tac.ten_goi }}</span></div>
              <div class="detail-item" v-if="detailData.account.doi_tac">
                <label>Ngày hết hạn</label>
                <span :class="{ 'text-danger fw-bold': isExpired(detailData.account.doi_tac.ngay_ket_thuc) }">
                  {{ formatDateShort(detailData.account.doi_tac.ngay_ket_thuc) }}
                  <span v-if="isExpired(detailData.account.doi_tac.ngay_ket_thuc)" class="ms-1">(Hết hạn)</span>
                </span>
              </div>
              <div class="detail-item" v-if="detailData.account.doi_tac"><label>Trạng thái gói</label><span>{{ detailData.account.doi_tac.trang_thai }}</span></div>
            </div>
            <div v-if="detailData.logs && detailData.logs.length" class="detail-logs">
              <h6 class="fw-bold mb-3"><i class="bx bx-history me-2 text-warning"></i>Lịch sử hoạt động (20 gần nhất)</h6>
              <div class="log-item" v-for="log in detailData.logs" :key="log.id">
                <span class="log-time">{{ formatDate(log.thoi_gian || log.created_at) }}</span>
                <span class="log-action">{{ log.hanh_dong }}</span>
              </div>
            </div>
            <p v-else class="text-muted small mt-3 mb-0">Chưa có lịch sử hoạt động.</p>
          </div>
          <div class="modal-body-custom text-center py-5" v-else>
            <div class="spinner-border text-warning"></div>
            <p class="mt-3 text-muted small">Đang tải chi tiết...</p>
          </div>
        </div>
      </div>
    </transition>

    <!-- ===== MODAL: CHỈNH SỬA ===== -->
    <transition name="modal-fade">
      <div class="modal-overlay" v-if="modal.edit" @click.self="modal.edit = false">
        <div class="modal-box">
          <div class="modal-hdr">
            <span><i class="bx bx-edit me-2"></i>Chỉnh Sửa Tài Khoản</span>
            <button class="modal-close" @click="modal.edit = false"><i class="bx bx-x"></i></button>
          </div>
          <div class="modal-body-custom">
            <div class="form-group-custom">
              <label>Họ tên <span class="req">*</span></label>
              <input class="form-inp" v-model.trim="editForm.ho_ten" placeholder="Nhập họ tên" maxlength="255" />
            </div>
            <div class="form-group-custom">
              <label>Email <span class="req">*</span></label>
              <input class="form-inp" type="email" v-model.trim="editForm.email" placeholder="Nhập email" />
            </div>
            <div class="form-group-custom">
              <label>Số điện thoại</label>
              <input class="form-inp" v-model.trim="editForm.so_dien_thoai" placeholder="Nhập SĐT" maxlength="20" />
            </div>
            <div class="form-row-2">
              <div class="form-group-custom">
                <label>Quyền hạn <span class="req">*</span></label>
                <select class="form-inp" v-model="editForm.vai_tro" v-if="editForm.vai_tro === 'Admin'" disabled>
                  <option value="Admin">Admin (Quản trị)</option>
                </select>
                <select class="form-inp" v-model="editForm.is_doi_tac" v-else>
                  <option :value="0">Thành viên</option>
                  <option :value="1">Trưởng Nhánh</option>
                </select>
              </div>
              <div class="form-group-custom">
                <label>Trạng thái <span class="req">*</span></label>
                <select class="form-inp" v-model="editForm.trang_thai">
                  <option value="Hoạt động">Hoạt động</option>
                  <option value="Khóa">Khóa</option>
                </select>
              </div>
            </div>
            <div v-if="editForm.vai_tro !== 'Admin' && selectedAcc?.is_doi_tac == 1 && selectedAcc?.doi_tac" class="partner-info-box mt-3">
              <div class="partner-info-card">
                <div class="card-details">
                  <div class="info-row">
                    <span class="info-label">Gói hiện tại:</span>
                    <span class="info-value text-gold">{{ selectedAcc.doi_tac.ten_goi }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Hạn sử dụng:</span>
                    <span class="info-value" :class="{ 'text-danger fw-bold': isExpired(selectedAcc.doi_tac.ngay_ket_thuc) }">
                      {{ formatDateShort(selectedAcc.doi_tac.ngay_ket_thuc) }}
                      <span v-if="isExpired(selectedAcc.doi_tac.ngay_ket_thuc)"> (Hết hạn)</span>
                    </span>
                  </div>
                </div>
                <button type="button" class="btn-renew-partner" @click="openUpgradeFromEdit">
                  <i class="bx bx-crown me-1"></i>Gia hạn / Nâng cấp
                </button>
              </div>
            </div>
            <div class="modal-footer-btns">
              <button class="btn-secondary-custom" @click="modal.edit = false" :disabled="isProcessing">Hủy</button>
              <button class="btn-primary-custom" @click="submitEdit" :disabled="isProcessing">
                <span v-if="!isProcessing"><i class="bx bx-save me-1"></i>Lưu thay đổi</span>
                <span v-else><i class="bx bx-loader-alt bx-spin me-1"></i>Đang lưu...</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- ===== MODAL: RESET MẬT KHẨU ===== -->
    <transition name="modal-fade">
      <div class="modal-overlay" v-if="modal.resetPwd" @click.self="modal.resetPwd = false">
        <div class="modal-box modal-sm">
          <div class="modal-hdr">
            <span><i class="bx bx-key me-2"></i>Reset Mật Khẩu</span>
            <button class="modal-close" @click="modal.resetPwd = false"><i class="bx bx-x"></i></button>
          </div>
          <div class="modal-body-custom">
            <p class="text-muted small mb-3">
              Đặt mật khẩu mới cho <strong>{{ selectedAcc?.ho_ten }}</strong>.
              Tài khoản sẽ bị buộc đăng xuất trên tất cả thiết bị ngay lập tức.
            </p>
            <div class="form-group-custom">
              <label>Mật khẩu mới <span class="req">*</span></label>
              <div class="pwd-wrap">
                <input :type="showPwd ? 'text' : 'password'" class="form-inp" v-model="newPassword"
                  placeholder="Tối thiểu 6 ký tự" maxlength="128" @keyup.enter="submitResetPwd" />
                <button class="pwd-toggle" @click="showPwd = !showPwd" type="button">
                  <i class="bx" :class="showPwd ? 'bx-hide' : 'bx-show'"></i>
                </button>
              </div>
              <div class="pwd-strength" v-if="newPassword">
                <div class="pwd-bar" :class="pwdStrengthClass"></div>
                <span class="pwd-label">{{ pwdStrengthLabel }}</span>
              </div>
            </div>
            <div class="modal-footer-btns">
              <button class="btn-secondary-custom" @click="modal.resetPwd = false" :disabled="isProcessing">Hủy</button>
              <button class="btn-warning-custom" @click="submitResetPwd" :disabled="isProcessing || newPassword.length < 6">
                <span v-if="!isProcessing"><i class="bx bx-key me-1"></i>Xác nhận Reset</span>
                <span v-else><i class="bx bx-loader-alt bx-spin me-1"></i>Đang xử lý...</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- ===== MODAL: CẤP QUYỀN ĐỐI TÁC ===== -->
    <transition name="modal-fade">
      <div class="modal-overlay" v-if="modal.upgrade" @click.self="modal.upgrade = false">
        <div class="modal-box">
          <div class="modal-hdr gold-hdr">
            <span><i class="bx bx-crown me-2"></i>Cấp / Gia Hạn Đối Tác</span>
            <button class="modal-close" @click="modal.upgrade = false"><i class="bx bx-x"></i></button>
          </div>
          <div class="modal-body-custom">
            <div class="upgrade-target-info">
              <div class="mini-avatar" :style="{ background: avatarColor(selectedAcc?.ho_ten) }">
                {{ initials(selectedAcc?.ho_ten) }}
              </div>
              <div>
                <div class="fw-semibold">{{ selectedAcc?.ho_ten }}</div>
                <div class="small text-muted">{{ selectedAcc?.email }}</div>
              </div>
            </div>
            <div class="form-group-custom mt-3">
              <label>Chọn gói <span class="req">*</span></label>
              <select class="form-inp" v-model="upgradeForm.ten_goi" @change="onPackageChange">
                <option value="">-- Chọn gói dịch vụ --</option>
                <option v-for="pkg in availablePackages" :key="pkg.id" :value="pkg.ten_goi">
                  {{ pkg.ten_goi }} — {{ formatCurrency(pkg.gia_ca) }} / {{ Math.round((pkg.thoi_han || 12) / 12) }} năm
                </option>
              </select>
            </div>
            <div class="form-row-2">
              <div class="form-group-custom">
                <label>Số tiền (VNĐ) <span class="req">*</span></label>
                <input class="form-inp" type="number" v-model.number="upgradeForm.so_tien" min="0" />
              </div>
              <div class="form-group-custom">
                <label>Thời hạn (năm) <span class="req">*</span></label>
                <input class="form-inp" type="number" v-model.number="upgradeForm.thoi_han_nam" min="1" max="50" />
              </div>
            </div>
            <div class="upgrade-summary" v-if="upgradeForm.ten_goi">
              <i class="bx bx-info-circle me-2 text-warning"></i>
              Hết hạn dự kiến: <strong>{{ calcExpireDate }}</strong>
            </div>
            <div class="modal-footer-btns">
              <button class="btn-secondary-custom" @click="modal.upgrade = false" :disabled="isProcessing">Hủy</button>
              <button class="btn-gold-custom" @click="submitUpgrade" :disabled="isProcessing || !upgradeForm.ten_goi">
                <span v-if="!isProcessing"><i class="bx bx-crown me-1"></i>Xác nhận Cấp</span>
                <span v-else><i class="bx bx-loader-alt bx-spin me-1"></i>Đang xử lý...</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>

  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

const BASE = 'http://127.0.0.1:8000/api';

export default {
  name: 'QuanLyTaiKhoan',

  directives: {
    'click-outside': {
      mounted(el, binding) {
        el.__vClickOutside__ = (e) => { if (!el.contains(e.target)) binding.value(e); };
        document.addEventListener('click', el.__vClickOutside__);
      },
      unmounted(el) {
        document.removeEventListener('click', el.__vClickOutside__);
        delete el.__vClickOutside__;
      }
    }
  },

  data() {
    return {
      accounts: [],
      stats: {},
      detailData: null,
      availablePackages: [],

      isLoading: false,
      isProcessing: false,
      isBulkProcessing: false,

      activeFilter: 'ALL',
      searchQuery: '',
      sortBy: 'created_at',
      sortOrder: 'desc',
      perPage: 10,
      searchTimer: null,
      _abortController: null, // for cancellable requests

      pagination: { current_page: 1, last_page: 1, total: 0, from: 1 },

      selectedIds: [],
      modal: { detail: false, edit: false, resetPwd: false, upgrade: false },
      selectedAcc: null,
      openMenuId: null,

      editForm: { id: null, ho_ten: '', email: '', so_dien_thoai: '', vai_tro: 'Thành viên', is_doi_tac: 0, trang_thai: 'Hoạt động' },
      newPassword: '',
      showPwd: false,
      upgradeForm: { id_nguoi_dung: null, ten_goi: '', so_tien: 0, thoi_han_nam: 1 },

      filters: [
        { value: 'ALL', label: 'Tất cả' },
        { value: 'MEMBER', label: 'Thành viên' },
        { value: 'PARTNER', label: 'Đối tác' },
        { value: 'ADMIN', label: 'Admin' },
        { value: 'LOCKED', label: 'Bị khóa' },
        { value: 'PENDING', label: 'Chờ duyệt' },
        { value: 'DELETED', label: 'Đã xóa' },
      ],

      statCards: [
        { key: 'total_accounts',         label: 'Tổng tài khoản',     icon: 'bx bx-group',          bg: 'linear-gradient(135deg,#4f46e5,#818cf8)' },
        { key: 'total_members',          label: 'Thành viên',          icon: 'bx bx-user',           bg: 'linear-gradient(135deg,#0ea5e9,#38bdf8)' },
        { key: 'total_partners',         label: 'Đối tác',             icon: 'bx bx-crown',          bg: 'linear-gradient(135deg,#d97706,#f59e0b)' },
        { key: 'total_admins',           label: 'Admin',               icon: 'bx bx-shield-quarter', bg: 'linear-gradient(135deg,#dc2626,#f87171)' },
        { key: 'total_locked',           label: 'Bị khóa',             icon: 'bx bx-lock',           bg: 'linear-gradient(135deg,#475569,#94a3b8)' },
        { key: 'total_pending_partners', label: 'Chờ duyệt đối tác',  icon: 'bx bx-time-five',      bg: 'linear-gradient(135deg,#ea580c,#fb923c)' },
      ],
    };
  },

  computed: {
    pageNumbers() {
      const total = this.pagination.last_page, cur = this.pagination.current_page;
      if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
      const pages = new Set([1, total, cur, cur-1, cur+1].filter(p => p >= 1 && p <= total));
      const sorted = [...pages].sort((a, b) => a - b);
      const result = []; let prev = 0;
      for (const p of sorted) { if (p - prev > 1) result.push('...'); result.push(p); prev = p; }
      return result;
    },
    allSelected() {
      return this.accounts.length > 0 && this.accounts.every(a => this.selectedIds.includes(a.id));
    },
    someSelected() {
      return this.selectedIds.length > 0 && !this.allSelected;
    },
    pwdStrengthClass() {
      const len = this.newPassword.length;
      if (len < 6) return 'str-weak';
      if (len < 10) return 'str-medium';
      return 'str-strong';
    },
    pwdStrengthLabel() {
      const len = this.newPassword.length;
      if (len < 6) return 'Yếu';
      if (len < 10) return 'Trung bình';
      return 'Mạnh';
    },
    calcExpireDate() {
      if (!this.upgradeForm.thoi_han_nam) return '—';
      const d = new Date();
      d.setFullYear(d.getFullYear() + Number(this.upgradeForm.thoi_han_nam));
      return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },
  },

  mounted() {
    this.loadAll();
    this.loadPackages();
  },

  beforeUnmount() {
    // Cleanup: cancel pending requests & clear timers
    if (this._abortController) this._abortController.abort();
    clearTimeout(this.searchTimer);
  },

  methods: {
    hdr() {
      return { headers: { Authorization: 'Bearer ' + localStorage.getItem('access_token') } };
    },

    loadAll() {
      this.loadStats();
      this.loadAccounts();
    },

    loadStats() {
      axios.get(`${BASE}/admin/quan-ly-tai-khoan/statistics`, this.hdr())
        .then(r => { if (r.data.status) this.stats = r.data.data; })
        .catch(() => {});
    },

    loadAccounts(page = 1) {
      // Cancel previous request if still pending (prevent duplicate/stale data)
      if (this._abortController) this._abortController.abort();
      this._abortController = new AbortController();

      this.isLoading = true;
      this.selectedIds = []; // reset selection on load

      const params = {
        page,
        per_page: this.perPage,
        sort_by: this.sortBy,
        sort_order: this.sortOrder,
      };
      if (this.searchQuery.trim()) params.search = this.searchQuery.trim();
      if (this.activeFilter !== 'ALL') params.filter = this.activeFilter;

      axios.get(`${BASE}/admin/quan-ly-tai-khoan/accounts`, {
        ...this.hdr(),
        params,
        signal: this._abortController.signal,
      })
        .then(r => {
          if (r.data.status) {
            const d = r.data.data;
            this.accounts = d.data;
            this.pagination = { current_page: d.current_page, last_page: d.last_page, total: d.total, from: d.from };
          }
        })
        .catch(e => {
          if (axios.isCancel && axios.isCancel(e)) return; // ignore cancelled
          if (e.name === 'CanceledError' || e.name === 'AbortError') return;
          toastr.error('Lỗi tải danh sách tài khoản!');
        })
        .finally(() => { this.isLoading = false; });
    },

    loadPackages() {
      axios.get(`${BASE}/goi-dich-vu/get-data`, this.hdr())
        .then(r => { if (r.data.status) this.availablePackages = r.data.data || []; })
        .catch(() => {});
    },

    setFilter(val) { this.activeFilter = val; this.loadAccounts(); },

    debouncedSearch() {
      clearTimeout(this.searchTimer);
      this.searchTimer = setTimeout(() => this.loadAccounts(), 420);
    },

    toggleSortDir() {
      this.sortOrder = this.sortOrder === 'desc' ? 'asc' : 'desc';
      this.loadAccounts();
    },

    goPage(page) { this.loadAccounts(page); },

    // Checkbox selection
    toggleSelect(id) {
      const idx = this.selectedIds.indexOf(id);
      if (idx === -1) this.selectedIds.push(id);
      else this.selectedIds.splice(idx, 1);
    },
    toggleSelectAll() {
      if (this.allSelected) this.selectedIds = [];
      else this.selectedIds = this.accounts.map(a => a.id);
    },

    // Dropdown menu
    toggleMenu(id) { this.openMenuId = this.openMenuId === id ? null : id; },
    closeMenu() { this.openMenuId = null; },

    // Open modals
    openDetail(acc) {
      this.selectedAcc = acc;
      this.modal.detail = true;
      this.detailData = null;
      axios.get(`${BASE}/admin/quan-ly-tai-khoan/account-detail/${acc.id}`, this.hdr())
        .then(r => { if (r.data.status) this.detailData = r.data.data; })
        .catch(() => toastr.error('Lỗi tải chi tiết tài khoản!'));
    },

    openEdit(acc) {
      this.selectedAcc = acc;
      this.editForm = { 
        id: acc.id, 
        ho_ten: acc.ho_ten, 
        email: acc.email, 
        so_dien_thoai: acc.so_dien_thoai || '', 
        vai_tro: acc.vai_tro, 
        is_doi_tac: acc.is_doi_tac ?? 0, 
        trang_thai: acc.trang_thai 
      };
      this.modal.edit = true;
    },

    openResetPwd(acc) {
      this.selectedAcc = acc; this.newPassword = ''; this.showPwd = false; this.modal.resetPwd = true;
    },

    openUpgrade(acc) {
      this.selectedAcc = acc;
      this.upgradeForm = { id_nguoi_dung: acc.id, ten_goi: '', so_tien: 0, thoi_han_nam: 1 };
      this.modal.upgrade = true;
    },

    onPackageChange() {
      const pkg = this.availablePackages.find(p => p.ten_goi === this.upgradeForm.ten_goi);
      if (pkg) {
        this.upgradeForm.so_tien = pkg.gia_ca || 0;
        this.upgradeForm.thoi_han_nam = Math.max(1, Math.round((pkg.thoi_han || 12) / 12));
      }
    },
    openUpgradeFromEdit() {
      this.modal.edit = false;
      this.openUpgrade(this.selectedAcc);
    },

    // API actions
    submitEdit() {
      if (!this.editForm.ho_ten?.trim() || !this.editForm.email?.trim()) {
        toastr.warning('Vui lòng điền đầy đủ thông tin bắt buộc!'); return;
      }
      this.isProcessing = true;
      axios.post(`${BASE}/admin/quan-ly-tai-khoan/update-account`, this.editForm, this.hdr())
        .then(r => {
          if (r.data.status) { toastr.success(r.data.message); this.modal.edit = false; this.loadAll(); }
          else toastr.error(r.data.message);
        })
        .catch(e => toastr.error(e.response?.data?.message || 'Lỗi cập nhật!'))
        .finally(() => { this.isProcessing = false; });
    },

    doLock(acc) {
      if (!confirm(`Khóa tài khoản "${acc.ho_ten}"?\nNgười dùng sẽ bị đăng xuất ngay lập tức trên tất cả thiết bị.`)) return;
      this.isProcessing = true;
      axios.post(`${BASE}/admin/quan-ly-tai-khoan/lock-account`, { id: acc.id }, this.hdr())
        .then(r => { toastr[r.data.status ? 'success' : 'error'](r.data.message); if (r.data.status) this.loadAll(); })
        .catch(e => toastr.error(e.response?.data?.message || 'Lỗi!'))
        .finally(() => { this.isProcessing = false; });
    },

    doUnlock(acc) {
      if (!confirm(`Mở khóa tài khoản "${acc.ho_ten}"?`)) return;
      this.isProcessing = true;
      axios.post(`${BASE}/admin/quan-ly-tai-khoan/unlock-account`, { id: acc.id }, this.hdr())
        .then(r => { toastr[r.data.status ? 'success' : 'error'](r.data.message); if (r.data.status) this.loadAll(); })
        .catch(e => toastr.error(e.response?.data?.message || 'Lỗi!'))
        .finally(() => { this.isProcessing = false; });
    },

    submitResetPwd() {
      if (this.newPassword.length < 6) { toastr.warning('Mật khẩu phải có ít nhất 6 ký tự!'); return; }
      this.isProcessing = true;
      axios.post(`${BASE}/admin/quan-ly-tai-khoan/reset-password`, { id: this.selectedAcc.id, mat_khau_moi: this.newPassword }, this.hdr())
        .then(r => {
          if (r.data.status) { toastr.success(r.data.message); this.modal.resetPwd = false; this.newPassword = ''; }
          else toastr.error(r.data.message);
        })
        .catch(e => toastr.error(e.response?.data?.message || 'Lỗi!'))
        .finally(() => { this.isProcessing = false; });
    },

    submitUpgrade() {
      if (!this.upgradeForm.ten_goi) { toastr.warning('Vui lòng chọn gói!'); return; }
      if (!this.upgradeForm.thoi_han_nam || this.upgradeForm.thoi_han_nam < 1) { toastr.warning('Thời hạn phải ít nhất 1 năm!'); return; }
      this.isProcessing = true;
      axios.post(`${BASE}/admin/quan-ly-tai-khoan/upgrade-partner`, this.upgradeForm, this.hdr())
        .then(r => {
          if (r.data.status) { toastr.success(r.data.message); this.modal.upgrade = false; this.loadAll(); }
          else toastr.error(r.data.message);
        })
        .catch(e => toastr.error(e.response?.data?.message || 'Lỗi!'))
        .finally(() => { this.isProcessing = false; });
    },

    doRemovePartner(acc) {
      if (!confirm(`Hủy quyền đối tác của "${acc.ho_ten}"?\nThao tác này sẽ đồng bộ is_doi_tac = 0 ngay lập tức.`)) return;
      this.isProcessing = true;
      axios.post(`${BASE}/admin/quan-ly-tai-khoan/remove-partner`, { id: acc.id }, this.hdr())
        .then(r => { toastr[r.data.status ? 'success' : 'error'](r.data.message); if (r.data.status) this.loadAll(); })
        .catch(e => toastr.error(e.response?.data?.message || 'Lỗi!'))
        .finally(() => { this.isProcessing = false; });
    },

    doDelete(acc) {
      if (!confirm(`Xóa tài khoản "${acc.ho_ten}"?\n(Soft delete — có thể khôi phục)`)) return;
      this.isProcessing = true;
      axios.post(`${BASE}/admin/quan-ly-tai-khoan/delete-account`, { id: acc.id }, this.hdr())
        .then(r => { toastr[r.data.status ? 'success' : 'error'](r.data.message); if (r.data.status) this.loadAll(); })
        .catch(e => toastr.error(e.response?.data?.message || 'Lỗi!'))
        .finally(() => { this.isProcessing = false; });
    },

    doRestore(acc) {
      if (!confirm(`Khôi phục tài khoản "${acc.ho_ten}"?`)) return;
      this.isProcessing = true;
      axios.post(`${BASE}/admin/quan-ly-tai-khoan/restore-account`, { id: acc.id }, this.hdr())
        .then(r => { toastr[r.data.status ? 'success' : 'error'](r.data.message); if (r.data.status) this.loadAll(); })
        .catch(e => toastr.error(e.response?.data?.message || 'Lỗi!'))
        .finally(() => { this.isProcessing = false; });
    },

    // Bulk actions
    doBulk(action) {
      if (this.selectedIds.length === 0) return;
      const labels = { lock: 'khóa', unlock: 'mở khóa', delete: 'xóa', restore: 'khôi phục' };
      if (!confirm(`Bạn chắc chắn muốn ${labels[action]} ${this.selectedIds.length} tài khoản đã chọn?`)) return;
      this.isBulkProcessing = true;
      axios.post(`${BASE}/admin/quan-ly-tai-khoan/bulk-action`, { action, ids: this.selectedIds }, this.hdr())
        .then(r => {
          toastr[r.data.status ? 'success' : 'error'](r.data.message);
          if (r.data.status) { this.selectedIds = []; this.loadAll(); }
        })
        .catch(e => toastr.error(e.response?.data?.message || 'Lỗi bulk action!'))
        .finally(() => { this.isBulkProcessing = false; });
    },

    // Export CSV
    exportCSV() {
      const headers = ['ID', 'Họ tên', 'Email', 'SĐT', 'Vai trò', 'Loại TK', 'Gói', 'Hạn sử dụng', 'Trạng thái'];
      const rows = this.accounts.map(a => [
        a.id, a.ho_ten, a.email, a.so_dien_thoai || '',
        this.roleLabel(a), this.typeLabel(a),
        a.doi_tac?.ten_goi || '',
        a.doi_tac?.ngay_ket_thuc || '',
        this.statusLabel(a),
      ]);
      const csv = [headers, ...rows].map(r => r.map(v => `"${String(v).replace(/"/g, '""')}"`).join(',')).join('\n');
      const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url; a.download = `tai-khoan-${new Date().toISOString().slice(0,10)}.csv`;
      document.body.appendChild(a); a.click();
      document.body.removeChild(a); URL.revokeObjectURL(url);
      toastr.success('Đã xuất file CSV thành công!');
    },

    // Display helpers
    initials(name) {
      if (!name) return '?';
      return name.trim().split(/\s+/).map(w => w[0]).join('').toUpperCase().slice(0, 2);
    },
    avatarColor(name) {
      const colors = [
        'linear-gradient(135deg,#4f46e5,#818cf8)', 'linear-gradient(135deg,#0ea5e9,#38bdf8)',
        'linear-gradient(135deg,#d97706,#f59e0b)', 'linear-gradient(135deg,#dc2626,#f87171)',
        'linear-gradient(135deg,#059669,#34d399)', 'linear-gradient(135deg,#7c3aed,#a78bfa)',
        'linear-gradient(135deg,#db2777,#f472b6)',
      ];
      if (!name) return colors[0];
      return colors[[...name].reduce((s, c) => s + c.charCodeAt(0), 0) % colors.length];
    },
    isSubAdmin(acc) {
      const chucVuName = acc?.chuc_vu?.ten_chuc_vu?.toLowerCase() || '';
      return chucVuName.includes('quản trị') || chucVuName.includes('admin');
    },
    isAdminLike(acc) {
      const roleName = acc?.vai_tro?.toLowerCase() || '';
      return roleName === 'admin' || this.isSubAdmin(acc);
    },
    roleLabel(acc) {
      if (this.isAdminLike(acc)) return 'Quản trị viên';
      return acc?.vai_tro || '—';
    },
    roleClass(acc) {
      return this.isAdminLike(acc) ? 'role-admin' : 'role-member';
    },
    typeClass(acc) {
      if (acc.deleted_at) return 'type-deleted';
      if (acc.is_doi_tac == 1) return acc.doi_tac?.trang_thai === 'PENDING' ? 'type-pending' : 'type-partner';
      return 'type-member';
    },
    typeLabel(acc) {
      if (acc.deleted_at) return 'Đã xóa';
      if (acc.is_doi_tac == 1) return acc.doi_tac?.trang_thai === 'PENDING' ? 'Chờ duyệt' : 'Đối tác';
      return 'Thành viên';
    },
    statusClass(acc) {
      if (acc.deleted_at) return 'status-deleted';
      if (acc.trang_thai === 'Khóa') return 'status-locked';
      if (acc.is_doi_tac == 1) return (!acc.doi_tac || isExpiredFn(acc.doi_tac.ngay_ket_thuc)) ? 'status-expired' : 'status-active-partner';
      return 'status-active';
    },
    statusDotClass(acc) {
      if (acc.deleted_at || acc.trang_thai === 'Khóa') return 'dot-red';
      if (acc.is_doi_tac == 1) return (!acc.doi_tac || isExpiredFn(acc.doi_tac.ngay_ket_thuc)) ? 'dot-gray' : 'dot-gold';
      return 'dot-green';
    },
    statusLabel(acc) {
      if (acc.deleted_at) return 'Đã xóa';
      if (acc.trang_thai === 'Khóa') return 'Bị khóa';
      if (acc.is_doi_tac == 1) {
        if (!acc.doi_tac) return 'Đối tác';
        if (acc.doi_tac.trang_thai === 'PENDING') return 'Chờ duyệt';
        if (isExpiredFn(acc.doi_tac.ngay_ket_thuc)) return 'Hết hạn';
        return 'Đối tác';
      }
      return 'Hoạt động';
    },
    isExpired(date) { return isExpiredFn(date); },
    formatDate(d) {
      if (!d) return '—';
      return new Date(d).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    },
    formatDateShort(d) {
      if (!d) return '—';
      return new Date(d).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },
    formatCurrency(val) {
      if (!val) return '0 VNĐ';
      return Number(val).toLocaleString('vi-VN') + ' VNĐ';
    },
  },
};

function isExpiredFn(date) { return !!date && new Date(date) < new Date(); }
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap');

/* ── ROOT ─── */
.qltkh-wrapper { font-family: 'Inter', sans-serif; color: var(--text-main, #1e293b); }

/* ── STATS GRID ─── */
.stats-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; }
@media (max-width: 1400px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px)  { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px)  { .stats-grid { grid-template-columns: 1fr; } }

.stat-card {
  background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.05));
  border-radius: 18px; padding: 18px 16px; display: flex; align-items: center; gap: 14px;
  transition: transform 0.2s, box-shadow 0.2s;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(0,0,0,0.06); }
.stat-icon { width: 46px; height: 46px; border-radius: 14px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.stat-icon i { font-size: 22px; color: #fff; }
.stat-value { font-size: 24px; font-weight: 800; font-family: 'Outfit', sans-serif; line-height: 1; }
.stat-label { font-size: 11.5px; color: var(--text-sub, #64748b); margin-top: 4px; font-weight: 500; }

/* ── MAIN PANEL ─── */
.main-panel { background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.05)); border-radius: 22px; overflow: hidden; box-shadow: 0 8px 28px rgba(0,0,0,0.03); }

.panel-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding: 22px 24px 18px; border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.05)); }
.panel-title-wrap { display: flex; align-items: center; gap: 10px; }
.panel-icon { font-size: 22px; color: #d97706; }
.panel-title { margin: 0; font-size: 16px; font-weight: 800; font-family: 'Outfit', sans-serif; background: linear-gradient(135deg,#d97706,#f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.live-badge { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 700; background: rgba(16,185,129,0.08); color: #059669; border: 1px solid rgba(16,185,129,0.2); border-radius: 20px; padding: 3px 10px; }
.live-dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; box-shadow: 0 0 6px rgba(16,185,129,0.7); animation: livePulse 2s infinite; }
@keyframes livePulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }

.panel-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.filter-group { background: var(--input-bg, #f1f5f9); padding: 4px; border-radius: 30px; display: flex; flex-wrap: wrap; gap: 2px; }
.filter-pill { background: transparent; border: none; cursor: pointer; font-size: 12px; font-weight: 600; color: var(--text-sub, #64748b); padding: 7px 14px; border-radius: 30px; transition: all 0.2s; }
.filter-pill:hover { color: var(--text-main, #1e293b); }
.filter-pill.active { background: var(--card-bg, #fff); color: #d97706; box-shadow: 0 3px 8px rgba(0,0,0,0.06); }

.btn-icon-round { width: 36px; height: 36px; border-radius: 50%; background: var(--input-bg, #f1f5f9); border: 1px solid var(--border-color, rgba(0,0,0,0.05)); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; font-size: 18px; color: var(--text-sub, #64748b); }
.btn-icon-round:hover { background: rgba(217,119,6,0.08); color: #d97706; border-color: #f59e0b; }
.btn-icon-round:disabled { opacity: 0.5; cursor: not-allowed; }

/* Toolbar */
.toolbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 24px; background: var(--app-bg, #f8fafc); border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.04)); }
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-ico { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-sub, #64748b); font-size: 16px; pointer-events: none; }
.search-input { width: 100%; padding: 9px 36px 9px 36px; background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.07)); border-radius: 12px; font-size: 13px; color: var(--text-main, #1e293b); outline: none; transition: all 0.2s; }
.search-input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
.search-clear { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-sub, #94a3b8); font-size: 18px; line-height: 1; padding: 0; }
.sort-wrap { display: flex; align-items: center; gap: 6px; }
.per-page-wrap { display: flex; align-items: center; }
.sort-select { padding: 8px 12px; border-radius: 10px; font-size: 12.5px; font-weight: 500; background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.07)); color: var(--text-main, #1e293b); outline: none; cursor: pointer; }
.sort-dir { width: 34px; height: 34px; border-radius: 10px; background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.07)); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 18px; color: var(--text-sub, #64748b); transition: all 0.2s; }
.sort-dir:hover { color: #d97706; border-color: #f59e0b; }
.result-badge { background: rgba(217,119,6,0.07); color: #d97706; border: 1px solid rgba(217,119,6,0.15); border-radius: 20px; padding: 5px 14px; font-size: 12px; font-weight: 600; white-space: nowrap; }

/* Bulk bar */
.bulk-bar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; padding: 12px 24px; background: rgba(79,70,229,0.04); border-bottom: 1px solid rgba(79,70,229,0.1); }
.bulk-count { font-size: 13px; font-weight: 600; color: #4f46e5; white-space: nowrap; }
.bulk-actions { display: flex; gap: 8px; flex-wrap: wrap; }
.bulk-btn { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; display: flex; align-items: center; transition: all 0.2s; }
.bulk-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.bulk-lock   { background: rgba(100,116,139,0.1); color: #64748b; }
.bulk-lock:hover:not(:disabled) { background: #64748b; color: #fff; }
.bulk-unlock { background: rgba(16,185,129,0.1); color: #059669; }
.bulk-unlock:hover:not(:disabled) { background: #059669; color: #fff; }
.bulk-delete { background: rgba(239,68,68,0.1); color: #ef4444; }
.bulk-delete:hover:not(:disabled) { background: #ef4444; color: #fff; }
.bulk-restore { background: rgba(79,70,229,0.1); color: #4f46e5; }
.bulk-restore:hover:not(:disabled) { background: #4f46e5; color: #fff; }
.bulk-cancel { background: transparent; color: var(--text-sub, #64748b); }
.bulk-spinner { font-size: 12px; color: #d97706; display: flex; align-items: center; gap: 6px; }

/* Slide down transition */
.slide-down-enter-active { transition: all 0.25s ease; }
.slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }

/* TABLE */
.table-wrap { overflow-x: auto; max-height: 560px; overflow-y: auto; }
.table-wrap::-webkit-scrollbar { width: 4px; height: 4px; }
.table-wrap::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.08); border-radius: 99px; }

.acc-table { width: 100%; border-collapse: collapse; min-width: 960px; }
.acc-table thead { position: sticky; top: 0; z-index: 10; background: var(--app-bg, #f8fafc); }
.acc-table thead th { padding: 12px 14px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; color: var(--text-sub, #64748b); border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.06)); white-space: nowrap; }
.acc-table tbody td { padding: 13px 14px; border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.04)); font-size: 13px; color: var(--text-main, #1e293b); vertical-align: middle; }
.acc-row { transition: background 0.15s; }
.acc-row:hover { background: rgba(217,119,6,0.015); }
.acc-row:last-child td { border-bottom: none; }
.row-deleted { opacity: 0.55; }
.row-selected { background: rgba(79,70,229,0.04) !important; }

.tbl-check { width: 16px; height: 16px; cursor: pointer; accent-color: #d97706; }
.text-break { word-break: break-all; }

/* Identity */
.acc-identity { display: flex; align-items: center; gap: 10px; }
.acc-avatar { width: 36px; height: 36px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; }
.acc-name { display: block; font-weight: 600; font-size: 13px; }
.acc-date { display: block; font-size: 11px; color: var(--text-sub, #94a3b8); margin-top: 1px; }

/* Badges */
.badge-role, .badge-type, .badge-status { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
.role-admin   { background: rgba(220,38,38,0.08); color: #dc2626; border: 1px solid rgba(220,38,38,0.15); }
.role-member  { background: rgba(14,165,233,0.08); color: #0ea5e9; border: 1px solid rgba(14,165,233,0.15); }
.type-partner { background: rgba(217,119,6,0.1); color: #d97706; border: 1px solid rgba(217,119,6,0.2); }
.type-member  { background: rgba(79,70,229,0.08); color: #4f46e5; border: 1px solid rgba(79,70,229,0.15); }
.type-pending { background: rgba(234,88,12,0.08); color: #ea580c; border: 1px solid rgba(234,88,12,0.2); }
.type-deleted { background: rgba(100,116,139,0.07); color: #64748b; border: 1px solid rgba(100,116,139,0.15); }

.status-active         { background: rgba(5,150,105,0.08); color: #059669; border: 1px solid rgba(5,150,105,0.15); }
.status-active-partner { background: rgba(217,119,6,0.08); color: #d97706; border: 1px solid rgba(217,119,6,0.2); }
.status-locked         { background: rgba(100,116,139,0.07); color: #64748b; border: 1px solid rgba(100,116,139,0.15); }
.status-expired        { background: rgba(239,68,68,0.07); color: #ef4444; border: 1px solid rgba(239,68,68,0.15); }
.status-deleted        { background: rgba(100,116,139,0.07); color: #94a3b8; border: 1px solid rgba(100,116,139,0.1); }

.status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.dot-green { background: #10b981; box-shadow: 0 0 6px rgba(16,185,129,0.5); }
.dot-gold  { background: #f59e0b; box-shadow: 0 0 6px rgba(245,158,11,0.5); }
.dot-red   { background: #ef4444; }
.dot-gray  { background: #94a3b8; }

/* Package */
.pkg-info { display: flex; flex-direction: column; gap: 2px; }
.pkg-name { font-size: 12px; font-weight: 600; color: #d97706; }
.pkg-expire { font-size: 11px; color: var(--text-sub, #64748b); }
.pkg-expire.expired { color: #ef4444; font-weight: 600; }

/* Actions */
.actions-cell { display: flex; align-items: center; justify-content: center; gap: 4px; }
.act-btn { width: 30px; height: 30px; border-radius: 8px; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 15px; transition: all 0.2s; }
.act-view { background: rgba(79,70,229,0.07); color: #4f46e5; }
.act-view:hover { background: #4f46e5; color: #fff; }
.act-edit { background: rgba(14,165,233,0.07); color: #0ea5e9; }
.act-edit:hover:not(:disabled) { background: #0ea5e9; color: #fff; }
.act-edit:disabled { opacity: 0.3; cursor: not-allowed; }
.act-more { background: var(--input-bg, #f1f5f9); color: var(--text-sub, #64748b); }
.act-more:hover { background: rgba(0,0,0,0.07); }

/* Dropdown */
.act-more-wrap { position: relative; }
.dropdown-menu-custom { position: absolute; right: 0; top: calc(100% + 6px); z-index: 1050; background: var(--card-bg, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.08)); border-radius: 14px; padding: 6px; min-width: 185px; box-shadow: 0 12px 36px rgba(0,0,0,0.1); display: none; }
.dropdown-menu-custom.show { display: block; }
.dd-item { width: 100%; padding: 8px 12px; border: none; background: none; text-align: left; font-size: 12.5px; font-weight: 500; color: var(--text-main, #334155); border-radius: 9px; cursor: pointer; transition: background 0.15s; display: flex; align-items: center; }
.dd-item:hover { background: var(--input-bg, #f1f5f9); }
.dd-item.danger { color: #ef4444; }
.dd-item.success { color: #059669; }
.dd-divider { border-top: 1px solid var(--border-color, rgba(0,0,0,0.06)); margin: 4px 0; }

/* Skeleton */
.skeleton-row td { padding: 14px; }
.skeleton-bar { height: 20px; border-radius: 8px; background: linear-gradient(90deg, var(--input-bg,#f1f5f9) 25%, rgba(0,0,0,0.04) 50%, var(--input-bg,#f1f5f9) 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; }
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

/* Empty */
.empty-cell { text-align: center; padding: 56px 20px; color: var(--text-sub, #94a3b8); font-size: 13.5px; }

/* Pagination */
.pagination-bar { display: flex; align-items: center; justify-content: center; gap: 5px; padding: 16px 24px; border-top: 1px solid var(--border-color, rgba(0,0,0,0.04)); }
.pg-btn { width: 34px; height: 34px; border-radius: 10px; border: 1px solid var(--border-color, rgba(0,0,0,0.08)); background: var(--card-bg, #fff); color: var(--text-main, #334155); font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
.pg-btn:hover:not(:disabled) { background: rgba(217,119,6,0.08); border-color: #f59e0b; color: #d97706; }
.pg-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.pg-btn.pg-active { background: #d97706; border-color: #d97706; color: #fff; }
.pg-dots { padding: 0 4px; color: var(--text-sub, #94a3b8); font-size: 14px; }
.pg-info { font-size: 12px; color: var(--text-sub, #94a3b8); margin-left: 8px; white-space: nowrap; }

/* ── MODALS ─── */
.modal-fade-enter-active { transition: all 0.22s ease; }
.modal-fade-leave-active { transition: all 0.18s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
.modal-fade-enter-from .modal-box { transform: translateY(16px) scale(0.97); }
.modal-fade-leave-to .modal-box { transform: translateY(8px) scale(0.98); }

.modal-overlay { position: fixed; inset: 0; z-index: 2000; background: rgba(0,0,0,0.45); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; padding: 20px; }
.modal-box { background: var(--card-bg, #fff); border-radius: 22px; width: 100%; max-width: 520px; box-shadow: 0 24px 64px rgba(0,0,0,0.18); max-height: 90vh; overflow-y: auto; transition: transform 0.22s ease; }
.modal-box.modal-sm { max-width: 400px; }
.modal-box.modal-lg { max-width: 680px; }
.modal-box::-webkit-scrollbar { width: 4px; }
.modal-box::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.08); border-radius: 99px; }

.modal-hdr { display: flex; align-items: center; justify-content: space-between; padding: 20px 22px 16px; border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.06)); font-size: 15px; font-weight: 700; font-family: 'Outfit', sans-serif; color: var(--text-main, #1e293b); }
.gold-hdr { color: #d97706; }
.modal-close { background: none; border: none; cursor: pointer; font-size: 22px; color: var(--text-sub, #94a3b8); transition: all 0.2s; display: flex; align-items: center; }
.modal-close:hover { color: var(--text-main, #1e293b); transform: rotate(90deg); }
.modal-body-custom { padding: 20px 22px 22px; }

/* Detail */
.detail-hero { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.06)); }
.detail-avatar { width: 60px; height: 60px; border-radius: 18px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; color: #fff; }
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px; }
.detail-item { background: var(--app-bg, #f8fafc); border-radius: 12px; padding: 12px 14px; }
.detail-item label { display: block; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-sub, #94a3b8); margin-bottom: 4px; }
.detail-item span { font-size: 13.5px; font-weight: 600; color: var(--text-main, #1e293b); }

.detail-logs h6 { font-size: 13px; }
.log-item { display: flex; align-items: flex-start; gap: 12px; padding: 9px 0; border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.04)); }
.log-item:last-child { border-bottom: none; }
.log-time { font-size: 10.5px; color: var(--text-sub, #94a3b8); white-space: nowrap; flex-shrink: 0; padding-top: 2px; }
.log-action { font-size: 12.5px; color: var(--text-main, #334155); line-height: 1.5; }

/* Forms */
.form-group-custom { margin-bottom: 14px; }
.form-group-custom label { display: block; font-size: 12px; font-weight: 600; color: var(--text-sub, #475569); margin-bottom: 6px; letter-spacing: 0.3px; }
.req { color: #ef4444; }
.form-inp { width: 100%; padding: 10px 14px; border-radius: 12px; border: 1px solid var(--border-color, rgba(0,0,0,0.08)); background: var(--input-bg, #f8fafc); color: var(--text-main, #1e293b); font-size: 13.5px; outline: none; transition: all 0.2s; font-family: 'Inter', sans-serif; }
.form-inp:focus { border-color: #f59e0b; background: var(--card-bg, #fff); box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.pwd-wrap { position: relative; }
.pwd-wrap .form-inp { padding-right: 40px; }
.pwd-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-sub, #94a3b8); font-size: 18px; }

.pwd-strength { margin-top: 8px; }
.pwd-bar { height: 3px; border-radius: 4px; transition: all 0.3s; }
.str-weak   .pwd-bar { background: #ef4444; width: 33%; }
.str-medium .pwd-bar { background: #f59e0b; width: 66%; }
.str-strong .pwd-bar { background: #10b981; width: 100%; }
.pwd-label { font-size: 11px; font-weight: 600; display: block; margin-top: 4px; }
.str-weak   .pwd-label { color: #ef4444; }
.str-medium .pwd-label { color: #f59e0b; }
.str-strong .pwd-label { color: #10b981; }

/* Upgrade modal specifics */
.upgrade-target-info { display: flex; align-items: center; gap: 12px; background: var(--app-bg, #f8fafc); border-radius: 14px; padding: 14px; }
.mini-avatar { width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; color: #fff; }
.upgrade-summary { background: rgba(217,119,6,0.06); border: 1px solid rgba(217,119,6,0.15); border-radius: 12px; padding: 12px 14px; font-size: 13px; color: var(--text-main, #334155); margin-top: 4px; }

/* Partner info box in edit modal */
.partner-info-box {
  background: linear-gradient(135deg, rgba(217, 119, 6, 0.05), rgba(245, 158, 11, 0.02));
  border: 1px dashed rgba(217, 119, 6, 0.3);
  border-radius: 14px;
  padding: 14px;
}
.partner-info-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}
.card-details {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.info-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px;
}
.info-label {
  color: var(--text-sub, #64748b);
  font-weight: 500;
}
.info-value {
  font-weight: 700;
  color: var(--text-main, #1e293b);
}
.info-value.text-gold {
  color: #d97706;
}
.btn-renew-partner {
  background: linear-gradient(135deg, #d97706, #b45309);
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 6px 14px;
  font-size: 11.5px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2);
}
.btn-renew-partner:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 14px rgba(217, 119, 6, 0.3);
}

/* Buttons */
.modal-footer-btns { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
.btn-primary-custom, .btn-secondary-custom, .btn-warning-custom, .btn-gold-custom {
  padding: 10px 22px; border-radius: 24px; font-size: 13px; font-weight: 700; border: none;
  cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 2px;
}
.btn-secondary-custom { background: var(--input-bg, #f1f5f9); color: var(--text-sub, #64748b); }
.btn-secondary-custom:hover:not(:disabled) { background: rgba(0,0,0,0.07); }
.btn-primary-custom { background: linear-gradient(135deg,#4f46e5,#6366f1); color: #fff; box-shadow: 0 4px 14px rgba(79,70,229,0.25); }
.btn-primary-custom:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79,70,229,0.3); }
.btn-warning-custom { background: linear-gradient(135deg,#f59e0b,#d97706); color: #fff; box-shadow: 0 4px 14px rgba(245,158,11,0.25); }
.btn-warning-custom:hover:not(:disabled) { transform: translateY(-1px); }
.btn-gold-custom { background: linear-gradient(135deg,#d97706,#b45309); color: #fff; box-shadow: 0 4px 14px rgba(217,119,6,0.3); }
.btn-gold-custom:hover:not(:disabled) { transform: translateY(-1px); }
.btn-primary-custom:disabled, .btn-warning-custom:disabled, .btn-gold-custom:disabled, .btn-secondary-custom:disabled { opacity: 0.55; cursor: not-allowed; transform: none; box-shadow: none; }
</style>
