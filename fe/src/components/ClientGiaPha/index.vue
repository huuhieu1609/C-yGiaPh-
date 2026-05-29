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
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-15 shadow-lg border-0">
          <div class="modal-header border-0 bg-dark text-white" style="border-radius:15px 15px 0 0">
            <h5 class="modal-title fw-bold" style="color:#d4af37">
              <i class="bx bx-id-card me-2"></i>Thông Tin Thành Viên
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 text-center">
            <div class="modal-avatar-wrapper mb-3">
              <img
                :src="currentMember.avatar || `https://ui-avatars.com/api/?name=${currentMember.ho_ten}&background=d4af37&color=fff&size=128`"
                class="rounded-circle border border-3 border-warning"
                width="120" height="120" style="object-fit:cover"
                alt="avatar"
              >
            </div>
            <h4 class="fw-bold mb-1">{{ currentMember.ho_ten }}</h4>
            <span class="badge bg-light text-dark border mb-3 px-3 py-2">Đời thứ {{ currentMember.doi_thu }}</span>
            <div class="row text-start mt-3">
              <div class="col-6 mb-3">
                <span class="text-muted small d-block">Giới tính</span>
                <span class="fw-semibold">{{ currentMember.gioi_tinh }}</span>
              </div>
              <div class="col-6 mb-3">
                <span class="text-muted small d-block">Trạng thái</span>
                <span class="fw-semibold" :class="currentMember.trang_thai === 'Đã mất' ? 'text-danger' : 'text-success'">
                  {{ currentMember.trang_thai }}
                </span>
              </div>
              <div class="col-6 mb-3">
                <span class="text-muted small d-block">Ngày sinh</span>
                <span class="fw-semibold">{{ currentMember.ngay_sinh ? fmtDate(currentMember.ngay_sinh) : 'Không rõ' }}</span>
              </div>
              <div class="col-6 mb-3" v-if="currentMember.trang_thai === 'Đã mất'">
                <span class="text-muted small d-block">Ngày mất</span>
                <span class="fw-semibold">{{ currentMember.ngay_mat || 'Không rõ' }}</span>
              </div>
              <div class="col-12" v-if="currentMember.ghi_chu">
                <span class="text-muted small d-block">Ghi chú</span>
                <p class="mb-0 text-dark opacity-75">{{ currentMember.ghi_chu }}</p>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 justify-content-center gap-2 flex-wrap">
            <button class="btn btn-outline-warning px-3 radius-10 fw-bold" @click="openProposal('edit')">
              <i class="bx bx-edit-alt"></i> Đề xuất sửa
            </button>
            <button v-if="currentMember.loai_quan_he === 'Chính'" class="btn btn-outline-primary px-3 radius-10 fw-bold" @click="openProposal('add_child')">
              <i class="bx bx-plus-circle"></i> Đề xuất thêm con
            </button>
            <button v-if="currentMember.loai_quan_he === 'Chính'" class="btn btn-outline-info px-3 radius-10 fw-bold" @click="openProposal('add_spouse')">
              <i class="bx bx-heart"></i> Đề xuất thêm Vợ/Chồng
            </button>
            <button v-if="isDirectRelative" class="btn btn-outline-danger px-3 radius-10 fw-bold" @click="openLifeStatusModal">
              <i class="bx bx-heart-voice"></i> Cập nhật Sống/Mất
            </button>
            <button class="btn btn-warning text-dark px-3 radius-10 fw-bold" style="background:#d4af37; border-color:#d4af37;" @click="showQRCardFromModal">
              <i class="bx bx-qr"></i> Xem Mã QR
            </button>
            <button class="btn btn-secondary px-4 radius-10" data-bs-dismiss="modal">Đóng</button>
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
              <div class="col-md-6" v-if="proposalForm.trang_thai === 'Đã mất'">
                <label class="form-label fw-bold">Ngày mất</label>
                <input type="date" class="form-control radius-8 border-2 shadow-none" v-model="proposalForm.ngay_mat">
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

    // Arrange couple cards. If exactly two spouses, place the main person in the middle.
    const coupleChildren = [];
    const hasMultipleSpouses = m.spouses && m.spouses.length === 2;

    let children = null;
    const kids0 = [];
    const kids1 = [];

    if (hasMultipleSpouses) {
      if (m.children && m.children.length) {
        m.children.forEach(c => {
          if (c.me_id == m.spouses[1].id || c.cha_id == m.spouses[1].id) {
            kids1.push(c);
          } else {
            kids0.push(c);
          }
        });
      }

      // spouse - connector - main - connector - spouse
      coupleChildren.push(makeCard(m.spouses[0], true, 'spouse-left', 1));
      coupleChildren.push(h('div', { 
        class: [
          'tree-connector-h', 
          'spouse-connector-0',
          kids0.length > 0 ? 'spouse-connector' : ''
        ], 
        style: { order: 2 } 
      }, [ h('i', { class: 'bx bxs-heart connector-heart' }) ]));
      coupleChildren.push(makeCard(m, false, 'main-centered', 3));
      coupleChildren.push(h('div', { 
        class: [
          'tree-connector-h', 
          'spouse-connector-1',
          kids1.length > 0 ? 'spouse-connector' : ''
        ], 
        style: { order: 4 } 
      }, [ h('i', { class: 'bx bxs-heart connector-heart' }) ]));
      coupleChildren.push(makeCard(m.spouses[1], true, 'spouse-right', 5));

      const col0 = h('div', { class: 'union-column union-column-0' }, [
        kids0.length > 0
          ? h('ul', { class: 'tree-ul' }, kids0.map(c => h(TreeItem, { key: c.id, member: c, listDoiTocHo: this.listDoiTocHo, onView: x => this.$emit('view', x), onShowQr: x => this.$emit('show-qr', x) })))
          : h('div', { class: 'union-empty-placeholder' })
      ]);

      const col1 = h('div', { class: 'union-column union-column-1' }, [
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
        ghi_chu: ''
      },
       proposalsHistoryModal: null,
      proposalsList: [],
      isProposalsLoading: false,
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
      if (this.currentUser.vai_tro === 'Admin' || this.currentUser.is_doi_tac == 1) return true;

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
    this.loadDoiTocHo();
    this.loadData();
  },
  methods: {
    fmtDate,
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadDoiTocHo() {
      axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data', this.getHeaders())
        .then(r => { if (r.data.status) this.listDoiTocHo = r.data.data.sort((a,b)=>a.so_doi-b.so_doi); });
    },
    loadData() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
        .then(r => { if (r.data.status) this.allMembers = r.data.data; })
        .finally(() => { this.isLoading = false; });
    },
    onView(m) { this.currentMember = m; this.modal.show(); },
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
          ghi_chu: this.currentMember.ghi_chu || ''
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
          ghi_chu: ''
        };
      }
      this.modal.hide();
      this.proposalModal.show();
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
          ghi_chu: this.proposalForm.ghi_chu,
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
  width: 30px;
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
.union-column-0 > .tree-ul {
  transform: translateX(55px);
}
.union-column-1 > .tree-ul {
  transform: translateX(-55px);
}
.tree-connector-h.spouse-connector::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 2px;
  height: 95px;
  background: #d4af37;
  z-index: 1;
}
.has-multiple-spouses-li > .tree-ul::before {
  display: none !important;
}
.union-column > .tree-ul::before {
  display: none !important;
}
.union-empty-placeholder {
  width: 220px;
  height: 90px;
  visibility: hidden;
}
</style>
