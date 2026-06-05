<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="page-content">
      <div class="page-header text-center">
        <span class="subtitle-gradient d-block text-uppercase mb-2">DI SẢN TRỰC TUYẾN</span>
        <h2 class="text-gradient fw-bold display-5 mb-1">Cây Gia Phả Dòng Họ</h2>
        <p class="text-white-50 mb-0">Sơ đồ phả hệ các thế hệ dòng họ</p>
      </div>

      <!-- Demo Mode Alert Banner -->
      <div v-if="!isLoggedIn" class="demo-alert-banner mb-4 text-center animate__animated animate__fadeInDown">
        <div class="demo-banner-content">
          <i class="bx bx-info-circle demo-icon animate-ring"></i>
          <span class="demo-text">Bạn đang trải nghiệm <strong>Cây Gia Phả Mẫu (Demo)</strong> dành cho khách vãng lai.</span>
          <div class="demo-action-buttons">
            <router-link to="/login" class="btn btn-sm btn-login-demo">Đăng Nhập</router-link>
            <router-link to="/register" class="btn btn-sm btn-register-demo">Khởi Tạo Gia Phả</router-link>
          </div>
        </div>
      </div>

      <div class="tree-card-wrapper">
        <div class="card-toolbar">
          <div class="toolbar-left">
            <button class="tb-btn" @click="loadData" title="Làm mới"><i class="bx bx-refresh"></i></button>
            <button class="tb-btn" @click="zoomIn"><i class="bx bx-plus"></i></button>
            <span class="zoom-label">{{ Math.round(zoom * 100) }}%</span>
            <button class="tb-btn" @click="zoomOut"><i class="bx bx-minus"></i></button>
            <button class="tb-btn" @click="resetView" title="Đặt lại"><i class="bx bx-expand-alt"></i></button>
            <button class="tb-btn-text text-warning ms-2" @click="openProposalsHistoryModal" title="Lịch sử đề xuất">
              <i class="bx bx-git-pull-request"></i> Đề xuất
            </button>
          </div>
          <div class="toolbar-right">
             <span class="toolbar-hint">
              <i class="bx bx-mouse-alt"></i> Cuộn thu phóng &bull; Kéo di chuyển &bull; Click xem chi tiết
            </span>
          </div>
        </div>

        <div
          class="tree-viewport"
          ref="viewport"
          @wheel.prevent="handleWheel"
          @mousedown="startPan"
          @mousemove="doPan"
          @mouseup="endPan"
          @mouseleave="endPan"
        >
          <div class="tree-canvas" :style="canvasStyle">
            <div v-if="isLoading" class="empty-state">
              <div class="ld-ring"></div>
              <p>Đang tải dữ liệu...</p>
            </div>
            <div v-else-if="!treeData.length" class="empty-state">
              <i class="bx bx-git-repo-forked"></i>
              <p>Chưa có dữ liệu gia phả</p>
            </div>
            <div class="tree" v-else>
              <ul class="tree-ul">
                <TreeItem
                  v-for="root in treeData"
                  :key="root.id"
                  :member="root"
                  :listDoiTocHo="listDoiTocHo"
                  @view="onView"
                  @show-qr="showQRCard"
                />
              </ul>
            </div>
          </div>
        </div>

        <div class="view-hint">
          <i class="bx bx-mouse"></i> Cuộn để thu phóng &bull; <i class="bx bx-move"></i> Kéo để di chuyển
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewMemberModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content premium-member-modal-content border-0 overflow-hidden shadow-2xl">
          <!-- Parallax/Gold Header -->
          <div class="modal-header premium-modal-header border-0 bg-royal text-white d-flex align-items-center justify-content-between py-3 px-4" style="border-radius:24px 24px 0 0 !important;">
            <h5 class="modal-title fw-extrabold d-flex align-items-center gap-2 text-gold" style="color:#d4af37 !important;">
              <i class="bx bx-id-card fs-4"></i>
              <span>Hồ Sơ Thành Viên</span>
            </h5>
            <button type="button" class="btn-close-royal" data-bs-dismiss="modal">
              <i class="bx bx-x fs-3"></i>
            </button>
          </div>

          <div class="modal-body premium-modal-body p-4 text-center position-relative">
            <div class="modal-decor-light"></div>
            
            <!-- Avatar Frame -->
            <div class="modal-avatar-wrapper-premium mb-3 d-inline-block position-relative">
              <img
                :src="currentMember.avatar || `https://ui-avatars.com/api/?name=${currentMember.ho_ten}&background=d4af37&color=fff&size=128`"
                class="rounded-circle border border-4 border-white shadow-lg profile-avatar-modal"
                width="120" height="120" style="object-fit:cover"
                alt="avatar"
              >
              <span class="status-indicator-modal shadow-sm" :class="currentMember.trang_thai === 'Còn sống' ? 'status-alive' : 'status-deceased'">
                <i class="bx" :class="currentMember.trang_thai === 'Còn sống' ? 'bx-heart' : 'bx-bookmark-heart'"></i>
              </span>
            </div>

            <!-- Name and Generation -->
            <h3 class="fw-extrabold text-dark mb-1 font-serif">{{ currentMember.ho_ten }}</h3>
            <span class="badge badge-generation-modal mb-3"><i class="bx bx-git-branch"></i> Đời tộc họ thứ {{ currentMember.doi_thu }}</span>

            <!-- Relationship with logged-in user -->
            <div v-if="isLoggedIn && currentMemberRelationshipLoading" class="mb-4 p-3 bg-light-gold border-dashed-gold radius-12 text-center shadow-xs">
              <div class="spinner-border spinner-border-sm text-warning me-2" role="status" style="width: 1.2rem; height: 1.2rem;"></div>
              <span class="text-muted font-12 fw-medium">Hệ thống đang đối chiếu vai vế gia tộc...</span>
            </div>
            
            <div v-else-if="isLoggedIn && currentMemberRelationship" class="mb-4 p-3 bg-light-gold border-dashed-gold radius-12 text-center shadow-xs animate__animated animate__fadeIn">
              <span class="text-muted small d-block mb-1 text-uppercase fw-bold font-10 tracking-wider">Mối Quan Hệ Với Bạn</span>
              <div class="relationship-term-modal font-serif">{{ currentMemberRelationship }}</div>
              <div class="small text-muted mt-1 font-12 italic">{{ currentMemberRelationshipDesc }}</div>
            </div>

            <!-- Vitals Grid -->
            <div class="row g-3 text-start mt-1">
              <div class="col-6">
                <div class="vital-item-card p-3 radius-12 border bg-light-soft">
                  <span class="text-muted small d-block mb-1 text-uppercase fw-bold font-10">Giới tính</span>
                  <div class="d-flex align-items-center gap-2">
                    <i class="bx fs-5" :class="currentMember.gioi_tinh === 'Nam' ? 'bx-male-sign text-primary' : 'bx-female-sign text-pink'"></i>
                    <span class="fw-bold text-dark font-14">{{ currentMember.gioi_tinh }}</span>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="vital-item-card p-3 radius-12 border bg-light-soft">
                  <span class="text-muted small d-block mb-1 text-uppercase fw-bold font-10">Trạng thái</span>
                  <div class="d-flex align-items-center gap-2">
                    <i class="bx fs-5" :class="currentMember.trang_thai === 'Đã mất' ? 'bx-bookmark-heart text-danger' : 'bx-heart text-success'"></i>
                    <span class="fw-bold font-14" :class="currentMember.trang_thai === 'Đã mất' ? 'text-danger' : 'text-success'">
                      {{ currentMember.trang_thai }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="vital-item-card p-3 radius-12 border bg-light-soft">
                  <span class="text-muted small d-block mb-1 text-uppercase fw-bold font-10">Hôn nhân</span>
                  <div class="d-flex align-items-center gap-2">
                    <i class="bx fs-5" :class="{
                      'bx-heart-circle text-danger': currentMember.tinh_trang_hon_nhan === 'Đã kết hôn',
                      'bx-user text-secondary': currentMember.tinh_trang_hon_nhan === 'Độc thân',
                      'bx-x-circle text-warning': currentMember.tinh_trang_hon_nhan === 'Ly hôn',
                      'bx-heart text-muted': currentMember.tinh_trang_hon_nhan === 'Góa',
                      'bx-minus-circle text-muted': !currentMember.tinh_trang_hon_nhan
                    }"></i>
                    <span class="fw-bold font-14" :class="{
                      'text-danger': currentMember.tinh_trang_hon_nhan === 'Đã kết hôn',
                      'text-secondary': currentMember.tinh_trang_hon_nhan === 'Độc thân',
                      'text-warning': currentMember.tinh_trang_hon_nhan === 'Ly hôn',
                      'text-muted': !currentMember.tinh_trang_hon_nhan || currentMember.tinh_trang_hon_nhan === 'Góa'
                    }">
                      {{ currentMember.tinh_trang_hon_nhan || 'Chưa rõ' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="vital-item-card p-3 radius-12 border bg-light-soft">
                  <span class="text-muted small d-block mb-1 text-uppercase fw-bold font-10">Ngày sinh</span>
                  <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-calendar text-muted fs-5"></i>
                    <span class="fw-bold text-dark font-14">{{ currentMember.ngay_sinh ? fmtDate(currentMember.ngay_sinh) : 'Chưa rõ' }}</span>
                  </div>
                </div>
              </div>
              <div class="col-6" v-if="currentMember.trang_thai === 'Đã mất'">
                <div class="vital-item-card p-3 radius-12 border bg-light-soft">
                  <span class="text-muted small d-block mb-1 text-uppercase fw-bold font-10">Ngày mất</span>
                  <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-calendar-x text-danger fs-5"></i>
                    <span class="fw-bold text-dark font-14">{{ currentMember.ngay_mat ? fmtDate(currentMember.ngay_mat) : 'Chưa rõ' }}</span>
                  </div>
                </div>
              </div>
              
              <!-- Biography / Notes -->
              <div class="col-12" v-if="currentMember.ghi_chu">
                <div class="vital-item-card p-3 radius-12 border bg-light-soft border-start border-4 border-warning-gold">
                  <span class="text-muted small d-block mb-1 text-uppercase fw-bold font-10"><i class="bx bx-book-open"></i> Ghi chép / Ghi chú</span>
                  <p class="mb-0 text-dark font-13 fst-italic lh-relaxed opacity-90">{{ currentMember.ghi_chu }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer Dock with Modern Layout -->
          <div class="modal-footer premium-modal-footer bg-light border-0 justify-content-center gap-2 py-3 px-4 flex-wrap">
            <button class="btn btn-premium-action btn-edit-premium" @click="openProposal('edit')">
              <i class="bx bx-edit-alt"></i> Đề xuất sửa
            </button>
            <button v-if="currentMember.loai_quan_he === 'Chính'" class="btn btn-premium-action btn-child-premium" @click="openProposal('add_child')">
              <i class="bx bx-plus-circle"></i> Đề xuất con
            </button>
            <button v-if="currentMember.loai_quan_he === 'Chính'" class="btn btn-premium-action btn-spouse-premium" @click="openProposal('add_spouse')">
              <i class="bx bx-heart"></i> Đề xuất Vợ/Chồng
            </button>
            <button class="btn btn-premium-action btn-delete-premium" @click="openDeleteProposal">
              <i class="bx bx-trash"></i> Đề xuất xóa
            </button>
            <button v-if="isDirectRelative" class="btn btn-premium-action btn-delete-premium" style="background: #ef4444 !important; color: white !important;" @click="openLifeStatusModal">
              <i class="bx bx-heart-voice"></i> Cập nhật Sống/Mất
            </button>
            <button class="btn btn-premium-action btn-qr-premium" @click="showQRCardFromModal">
              <i class="bx bx-qr"></i> Xem Mã QR
            </button>
            <button class="btn btn-premium-close px-4" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Update Life Status Modal -->
    <div class="modal fade" id="lifeStatusModal" tabindex="-1" aria-hidden="true" style="z-index: 2100 !important;">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-15 shadow-lg border-0 bg-dark text-white">
          <div class="modal-header border-0 bg-black/40 pb-2">
            <h5 class="modal-title fw-bold" style="color:#d4af37">
              <i class="bx bx-sync me-2"></i>Cập nhật trạng thái sức khỏe
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 text-center">
            <h5 class="fw-bold mb-3" style="color: #ffd700;">{{ currentMember.ho_ten }}</h5>
            
            <div class="mb-3 text-start">
              <label class="form-label fw-bold text-white-50">Tình trạng hiện tại</label>
              <select class="form-select bg-secondary text-white border-0 radius-8" v-model="lifeStatusForm.trang_thai">
                <option value="Còn sống">Còn sống</option>
                <option value="Đã mất">Đã mất</option>
              </select>
            </div>
            
            <div class="mb-3 text-start" v-if="lifeStatusForm.trang_thai === 'Đã mất'">
              <label class="form-label fw-bold text-white-50">Ngày mất (Dương lịch)</label>
              <input type="date" class="form-control bg-secondary text-white border-0 radius-8" v-model="lifeStatusForm.ngay_mat">
            </div>
          </div>
          <div class="modal-footer border-0 justify-content-center">
            <button class="btn btn-light px-4 radius-10" data-bs-dismiss="modal">Hủy</button>
            <button class="btn btn-warning text-dark px-4 radius-10 fw-bold" @click="submitLifeStatus">Cập nhật ngay</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit/Add Proposal Modal -->
    <div class="modal fade" id="proposalModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-15 shadow-lg border-0">
          <div class="modal-header border-0 bg-primary text-white" style="border-radius:15px 15px 0 0">
            <h5 class="modal-title fw-bold">
              <i class="bx bx-git-pull-request me-2"></i>{{ proposalTitle }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Họ và Tên</label>
                <input type="text" class="form-control radius-8 border-2 shadow-none" v-model="proposalForm.ho_ten" placeholder="Nguyễn Văn A">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Giới tính</label>
                <select class="form-select radius-8 border-2 shadow-none" v-model="proposalForm.gioi_tinh">
                  <option value="Nam">Nam</option>
                  <option value="Nữ">Nữ</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold">Đời thứ</label>
                <input type="number" class="form-control radius-8 border-2 shadow-none" v-model="proposalForm.doi_thu" min="1">
              </div>
              <div class="col-md-6" v-if="proposalType === 'add_child' && currentMember.spouses && currentMember.spouses.length > 0">
                <label class="form-label fw-bold">Chọn cha/mẹ còn lại (Vợ/Chồng)</label>
                <select class="form-select radius-8 border-2 shadow-none" v-model="proposalForm.other_parent_id">
                  <option :value="null">-- Chưa xác định / Khác --</option>
                  <option v-for="s in currentMember.spouses" :key="s.id" :value="s.id">{{ s.ho_ten }}</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Ngày sinh</label>
                <input type="date" class="form-control radius-8 border-2 shadow-none" v-model="proposalForm.ngay_sinh">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Trạng thái</label>
                <select class="form-select radius-8 border-2 shadow-none" v-model="proposalForm.trang_thai">
                  <option value="Còn sống">Còn sống</option>
                  <option value="Đã mất">Đã mất</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">Tình trạng hôn nhân</label>
                <select class="form-select radius-8 border-2 shadow-none" v-model="proposalForm.tinh_trang_hon_nhan">
                  <option value="">-- Chưa rõ --</option>
                  <option value="Độc thân">Độc thân</option>
                  <option value="Đã kết hôn">Đã kết hôn</option>
                  <option value="Ly hôn">Ly hôn</option>
                  <option value="Góa">Góa</option>
                </select>
              </div>
              <div class="col-md-6" v-if="proposalForm.trang_thai === 'Đã mất'">
                <label class="form-label fw-bold">Ngày mất</label>
                <input type="date" class="form-control radius-8 border-2 shadow-none" v-model="proposalForm.ngay_mat">
              </div>
              <!-- Premium Avatar File Upload -->
              <div class="col-md-12">
                <label class="form-label fw-bold d-flex justify-content-between">
                  <span>Hình ảnh đại diện</span>
                  <span class="text-muted small fw-normal" v-if="proposalForm.avatar">Đã tải lên</span>
                </label>
                
                <!-- Hidden Input -->
                <input 
                  type="file" 
                  ref="avatarFileInput" 
                  class="d-none" 
                  accept="image/png, image/jpeg, image/jpg" 
                  @change="uploadAvatarFile"
                >

                <!-- Premium Upload Widget Box -->
                <div class="avatar-upload-widget radius-10 overflow-hidden">
                  <!-- Case 1: Currently Uploading -->
                  <div v-if="isUploading" class="upload-state-box loading-state py-4 text-center">
                    <div class="spinner-border text-primary mb-2" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0 text-muted small">Đang xử lý và tải ảnh lên máy chủ...</p>
                  </div>

                  <!-- Case 2: Uploaded & Showing Preview -->
                  <div v-else-if="proposalForm.avatar" class="upload-state-box preview-state d-flex align-items-center gap-3 p-3">
                    <img :src="proposalForm.avatar" class="preview-thumbnail rounded-circle border border-2 border-primary shadow" alt="Avatar Preview">
                    <div class="preview-actions text-start">
                      <button type="button" class="btn btn-sm btn-outline-primary radius-8 px-3 me-2" @click="triggerFileInput">
                        <i class="bx bx-sync me-1"></i>Thay ảnh khác
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger radius-8 px-3" @click="removeAvatar">
                        <i class="bx bx-trash me-1"></i>Xóa
                      </button>
                      <span class="d-block text-muted small mt-1 italic">Ảnh sẽ được lưu khi gửi đề xuất</span>
                    </div>
                  </div>

                  <!-- Case 3: Empty / Click to upload -->
                  <div v-else class="upload-state-box empty-state-box text-center p-4 border-2 border-dashed radius-10 cursor-pointer" @click="triggerFileInput">
                    <i class="bx bx-cloud-upload display-6 text-primary mb-2"></i>
                    <h6 class="fw-bold mb-1 text-dark">Tải ảnh lên từ máy tính</h6>
                    <p class="mb-0 text-muted small">Nhấp vào đây hoặc kéo thả tệp hình ảnh vào đây</p>
                    <span class="text-muted font-9 d-block mt-1 opacity-75">Hỗ trợ PNG, JPG, JPEG (Tối đa 2MB)</span>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <label class="form-label fw-bold">Ghi chú / Tiểu sử</label>
                <textarea class="form-control radius-8 border-2 shadow-none" rows="3" v-model="proposalForm.ghi_chu" placeholder="Thông tin thêm về thành viên..."></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button class="btn btn-light px-4 radius-10" data-bs-dismiss="modal">Hủy</button>
            <button class="btn btn-primary px-4 radius-10 fw-bold" @click="submitProposal">Gửi Đề Xuất</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Vue QR Card Modal -->
    <div v-if="showQRModal" class="custom-modal-backdrop animate__animated animate__fadeIn" @click.self="closeQRModal" style="z-index: 2000 !important;">
        <div class="custom-modal-content animate__animated animate__zoomIn p-4 rounded-4 shadow-2xl bg-white position-relative text-center" style="max-width: 420px; z-index: 2060;">
            <button class="btn-close-custom position-absolute top-0 end-0 m-3 border-0 bg-transparent" @click="closeQRModal">
                <i class="bx bx-x fs-2 text-muted"></i>
            </button>
            
            <h5 class="fw-bold mb-3 text-dark">Mã QR Thành Viên</h5>

            <!-- QR Card Canvas container (for printing / visual preview) -->
            <div id="qr-card-print" class="qr-card-container p-4 rounded-3 border border-3 border-gold bg-royal shadow-sm mb-4 position-relative overflow-hidden">
                <div class="card-watermark"></div>
                <div class="card-header-royal mb-3 border-bottom border-light-gold pb-2">
                    <div class="fw-extrabold text-gold tracking-widest font-13 text-uppercase" style="color: #d4af37 !important;">Hệ Thống Gia Phả Số</div>
                    <div class="text-white-50 font-9 text-uppercase tracking-wider">Thẻ Nhận Diện Thành Viên</div>
                </div>
                
                <div class="d-flex align-items-center gap-3 mb-3 text-start">
                    <img :src="activeMember.avatar || ('https://ui-avatars.com/api/?name=' + activeMember.ho_ten + '&background=d4af37&color=fff')" class="rounded-circle border border-2 border-gold shadow-sm" width="55" height="55" style="object-fit: cover;">
                    <div class="overflow-hidden">
                        <h5 class="fw-extrabold text-white mb-0 text-truncate drop-shadow" style="color: white !important;">{{ activeMember.ho_ten }}</h5>
                        <div class="d-flex gap-1.5 mt-1 flex-wrap">
                            <span class="badge badge-gold-soft font-9 px-2 py-0.5" style="background: rgba(212, 175, 55, 0.15) !important; color: #ffd891 !important; border: 1px solid rgba(212, 175, 55, 0.25);">Đời {{ activeMember.doi_thu }}</span>
                            <span class="badge badge-gold-soft font-9 px-2 py-0.5" style="background: rgba(212, 175, 55, 0.15) !important; color: #ffd891 !important; border: 1px solid rgba(212, 175, 55, 0.25);">{{ activeMember.gioi_tinh }}</span>
                            <span class="badge badge-gold-soft font-9 px-2 py-0.5" style="background: rgba(212, 175, 55, 0.15) !important; color: #ffd891 !important; border: 1px solid rgba(212, 175, 55, 0.25);">{{ activeMember.trang_thai }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- QR Frame -->
                <div class="qr-frame-royal bg-white p-3 rounded-3 shadow-md d-inline-block position-relative border border-2 border-gold">
                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(getMemberQRUrl(activeMember))" class="img-fluid" width="180" height="180" alt="QR Code">
                </div>
                
                <p class="text-white-50 font-10 mt-3 mb-0 italic">Quét mã để truy cập tiểu sử & xem mối quan hệ dòng tộc</p>
            </div>

            <!-- Control Buttons -->
            <div class="d-flex gap-3">
                <button class="btn btn-outline-secondary w-50 py-2 rounded-pill fw-bold" @click="printQRCard">
                    <i class="bx bx-printer me-1"></i> In Thẻ QR
                </button>
                <button class="btn btn-gold w-50 py-2 rounded-pill fw-bold text-dark shadow-sm" @click="downloadQRCard" style="background: #d4af37 !important; border-color: #d4af37 !important; font-weight: bold;">
                    <i class="bx bx-download me-1"></i> Tải Thẻ Về
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Proposal Modal -->
    <div v-if="showDeleteProposalModal" class="custom-modal-backdrop animate__animated animate__fadeIn" @click.self="closeDeleteProposalModal" style="z-index: 2000 !important;">
        <div class="custom-modal-content animate__animated animate__zoomIn p-4 rounded-4 shadow-2xl bg-white position-relative text-center" style="max-width: 460px; z-index: 2060;">
            <button class="btn-close-custom position-absolute top-0 end-0 m-3 border-0 bg-transparent" @click="closeDeleteProposalModal">
                <i class="bx bx-x fs-2 text-muted"></i>
            </button>
            
            <div class="text-danger mb-3">
              <i class="bx bx-trash display-5" style="color: #dc3545 !important;"></i>
            </div>
            <h5 class="fw-bold mb-2 text-dark">Đề Xuất Xóa Thành Viên</h5>
            <p class="text-muted small mb-4">Bạn đang tạo đề xuất yêu cầu quản trị viên/đối tác xóa thành viên **{{ currentMember.ho_ten }}** khỏi cây gia phả.</p>

            <div class="text-start mb-4">
              <label class="form-label fw-bold text-dark small">Lý do đề xuất xóa <span class="text-danger">*</span></label>
              <textarea 
                class="form-control radius-8 border-2 shadow-none" 
                rows="3" 
                v-model="deleteProposalReason" 
                placeholder="Nhập lý do chi tiết vì sao cần xóa thành viên này..."
              ></textarea>
            </div>

            <!-- Control Buttons -->
            <div class="d-flex gap-3">
                <button class="btn btn-outline-secondary w-50 py-2 rounded-pill fw-bold" @click="closeDeleteProposalModal">
                    Hủy bỏ
                </button>
                <button class="btn btn-danger w-50 py-2 rounded-pill fw-bold text-white shadow-sm" @click="submitDeleteProposal" style="background-color: #dc3545 !important; border-color: #dc3545 !important;">
                    Gửi đề xuất xóa
                </button>
            </div>
        </div>
    </div>

    <!-- proposalsHistoryModal -->
    <div class="modal fade" id="proposalsHistoryModal" tabindex="-1" aria-hidden="true" style="z-index: 2050 !important;">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content radius-15 shadow-lg border-0 bg-dark text-white">
          <div class="modal-header border-0 bg-black/40 pb-2">
            <h5 class="modal-title fw-bold" style="color:#d4af37">
              <i class="bx bx-git-pull-request me-2"></i>Lịch Sử Đề Xuất Phả Hệ
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 scrollable-proposals" style="max-height: 480px; overflow-y: auto;">
            
            <div v-if="isProposalsLoading" class="text-center py-4">
              <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="text-white-50 mt-2">Đang tải lịch sử đề xuất...</p>
            </div>

            <div v-else-if="proposalsList.length === 0" class="text-center py-5 text-muted">
              <i class="bx bx-git-pull-request fs-1 opacity-25 mb-2"></i>
              <p class="mb-0 text-white-50">Bạn chưa gửi đề xuất chỉnh sửa phả hệ nào.</p>
            </div>

            <div v-else class="timeline-list">
              <div v-for="item in proposalsList" :key="item.id" class="proposal-history-item mb-4 p-3 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                  <div>
                    <h6 class="fw-bold mb-1" style="color:#ffd700;">{{ typeLabel(item.type) }}</h6>
                    <span class="text-white-50 small">
                      <i class="bx bx-time-five me-1"></i>Gửi lúc: {{ fmtDateTime(item.created_at) }}
                    </span>
                  </div>
                  <span class="badge px-3 py-1.5" :class="statusClass(item.status)">
                    {{ statusLabel(item.status) }}
                  </span>
                </div>

                <div class="proposal-body text-white-50 mt-3">
                  <!-- Target Deceased/Member -->
                  <div class="target-member mb-2 p-2 rounded bg-white/5 border border-white/5" v-if="item.thanh_vien">
                    <span class="small d-block text-white-50 mb-1">
                      {{ item.type === 'edit' ? 'Thành viên cần chỉnh sửa:' : (item.type === 'add_child' ? 'Đề xuất làm con của:' : 'Đề xuất kết hôn với:') }}
                    </span>
                    <strong>{{ item.thanh_vien.ho_ten }}</strong> (Đời {{ item.thanh_vien.doi_thu }})
                  </div>

                  <!-- Details -->
                  <div class="p-2 rounded" style="background: rgba(0,0,0,0.2);">
                    <div class="row g-2 text-start small">
                      <div class="col-6">Họ tên: <strong class="text-white">{{ item.data.ho_ten }}</strong></div>
                      <div class="col-6">Giới tính: <strong class="text-white">{{ item.data.gioi_tinh }}</strong></div>
                      <div class="col-6">Thế hệ: <strong class="text-white">Đời {{ item.data.doi_thu }}</strong></div>
                      <div class="col-6" v-if="item.data.ngay_sinh">Ngày sinh: <strong class="text-white">{{ fmtDate(item.data.ngay_sinh) }}</strong></div>
                      <div class="col-6">Trạng thái: <strong :class="item.data.trang_thai === 'Đã mất' ? 'text-danger' : 'text-success'">{{ item.data.trang_thai }}</strong></div>
                      <div class="col-6" v-if="item.data.trang_thai === 'Đã mất' && item.data.ngay_mat">Ngày mất: <strong class="text-white">{{ fmtDate(item.data.ngay_mat) }}</strong></div>
                      <div class="col-12 mt-1" v-if="item.data.ghi_chu">Ghi chú: <p class="mb-0 text-white-50">{{ item.data.ghi_chu }}</p></div>
                    </div>
                  </div>

                  <!-- Clan Feedback Note -->
                  <div class="mt-2 p-2 rounded border" :class="item.status === 'approved' ? 'border-success/20 bg-success/5 text-success' : 'border-danger/20 bg-danger/5 text-danger'" v-if="item.note">
                    <span class="d-block small fw-bold mb-1"><i class="bx bx-comment-detail me-1"></i>Phản hồi từ Gia tộc:</span>
                    <p class="mb-0 text-white">{{ item.note }}</p>
                  </div>

                </div>
              </div>
            </div>

          </div>
          <div class="modal-footer border-0">
            <button class="btn btn-light px-4 radius-10" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, h } from 'vue';
import axios from 'axios';

import toastr from 'toastr';

const fmtDate = (d) => d ? new Date(d).toLocaleDateString('vi-VN') : '';

const TreeItem = defineComponent({
  name: 'TreeItem',
  props: ['member', 'listDoiTocHo'],
  emits: ['view', 'show-qr'],
  data() {
    return {
      clickTimeout: null,
      isDoubleClick: false
    };
  },
  render() {
    const m = this.member;
    const hasChildren = m.children && m.children.length > 0;

    if (m.isDummy) {
      const kids = hasChildren
        ? h('ul', { class: 'tree-ul' }, m.children.map(c => h(TreeItem, { key: c.id, member: c, listDoiTocHo: this.listDoiTocHo, onView: x => this.$emit('view', x), onShowQr: x => this.$emit('show-qr', x) })))
        : null;
      return h('li', { class: 'tree-li' }, [
        h('div', { class: 'tree-node-group dummy-group' }, [
          h('div', { class: 'dummy-line' })
        ]),
        kids
      ]);
    }

    let children = null;

    const getTenDoi = (n) => {
      if (!this.listDoiTocHo || !this.listDoiTocHo.length) return '';
      const d = this.listDoiTocHo.find(x => x.so_doi == n);
      return d && d.ten_doi ? ` (${d.ten_doi.toUpperCase()})` : '';
    };

    const genClass = `gen-${(m.doi_thu % 5) + 1}`;

    const makeCard = (person, isSpouse = false, extraClass = '', order = null) => {
      const dead = person.trang_thai === 'Đã mất';
      const gClass = isSpouse ? '' : `gen-${(person.doi_thu % 5) + 1}`;
      const src = person.avatar
        || `https://ui-avatars.com/api/?name=${encodeURIComponent(person.ho_ten)}&background=d4af37&color=fff&size=100`;

      const initials = person.ho_ten.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();

      return h('div', {
        class: ['tree-node-card', gClass, extraClass, { spouse: isSpouse, 'is-dead': dead }],
        style: order !== null ? { order } : undefined,
        onClick: e => {
          e.stopPropagation();
          clearTimeout(this.clickTimeout);
          this.isDoubleClick = false;
          this.clickTimeout = setTimeout(() => {
            if (!this.isDoubleClick) {
              this.$emit('view', person);
            }
          }, 200);
        },
        onDblclick: e => {
          e.stopPropagation();
          this.isDoubleClick = true;
          clearTimeout(this.clickTimeout);
          this.$emit('show-qr', person);
        }
      }, [
        h('div', { class: 'node-avatar-container' }, [
          person.avatar 
            ? h('img', { src, class: 'node-avatar shadow-sm', width: 50, height: 50 })
            : h('div', { class: 'node-initials shadow-sm' }, initials)
        ]),
        h('div', { class: 'node-content' }, [
          h('div', { class: 'node-name' }, person.ho_ten),
          person.ngay_sinh ? h('div', { class: 'node-date' }, fmtDate(person.ngay_sinh)) : null,
          h('div', { class: ['node-tag', isSpouse ? 'spouse-tag' : ''] },
            isSpouse ? 'VỢ/CHỒNG' : `ĐỜI ${person.doi_thu}${getTenDoi(person.doi_thu)}`
          )
        ])
      ]);
    };

    // Arrange couple cards. Lay out spouses sequentially (husband - wife 1 - wife 2)
    const coupleChildren = [];
    const hasMultipleSpouses = m.spouses && m.spouses.length === 2;

    let kids0 = [];
    let kids1 = [];
    if (hasMultipleSpouses) {
        const spouse0 = m.spouses[0];
        const spouse1 = m.spouses[1];
        const parentKey = m.gioi_tinh === 'Nam' ? 'me_id' : 'cha_id';
        kids1 = (m.children || []).filter(child => child[parentKey] == spouse1.id);
        const kids1Ids = kids1.map(k => k.id);
        kids0 = (m.children || []).filter(child => !kids1Ids.includes(child.id));
    }

    if (hasMultipleSpouses) {
      // spouse - connector - main - connector - spouse
      coupleChildren.push(makeCard(m.spouses[0], true, 'spouse-left', 1));
      coupleChildren.push(h('div', { 
        class: ['tree-connector-h', 'spouse-connector', 'spouse-connector-0'], 
        style: { order: 2 } 
      }, [ h('i', { class: 'bx bxs-heart connector-heart' }) ]));
      coupleChildren.push(makeCard(m, false, 'main-centered', 3));
      coupleChildren.push(h('div', { 
        class: ['tree-connector-h', 'spouse-connector', 'spouse-connector-1'], 
        style: { order: 4 } 
      }, [ h('i', { class: 'bx bxs-heart connector-heart' }) ]));
      coupleChildren.push(makeCard(m.spouses[1], true, 'spouse-right', 5));

      const sp0 = m.spouses[0];
      const sp1 = m.spouses[1];
      const kids0 = hasChildren ? m.children.filter(c => c.me_id == sp0.id || c.cha_id == sp0.id) : [];
      const kids1 = hasChildren ? m.children.filter(c => c.me_id == sp1.id || c.cha_id == sp1.id) : [];

      const col0 = h('div', { class: ['union-column', 'union-column-0', kids0.length === 0 ? 'union-column-empty' : ''] }, [
        kids0.length > 0
          ? h('ul', { class: 'tree-ul' }, kids0.map(c => h(TreeItem, { key: c.id, member: c, listDoiTocHo: this.listDoiTocHo, onView: x => this.$emit('view', x), onShowQr: x => this.$emit('show-qr', x) })))
          : h('div', { class: 'union-empty-placeholder' })
      ]);

      const col1 = h('div', { class: ['union-column', 'union-column-1', kids1.length === 0 ? 'union-column-empty' : ''] }, [
        kids1.length > 0
          ? h('ul', { class: 'tree-ul' }, kids1.map(c => h(TreeItem, { key: c.id, member: c, listDoiTocHo: this.listDoiTocHo, onView: x => this.$emit('view', x), onShowQr: x => this.$emit('show-qr', x) })))
          : h('div', { class: 'union-empty-placeholder' })
      ]);

      children = h('div', { class: 'unions-wrapper' }, [col0, col1]);
    } else {
      // default: main then spouses in sequence
      coupleChildren.push(makeCard(m, false));
      if (m.spouses && m.spouses.length) {
        m.spouses.forEach(s => {
          coupleChildren.push(h('div', { class: 'tree-connector-h' }, [ h('i', { class: 'bx bxs-heart connector-heart' }) ]));
          coupleChildren.push(makeCard(s, true));
        });
      }
      children = hasChildren
        ? h('ul', { class: 'tree-ul' }, m.children.map(c => h(TreeItem, { key: c.id, member: c, listDoiTocHo: this.listDoiTocHo, onView: x => this.$emit('view', x), onShowQr: x => this.$emit('show-qr', x) })))
        : null;
    }

    const nodeGroup = h('div', { class: 'tree-node-group' }, [
      h('div', { class: 'couple-wrapper' }, coupleChildren)
    ]);

    return h('li', { class: ['tree-li', { 'has-multiple-spouses-li': hasMultipleSpouses }] }, [nodeGroup, children]);
  }
});

const mockDoiTocHo = [
  { so_doi: 1, ten_doi: 'Đời 1' },
  { so_doi: 2, ten_doi: 'Đời 2' },
  { so_doi: 3, ten_doi: 'Đời 3' },
  { so_doi: 4, ten_doi: 'Đời 4' }
];

const mockMembers = [
  {
    id: 1,
    ho_ten: 'Nguyễn Đức Long',
    gioi_tinh: 'Nam',
    doi_thu: 1,
    ngay_sinh: '1920-05-15',
    ngay_mat: '2005-10-20',
    trang_thai: 'Đã mất',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Cụ tổ sáng lập dòng họ Nguyễn Đức. Từng tham gia kháng chiến chống Pháp, tính tình hiền lành, đức độ, chăm lo cho gia tộc.',
    avatar: '',
    loai_quan_he: 'Chính',
    spouse_of_id: null,
    cha_id: null,
    me_id: null
  },
  {
    id: 101,
    ho_ten: 'Lê Thị Hoa',
    gioi_tinh: 'Nữ',
    doi_thu: 1,
    ngay_sinh: '1923-08-12',
    ngay_mat: '2010-04-18',
    trang_thai: 'Đã mất',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Cụ bà tần tảo, một đời vì chồng vì con. Nữ công gia chánh xuất sắc.',
    avatar: '',
    loai_quan_he: 'Vợ/Chồng',
    spouse_of_id: 1,
    cha_id: null,
    me_id: null
  },
  {
    id: 2,
    ho_ten: 'Nguyễn Đức Cường',
    gioi_tinh: 'Nam',
    doi_thu: 2,
    ngay_sinh: '1948-02-10',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Nguyên trưởng tộc đời thứ 2 dòng họ Nguyễn Đức. Nhà giáo ưu tú đã về hưu.',
    avatar: '',
    loai_quan_he: 'Chính',
    spouse_of_id: null,
    cha_id: 1,
    me_id: 101
  },
  {
    id: 102,
    ho_ten: 'Trần Thị Lan',
    gioi_tinh: 'Nữ',
    doi_thu: 2,
    ngay_sinh: '1952-11-20',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Người vợ hiền hậu, chăm sóc gia đình chu đáo. Cựu bác sĩ bệnh viện tỉnh.',
    avatar: '',
    loai_quan_he: 'Vợ/Chồng',
    spouse_of_id: 2,
    cha_id: null,
    me_id: null
  },
  {
    id: 3,
    ho_ten: 'Nguyễn Đức Thịnh',
    gioi_tinh: 'Nam',
    doi_thu: 2,
    ngay_sinh: '1952-09-05',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Kỹ sư nông nghiệp kỳ cựu, đóng góp lớn cho phong trào cải tạo đồng ruộng địa phương.',
    avatar: '',
    loai_quan_he: 'Chính',
    spouse_of_id: null,
    cha_id: 1,
    me_id: 101
  },
  {
    id: 103,
    ho_ten: 'Phạm Thị Hồng',
    gioi_tinh: 'Nữ',
    doi_thu: 2,
    ngay_sinh: '1955-04-14',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Nữ doanh nhân kinh doanh hàng tiêu dùng, tính tình xởi lởi, nhiệt tình với công việc gia tộc.',
    avatar: '',
    loai_quan_he: 'Vợ/Chồng',
    spouse_of_id: 3,
    cha_id: null,
    me_id: null
  },
  {
    id: 4,
    ho_ten: 'Nguyễn Đức Anh',
    gioi_tinh: 'Nam',
    doi_thu: 3,
    ngay_sinh: '1975-06-25',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Thạc sĩ Công nghệ thông tin, đang công tác tại tập đoàn viễn thông quốc gia.',
    avatar: '',
    loai_quan_he: 'Chính',
    spouse_of_id: null,
    cha_id: 2,
    me_id: 102
  },
  {
    id: 104,
    ho_ten: 'Hoàng Thị Bích',
    gioi_tinh: 'Nữ',
    doi_thu: 3,
    ngay_sinh: '1978-03-30',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Giáo viên trung học phổ thông môn Ngữ Văn, yêu cái đẹp và văn hóa truyền thống.',
    avatar: '',
    loai_quan_he: 'Vợ/Chồng',
    spouse_of_id: 4,
    cha_id: null,
    me_id: null
  },
  {
    id: 5,
    ho_ten: 'Nguyễn Thị Mai',
    gioi_tinh: 'Nữ',
    doi_thu: 3,
    ngay_sinh: '1980-12-08',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Kiến trúc sư thiết kế nội thất, yêu thích nghệ thuật cắm hoa.',
    avatar: '',
    loai_quan_he: 'Chính',
    spouse_of_id: null,
    cha_id: 2,
    me_id: 102
  },
  {
    id: 105,
    ho_ten: 'Trần Văn Hải',
    gioi_tinh: 'Nam',
    doi_thu: 3,
    ngay_sinh: '1978-01-15',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Phó giám đốc công ty logistics lớn, người con rể hiền lành, biết đối nhân xử thế.',
    avatar: '',
    loai_quan_he: 'Vợ/Chồng',
    spouse_of_id: 5,
    cha_id: null,
    me_id: null
  },
  {
    id: 6,
    ho_ten: 'Nguyễn Đức Tuấn',
    gioi_tinh: 'Nam',
    doi_thu: 3,
    ngay_sinh: '1982-10-18',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Tiến sĩ Y khoa, hiện là trưởng khoa ngoại chấn thương chỉnh hình bệnh viện trung ương.',
    avatar: '',
    loai_quan_he: 'Chính',
    spouse_of_id: null,
    cha_id: 3,
    me_id: 103
  },
  {
    id: 106,
    ho_ten: 'Lê Thị Thu',
    gioi_tinh: 'Nữ',
    doi_thu: 3,
    ngay_sinh: '1985-05-20',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Đã kết hôn',
    ghi_chu: 'Thạc sĩ dược học, hiện đang quản lý chuỗi nhà thuốc gia đình.',
    avatar: '',
    loai_quan_he: 'Vợ/Chồng',
    spouse_of_id: 6,
    cha_id: null,
    me_id: null
  },
  {
    id: 7,
    ho_ten: 'Nguyễn Đức Minh',
    gioi_tinh: 'Nam',
    doi_thu: 4,
    ngay_sinh: '2002-04-20',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Độc thân',
    ghi_chu: 'Sinh viên năm cuối ngành Khoa học máy tính, đạt nhiều giải thưởng học thuật xuất sắc.',
    avatar: '',
    loai_quan_he: 'Chính',
    spouse_of_id: null,
    cha_id: 4,
    me_id: 104
  },
  {
    id: 8,
    ho_ten: 'Nguyễn Ngọc Linh',
    gioi_tinh: 'Nữ',
    doi_thu: 4,
    ngay_sinh: '2006-08-30',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Độc thân',
    ghi_chu: 'Sinh viên năm nhất ngành Ngoại thương, năng động và đam mê hoạt động ngoại khóa.',
    avatar: '',
    loai_quan_he: 'Chính',
    spouse_of_id: null,
    cha_id: 4,
    me_id: 104
  },
  {
    id: 9,
    ho_ten: 'Nguyễn Đức Hải',
    gioi_tinh: 'Nam',
    doi_thu: 4,
    ngay_sinh: '2012-07-15',
    ngay_mat: null,
    trang_thai: 'Còn sống',
    tinh_trang_hon_nhan: 'Độc thân',
    ghi_chu: 'Học sinh trung học cơ sở, đạt thành tích cao môn Toán học cấp thành phố.',
    avatar: '',
    loai_quan_he: 'Chính',
    spouse_of_id: null,
    cha_id: 6,
    me_id: 106
  }
];

export default {
  name: 'ClientGiaPha',
  components: { TreeItem },
  data() {
    return {
      allMembers: [],
      listDoiTocHo: [],
      currentMember: {},
      modal: null,
      isLoading: true,
      zoom: 0.9,
      posX: 0,
      posY: 0,
      isPanning: false,
      lastMouseX: 0,
      lastMouseY: 0,
      proposalModal: null,
      proposalType: 'edit',
      proposalTitle: '',
      showQRModal: false,
      activeMember: {},
      proposalForm: {
        ho_ten: '',
        gioi_tinh: 'Nam',
        doi_thu: 1,
        ngay_sinh: '',
        trang_thai: 'Còn sống',
        ngay_mat: '',
        tinh_trang_hon_nhan: '',
        ghi_chu: '',
        avatar: ''
      },
       proposalsHistoryModal: null,
      proposalsList: [],
      isProposalsLoading: false,
      isUploading: false,
      showDeleteProposalModal: false,
      deleteProposalReason: '',
      currentMemberRelationship: null,
      currentMemberRelationshipDesc: null,
      currentMemberRelationshipLoading: false,
      isLoggedIn: !!localStorage.getItem('access_token'),
      currentUser: null,
      lifeStatusForm: { trang_thai: 'Còn sống', ngay_mat: '' },
      lifeStatusModalObj: null
    };
  },
  computed: {
    treeData() {
      const list = JSON.parse(JSON.stringify(this.allMembers));
      const map = {};
      const roots = [];
      list.forEach(i => { map[i.id] = i; i.children = []; i.spouses = []; });

      const getDummy = (pid, doi) => {
        const id = `dummy_${pid}_${doi}`;
        if (!map[id]) {
          map[id] = { id, isDummy: true, doi_thu: doi, children: [], spouses: [] };
          map[pid].children.push(map[id]);
        }
        return map[id];
      };

      list.forEach(i => {
        if (i.loai_quan_he === 'Vợ/Chồng' && i.spouse_of_id && map[i.spouse_of_id]) {
          map[i.spouse_of_id].spouses.push(i);
          map[i.spouse_of_id].spouses.sort((a, b) => a.id - b.id);
        } else if (i.cha_id && map[i.cha_id]) {
          let parent = map[i.cha_id];
          if (i.doi_thu > parent.doi_thu + 1) {
            let cur = parent;
            for (let d = parent.doi_thu + 1; d < i.doi_thu; d++) cur = getDummy(cur.id, d);
            cur.children.push(i);
          } else {
            parent.children.push(i);
          }
        } else if (i.loai_quan_he === 'Chính') {
          roots.push(i);
        }
      });
      return roots;
    },
    canvasStyle() {
      return {
        transform: `translate(${this.posX}px, ${this.posY}px) scale(${this.zoom})`,
        transformOrigin: 'top center'
      };
    },
    isDirectRelative() {
      if (!this.currentUser || !this.currentUser.email) return false;
      const roleName = this.currentUser.vai_tro?.toLowerCase() || '';
      const chucVuName = this.currentUser.chuc_vu?.ten_chuc_vu?.toLowerCase() || '';
      const isSubAdmin = chucVuName.includes('quản trị') || roleName.includes('admin');
      if (roleName === 'admin' || isSubAdmin || this.currentUser.is_doi_tac == 1) return true;

      const me = this.allMembers.find(m => m.email === this.currentUser.email);
      if (!me) return false;

      const target = this.currentMember;
      if (!target) return false;

      const isChild = (me.cha_id == target.id || me.me_id == target.id);
      const isParent = (target.cha_id == me.id || target.me_id == me.id);
      const isSpouse = (me.spouse_of_id == target.id || target.spouse_of_id == me.id);

      return isChild || isParent || isSpouse;
    }
  },
  mounted() {
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(document.getElementById('viewMemberModal'));
      this.proposalModal = new window.bootstrap.Modal(document.getElementById('proposalModal'));
      if (document.getElementById('proposalsHistoryModal')) {
        this.proposalsHistoryModal = new window.bootstrap.Modal(document.getElementById('proposalsHistoryModal'));
      }
    }
    const userStr = localStorage.getItem('user');
    if (userStr) {
      try {
        const u = JSON.parse(userStr);
        this.currentUser = u.user || u;
      } catch (e) {
        console.error(e);
      }
    }
    this.isLoggedIn = !!localStorage.getItem('access_token');
    this.loadDoiTocHo();
    this.loadData();
  },
  methods: {
    fmtDate,
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadDoiTocHo() {
      if (!this.isLoggedIn) {
        this.listDoiTocHo = mockDoiTocHo;
        return;
      }
      axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data', this.getHeaders())
        .then(r => { if (r.data.status) this.listDoiTocHo = r.data.data.sort((a,b)=>a.so_doi-b.so_doi); });
    },
    loadData() {
      this.isLoading = true;
      if (!this.isLoggedIn) {
        this.allMembers = mockMembers;
        this.isLoading = false;
        return;
      }
      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
        .then(r => { if (r.data.status) this.allMembers = r.data.data; })
        .finally(() => { this.isLoading = false; });
    },
    onView(m) {
      this.currentMember = m;
      this.currentMemberRelationship = null;
      this.currentMemberRelationshipDesc = null;
      this.currentMemberRelationshipLoading = false;

      const token = localStorage.getItem('access_token');
      const userStr = localStorage.getItem('user');
      const user = userStr ? JSON.parse(userStr) : null;

      if (token && user && user.email) {
        const myMember = this.allMembers.find(member => member.email === user.email);
        if (myMember) {
          if (myMember.id === m.id) {
            this.currentMemberRelationship = 'Bản thân';
            this.currentMemberRelationshipDesc = 'Đây chính là hồ sơ gia phả liên kết với tài khoản của bạn.';
          } else {
            this.currentMemberRelationshipLoading = true;
            axios.post('http://127.0.0.1:8000/api/thanh-vien/xac-dinh-quan-he', {
              id_a: myMember.id,
              id_b: m.id
            }, {
              headers: { Authorization: `Bearer ${token}` }
            })
            .then(res => {
              if (res.data.status) {
                this.currentMemberRelationship = res.data.appellation || res.data.term;
                this.currentMemberRelationshipDesc = res.data.description;
              }
            })
            .catch(() => {
              // Silent fail
            })
            .finally(() => {
              this.currentMemberRelationshipLoading = false;
            });
          }
        }
      }

      this.modal.show();
    },
    showQRCardFromModal() {
      if (this.modal) this.modal.hide();
      this.showQRCard(this.currentMember);
    },
    zoomIn() { if (this.zoom < 2.0) this.zoom = +(this.zoom + 0.1).toFixed(1); },
    zoomOut() { if (this.zoom > 0.2) this.zoom = +(this.zoom - 0.1).toFixed(1); },
    resetView() { this.zoom = 0.9; this.posX = 0; this.posY = 0; },
    handleWheel(e) { e.preventDefault(); e.deltaY < 0 ? this.zoomIn() : this.zoomOut(); },
    startPan(e) { if (e.button !== 0) return; this.isPanning = true; this.lastMouseX = e.clientX; this.lastMouseY = e.clientY; },
    doPan(e) {
      if (!this.isPanning) return;
      this.posX += e.clientX - this.lastMouseX;
      this.posY += e.clientY - this.lastMouseY;
      this.lastMouseX = e.clientX; this.lastMouseY = e.clientY;
    },
    endPan() { this.isPanning = false; },
    openProposal(type) {
      if (!this.isLoggedIn) {
        toastr.info('Vui lòng đăng nhập để gửi đề xuất thay đổi gia phả!');
        return;
      }
      this.proposalType = type;
      if (type === 'edit') {
        this.proposalTitle = `Đề Xuất Chỉnh Sửa Thông Tin - ${this.currentMember.ho_ten}`;
        this.proposalForm = {
          ho_ten: this.currentMember.ho_ten,
          gioi_tinh: this.currentMember.gioi_tinh || 'Nam',
          doi_thu: this.currentMember.doi_thu,
          ngay_sinh: this.currentMember.ngay_sinh ? this.currentMember.ngay_sinh.substring(0, 10) : '',
          trang_thai: this.currentMember.trang_thai || 'Còn sống',
          ngay_mat: this.currentMember.ngay_mat ? this.currentMember.ngay_mat.substring(0, 10) : '',
          tinh_trang_hon_nhan: this.currentMember.tinh_trang_hon_nhan || '',
          ghi_chu: this.currentMember.ghi_chu || '',
          avatar: this.currentMember.avatar || ''
        };
      } else if (type === 'add_child') {
        this.proposalTitle = `Đề Xuất Thêm Con của ${this.currentMember.ho_ten}`;
        this.proposalForm = {
          ho_ten: '',
          gioi_tinh: 'Nam',
          doi_thu: this.currentMember.doi_thu + 1,
          ngay_sinh: '',
          trang_thai: 'Còn sống',
          ngay_mat: '',
          ghi_chu: '',
          avatar: '',
          other_parent_id: null
        };
      } else if (type === 'add_spouse') {
        this.proposalTitle = `Đề Xuất Thêm Vợ/Chồng của ${this.currentMember.ho_ten}`;
        this.proposalForm = {
          ho_ten: '',
          gioi_tinh: this.currentMember.gioi_tinh === 'Nam' ? 'Nữ' : 'Nam',
          doi_thu: this.currentMember.doi_thu,
          ngay_sinh: '',
          trang_thai: 'Còn sống',
          ngay_mat: '',
          ghi_chu: '',
          avatar: ''
        };
      }
      this.modal.hide();
      this.proposalModal.show();
    },
    triggerFileInput() {
      if (this.$refs.avatarFileInput) {
        this.$refs.avatarFileInput.click();
      }
    },
    uploadAvatarFile(event) {
      const file = event.target.files[0];
      if (!file) return;

      const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
      if (!allowedTypes.includes(file.type)) {
        toastr.warning('Chỉ chấp nhận định dạng hình ảnh (.png, .jpg, .jpeg)!');
        return;
      }
      if (file.size > 2 * 1024 * 1024) {
        toastr.warning('Dung lượng ảnh tối đa là 2MB!');
        return;
      }

      const token = localStorage.getItem('access_token');
      if (!token) {
        toastr.error('Vui lòng đăng nhập để tải ảnh lên!');
        return;
      }

      this.isUploading = true;
      const formData = new FormData();
      formData.append('avatar', file);

      axios.post('http://127.0.0.1:8000/api/thanh-vien/upload-avatar', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${token}`
        }
      })
      .then(res => {
        if (res.data.status) {
          this.proposalForm.avatar = res.data.url;
          toastr.success('Tải ảnh đại diện lên thành công!');
        } else {
          toastr.error(res.data.message || 'Lỗi tải ảnh lên.');
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Tải ảnh lên thất bại, vui lòng thử lại.');
      })
      .finally(() => {
        this.isUploading = false;
        if (this.$refs.avatarFileInput) {
          this.$refs.avatarFileInput.value = '';
        }
      });
    },
    removeAvatar() {
      this.proposalForm.avatar = '';
      if (this.$refs.avatarFileInput) {
        this.$refs.avatarFileInput.value = '';
      }
    },
    openDeleteProposal() {
      if (!this.isLoggedIn) {
        toastr.info('Vui lòng đăng nhập để gửi đề xuất xóa thành viên!');
        return;
      }
      if (this.modal) this.modal.hide();
      this.deleteProposalReason = '';
      this.showDeleteProposalModal = true;
    },
    closeDeleteProposalModal() {
      this.showDeleteProposalModal = false;
      this.deleteProposalReason = '';
    },
    submitDeleteProposal() {
      if (!this.deleteProposalReason.trim()) {
        toastr.warning('Vui lòng nhập lý do đề xuất xóa thành viên!');
        return;
      }
      const token = localStorage.getItem('access_token');
      if (!token) {
        toastr.error('Vui lòng đăng nhập để thực hiện đề xuất!');
        return;
      }
      const payload = {
        type: 'delete',
        thanh_vien_id: this.currentMember.id,
        data: {
          ho_ten: this.currentMember.ho_ten,
          ly_do_xoa: this.deleteProposalReason.trim(),
          chi_nhanh_id: this.currentMember.chi_nhanh_id
        }
      };

      axios.post('http://127.0.0.1:8000/api/de-xuat/create', payload, {
        headers: { Authorization: `Bearer ${token}` }
      })
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.closeDeleteProposalModal();
          this.loadData();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Gửi đề xuất xóa thất bại, vui lòng thử lại.');
      });
    },
    submitProposal() {
      if (!this.proposalForm.ho_ten) {
        toastr.warning('Vui lòng nhập họ và tên!');
        return;
      }
      const token = localStorage.getItem('access_token');
      if (!token) {
        toastr.error('Vui lòng đăng nhập để thực hiện đề xuất!');
        return;
      }
      const payload = {
        type: this.proposalType,
        thanh_vien_id: this.currentMember.id,
        data: {
          ho_ten: this.proposalForm.ho_ten,
          gioi_tinh: this.proposalForm.gioi_tinh,
          doi_thu: this.proposalForm.doi_thu,
          ngay_sinh: this.proposalForm.ngay_sinh || null,
          trang_thai: this.proposalForm.trang_thai,
          ngay_mat: this.proposalForm.trang_thai === 'Đã mất' ? (this.proposalForm.ngay_mat || null) : null,
          tinh_trang_hon_nhan: this.proposalForm.tinh_trang_hon_nhan || null,
          ghi_chu: this.proposalForm.ghi_chu,
          avatar: this.proposalForm.avatar || null,
          chi_nhanh_id: this.currentMember.chi_nhanh_id,
          me_id: this.proposalForm.other_parent_id || null
        }
      };

      axios.post('http://127.0.0.1:8000/api/de-xuat/create', payload, {
        headers: { Authorization: `Bearer ${token}` }
      })
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.proposalModal.hide();
          this.loadData();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Gửi đề xuất thất bại, vui lòng thử lại.');
      });
    },
    showQRCard(member) {
      this.activeMember = member;
      this.showQRModal = true;
    },
    closeQRModal() {
      this.showQRModal = false;
    },
    getMemberQRUrl(member) {
      if (!member || !member.id) return '';
      const origin = window.location.origin;
      return `${origin}/thanh-vien/detail/${member.id}`;
    },
    printQRCard() {
      const printWindow = window.open('', '_blank');
      printWindow.document.write(`
        <html>
        <head>
            <title>In mã QR - ${this.activeMember.ho_ten}</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    margin: 0;
                    background: white;
                }
                .qr-card-container {
                    width: 380px;
                    border: 4px solid #d4af37 !important;
                    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
                    color: white !important;
                    padding: 25px;
                    border-radius: 15px;
                    text-align: center;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
                .text-gold { color: #d4af37 !important; }
                .badge-gold-soft {
                    background: rgba(212, 175, 55, 0.2) !important;
                    color: #ffd891 !important;
                    border: 1px solid rgba(212, 175, 55, 0.3) !important;
                    font-size: 10px;
                    padding: 4px 8px;
                    border-radius: 4px;
                    margin-right: 5px;
                }
                .qr-frame-royal {
                    background: white !important;
                    padding: 15px;
                    border-radius: 10px;
                    display: inline-block;
                    border: 2px solid #d4af37;
                    margin-top: 15px;
                }
                .drop-shadow { text-shadow: 0 1px 2px rgba(0,0,0,0.5); }
                @media print {
                    body { height: auto; }
                    .qr-card-container {
                        page-break-inside: avoid;
                        margin: 0 auto;
                    }
                }
            </style>
        </head>
        <body>
            <div class="qr-card-container">
                <div style="border-bottom: 1px solid rgba(212, 175, 55, 0.3); padding-bottom: 10px; margin-bottom: 15px;">
                    <div class="text-gold" style="font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 2px;">Hệ Thống Gia Phả Số</div>
                    <div style="color: rgba(255,255,255,0.6); font-size: 10px; text-transform: uppercase;">Thẻ Nhận Diện Thành Viên</div>
                </div>
                
                <div style="display: flex; align-items: center; text-align: left; margin-bottom: 15px; gap: 15px;">
                    <img src="${this.activeMember.avatar || ('https://ui-avatars.com/api/?name=' + this.activeMember.ho_ten + '&background=d4af37&color=fff')}" style="border: 2px solid #d4af37; border-radius: 50%; width: 55px; height: 55px; object-fit: cover;">
                    <div>
                        <h4 class="drop-shadow" style="font-weight: bold; margin: 0; color: white;">${this.activeMember.ho_ten}</h4>
                        <div style="margin-top: 5px;">
                            <span class="badge-gold-soft">Đời ${this.activeMember.doi_thu}</span>
                            <span class="badge-gold-soft">${this.activeMember.gioi_tinh}</span>
                            <span class="badge-gold-soft">${this.activeMember.trang_thai}</span>
                        </div>
                    </div>
                </div>
                
                <div class="qr-frame-royal">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(this.getMemberQRUrl(this.activeMember))}" width="180" height="180">
                </div>
                <p style="color: rgba(255,255,255,0.5); font-size: 10px; margin-top: 15px; margin-bottom: 0; font-style: italic;">Quét mã để xem hồ sơ phả hệ & quan hệ dòng họ</p>
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() { window.close(); }, 500);
                };
            <\/script>
        </body>
        </html>
      `);
      printWindow.document.close();
    },
    downloadQRCard() {
      const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=${encodeURIComponent(this.getMemberQRUrl(this.activeMember))}`;
      fetch(qrUrl)
        .then(response => {
          if (!response.ok) throw new Error('Tải ảnh thất bại');
          return response.blob();
        })
        .then(blob => {
          const link = document.createElement('a');
          link.href = URL.createObjectURL(blob);
          link.download = `QRCode_${this.activeMember.ho_ten.replace(/\s+/g, '_')}.png`;
          link.click();
          URL.revokeObjectURL(link.href);
          toastr.success('Tải mã QR thành công!');
        })
        .catch(err => {
          toastr.error('Lỗi khi tải mã QR về!');
        });
    },
    openProposalsHistoryModal() {
      if (!this.isLoggedIn) {
        toastr.info('Vui lòng đăng nhập để xem lịch sử đề xuất!');
        return;
      }
      if (this.proposalsHistoryModal) this.proposalsHistoryModal.show();
      const token = localStorage.getItem('access_token');
      if (!token) return;
      this.isProposalsLoading = true;
      axios.get('http://127.0.0.1:8000/api/de-xuat/my-proposals', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.proposalsList = res.data.data;
          } else {
            toastr.error(res.data.message);
          }
        })
        .catch(err => {
          toastr.error('Không thể lấy lịch sử đề xuất.');
        })
        .finally(() => {
          this.isProposalsLoading = false;
        });
    },
    fmtDateTime(d) {
      if (!d) return '';
      const dt = new Date(d);
      return dt.toLocaleDateString('vi-VN') + ' ' + dt.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
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
    openLifeStatusModal() {
      if (!this.isLoggedIn) {
        toastr.info('Vui lòng đăng nhập để cập nhật trạng thái sống/mất!');
        return;
      }
      this.lifeStatusForm = {
        trang_thai: this.currentMember.trang_thai || 'Còn sống',
        ngay_mat: this.currentMember.ngay_mat ? this.currentMember.ngay_mat.substring(0, 10) : ''
      };
      if (this.modal) this.modal.hide();
      this.$nextTick(() => {
        if (!this.lifeStatusModalObj && window.bootstrap) {
          this.lifeStatusModalObj = new window.bootstrap.Modal(document.getElementById('lifeStatusModal'));
        }
        if (this.lifeStatusModalObj) this.lifeStatusModalObj.show();
      });
    },
    submitLifeStatus() {
      const token = localStorage.getItem('access_token');
      if (!token) return;
      axios.post(`http://127.0.0.1:8000/api/thanh-vien/${this.currentMember.id}/update-life-status`, {
        trang_thai: this.lifeStatusForm.trang_thai,
        ngay_mat: this.lifeStatusForm.trang_thai === 'Đã mất' ? (this.lifeStatusForm.ngay_mat || null) : null
      }, {
        headers: { Authorization: `Bearer ${token}` }
      })
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          if (this.lifeStatusModalObj) this.lifeStatusModalObj.hide();
          this.loadData();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể cập nhật trạng thái.');
      });    }
  }
};
</script>

