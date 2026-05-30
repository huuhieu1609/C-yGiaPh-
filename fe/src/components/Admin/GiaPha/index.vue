<template>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 radius-10 overflow-hidden">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-git-branch text-primary"></i> Quản Lý Cây Gia Phả</h5>
                        </div>
                        <div class="col-md-9 text-md-end d-flex align-items-center justify-content-end gap-3 flex-wrap">
                            <!-- Partner Filter Select -->
                            <div class="d-flex align-items-center gap-2">
                                <div class="position-relative">
                                    <select class="form-select radius-30 border-2 px-4 pe-5 fw-bold text-dark shadow-sm select-partner-premium"
                                            ref="partnerSelect"
                                            :value="selectedPartner ? selectedPartner.id : ''"
                                            @change="onPartnerChange"
                                            style="height: 40px; min-width: 250px; cursor: pointer; border-color: #ffc107;">
                                        <option value="">-- Chọn Đối Tác --</option>
                                        <option v-for="item in listPartners" :key="item.id" :value="item.id" :disabled="!item.id_chi_nhanh">
                                            {{ item.nguoi_dung?.ho_ten || item.ten_goi }} - {{ item.dong_ho || 'Chưa thiết lập' }} {{ !item.id_chi_nhanh ? '(Chưa tạo cây)' : '' }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Search Bar -->
                            <div class="position-relative" style="width: 220px;">
                                <input type="text" class="form-control ps-5 radius-30 border-2" v-model="searchQuery" placeholder="Tìm thành viên...">
                                <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary"><i class="bx bx-search"></i></span>
                            </div>
                            
                            <!-- Zoom Controls -->
                            <div class="btn-group shadow-sm radius-30 overflow-hidden border">
                                <button class="btn btn-white px-3" @click="zoomOut" title="Thu nhỏ"><i class="bx bx-minus"></i></button>
                                <button class="btn btn-white px-2 fw-bold" style="min-width: 60px;">{{ Math.round(zoom * 100) }}%</button>
                                <button class="btn btn-white px-3" @click="zoomIn" title="Phóng to"><i class="bx bx-plus"></i></button>
                                <button class="btn btn-white px-3" @click="resetView" title="Đặt lại"><i class="bx bx-refresh"></i></button>
                            </div>

                            <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" @click="loadData" :disabled="isLoading" title="Làm mới phả hệ">
                                <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                            </button>
                         </div>
                    </div>
                </div>
                <div class="card-body p-0 position-relative">
                    <!-- Pan & Zoom Tree Container -->
                    <div class="tree-viewport" 
                         ref="viewport"
                         @wheel.prevent="handleWheel"
                         @mousedown="startPan"
                         @mousemove="doPan"
                         @mouseup="endPan"
                         @mouseleave="endPan"
                         :style="{ cursor: isPanning ? 'grabbing' : 'grab' }">
                        
                        <div class="tree-canvas" :style="canvasStyle">
                            <!-- State 1: No partner chosen yet -->
                            <div v-if="!selectedPartner" class="text-center py-5 mt-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="bx bx-user-voice fs-1 text-warning opacity-75 animate__animated animate__pulse animate__infinite"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Chưa Chọn Đối Tác Quản Lý</h4>
                                <p class="text-muted small mx-auto" style="max-width: 450px;">
                                    Vui lòng lựa chọn một tài khoản đối tác từ danh sách để tải dữ liệu cây gia phả và thực hiện quản lý thành viên dòng họ.
                                </p>
                                <button class="btn btn-warning radius-30 px-4 mt-3 fw-bold shadow-sm" @click="$refs.partnerSelect.focus()">
                                    <i class="bx bx-list-ul me-1"></i> Chọn Đối Tác Ngay
                                </button>
                            </div>

                            <!-- State 2: Partner chosen and has tree data -->
                            <div class="tree" v-else-if="treeData.length">
                                <ul>
                                    <TreeItem 
                                        v-for="member in treeData" 
                                        :key="member.id" 
                                        :member="member" 
                                        :listDoiTocHo="listDoiTocHo"
                                        :searchQuery="searchQuery"
                                        @edit="onEdit"
                                        @show-qr="showQRCard"
                                    />
                                </ul>
                            </div>

                            <!-- State 3: Partner chosen but no tree data found -->
                            <div v-else class="text-center py-5 mt-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="bx bx-git-repo-forked fs-1 text-muted opacity-25"></i>
                                </div>
                                <h5 class="text-muted">Chưa có dữ liệu thành viên cho nhánh này</h5>
                                <p class="text-muted small">Hãy kiểm tra bộ lọc hoặc bắt đầu thêm thành viên.</p>
                                <button class="btn btn-outline-primary btn-sm radius-30 px-4 mt-2" @click="openAddModal">Thêm ngay</button>
                            </div>
                        </div>
                    </div>

                    <!-- Mini Map or Navigation Hint -->
                    <div class="view-controls position-absolute bottom-0 end-0 m-3 p-2 bg-white bg-opacity-75 rounded-3 shadow-sm border small text-muted d-none d-md-block">
                        <i class="bx bx-mouse ms-1"></i> Cuộn để thu phóng • <i class="bx bx-move ms-1"></i> Kéo để di chuyển
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Thêm/Sửa Thành Viên (Giữ logic cũ của Admin) -->
        <div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content radius-15 shadow-lg border-0">
                    <div class="modal-header border-0 text-white radius-top-15" :class="isEditing ? 'bg-warning text-dark' : 'bg-primary'">
                        <h5 class="modal-title fw-bold">
                            {{ isEditing ? 'Chỉnh Sửa Thông Tin' : 'Thêm Thành Viên Mới' }}
                        </h5>
                        <button type="button" class="btn-close" :class="!isEditing ? 'btn-close-white' : ''" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-12 text-center mb-1">
                                <div class="position-relative d-inline-block">
                                    <img :src="avatarPreview || currentMember.avatar || ('https://ui-avatars.com/api/?name=' + (currentMember.ho_ten || 'A') + '&background=d4af37&color=fff')" class="rounded-circle border border-3 border-warning" alt="Avatar" width="100" height="100" style="object-fit: cover;">
                                    <label for="member-avatar-upload" class="btn btn-sm btn-warning rounded-circle position-absolute bottom-0 end-0 shadow-sm" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="Chọn ảnh">
                                        <i class="bx bx-camera text-dark"></i>
                                    </label>
                                    <input type="file" id="member-avatar-upload" @change="onAvatarChange" class="d-none" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Thuộc Dòng Họ</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.chi_nhanh_id">
                                    <option :value="null">-- Không xác định --</option>
                                    <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và Tên</label>
                                <input type="text" class="form-control radius-8 border-2 shadow-none" v-model="currentMember.ho_ten">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Giới tính</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.gioi_tinh">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Đời thứ</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.doi_thu">
                                    <option v-for="doi in listDoiTocHo" :key="doi.id" :value="doi.so_doi">
                                        Đời {{ doi.so_doi }} - {{ doi.ten_doi }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" class="form-control radius-8 border-2 shadow-none" v-model="currentMember.ngay_sinh">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Trạng thái</label>
                                <div class="d-flex gap-3 pt-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="Còn sống" v-model="currentMember.trang_thai" id="status1">
                                        <label class="form-check-label" for="status1">Còn sống</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="Đã mất" v-model="currentMember.trang_thai" id="status2">
                                        <label class="form-check-label" for="status2">Đã mất</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" v-if="currentMember.trang_thai === 'Đã mất'">
                                <label class="form-label fw-bold">Ngày mất</label>
                                <input type="date" class="form-control radius-8 border-2 shadow-none" v-model="currentMember.ngay_mat">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tình trạng hôn nhân</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.tinh_trang_hon_nhan">
                                    <option value="">-- Chưa rõ --</option>
                                    <option value="Độc thân">Độc thân</option>
                                    <option value="Đã kết hôn">Đã kết hôn</option>
                                    <option value="Ly hôn">Ly hôn</option>
                                    <option value="Góa">Góa</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Quan hệ với dòng họ</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.loai_quan_he">
                                    <option value="Chính">Thành viên chính (Con cháu)</option>
                                    <option value="Vợ/Chồng">Người phối ngẫu (Vợ/Chồng)</option>
                                </select>
                            </div>

                            <div class="col-md-6" v-if="currentMember.loai_quan_he === 'Chính'">
                                <label class="form-label fw-bold">Con của ông (Cha)</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.cha_id">
                                    <option :value="null">--- Thủy Tổ ---</option>
                                    <option v-for="m in allMembers" :key="m.id" :value="m.id" 
                                        v-show="m.id !== currentMember.id && m.loai_quan_he === 'Chính' && m.chi_nhanh_id === currentMember.chi_nhanh_id">
                                        {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6" v-if="currentMember.loai_quan_he === 'Chính' && currentMember.cha_id">
                                <label class="form-label fw-bold">Chọn mẹ còn lại (Vợ/Chồng)</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.me_id">
                                    <option :value="null">-- Chưa xác định / Khác --</option>
                                    <option v-for="s in getSpousesOfFather(currentMember.cha_id)" :key="s.id" :value="s.id">{{ s.ho_ten }}</option>
                                </select>
                            </div>

                            <div class="col-md-6" v-if="currentMember.loai_quan_he === 'Vợ/Chồng'">
                                <label class="form-label fw-bold">Là Vợ/Chồng của ai?</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.spouse_of_id">
                                    <option v-for="m in allMembers" :key="m.id" :value="m.id" 
                                        v-show="m.loai_quan_he === 'Chính' && m.chi_nhanh_id === currentMember.chi_nhanh_id">
                                        {{ m.ho_ten }}
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Ghi chú</label>
                                <textarea class="form-control radius-8 border-2 shadow-none" rows="2" v-model="currentMember.ghi_chu"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button v-if="isEditing" type="button" class="btn btn-outline-danger me-auto radius-8 px-3" @click="handleDelete">Xóa</button>
                        <button v-if="isEditing" type="button" class="btn btn-modern-cancel px-4" @click="showQRCardFromModal">Xem Mã QR</button>
                        <button type="button" class="btn btn-modern-cancel px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-modern-save px-4 fw-bold" :class="isEditing ? 'btn-warn' : 'btn-save'" @click="saveMember">
                            {{ isEditing ? 'Cập Nhật' : 'Lưu Lại' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue QR Card Modal -->
        <div v-if="showQRModal" class="custom-modal-backdrop animate__animated animate__fadeIn" @click.self="closeQRModal">
            <div class="custom-modal-content animate__animated animate__zoomIn p-4 rounded-4 shadow-2xl bg-white position-relative text-center" style="max-width: 420px; z-index: 1060;">
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


    </div>
</template>

<script>
import { defineComponent, h } from 'vue';
import axios from 'axios';
import toastr from 'toastr';

const TreeItem = defineComponent({
    name: 'TreeItem',
    props: ['member', 'listDoiTocHo', 'searchQuery'],
    emits: ['edit', 'show-qr'],
    data() {
        return {
            clickTimeout: null,
            isDoubleClick: false
        };
    },
    render() {
        const getTenDoi = (doi_thu) => {
            if (!this.listDoiTocHo || !this.listDoiTocHo.length) return '';
            const doi = this.listDoiTocHo.find(d => d.so_doi == doi_thu);
            return doi && doi.ten_doi ? ` (${doi.ten_doi})` : '';
        };
        const formatDate = (dateString) => {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        };

        const hasChildren = this.member.children && this.member.children.length > 0;

        if (this.member.isDummy) {
            const nodeGroup = h('div', { class: 'tree-node-group' }, [
                h('div', { 
                    class: 'tree-dummy-node',
                    style: 'width: 2px; height: 100px; background-color: #ddd; margin: 0 auto;'
                })
            ]);
            const children = hasChildren ? h('ul', 
                this.member.children.map(child => h(TreeItem, { 
                    member: child, 
                    listDoiTocHo: this.listDoiTocHo, 
                    searchQuery: this.searchQuery,
                    onEdit: (m) => this.$emit('edit', m),
                    onShowQr: (m) => this.$emit('show-qr', m)
                }))
            ) : null;
            return h('li', [nodeGroup, children]);
        }

        let children = null;
        const generationClass = `gen-${(this.member.doi_thu % 5) + 1}`;
        const isHighlighted = this.searchQuery && this.member.ho_ten.toLowerCase().includes(this.searchQuery.toLowerCase());
        
        // Build main card and spouse chunks; center main if exactly two spouses
        const makeMainCard = (order = null, extraClass = '') => h('div', { 
            class: ['tree-node-card', generationClass, extraClass, { 'principal': !this.member.cha_id, 'is-dead': this.member.trang_thai === 'Đã mất', 'highlighted': isHighlighted }],
            style: order !== null ? { order } : undefined,
            onClick: (e) => { e.stopPropagation(); clearTimeout(this.clickTimeout); this.isDoubleClick = false; this.clickTimeout = setTimeout(() => { if (!this.isDoubleClick) this.$emit('edit', this.member); }, 200); },
            onDblclick: (e) => { e.stopPropagation(); this.isDoubleClick = true; clearTimeout(this.clickTimeout); this.$emit('show-qr', this.member); }
        }, [ h('div', { class: 'node-avatar-container' }, [ h('img', { src: this.member.avatar ? this.member.avatar : ('https://ui-avatars.com/api/?name=' + this.member.ho_ten + '&background=d4af37&color=fff'), class: 'node-avatar shadow-sm' }) ]), h('div', { class: 'node-content' }, [ h('div', { class: 'node-name' }, this.member.ho_ten), this.member.ngay_sinh ? h('div', { class: 'node-date' }, formatDate(this.member.ngay_sinh)) : null, h('div', { class: 'node-tag' }, `Đời ${this.member.doi_thu}${getTenDoi(this.member.doi_thu)}`) ]), h('div', { class: 'node-edit-btn' }, [ h('i', { class: 'bx bx-pencil' }) ]) ]);

        const makeSpouseChunk = (spouse, order = null) => {
            const spouseClass = order === 1 ? 'spouse-left' : (order === 5 ? 'spouse-right' : '');
            return h('div', { class: ['tree-node-card', 'spouse', spouseClass, { 'is-dead': spouse.trang_thai === 'Đã mất', 'highlighted': this.searchQuery && spouse.ho_ten.toLowerCase().includes(this.searchQuery.toLowerCase()) }], style: order !== null ? { order: order } : undefined, onClick: (e) => { e.stopPropagation(); clearTimeout(this.clickTimeout); this.isDoubleClick = false; this.clickTimeout = setTimeout(() => { if (!this.isDoubleClick) this.$emit('edit', spouse); }, 200); }, onDblclick: (e) => { e.stopPropagation(); this.isDoubleClick = true; clearTimeout(this.clickTimeout); this.$emit('show-qr', spouse); } }, [ h('div', { class: 'node-avatar-container' }, [ h('img', { src: spouse.avatar ? spouse.avatar : ('https://ui-avatars.com/api/?name=' + spouse.ho_ten + '&background=d4af37&color=fff'), class: 'node-avatar shadow-sm' }) ]), h('div', { class: 'node-content' }, [ h('div', { class: 'node-name' }, spouse.ho_ten), h('div', { class: 'node-tag spouse-tag' }, 'Vợ/Chồng') ]), h('div', { class: 'node-edit-btn' }, [ h('i', { class: 'bx bx-pencil' }) ]) ]);
        };

        const coupleChildren = [];
        const hasMultipleSpouses = this.member.spouses && this.member.spouses.length === 2;

        let kids0 = [];
        let kids1 = [];
        if (hasMultipleSpouses) {
            const spouse0 = this.member.spouses[0];
            const spouse1 = this.member.spouses[1];
            const parentKey = this.member.gioi_tinh === 'Nam' ? 'me_id' : 'cha_id';
            kids1 = (this.member.children || []).filter(child => child[parentKey] == spouse1.id);
            const kids1Ids = kids1.map(k => k.id);
            kids0 = (this.member.children || []).filter(child => !kids1Ids.includes(child.id));
        }

        if (hasMultipleSpouses) {
            coupleChildren.push(makeSpouseChunk(this.member.spouses[0], 1));
            coupleChildren.push(h('div', { 
                class: ['tree-connector-h', 'spouse-connector', 'spouse-connector-0'], 
                style: { order: 2 } 
            }, [ h('i', { class: 'bx bxs-heart connector-heart' }) ]));
            coupleChildren.push(makeMainCard(3, 'main-centered'));
            coupleChildren.push(h('div', { 
                class: ['tree-connector-h', 'spouse-connector', 'spouse-connector-1'], 
                style: { order: 4 } 
            }, [ h('i', { class: 'bx bxs-heart connector-heart' }) ]));
            coupleChildren.push(makeSpouseChunk(this.member.spouses[1], 5));

            const col0 = h('div', { class: ['union-column', 'union-column-0', kids0.length === 0 ? 'union-column-empty' : ''] }, [
                kids0.length > 0
                    ? h('ul', kids0.map(child => h(TreeItem, { 
                        key: child.id,
                        member: child, 
                        listDoiTocHo: this.listDoiTocHo, 
                        searchQuery: this.searchQuery,
                        onEdit: (m) => this.$emit('edit', m),
                        onShowQr: (m) => this.$emit('show-qr', m)
                    })))
                    : h('div', { class: 'union-empty-placeholder' })
            ]);

            const col1 = h('div', { class: ['union-column', 'union-column-1', kids1.length === 0 ? 'union-column-empty' : ''] }, [
                kids1.length > 0
                    ? h('ul', kids1.map(child => h(TreeItem, { 
                        key: child.id,
                        member: child, 
                        listDoiTocHo: this.listDoiTocHo, 
                        searchQuery: this.searchQuery,
                        onEdit: (m) => this.$emit('edit', m),
                        onShowQr: (m) => this.$emit('show-qr', m)
                    })))
                    : h('div', { class: 'union-empty-placeholder' })
            ]);

            children = h('div', { class: 'unions-wrapper' }, [col0, col1]);
        } else {
            coupleChildren.push(makeMainCard());
            if (this.member.spouses && this.member.spouses.length) {
                this.member.spouses.forEach(sp => {
                    coupleChildren.push(h('div', { class: 'tree-connector-h' }, [ h('i', { class: 'bx bxs-heart connector-heart' }) ]));
                    coupleChildren.push(makeSpouseChunk(sp));
                });
            }
            children = hasChildren ? h('ul', 
                this.member.children.map(child => h(TreeItem, { 
                    key: child.id,
                    member: child, 
                    listDoiTocHo: this.listDoiTocHo, 
                    searchQuery: this.searchQuery,
                    onEdit: (m) => this.$emit('edit', m),
                    onShowQr: (m) => this.$emit('show-qr', m)
                }))
            ) : null;
        }

        const nodeGroup = h('div', { class: 'tree-node-group' }, [ h('div', { class: 'couple-wrapper' }, coupleChildren) ]);
        
        return h('li', { class: ['tree-li', { 'has-multiple-spouses-li': hasMultipleSpouses }] }, [nodeGroup, children]);
    }
});

export default {
    name: 'AdminGenealogy',
    components: { TreeItem },
    data() {
        return {
            allMembers: [],
            listChiNhanh: [],
            listDoiTocHo: [],
            listPartners: [],
            selectedPartner: null,
            showPartnerSelectorModal: false,
            partnerSearchQuery: '',
            selectedChiNhanh: null,
            currentMember: {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, me_id: null, gioi_tinh: 'Nam', chi_nhanh_id: null,
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, tinh_trang_hon_nhan: '', ghi_chu: '', avatar: null
            },
            avatarPreview: null,
            isEditing: false,
            modal: null,
            searchQuery: '',
            showQRModal: false,
            activeMember: {},
            isLoading: false,
            
            // Zoom & Pan state
            zoom: 1,
            posX: 0,
            posY: 0,
            isPanning: false,
            lastMouseX: 0,
            lastMouseY: 0
        }
    },
    computed: {
        filteredPartners() {
            if (!this.partnerSearchQuery) {
                return this.listPartners;
            }
            const q = this.partnerSearchQuery.toLowerCase();
            return this.listPartners.filter(item => {
                const partnerName = (item.nguoi_dung?.ho_ten || item.ten_goi || '').toLowerCase();
                const partnerEmail = (item.nguoi_dung?.email || '').toLowerCase();
                const clanName = (item.dong_ho || '').toLowerCase();
                return partnerName.includes(q) || partnerEmail.includes(q) || clanName.includes(q);
            });
        },
        treeData() {
            if (!this.selectedPartner || !this.selectedChiNhanh) {
                return [];
            }

            let list = JSON.parse(JSON.stringify(this.allMembers));
            
            // Branch filtering
            list = list.filter(item => 
                item.chi_nhanh_id == this.selectedChiNhanh || 
                (item.loai_quan_he === 'Vợ/Chồng')
            );
            
            const map = {};
            const roots = [];
            list.forEach(item => { map[item.id] = item; item.children = []; item.spouses = []; });
            
            // Helper for creating dummy nodes for skipped generations
            const getDummyNode = (parentId, doi_thu) => {
                let dummyId = 'dummy_' + parentId + '_' + doi_thu;
                if (!map[dummyId]) {
                    map[dummyId] = {
                        id: dummyId,
                        isDummy: true,
                        doi_thu: doi_thu,
                        children: [],
                        spouses: []
                    };
                    map[parentId].children.push(map[dummyId]);
                }
                return map[dummyId];
            };

            list.forEach(item => {
                if (item.loai_quan_he === 'Vợ/Chồng' && item.spouse_of_id && map[item.spouse_of_id]) {
                    map[item.spouse_of_id].spouses.push(item);
                    map[item.spouse_of_id].spouses.sort((a, b) => a.id - b.id);
                } else if (item.cha_id && map[item.cha_id]) {
                    let parent = map[item.cha_id];
                    // Handle generation jumps
                    if (item.doi_thu > parent.doi_thu + 1) {
                        let currentParent = parent;
                        for (let d = parent.doi_thu + 1; d < item.doi_thu; d++) {
                            currentParent = getDummyNode(currentParent.id, d);
                        }
                        currentParent.children.push(item);
                    } else {
                        parent.children.push(item);
                    }
                } else if (item.loai_quan_he === 'Chính') {
                    roots.push(item);
                }
            });
            return roots.filter(r => r.chi_nhanh_id == this.selectedChiNhanh);
        },
        canvasStyle() {
            return {
                transform: `translate(${this.posX}px, ${this.posY}px) scale(${this.zoom})`,
                transformOrigin: 'top center'
            };
        }
    },
    mounted() {
        if (window.bootstrap) {
            this.modal = new window.bootstrap.Modal(document.getElementById('memberModal'));
        }
        this.loadChiNhanh();
        this.loadPartners();
        this.loadDoiTocHo();
        this.loadData();
    },
    methods: {
        getHeaders() {
            return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
        },
        loadPartners() {
            axios.get('http://127.0.0.1:8000/api/admin/doi-tac/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listPartners = res.data.data;
                    }
                });
        },
        onPartnerChange(event) {
            const partnerId = event.target.value;
            if (!partnerId) {
                this.clearPartnerSelection();
                return;
            }
            const partner = this.listPartners.find(p => p.id == partnerId);
            if (partner) {
                this.selectPartner(partner);
            }
        },
        selectPartner(item) {
            this.selectedPartner = item;
            this.selectedChiNhanh = item.id_chi_nhanh;
            this.filterTree();
            toastr.success(`Đã chọn đối tác: ${item.nguoi_dung?.ho_ten || item.ten_goi}`);
        },
        clearPartnerSelection() {
            this.selectedPartner = null;
            this.selectedChiNhanh = null;
            this.filterTree();
            toastr.info('Đã gỡ chọn đối tác.');
        },
        getAvatarInitials(name) {
            if (!name) return 'DT';
            const parts = name.trim().split(/\s+/);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        },
        getAvatarBg(name) {
            if (!name) return '#d4af37';
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            const colors = [
                'linear-gradient(135deg, #d4af37 0%, #aa8c2c 100%)',
                'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)',
                'linear-gradient(135deg, #0f2027 0%, #203a43 100%)',
                'linear-gradient(135deg, #56ab2f 0%, #a8e063 100%)',
                'linear-gradient(135deg, #e65c00 0%, #f9d423 100%)',
                'linear-gradient(135deg, #833ab4 0%, #fd1d1d 100%)'
            ];
            const index = Math.abs(hash) % colors.length;
            return colors[index];
        },
        loadDoiTocHo() {
            axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listDoiTocHo = res.data.data.sort((a, b) => a.so_doi - b.so_doi);
                    }
                });
        },
        loadChiNhanh() {
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listChiNhanh = res.data.data;
                    }
                });
        },
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.allMembers = res.data.data;
                    }
                })
                .catch(err => {
                    toastr.error('Lỗi khi tải phả hệ!');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        filterTree() {
            // Reactively updates through treeData computed
        },
        // View Controls
        zoomIn() { if (this.zoom < 2) this.zoom += 0.1; },
        zoomOut() { if (this.zoom > 0.3) this.zoom -= 0.1; },
        resetView() { this.zoom = 1; this.posX = 0; this.posY = 0; },
        handleWheel(e) { e.preventDefault(); if (e.deltaY < 0) this.zoomIn(); else this.zoomOut(); },
        startPan(e) { if (e.button !== 0) return; this.isPanning = true; this.lastMouseX = e.clientX; this.lastMouseY = e.clientY; },
        doPan(e) { if (!this.isPanning) return; const dx = e.clientX - this.lastMouseX; const dy = e.clientY - this.lastMouseY; this.posX += dx; this.posY += dy; this.lastMouseX = e.clientX; this.lastMouseY = e.clientY; },
        endPan() { this.isPanning = false; },

        // CRUD
        getSpousesOfFather(fatherId) {
            if (!fatherId) return [];
            return this.allMembers.filter(m => m.loai_quan_he === 'Vợ/Chồng' && m.spouse_of_id == fatherId);
        },
        openAddModal() {
            if (!this.selectedPartner) {
                toastr.warning('Vui lòng chọn đối tác quản lý trước khi thêm thành viên!');
                return;
            }
            this.isEditing = false;
            this.currentMember = {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, me_id: null, gioi_tinh: 'Nam', chi_nhanh_id: this.selectedChiNhanh,
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, tinh_trang_hon_nhan: '', ghi_chu: '', avatar: null
            };
            this.avatarFile = null;
            this.avatarPreview = null;
            this.modal.show();
        },
        onEdit(member) {
            this.isEditing = true;
            this.currentMember = { ...member };
            this.avatarFile = null;
            this.avatarPreview = null;
            this.modal.show();
        },
        onAvatarChange(e) {
            const file = e.target.files[0];
            if (file) { this.avatarFile = file; this.avatarPreview = URL.createObjectURL(file); }
        },
        saveMember() {
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/thanh-vien/update' 
                : 'http://127.0.0.1:8000/api/thanh-vien/create';
            
            const formData = new FormData();
            for (let key in this.currentMember) {
                if (this.currentMember[key] !== null && this.currentMember[key] !== undefined) {
                    formData.append(key, this.currentMember[key]);
                }
            }
            if (this.avatarFile) formData.set('avatar', this.avatarFile);
            
            axios.post(url, formData, { 
                headers: { 
                    'Content-Type': 'multipart/form-data',
                    'Authorization': `Bearer ${localStorage.getItem('access_token')}`
                } 
            })
            .then(res => {
                if (res.data.status) {
                    toastr.success(res.data.message);
                    this.loadData();
                    this.modal.hide();
                } else { toastr.error(res.data.message); }
            });
        },
        handleDelete() {
            if (confirm('Xóa thành viên này?')) {
                axios.post('http://127.0.0.1:8000/api/thanh-vien/delete', { id: this.currentMember.id }, this.getHeaders())
                    .then(res => {
                        if (res.data.status) { toastr.success(res.data.message); this.loadData(); this.modal.hide(); }
                    });
            }
        },
        showQRCard(member) {
            this.activeMember = member;
            this.showQRModal = true;
        },
        showQRCardFromModal() {
            this.modal.hide();
            this.showQRCard(this.currentMember);
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
        }
    }
}
</script>

