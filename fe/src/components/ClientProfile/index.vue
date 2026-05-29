<template>
  <div class="profile-container container mt-5 pt-5 pb-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
            <h3 class="fw-bold mb-0" style="color: #d4af37;">Hồ sơ cá nhân</h3>
            <p class="text-muted mt-2">Quản lý thông tin tài khoản của bạn</p>
          </div>
          <div class="card-body p-4 p-md-5">
            <div class="row">
              <!-- Left Column: Avatar & Basic Info -->
              <div class="col-md-4 text-center mb-4 mb-md-0 border-end">
                <div class="profile-avatar-container">
                  <img :src="profile.avatar || defaultAvatar" class="rounded-circle border border-3 border-warning profile-avatar shadow" alt="Avatar" width="150" height="150" style="object-fit: cover;">
                  <label for="avatar-upload" class="avatar-upload-btn rounded-circle position-absolute bottom-0 end-0" title="Cập nhật ảnh đại diện">
                    <i class="bx bx-camera"></i>
                  </label>
                  <input type="file" id="avatar-upload" @change="onFileChange" class="d-none" accept="image/*">
                </div>
                <h4 class="mt-3 fw-bold">{{ profile.ho_ten }}</h4>
                <span class="badge bg-light text-dark border mb-2">{{ profile.vai_tro || 'Khách hàng' }}</span>
                <p class="text-muted small mb-0">{{ profile.email }}</p>
                
                <div class="mt-4">
                  <button type="button" class="btn btn-light w-100 mb-2 border hover-shadow" @click="handleLogout" style="transition: all 0.3s ease;">
                    <i class="bx bx-log-out me-1"></i> Đăng Xuất
                  </button>
                </div>
              </div>

              <!-- Right Column: Forms -->
              <div class="col-md-8 ps-md-4">
                <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">Thông Tin Chung</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">Đổi Mật Khẩu</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Lịch Sử Giao Dịch</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="clan-tab" data-bs-toggle="tab" data-bs-target="#clan" type="button" role="tab">Quản Lý Dòng Họ</button>
                  </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                  <!-- Tab Thông tin chung -->
                  <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <form @submit.prevent="updateProfile">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">Họ và tên</label>
                          <input type="text" class="form-control" v-model="profile.ho_ten" placeholder="Nhập họ và tên" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">Số điện thoại</label>
                          <input type="text" class="form-control" v-model="profile.so_dien_thoai" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="col-md-12">
                          <label class="form-label fw-semibold">Email</label>
                          <input type="email" class="form-control" v-model="profile.email" disabled>
                          <small class="text-muted">Email không thể thay đổi sau khi đăng ký.</small>
                        </div>
                        <div class="col-md-12 mt-4 text-end">
                          <button type="submit" class="btn btn-primary px-4 py-2" style="background-color: #d4af37; border-color: #d4af37; color: #000; font-weight: 600;" :disabled="isUpdatingProfile">
                            <span v-if="!isUpdatingProfile"><i class="bx bx-save me-1"></i> Lưu Thay Đổi</span>
                            <span v-else><i class="bx bx-loader-alt bx-spin"></i> Đang xử lý...</span>
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>

                  <!-- Tab Đổi mật khẩu -->
                  <div class="tab-pane fade" id="password" role="tabpanel">
                    <form @submit.prevent="changePassword">
                      <div class="row g-3">
                        <div class="col-md-12">
                          <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                          <input type="password" class="form-control" v-model="passwordData.current_password" placeholder="Nhập mật khẩu hiện tại" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">Mật khẩu mới</label>
                          <input type="password" class="form-control" v-model="passwordData.new_password" placeholder="Nhập mật khẩu mới" required minlength="6">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                          <input type="password" class="form-control" v-model="passwordData.new_password_confirmation" placeholder="Nhập lại mật khẩu mới" required minlength="6">
                        </div>
                        <div class="col-md-12 mt-4 text-end">
                          <button type="submit" class="btn btn-dark px-4 py-2" :disabled="isChangingPassword">
                            <span v-if="!isChangingPassword"><i class="bx bx-lock-open-alt me-1"></i> Cập Nhật Mật Khẩu</span>
                            <span v-else><i class="bx bx-loader-alt bx-spin"></i> Đang xử lý...</span>
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>

                  <!-- Tab Lịch sử giao dịch -->
                  <div class="tab-pane fade" id="history" role="tabpanel">
                    <div v-if="isLoadingHistory" class="text-center py-4">
                      <i class="bx bx-loader-alt bx-spin fs-3 text-warning"></i>
                      <p class="mt-2 text-muted small">Đang tải lịch sử...</p>
                    </div>
                    <div v-else-if="transactions.length === 0" class="text-center py-4">
                      <i class="bx bx-receipt fs-1 text-muted"></i>
                      <p class="mt-2 text-muted">Chưa có lịch sử giao dịch nào.</p>
                      <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                        <router-link to="/dich-vu-goi" class="btn btn-sm px-3" style="background:#d4af37;color:#000;font-weight:600;transition:all 0.3s ease;">
                          <i class="bx bx-package me-1"></i> Đăng ký Gói Đối Tác
                        </router-link>
                        <router-link to="/dong-gop-quy" class="btn btn-sm btn-dark px-3" style="transition:all 0.3s ease;">
                          <i class="bx bx-heart me-1"></i> Đóng góp Quỹ Dòng Họ
                        </router-link>
                      </div>
                    </div>
                    <div v-else>
                      <div class="transaction-item" v-for="(t, i) in transactions" :key="i">
                        <div class="tx-icon" :class="t.trang_thai === 'Hoạt động' ? 'tx-success' : 'tx-pending'">
                          <i class="bx" :class="t.trang_thai === 'Hoạt động' ? 'bx-check' : 'bx-time-five'"></i>
                        </div>
                        <div class="tx-info">
                          <strong>{{ t.noi_dung || 'Đóng góp' }}</strong>
                          <span class="small text-muted d-block">{{ formatTxDate(t.created_at) }}</span>
                        </div>
                        <span class="badge" :class="t.trang_thai === 'Hoạt động' ? 'bg-success' : 'bg-warning text-dark'">{{ t.trang_thai || 'Chờ duyệt' }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Tab Quản Lý Dòng Họ -->
                  <div class="tab-pane fade" id="clan" role="tabpanel">
                    <div class="row g-3" v-if="loadingPermissions">
                      <div class="col-12 text-center py-5">
                        <div class="spinner-border text-warning" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Đang xác thực quyền hạn...</p>
                      </div>
                    </div>
                    <div v-else>
                      <!-- Sleek banner displaying user role and permissions -->
                      <div class="role-banner-container d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 gap-3">
                        <div class="d-flex align-items-center gap-3">
                          <div class="role-banner-icon">
                            <i class="bx bx-shield-quarter fs-4 text-warning animate-pulse"></i>
                          </div>
                          <div>
                            <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 10px;">Vai trò hệ thống</div>
                            <h6 class="fw-bold text-dark mb-0">Quyền Hạn Của Bạn</h6>
                          </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                          <span class="role-badge-gold" v-if="userPermissions.is_admin_or_partner">
                            <i class="bx bx-crown me-1"></i> Admin / Đối tác
                          </span>
                          <template v-else-if="userPermissions.roles && userPermissions.roles.length">
                            <span class="badge bg-dark text-white px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-1" v-for="r in userPermissions.roles" :key="r.name">
                              <i class="bx bx-user fs-6"></i> {{ r.display_name }}
                            </span>
                          </template>
                          <span class="badge bg-secondary text-white px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1" v-else>
                            <i class="bx bx-user-x fs-6"></i> Thành viên phổ thông
                          </span>
                        </div>
                      </div>

                      <div class="row g-3">
                        <!-- Card 1: Gán quyền -->
                        <div class="col-md-4">
                          <div class="card premium-clan-card premium-clan-card-indigo shadow-sm h-100 text-center border-0 p-4" :class="{ 'card-locked': !hasPermission('assign_roles') }">
                            <div v-if="!hasPermission('assign_roles')" class="lock-badge" title="Bạn không có quyền phân quyền nhánh">
                              <i class="bx bx-lock-alt"></i>
                            </div>
                            <div class="icon-box icon-purple mx-auto mb-3">
                              <i class="bx bx-user-pin"></i>
                            </div>
                            <h5 class="fw-bold mb-2 text-dark">Phân Quyền Nhánh</h5>
                            <p class="text-muted small mb-4">Gán hoặc thu hồi vai trò cho các thành viên trong chi nhánh của bạn.</p>
                            <button class="btn btn-premium w-100 fw-bold mt-auto" :class="hasPermission('assign_roles') ? 'btn-premium-indigo' : 'btn-premium-locked'" :disabled="!hasPermission('assign_roles')" @click="openClanTool('assign')">
                              <span v-if="hasPermission('assign_roles')">Truy cập <i class="bx bx-right-arrow-alt ms-1"></i></span>
                              <span v-else><i class="bx bx-lock-alt me-1"></i> Bị khóa</span>
                            </button>
                          </div>
                        </div>

                        <!-- Card 2: Chỉnh sửa lý lịch -->
                        <div class="col-md-4">
                          <div class="card premium-clan-card premium-clan-card-amber shadow-sm h-100 text-center border-0 p-4" :class="{ 'card-locked': !hasPermission('edit_members') }">
                            <div v-if="!hasPermission('edit_members')" class="lock-badge" title="Bạn không có quyền chỉnh sửa lý lịch">
                              <i class="bx bx-lock-alt"></i>
                            </div>
                            <div class="icon-box icon-amber mx-auto mb-3">
                              <i class="bx bx-edit-alt"></i>
                            </div>
                            <h5 class="fw-bold mb-2 text-dark">Chỉnh Sửa Lý Lịch</h5>
                            <p class="text-muted small mb-4">Cập nhật thông tin lý lịch hoặc trạng thái sống/mất của người thân.</p>
                            <button class="btn btn-premium w-100 fw-bold mt-auto" :class="hasPermission('edit_members') ? 'btn-premium-amber' : 'btn-premium-locked'" :disabled="!hasPermission('edit_members')" @click="openClanTool('edit')">
                              <span v-if="hasPermission('edit_members')">Truy cập <i class="bx bx-right-arrow-alt ms-1"></i></span>
                              <span v-else><i class="bx bx-lock-alt me-1"></i> Bị khóa</span>
                            </button>
                          </div>
                        </div>

                        <!-- Card 3: Xem danh sách nhánh -->
                        <div class="col-md-4">
                          <div class="card premium-clan-card premium-clan-card-emerald shadow-sm h-100 text-center border-0 p-4" :class="{ 'card-locked': !hasPermission('view_members') }">
                            <div v-if="!hasPermission('view_members')" class="lock-badge" title="Bạn không có quyền xem danh sách nhánh">
                              <i class="bx bx-lock-alt"></i>
                            </div>
                            <div class="icon-box icon-emerald mx-auto mb-3">
                              <i class="bx bx-git-branch"></i>
                            </div>
                            <h5 class="fw-bold mb-2 text-dark">Xem Danh Sách Nhánh</h5>
                            <p class="text-muted small mb-4">Xem danh sách đầy đủ thành viên thuộc chi nhánh dòng tộc của bạn.</p>
                            <button class="btn btn-premium w-100 fw-bold mt-auto" :class="hasPermission('view_members') ? 'btn-premium-emerald' : 'btn-premium-locked'" :disabled="!hasPermission('view_members')" @click="openClanTool('view')">
                              <span v-if="hasPermission('view_members')">Truy cập <i class="bx bx-right-arrow-alt ms-1"></i></span>
                              <span v-else><i class="bx bx-lock-alt me-1"></i> Bị khóa</span>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Tool Container -->
                      <div class="mt-4 tool-panel-container p-4" v-if="activeTool">
                        <div class="d-flex justify-content-between align-items-center mb-3" :class="{
                          'tool-header-indigo': activeTool === 'assign',
                          'tool-header-amber': activeTool === 'edit',
                          'tool-header-emerald': activeTool === 'view'
                        }" style="padding-left: 15px;">
                          <h4 class="fw-bold mb-0 text-dark">{{ toolTitle }}</h4>
                          <button class="btn btn-sm btn-outline-danger btn-close-panel radius-8" @click="activeTool = null">
                            <i class="bx bx-x me-1"></i>Đóng
                          </button>
                        </div>
                        <hr class="mt-0">

                        <!-- tool: assign -->
                        <div v-if="activeTool === 'assign'">
                          <PhanQuyen :all-members="allMembers" :chi-nhanhs="listData" :selected-chi-nhanh-id="myBranchId" />
                        </div>

                        <!-- tool: edit or view -->
                        <div v-if="activeTool === 'edit' || activeTool === 'view'">
                          <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                              <thead>
                                <tr class="text-muted small text-uppercase" style="border-bottom: 2px solid #f1f5f9;">
                                  <th style="font-weight: 600; padding: 12px 16px;">Thành viên</th>
                                  <th style="font-weight: 600; padding: 12px 16px;">Giới tính</th>
                                  <th style="font-weight: 600; padding: 12px 16px;">Đời thứ</th>
                                  <th style="font-weight: 600; padding: 12px 16px;">Trạng thái</th>
                                  <th style="font-weight: 600; padding: 12px 16px;" v-if="activeTool === 'edit'">Hành động</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr v-for="m in myBranchMembers" :key="m.id" style="border-bottom: 1px solid #f1f5f9;">
                                  <td style="padding: 16px;">
                                    <div class="d-flex align-items-center">
                                      <img :src="m.avatar || defaultAvatar" class="rounded-circle me-3 border border-2 shadow-sm" width="40" height="40" style="object-fit:cover;">
                                      <div>
                                        <span class="fw-bold text-dark d-block" style="font-size: 14.5px;">{{ m.ho_ten }}</span>
                                        <span class="small text-muted" style="font-size: 12px;">{{ m.email || 'Chưa liên kết email' }}</span>
                                      </div>
                                    </div>
                                  </td>
                                  <td class="text-dark" style="padding: 16px; font-size: 14px;">{{ m.gioi_tinh }}</td>
                                  <td style="padding: 16px;">
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5" style="font-size: 12px; font-weight: 500; border-radius: 8px;">Đời {{ m.doi_thu }}</span>
                                  </td>
                                  <td style="padding: 16px;">
                                    <span :class="m.trang_thai === 'Đã mất' ? 'status-badge-deceased' : 'status-badge-active'">
                                      {{ m.trang_thai }}
                                    </span>
                                  </td>
                                  <td v-if="activeTool === 'edit'" style="padding: 16px;">
                                    <button class="btn btn-sm btn-light border me-2 hover-shadow rounded-pill px-3 py-1.5" @click="editMember(m)" style="font-size: 12.5px; font-weight: 600; transition: all 0.2s;">
                                      <i class="bx bx-edit-alt text-primary me-1"></i>Sửa nhanh
                                    </button>
                                    <button class="btn btn-sm btn-light border hover-shadow rounded-pill px-3 py-1.5" @click="toggleLifeStatus(m)" style="font-size: 12.5px; font-weight: 600; transition: all 0.2s;">
                                      <i class="bx bx-sync text-warning me-1"></i>Đổi Sống/Mất
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
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Quick Edit Modal -->
    <div class="modal fade" id="quickEditModal" tabindex="-1" aria-hidden="true" ref="quickEditModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 radius-12 shadow-lg">
          <div class="modal-header bg-dark text-white border-0 py-3" style="border-radius:12px 12px 0 0">
            <h5 class="modal-title fw-bold" style="color:#d4af37;"><i class="bx bx-edit me-1"></i>Sửa nhanh thành viên</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeQuickEdit"></button>
          </div>
          <div class="modal-body p-4" v-if="editingMemberObj">
            <div class="mb-3">
              <label class="form-label fw-bold">Họ và tên</label>
              <input type="text" class="form-control" v-model="editingMemberObj.ho_ten" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Giới tính</label>
              <select class="form-select" v-model="editingMemberObj.gioi_tinh">
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Ngày sinh</label>
              <input type="date" class="form-control" v-model="editingMemberObj.ngay_sinh">
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Ghi chú</label>
              <textarea class="form-control" rows="3" v-model="editingMemberObj.ghi_chu"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-light radius-8" @click="closeQuickEdit">Hủy</button>
            <button type="button" class="btn btn-warning text-dark fw-bold radius-8" @click="saveQuickEdit" style="background:#d4af37; border-color:#d4af37;">Cập nhật</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Life Status Update Modal -->
    <div class="modal fade" id="lifeStatusUpdateModal" tabindex="-1" aria-hidden="true" ref="lifeStatusUpdateModal">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 radius-12 shadow-lg">
          <div class="modal-header bg-dark text-white border-0 py-3" style="border-radius:12px 12px 0 0">
            <h5 class="modal-title fw-bold" style="color:#d4af37;"><i class="bx bx-sync me-1"></i>Cập nhật trạng thái sống/mất</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeLifeStatusUpdateModal"></button>
          </div>
          <div class="modal-body p-4" v-if="lifeStatusTarget">
            <p class="fs-6 text-dark">
              Bạn có chắc chắn muốn đổi trạng thái của <strong class="text-primary">{{ lifeStatusTarget.ho_ten }}</strong> thành 
              <span class="badge shadow-sm" :class="lifeStatusTarget.nextStatus === 'Đã mất' ? 'bg-danger' : 'bg-success'">{{ lifeStatusTarget.nextStatus }}</span>?
            </p>
            
            <div class="mt-3" v-if="lifeStatusTarget.nextStatus === 'Đã mất'">
              <label class="form-label fw-bold text-dark">Ngày mất (Dương lịch)</label>
              <input type="date" class="form-control" v-model="lifeStatusTarget.ngay_mat">
              <small class="text-muted mt-1 d-block">Để trống nếu không rõ hoặc muốn mặc định là ngày hôm nay.</small>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-light radius-8" @click="closeLifeStatusUpdateModal">Hủy</button>
            <button type="button" class="btn btn-warning text-dark fw-bold radius-8" @click="submitLifeStatusUpdate" style="background:#d4af37; border-color:#d4af37;">Cập nhật</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';