<style>
/* KHÔNG SỬ DỤNG SCOPED ĐỂ CSS CÓ THỂ TÁC ĐỘNG VÀO RENDER FUNCTION */

.landing-page-wrapper {
  background: #0b1120;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
  position: relative;
  overflow: hidden;
}
.hero-background {
  position: absolute; inset: 0;
  background-image: linear-gradient(to bottom, rgba(11,17,32,0.5), rgba(11,17,32,1)), url('@/assets/images/bg_product.webp');
  background-size: cover; background-position: center;
  z-index: 0;
}
.glow-blob { position: absolute; width: 400px; height: 400px; border-radius: 50%; filter: blur(120px); opacity: 0.3; z-index: 1; }
.blob-purple { background: #1e0b3a; top: 0; left: -5%; }
.blob-blue { background: #031948; top: 30%; right: -5%; }

.page-content { position: relative; z-index: 2; padding: 0 20px 40px; max-width: 1500px; margin: 0 auto; }
.page-header { padding: 60px 0 30px; }
.subtitle-gradient { font-size: 0.8rem; font-weight: 700; letter-spacing: 0.2em; color: #d4af37; }
.text-gradient { background: linear-gradient(90deg,#fbff00,#ff8c00); -webkit-background-clip: text; background-clip: text; color: transparent; }

.tree-card-wrapper {
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 25px 70px rgba(0,0,0,0.5);
  position: relative;
}

.card-toolbar {
  display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; border-bottom: 1px solid #eee; background: #fff;
}
.toolbar-left { display: flex; align-items: center; gap: 10px; }
.tb-btn {
  width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e5e7eb; background: #f9fafb;
  color: #4b5563; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;
}
.tb-btn:hover { background: #d4af37; border-color: #d4af37; color: #fff; transform: translateY(-2px); }
.tb-btn-text {
  height: 40px; padding: 0 14px; border-radius: 12px; border: 1px solid #e5e7eb; background: #f9fafb;
  color: #4b5563; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 6px;
  cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.tb-btn-text.text-warning {
  color: #d4af37 !important;
  border-color: rgba(212, 175, 55, 0.25) !important;
  background: rgba(212, 175, 55, 0.05);
}
.tb-btn-text:hover { background: #d4af37; border-color: #d4af37; color: #fff !important; transform: translateY(-2px); }
.zoom-label { font-size: 0.9rem; font-weight: 800; color: #111827; min-width: 50px; text-align: center; }
.toolbar-hint { font-size: 0.85rem; color: #6b7280; }

.tree-viewport {
  height: 800px;
  background: #faf9f6;
  background-image: radial-gradient(rgba(212, 175, 55, 0.15) 1px, transparent 1px);
  background-size: 30px 30px;
  position: relative;
  overflow: hidden;
  cursor: grab;
}
.tree-viewport:active { cursor: grabbing; }

.tree-canvas {
  padding: 100px;
  transition: transform 0.05s linear;
  display: inline-block;
  min-width: 100%;
  text-align: center;
}

/* ─── CSS TREE STRUCTURE (FORCED HORIZONTAL) ─── */
.tree, .tree-ul, .tree-li {
  position: relative;
  transition: all 0.3s;
}

.tree-ul {
  padding-top: 50px !important;
  display: flex !important;
  flex-direction: row !important;
  flex-wrap: nowrap !important;
  justify-content: center !important;
  padding-left: 0 !important;
  margin-bottom: 0 !important;
  list-style: none !important;
}

.tree-li {
  float: none !important;
  text-align: center;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  padding: 50px 10px 0 10px !important;
  flex: 0 0 auto !important;
}

/* Vertical line from parent to current horizontal bar */
.tree-li::before, .tree-li::after {
  content: ''; position: absolute; top: 0; right: 50%;
  border-top: 2px solid #d4af37; width: 50%; height: 50px;
}
.tree-li::after { right: auto; left: 50%; border-left: 2px solid #d4af37; }

/* Remove lines for single child or edges */
.tree-li:only-child::after, .tree-li:only-child::before { display: none; }
.tree-li:only-child { padding-top: 0 !important; }
.tree-li:first-child::before, .tree-li:last-child::after { border: 0 none; }
.tree-li:last-child::before { border-right: 2px solid #d4af37; border-radius: 0 10px 0 0; }
.tree-li:first-child::after { border-radius: 10px 0 0 0; }

/* Vertical line down from current node to children ul */
.tree-ul::before {
  content: ''; position: absolute; top: 0; left: 50%;
  border-left: 2px solid #d4af37; width: 0; height: 50px;
}

/* ─── NODE STYLING ─── */
.tree-node-group {
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 10;
  margin: 0 auto;
  width: max-content;
}

.couple-wrapper {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0;
}

/* Ensure main card visually centered between two spouses */
.tree-node-card.main-centered {
  order: 2;
}
.tree-node-card.spouse-left { order: 1; }
.tree-node-card.spouse-right { order: 3; }

.tree-connector-h {
  width: 40px;
  height: 2px;
  background: #d4af37;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.connector-heart {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #fff;
  background: #d4af37;
  padding: 3px;
  border-radius: 50%;
  font-size: 14px;
  box-shadow: 0 2px 5px rgba(212, 175, 55, 0.4);
  z-index: 10;
}

/* The card itself */
.tree-node-card {
  background: #ffffff;
  border: 2px solid #d4af37;
  padding: 10px;
  border-radius: 12px;
  position: relative;
  box-shadow: 0 4px 15px rgba(212, 175, 55, 0.1);
  /* Fixed Uniform Dimensions */
  width: 220px;
  height: 90px;
  box-sizing: border-box;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: 0.3s ease;
  overflow: hidden;
}

.tree-node-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(212, 175, 55, 0.25);
  z-index: 100;
}

/* Generation Colors - Left border gradient */
.tree-node-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 6px;
    background: linear-gradient(to bottom, #d4af37, #fdfbf3);
    border-top-left-radius: 9px;
    border-bottom-left-radius: 9px;
}

.gen-1::before { background: linear-gradient(to bottom, #e1b12c, #fdfbf3); }
.gen-2::before { background: linear-gradient(to bottom, #d4af37, #fdfbf3); }
.gen-3::before { background: linear-gradient(to bottom, #b38d21, #fdfbf3); }
.gen-4::before { background: linear-gradient(to bottom, #957314, #fdfbf3); }
.gen-5::before { background: linear-gradient(to bottom, #e58e26, #fdfbf3); }


.tree-node-card.spouse {
  border-style: dashed;
  width: 220px;
  height: 90px;
}

.tree-node-card.is-dead {
  filter: grayscale(0.6);
  background: #f9f9f9;
  border-color: #bdc3c7;
}

/* Avatar styling inside card */
.node-avatar-container {
  width: 50px;
  height: 50px;
  flex-shrink: 0;
}

.node-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #d4af37;
  display: block;
}

.node-initials {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #d4af37;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 1.1rem;
  border: 2px solid #d4af37;
}

/* Card Content Styling */
.node-content {
  text-align: left;
  flex-grow: 1;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: center;
  height: 100%;
}

.node-name {
  font-weight: 700;
  font-size: 14px;
  color: #2f3640;
  margin-bottom: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.node-date {
  font-size: 11px;
  color: #7f8c8d;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.node-tag {
  font-size: 9px;
  font-weight: 700;
  color: #d4af37;
  text-transform: uppercase;
  margin-top: 3px;
  background: rgba(212, 175, 55, 0.1);
  padding: 2px 6px;
  border-radius: 4px;
  display: inline-block;
  border: 1px solid rgba(212, 175, 55, 0.3);
  width: max-content;
}

.spouse-tag {
  color: #e67e22;
  background: rgba(230, 126, 34, 0.1);
  border-color: rgba(230, 126, 34, 0.3);
}

/* Dummy Node for generation jumping */
.dummy-group {
  padding: 20px 0;
}
.dummy-line {
  width: 2px;
  height: 60px;
  background: #d4af37;
}

/* ─── UI ELEMENTS ─── */
.empty-state { text-align: center; padding: 120px; color: #9ca3af; }
.empty-state i { font-size: 4rem; display: block; margin-bottom: 15px; opacity: 0.3; }
.ld-ring { width: 50px; height: 50px; border: 4px solid #f3f4f6; border-top-color: #d4af37; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
@keyframes spin { to { transform: rotate(360deg); } }

.view-hint {
  position: absolute; bottom: 20px; right: 25px;
  font-size: 12px; color: #6b7280; font-weight: 500;
  background: rgba(255,255,255,0.9);
  padding: 6px 18px; border-radius: 30px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.radius-15 { border-radius: 15px !important; }
.radius-10 { border-radius: 10px !important; }

/* Custom Vue Modal Styling for QR Card */
.custom-modal-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.custom-modal-content {
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
}
.btn-close-custom {
    transition: all 0.2s ease;
}
.btn-close-custom:hover {
    transform: rotate(90deg);
}
.border-gold {
    border-color: #d4af37 !important;
}
.bg-royal {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
}
.qr-card-container {
    color: white !important;
    border-color: #d4af37 !important;
    background-size: cover;
    position: relative;
}
.card-header-royal {
    border-bottom: 1px solid rgba(212, 175, 55, 0.3) !important;
}
.badge-gold-soft {
    background: rgba(212, 175, 55, 0.15) !important;
    color: #ffd891 !important;
    border: 1px solid rgba(212, 175, 55, 0.25) !important;
}
.qr-frame-royal {
    background: white;
}
.btn-gold {
    background: #d4af37;
    color: #3b2c0c;
    border: none;
    transition: all 0.3s ease;
}
.btn-gold:hover {
    background: #e5c055;
    transform: translateY(-2px);
}
.card-watermark {
    position: absolute;
    top: -40px;
    right: -40px;
    width: 140px;
    height: 140px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
    pointer-events: none;
}
.drop-shadow {
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

/* Premium Avatar Upload Widget Styles */
.avatar-upload-widget {
  border: 1px solid #e5e7eb;
  background: #fdfdfd;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.avatar-upload-widget:hover {
  border-color: #3b82f6;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.08);
}
.upload-state-box {
  min-height: 110px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
.empty-state-box {
  background: rgba(59, 130, 246, 0.015);
  border-color: #d1d5db !important;
  transition: all 0.2s ease;
}
.empty-state-box:hover {
  background: rgba(59, 130, 246, 0.04);
  border-color: #3b82f6 !important;
}
.preview-thumbnail {
  width: 70px;
  height: 70px;
  object-fit: cover;
}
.cursor-pointer {
  cursor: pointer;
}
.italic {
  font-style: italic;
}
.font-9 {
  font-size: 10px !important;
  color: #888;
}
.border-dashed-gold {
  border: 1px dashed rgba(212, 175, 55, 0.4) !important;
}
.bg-light-gold {
  background-color: #fffcf3 !important;
}
.text-gold {
  color: #b58d1e !important;
}
.font-18 {
  font-size: 18px !important;
}
.font-10 {
  font-size: 10px !important;
}
.font-11 {
  font-size: 11px !important;
}
.font-12 {
  font-size: 12px !important;
}
.fw-extrabold {
  font-weight: 800 !important;
}
.radius-10 {
  border-radius: 10px !important;
}

/* Premium Member Modal Styling */
.premium-member-modal-content {
  border-radius: 24px !important;
  background: #ffffff;
  box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3) !important;
}
.premium-modal-header {
  border-radius: 24px 24px 0 0 !important;
  border-bottom: 2px solid #d4af37 !important;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
}
.btn-close-royal {
  background: transparent;
  border: none;
  color: rgba(255, 255, 255, 0.7);
  transition: all 0.3s ease;
  padding: 4px;
}
.btn-close-royal:hover {
  color: #d4af37;
  transform: rotate(90deg);
}
.font-serif {
  font-family: 'Playfair Display', serif !important;
}
.badge-generation-modal {
  background: rgba(212, 175, 55, 0.1) !important;
  color: #b58d1e !important;
  border: 1px solid rgba(212, 175, 55, 0.25);
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 11px !important;
  display: inline-block;
}
.modal-decor-light {
  position: absolute;
  top: 0; right: 0;
  width: 150px; height: 150px;
  background: radial-gradient(circle at top right, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
  pointer-events: none;
}
.modal-avatar-wrapper-premium {
  padding: 5px;
}
.profile-avatar-modal {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.profile-avatar-modal:hover {
  transform: scale(1.05);
}
.status-indicator-modal {
  position: absolute;
  bottom: 8px; right: 8px;
  width: 28px; height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #ffffff;
  color: white;
  font-size: 13px;
}
.status-indicator-modal.status-alive { background-color: #10b981; }
.status-indicator-modal.status-deceased { background-color: #ef4444; }

.relationship-term-modal {
  font-size: 1.8rem !important;
  font-weight: 900 !important;
  color: #b58d1e !important;
  text-shadow: 0 2px 4px rgba(212, 175, 55, 0.1);
  margin-top: 2px;
}

.vital-item-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0 !important;
  transition: all 0.2s ease;
}
.vital-item-card:hover {
  background: #ffffff;
  border-color: #cbd5e1 !important;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
}
.border-warning-gold {
  border-left: 4px solid #d4af37 !important;
}
.font-14 { font-size: 14px !important; }
.font-13 { font-size: 13px !important; }

/* Premium Action Buttons inside footer */
.btn-premium-action {
  font-weight: 700 !important;
  font-size: 12px !important;
  padding: 8px 14px !important;
  border-radius: 8px !important;
  transition: all 0.2s ease !important;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.btn-edit-premium {
  background: #ffffff !important;
  border: 1px solid #cbd5e1 !important;
  color: #475569 !important;
}
.btn-edit-premium:hover {
  background: #f1f5f9 !important;
  color: #0f172a !important;
  border-color: #94a3b8 !important;
}
.btn-child-premium {
  background: #ffffff !important;
  border: 1px solid #93c5fd !important;
  color: #2563eb !important;
}
.btn-child-premium:hover {
  background: #eff6ff !important;
  color: #1d4ed8 !important;
  border-color: #3b82f6 !important;
}
.btn-spouse-premium {
  background: #ffffff !important;
  border: 1px solid #fbcfe8 !important;
  color: #db2777 !important;
}
.btn-spouse-premium:hover {
  background: #fdf2f8 !important;
  color: #be185d !important;
  border-color: #f472b6 !important;
}
.btn-delete-premium {
  background: #ffffff !important;
  border: 1px solid #fca5a5 !important;
  color: #dc2626 !important;
}
.btn-delete-premium:hover {
  background: #fef2f2 !important;
  color: #b91c1c !important;
  border-color: #ef4444 !important;
}
.btn-qr-premium {
  background: #d4af37 !important;
  color: #3b2c0c !important;
  border: 1px solid #d4af37 !important;
}
.btn-qr-premium:hover {
  background: #c5a059 !important;
  border-color: #c5a059 !important;
  color: #2e2206 !important;
  transform: translateY(-1px);
}
.btn-premium-close {
  background: #64748b !important;
  color: #ffffff !important;
  border: none !important;
  font-weight: 700 !important;
  font-size: 12px !important;
  padding: 8px 18px !important;
  border-radius: 8px !important;
  transition: all 0.2s ease !important;
}
.btn-premium-close:hover {
  background: #475569 !important;
}

/* ─── MULTIPLE SPOUSES CHILDREN BRANCHES ALIGNMENT ─── */
.unions-wrapper {
  display: flex;
  flex-direction: row;
  justify-content: center;
  width: 100%;
  position: relative;
  margin-top: 50px;
}
.union-column {
  width: 50%;
  flex: 0 0 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}
.union-column-0::after {
  content: '';
  position: absolute;
  top: 0;
  right: 130px;
  left: 50%;
  height: 2px;
  background: #d4af37;
  z-index: 1;
}
.union-column-1::after {
  content: '';
  position: absolute;
  top: 0;
  left: 130px;
  right: 50%;
  height: 2px;
  background: #d4af37;
  z-index: 1;
}
.union-column-empty::after {
  display: none !important;
}
.tree-connector-h.spouse-connector::after {
  content: '';
  position: absolute;
  top: -10px;
  left: 50%;
  width: 2px;
  height: 105px;
  background: #d4af37;
  z-index: 1;
}
.has-multiple-spouses-li > .tree-ul::before {
  display: none !important;
}
.union-column > .tree-ul::before {
  display: block !important;
  content: '';
  position: absolute;
  top: 0 !important;
  left: 50% !important;
  border-left: 2px solid #d4af37 !important;
  width: 0 !important;
  height: 50px !important;
}
.union-empty-placeholder {
  width: 220px;
  height: 90px;
  visibility: hidden;
}

/* ─── DEMO MODE ALERT BANNER ─── */
.demo-alert-banner {
  background: rgba(212, 175, 55, 0.08);
  border: 1px solid rgba(212, 175, 55, 0.25);
  border-radius: 12px;
  padding: 15px 20px;
  color: #fff;
  max-width: 800px;
  margin: 0 auto;
  backdrop-filter: blur(8px);
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}
.demo-banner-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
}
.demo-icon {
  font-size: 1.5rem;
  color: #d4af37;
}
.demo-text {
  font-size: 0.95rem;
  color: #f3f4f6;
}
.demo-action-buttons {
  display: flex;
  gap: 10px;
}
.btn-login-demo {
  background: transparent;
  color: #d4af37;
  border: 1px solid #d4af37;
  font-weight: 700;
  border-radius: 8px;
  padding: 6px 15px;
  transition: all 0.3s;
}
.btn-login-demo:hover {
  background: #d4af37;
  color: #0b1120;
}
.btn-register-demo {
  background: #d4af37;
  color: #0b1120;
  border: 1px solid #d4af37;
  font-weight: 700;
  border-radius: 8px;
  padding: 6px 15px;
  transition: all 0.3s;
}
.btn-register-demo:hover {
  background: #c4a030;
  border-color: #c4a030;
  transform: translateY(-1px);
}
</style>