<style>
.tree-viewport {
    height: 700px;
    background: #faf9f6;
    background-image: radial-gradient(rgba(212, 175, 55, 0.15) 1px, transparent 1px);
    background-size: 30px 30px;
    position: relative;
    overflow: hidden;
    user-select: none;
}

.tree-canvas {
    padding: 100px;
    transition: transform 0.1s ease-out;
    display: inline-block;
    min-width: 100%;
}

.tree, .tree ul, .tree li {
    position: relative;
    transition: all 0.3s;
}

.tree ul { 
    padding-top: 50px; 
    display: flex !important; 
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    justify-content: center; 
    padding-left: 0; 
    margin-bottom: 0; 
}

.tree li { 
    text-align: center; 
    list-style-type: none; 
    padding: 50px 10px 0 10px; 
    flex: 0 0 auto !important;
}

/* Connecting Lines (Gold) */
.tree li::before, .tree li::after { 
    content: ''; 
    position: absolute; 
    top: 0; 
    right: 50%; 
    border-top: 2px solid #d4af37; 
    width: 50%; 
    height: 50px; 
}
.tree li::after { 
    right: auto; 
    left: 50%; 
    border-left: 2px solid #d4af37; 
}
.tree li:only-child::after, .tree li:only-child::before { display: none; }
.tree li:only-child { padding-top: 0; }
.tree li:first-child::before, .tree li:last-child::after { border: 0 none; }
.tree li:last-child::before { 
    border-right: 2px solid #d4af37; 
    border-radius: 0 10px 0 0; 
}
.tree li:first-child::after { 
    border-radius: 10px 0 0 0; 
}
.tree ul ul::before { 
    content: ''; 
    position: absolute; 
    top: 0; 
    left: 50%; 
    border-left: 2px solid #d4af37; 
    width: 0; 
    height: 50px; 
}