import PhanQuyen from '../DoiTac/PhanQuyen/index.vue';

export default {
  name: 'ClientProfile',
  components: { PhanQuyen },
  data() {
    return {
      profile: {
        ho_ten: '',
        email: '',
        so_dien_thoai: '',
        avatar: '',
        vai_tro: ''
      },
      avatarFile: null,
      defaultAvatar: "data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%231e293b'/%3E%3Ccircle cx='50' cy='35' r='18' fill='%23d4af37'/%3E%3Cpath d='M15 85 C15 67 30 55 50 55 C70 55 85 67 85 85 Z' fill='%23d4af37'/%3E%3C/svg%3E",
      passwordData: {
        current_password: '',
        new_password: '',
        new_password_confirmation: ''
      },
      isUpdatingProfile: false,
      isChangingPassword: false,
      transactions: [],
      isLoadingHistory: false,
      loadingPermissions: true,
      userPermissions: {
        is_admin_or_partner: false,
        roles: [],
        permissions: []
      },
      activeTool: null,
      allMembers: [],
      listData: [],
      myBranchId: null,
      editingMemberObj: null,
      quickEditModalObj: null,
      lifeStatusTarget: null,
      lifeStatusUpdateModalObj: null
    }
  },
  computed: {
    toolTitle() {
      if (this.activeTool === 'assign') return 'Phân Quyền Nhánh';
      if (this.activeTool === 'edit') return 'Chỉnh Sửa Lý Lịch';
      if (this.activeTool === 'view') return 'Xem Danh Sách Nhánh';
      return '';
    },
    myBranchMembers() {
      if (!this.allMembers || !this.allMembers.length) return [];
      if (this.userPermissions.is_admin_or_partner) {
        return this.allMembers;
      }
      if (!this.myBranchId) {
        return this.allMembers;
      }
      return this.allMembers.filter(m => m.chi_nhanh_id == this.myBranchId);
    }
  },
  mounted() {
    this.loadUserProfile();
    this.loadTransactions();
    this.loadClanData();
    
    // Auto-select tab if query param is set
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'quan-ly') {
      this.$nextTick(() => {
        const clanTabTrigger = document.getElementById('clan-tab');
        if (clanTabTrigger && window.bootstrap) {
          const tab = new window.bootstrap.Tab(clanTabTrigger);
          tab.show();
        }
      });
    }
  },
  methods: {
    getHeaders() {
      const token = localStorage.getItem('access_token');
      return {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      };
    },
    loadUserProfile() {
      axios.get('http://127.0.0.1:8000/api/me', this.getHeaders())
        .then(res => {
          this.profile = res.data.user;
          // Update local storage so that Topclient avatar also updates if refreshed
          localStorage.setItem('user', JSON.stringify(res.data.user));
          window.dispatchEvent(new Event('profile-updated'));
        })
        .catch(err => {
          if(err.response && err.response.status === 401) {
            this.handleLogout();
          }
        });
    },
    onFileChange(e) {
      const file = e.target.files[0];
      if (file) {
        this.avatarFile = file;
        this.profile.avatar = URL.createObjectURL(file); // Preview locally
      }
    },
    updateProfile() {
      this.isUpdatingProfile = true;
      
      const formData = new FormData();
      formData.append('ho_ten', this.profile.ho_ten);
      if (this.profile.so_dien_thoai) {
        formData.append('so_dien_thoai', this.profile.so_dien_thoai);
      }
      if (this.avatarFile) {
        formData.append('avatar', this.avatarFile);
      }

      axios.post('http://127.0.0.1:8000/api/update-profile', formData, {
        headers: {
          ...this.getHeaders().headers,
          'Content-Type': 'multipart/form-data'
        }
      })
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.profile = res.data.user;
          localStorage.setItem('user', JSON.stringify(res.data.user));
          window.dispatchEvent(new Event('profile-updated'));
          // Reset file input
          this.avatarFile = null;
        }
      })
      .catch(err => {
        const msg = err.response?.data?.message || 'Có lỗi xảy ra khi cập nhật thông tin!';
        toastr.error(msg);
      })
      .finally(() => {
        this.isUpdatingProfile = false;
      });
    },
    changePassword() {
      if (this.passwordData.new_password !== this.passwordData.new_password_confirmation) {
        toastr.warning('Mật khẩu xác nhận không khớp!');
        return;
      }

      this.isChangingPassword = true;

      axios.post('http://127.0.0.1:8000/api/change-password', this.passwordData, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.passwordData = {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
          };
        }
      })
      .catch(err => {
        const msg = err.response?.data?.message || 'Có lỗi xảy ra khi đổi mật khẩu!';
        toastr.error(msg);
      })
      .finally(() => {
        this.isChangingPassword = false;
      });
    },
    handleLogout() {
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
      this.$router.push('/login');
    },
    loadTransactions() {
      this.isLoadingHistory = true;
      const userStr = localStorage.getItem('user');
      const user = userStr ? JSON.parse(userStr) : null;
      const userId = user ? (user.user || user).id : null;

      axios.get('http://127.0.0.1:8000/api/dong-gop/get-data', this.getHeaders())
        .then(res => { 
          if (res.data.status && res.data.data) {
            // Lọc danh sách giao dịch chỉ hiển thị của tài khoản đang đăng nhập
            this.transactions = res.data.data.filter(t => t.nguoi_dung_id == userId);
          } 
        })
        .catch(() => {})
        .finally(() => { this.isLoadingHistory = false; });
    },
    formatTxDate(d) {
      if (!d) return '';
      return new Date(d).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    },
    loadClanData() {
      this.loadingPermissions = true;
      axios.get('http://127.0.0.1:8000/api/me/roles-permissions', this.getHeaders())
        .then(res => {
          if (res.data && res.data.status) {
            this.userPermissions = res.data;
            if (this.userPermissions.roles && this.userPermissions.roles.length) {
              this.myBranchId = this.userPermissions.roles[0].chi_nhanh_id;
            }
          }
        })
        .catch(err => {
          console.error(err);
        })
        .finally(() => {
          this.loadingPermissions = false;
        });

      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
        .then(res => {
          if (res.data && res.data.status) {
            this.allMembers = res.data.data;
          }
        });

      axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
        .then(res => {
          if (res.data && res.data.status) {
            this.listData = res.data.data;
          }
        });
    },
    hasPermission(name) {
      if (this.userPermissions.is_admin_or_partner) return true;
      return this.userPermissions.permissions && this.userPermissions.permissions.includes(name);
    },
    openClanTool(toolName) {
      this.activeTool = toolName;
    },
    toggleLifeStatus(member) {
      const nextStatus = member.trang_thai === 'Còn sống' ? 'Đã mất' : 'Còn sống';
      this.lifeStatusTarget = {
        id: member.id,
        ho_ten: member.ho_ten,
        currentStatus: member.trang_thai,
        nextStatus: nextStatus,
        ngay_mat: new Date().toISOString().substring(0, 10)
      };
      this.$nextTick(() => {
        if (!this.lifeStatusUpdateModalObj && window.bootstrap) {
          this.lifeStatusUpdateModalObj = new window.bootstrap.Modal(document.getElementById('lifeStatusUpdateModal'));
        }
        if (this.lifeStatusUpdateModalObj) {
          this.lifeStatusUpdateModalObj.show();
        }
      });
    },
    closeLifeStatusUpdateModal() {
      if (this.lifeStatusUpdateModalObj) {
        this.lifeStatusUpdateModalObj.hide();
      }
      this.lifeStatusTarget = null;
    },
    submitLifeStatusUpdate() {
      if (!this.lifeStatusTarget) return;
      const target = this.lifeStatusTarget;
      
      axios.post(`http://127.0.0.1:8000/api/thanh-vien/${target.id}/update-life-status`, {
        trang_thai: target.nextStatus,
        ngay_mat: target.nextStatus === 'Đã mất' ? target.ngay_mat : null
      }, this.getHeaders())
      .then(res => {
        if (res.data && res.data.status) {
          toastr.success(res.data.message);
          this.closeLifeStatusUpdateModal();
          this.loadClanData();
        } else {
          toastr.error(res.data.message || 'Lỗi khi cập nhật trạng thái.');
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Lỗi khi cập nhật trạng thái.');
      });
    },
    editMember(member) {
      this.editingMemberObj = {
        id: member.id,
        ho_ten: member.ho_ten,
        gioi_tinh: member.gioi_tinh,
        ngay_sinh: member.ngay_sinh ? member.ngay_sinh.substring(0, 10) : '',
        ghi_chu: member.ghi_chu
      };
      this.$nextTick(() => {
        if (!this.quickEditModalObj && window.bootstrap) {
          this.quickEditModalObj = new window.bootstrap.Modal(document.getElementById('quickEditModal'));
        }
        if (this.quickEditModalObj) {
          this.quickEditModalObj.show();
        }
      });
    },
    closeQuickEdit() {
      if (this.quickEditModalObj) {
        this.quickEditModalObj.hide();
      }
    },
    saveQuickEdit() {
      if (!this.editingMemberObj.ho_ten) {
        toastr.warning('Vui lòng nhập họ tên');
        return;
      }
      axios.post('http://127.0.0.1:8000/api/thanh-vien/update', this.editingMemberObj, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success('Cập nhật thành công!');
            this.closeQuickEdit();
            this.loadClanData();
          } else {
            toastr.error(res.data.message);
          }
        })
        .catch(err => {
          toastr.error('Lỗi khi lưu thông tin');
        });
    }
  }
}
</script>