/* (restored default connector behavior) */

/* Node Styling */
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
.tree-node-card.main-centered { order: 2; }
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
    overflow: visible;
}

.quick-actions {
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 20;
    pointer-events: none;
}
.tree-node-card:hover .quick-actions {
    opacity: 1;
    pointer-events: auto;
    bottom: -15px;
}
.btn-action {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    color: #fff;
    font-size: 16px;
    padding: 0;
    transition: transform 0.2s;
    background: #d4af37;
}
.btn-action:hover {
    transform: scale(1.1);
    background: #c39b2e;
}

.tree-node-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(212, 175, 55, 0.25);
    z-index: 100;
}

.tree-node-card.highlighted {
    border-color: #ffc107 !important;
    background: #fffbeb !important;
    animation: pulse-border 1.5s infinite;
}

@keyframes pulse-border {
    0% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); }
    70% { box-shadow: 0 0 0 15px rgba(212, 175, 55, 0); }
    100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
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

.tree-node-card.is-dead {
    filter: grayscale(0.6);
    background: #f9f9f9;
    border-color: #bdc3c7;
}

.node-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #d4af37;
    margin-left: 8px;
    flex-shrink: 0;
}

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

.tree-node-card.spouse {
    border-style: dashed;
    /* Fixed Uniform Dimensions for Spouse */
    width: 220px;
    height: 90px;
}