<style scoped>
.profile-container {
  min-height: calc(100vh - 300px);
}
.profile-avatar-container {
  position: relative;
  display: inline-block;
  transition: all 0.3s ease;
}
.profile-avatar {
  transition: all 0.3s ease;
}
.profile-avatar-container:hover .profile-avatar {
  filter: brightness(0.9);
  box-shadow: 0 0 15px rgba(212, 175, 55, 0.4) !important;
}
.avatar-upload-btn {
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  background-color: #d4af37;
  color: #1e293b;
  border: 2px solid #ffffff;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.avatar-upload-btn:hover {
  background-color: #1e293b;
  color: #d4af37;
  transform: scale(1.1);
  box-shadow: 0 6px 14px rgba(212, 175, 55, 0.3);
}
.avatar-upload-btn i {
  font-size: 20px;
}
.hover-shadow:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  background-color: #f8f9fa !important;
}
.form-control:focus {
  border-color: #d4af37;
  box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
}
.nav-tabs .nav-link {
  border: none;
  border-bottom: 2px solid transparent;
  padding: 10px 20px;
  color: #6c757d;
  font-weight: 600;
  transition: all 0.3s ease;
}
.nav-tabs .nav-link.active {
  border-bottom: 2px solid #d4af37;
  color: #d4af37 !important;
  background-color: transparent;
}
.nav-tabs .nav-link:hover {
  color: #e5c45e;
  border-bottom: 2px solid #e5c45e;
}

/* Transaction History */
.transaction-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}
.transaction-item:last-child { border-bottom: none; }
.tx-icon {
  width: 38px; height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
}
.tx-success { background: #e8f5e9; color: #2e7d32; }
.tx-pending { background: #fff8e1; color: #f9a825; }
.tx-info { flex: 1; min-width: 0; }
.tx-info strong {
  display: block;
  font-size: 13px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* PREMIUM CLAN MANAGEMENT CARDS & VIEWS */
.role-banner-container {
  background: rgba(255, 255, 255, 0.8) !important;
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(212, 175, 55, 0.25) !important;
  border-radius: 18px !important;
  box-shadow: 0 10px 30px rgba(212, 175, 55, 0.05) !important;
  transition: all 0.3s ease;
}
.role-banner-container:hover {
  border-color: rgba(212, 175, 55, 0.4) !important;
  box-shadow: 0 12px 35px rgba(212, 175, 55, 0.08) !important;
}
.role-banner-icon {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: linear-gradient(135deg, rgba(212, 175, 55, 0.15), rgba(229, 196, 94, 0.05));
  border: 1px solid rgba(212, 175, 55, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.1);
}
.role-badge-gold {
  background: linear-gradient(135deg, #f5d061 0%, #d4af37 100%) !important;
  color: #1a1a1a !important;
  font-weight: 700 !important;
  letter-spacing: 0.5px;
  text-shadow: 0 1px 0 rgba(255, 255, 255, 0.2);
  box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
  padding: 6px 14px !important;
  border-radius: 30px !important;
  border: 1px solid rgba(255, 255, 255, 0.25);
  display: inline-flex;
  align-items: center;
}

.premium-clan-card {
  position: relative;
  background: #ffffff !important;
  border: 1px solid rgba(0, 0, 0, 0.06) !important;
  border-radius: 24px !important;
  padding: 30px 24px !important;
  transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.premium-clan-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; height: 4px;
  background: transparent;
  transition: all 0.3s ease;
}
.premium-clan-card-indigo::before { background: linear-gradient(90deg, #6366f1, #4f46e5); }
.premium-clan-card-amber::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
.premium-clan-card-emerald::before { background: linear-gradient(90deg, #10b981, #059669); }

.premium-clan-card:hover:not(.card-locked) {
  transform: translateY(-8px);
  border-color: transparent !important;
}
.premium-clan-card-indigo:hover:not(.card-locked) {
  box-shadow: 0 20px 40px rgba(99, 102, 241, 0.12) !important;
}
.premium-clan-card-amber:hover:not(.card-locked) {
  box-shadow: 0 20px 40px rgba(245, 158, 11, 0.12) !important;
}
.premium-clan-card-emerald:hover:not(.card-locked) {
  box-shadow: 0 20px 40px rgba(16, 185, 129, 0.12) !important;
}

/* Card Locked state styling */
.card-locked {
  background: #fafafa !important;
  border: 1px solid #f1f5f9 !important;
  cursor: not-allowed;
  opacity: 0.75;
}
.card-locked .icon-box {
  background: #f1f5f9 !important;
  color: #cbd5e1 !important;
  border-color: #e2e8f0 !important;
}
.card-locked h5, .card-locked p {
  color: #94a3b8 !important;
}
.lock-badge {
  position: absolute;
  top: 18px;
  right: 18px;
  width: 30px;
  height: 30px;
  background: rgba(0, 0, 0, 0.03);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #94a3b8;
  font-size: 14px;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.icon-box {
  width: 68px;
  height: 68px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  margin-bottom: 20px;
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.icon-purple {
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0.03) 100%);
  color: #6366f1;
  border: 1px solid rgba(99, 102, 241, 0.12);
}
.icon-amber {
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(245, 158, 11, 0.03) 100%);
  color: #d97706;
  border: 1px solid rgba(245, 158, 11, 0.12);
}
.icon-emerald {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.03) 100%);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.12);
}
.premium-clan-card:hover:not(.card-locked) .icon-box {
  transform: scale(1.1) rotate(4deg);
}
.premium-clan-card-indigo:hover:not(.card-locked) .icon-box {
  background: #6366f1;
  color: #ffffff;
  box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
}
.premium-clan-card-amber:hover:not(.card-locked) .icon-box {
  background: #f59e0b;
  color: #ffffff;
  box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}
.premium-clan-card-emerald:hover:not(.card-locked) .icon-box {
  background: #10b981;
  color: #ffffff;
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
}

.btn-premium {
  border-radius: 16px !important;
  padding: 12px 20px !important;
  font-size: 14px !important;
  border: none !important;
  transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
}
.btn-premium-indigo {
  background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2) !important;
}
.btn-premium-indigo:hover:not(:disabled) {
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35) !important;
  transform: translateY(-2px);
}
.btn-premium-amber {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2) !important;
}
.btn-premium-amber:hover:not(:disabled) {
  box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35) !important;
  transform: translateY(-2px);
}
.btn-premium-emerald {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
  color: white !important;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2) !important;
}
.btn-premium-emerald:hover:not(:disabled) {
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35) !important;
  transform: translateY(-2px);
}
.btn-premium-locked {
  background: #f1f5f9 !important;
  color: #94a3b8 !important;
  border: 1px solid #e2e8f0 !important;
  box-shadow: none !important;
  cursor: not-allowed;
}