.node-edit-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 24px;
    height: 24px;
    background: #fff;
    border: 1px solid #d4af37;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    opacity: 0;
    transition: 0.2s;
    color: #d4af37;
    font-size: 12px;
}

.tree-node-card:hover .node-edit-btn {
    opacity: 1;
}

.node-edit-btn:hover {
    background: #d4af37;
    color: #fff;
}

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
/* .has-multiple-spouses-li > ul::before {
  display: none !important;
}
.union-column > ul::before {
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

/* Circular reload button */
.btn-refresh-premium {
    background: rgba(0, 0, 0, 0.03) !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    width: 36px;
    height: 36px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    transition: all 0.25s ease;
}
.btn-refresh-premium:hover {
    transform: rotate(30deg) scale(1.05);
    border-color: #ffd700 !important;
    background: rgba(255, 215, 0, 0.05) !important;
}
.btn-refresh-premium:active {
    transform: scale(0.95);
}

/* Partner Selector Premium Styling */
.partner-list-scroll {
    scrollbar-width: thin;
    scrollbar-color: #d4af37 transparent;
}
.partner-list-scroll::-webkit-scrollbar {
    width: 6px;
}
.partner-list-scroll::-webkit-scrollbar-thumb {
    background-color: #d4af37;
    border-radius: 10px;
}
.partner-select-card {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.partner-select-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.15);
    border-color: #ffd700 !important;
}
.partner-select-card.border-warning {
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
}
.bg-light-gold {
    background-color: rgba(212, 175, 55, 0.05) !important;
}
.partner-avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    font-size: 16px;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}
</style>