/* Tool Panel Styling */
.tool-panel-container {
  border: 1px solid rgba(0,0,0,0.05) !important;
  box-shadow: 0 15px 40px rgba(0,0,0,0.05) !important;
  border-radius: 24px !important;
  background-color: #ffffff;
  overflow: hidden;
  transition: all 0.3s ease;
}
.tool-header-indigo { border-left: 5px solid #6366f1; }
.tool-header-amber { border-left: 5px solid #f59e0b; }
.tool-header-emerald { border-left: 5px solid #10b981; }

.btn-close-panel {
  border-radius: 30px !important;
  padding: 8px 18px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  transition: all 0.2s ease;
}

/* Status dots */
.status-badge-active {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #ecfdf5 !important;
  color: #065f46 !important;
  padding: 6px 12px !important;
  border-radius: 30px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  border: 1px solid rgba(16, 185, 129, 0.15);
}
.status-badge-active::before {
  content: '';
  width: 6px; height: 6px;
  background-color: #10b981;
  border-radius: 50%;
  display: inline-block;
  box-shadow: 0 0 8px #10b981;
}
.status-badge-deceased {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #fef2f2 !important;
  color: #991b1b !important;
  padding: 6px 12px !important;
  border-radius: 30px !important;
  font-size: 12px !important;
  font-weight: 600 !important;
  border: 1px solid rgba(239, 68, 68, 0.15);
}
.status-badge-deceased::before {
  content: '';
  width: 6px; height: 6px;
  background-color: #ef4444;
  border-radius: 50%;
  display: inline-block;
}

/* Animation utilities */
@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}
.animate-pulse {
  animation: pulse 2s infinite ease-in-out;
}
</style>
