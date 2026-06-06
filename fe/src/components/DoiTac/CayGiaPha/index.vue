<template>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 radius-10 overflow-hidden">
                <div class="card-header bg-white py-3 border-0">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-git-branch text-primary"></i> Trực Quan Cây Gia Phả</h5>
                        </div>
                        <div class="col-md-9 text-md-end d-flex align-items-center justify-content-end gap-2 flex-wrap">
                            
                            <!-- Branch Selector (Global Filter) -->
                            <div class="me-2" style="width: 220px;">
                                <select class="form-select radius-30 border-2 shadow-none fw-bold" v-model="selectedChiNhanhId" @change="resetView">
                                    <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                                </select>
                            </div>

                            <!-- Search Bar -->
                            <div class="position-relative d-none d-lg-block" style="width: 200px;">
                                <input type="text" class="form-control ps-5 radius-30 border-2" v-model="searchQuery" placeholder="Tìm thành viên...">
                                <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary"><i class="bx bx-search"></i></span>
                            </div>
                            
                            <!-- Zoom Controls -->
                            <div class="btn-group shadow-sm radius-30 overflow-hidden border">
                                <button class="btn btn-white px-2" @click="zoomOut" title="Thu nhỏ"><i class="bx bx-minus"></i></button>
                                <button class="btn btn-white px-1 fw-bold" style="min-width: 50px; font-size: 13px;">{{ Math.round(zoom * 100) }}%</button>
                                <button class="btn btn-white px-2" @click="zoomIn" title="Phóng to"><i class="bx bx-plus"></i></button>
                                <button class="btn btn-white px-2" @click="resetView" title="Đặt lại"><i class="bx bx-refresh"></i></button>
                            </div>

                            <!-- Export Buttons -->
                            <div class="btn-group shadow-sm radius-30 overflow-hidden border me-2">
                                <button class="btn btn-white px-3" @click="exportToImage" title="Xuất Ảnh PNG">
                                    <i class="bx bx-image text-success"></i> Xuất Ảnh
                                </button>
                                <button class="btn btn-white px-3" @click="exportToPDF" title="Xuất PDF">
                                    <i class="bx bxs-file-pdf text-danger"></i> Xuất PDF
                                </button>
                            </div>

                            <button class="btn btn-primary radius-30 px-3 shadow-sm" @click="openAddModal">
                                <i class="bx bx-plus"></i> Thêm
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 position-relative">
                    <!-- Loading Overlay for Export -->
                    <div v-if="isExporting" class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex flex-column align-items-center justify-content-center" style="z-index: 1050 !important;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h5 class="mt-3 fw-bold text-dark">Đang xuất file...</h5>
                        <p class="text-muted small">Quá trình này có thể mất vài giây nếu cây gia phả lớn.</p>
                    </div>

                    <div v-if="listChiNhanh.length === 0" class="text-center py-5">
                        <div class="mb-4 mt-5">
                            <i class="bx bx-building-house fs-1 text-muted opacity-25" style="font-size: 100px !important;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Chưa có thông tin Dòng Họ!</h4>
                        <p class="text-muted">Bạn cần khởi tạo Dòng Họ (Chi Nhánh) để bắt đầu xây dựng cây gia phả.</p>
                        <router-link to="/doi-tac/dong-ho" class="btn btn-primary radius-30 px-5 mt-3 shadow-sm mb-5">
                            <i class="bx bx-plus-circle"></i> Khởi Tạo Ngay
                        </router-link>
                    </div>
                    <!-- Pan & Zoom Tree Container -->
                    <div v-else class="tree-viewport" 
                         ref="viewport"
                         @wheel.prevent="handleWheel"
                         @mousedown="startPan"
                         @mousemove="doPan"
                         @mouseup="endPan"
                         @mouseleave="endPan"
                         :style="{ cursor: isPanning ? 'grabbing' : 'grab' }">
                        
                        <div class="tree-canvas" :style="canvasStyle">
                            <div class="tree" v-if="treeData.length">
                                <ul>
                                    <TreeItem 
                                        v-for="member in treeData" 
                                        :key="member.id" 
                                        :member="member" 
                                        :listDoiTocHo="listDoiTocHo"
                                        :searchQuery="searchQuery"
                                        @edit="onEdit"
                                        @show-qr="showQRCard"
                                        @add-child="onAddChild"
                                        @add-spouse="onAddSpouse"
                                    />
                                </ul>
                            </div>
                            <div v-else class="text-center py-5 mt-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="bx bx-git-repo-forked fs-1 text-muted opacity-25"></i>
                                </div>
                                <h5 class="text-muted">Chưa có dữ liệu thành viên</h5>
                                <p class="text-muted small">Hãy bắt đầu bằng cách thêm thành viên đầu tiên (Thủy Tổ).</p>
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

        <!-- Modal Thêm/Sửa Thành Viên (Giữ nguyên logic cũ) -->
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
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và Tên</label>
                                <input type="text" class="form-control radius-8 border-2 shadow-none" v-model="currentMember.ho_ten" placeholder="Nguyễn Văn A">
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
                                        v-show="m.id !== currentMember.id && m.loai_quan_he === 'Chính'">
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
                                        v-show="m.loai_quan_he === 'Chính'">
                                        {{ m.ho_ten }}
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Ghi chú</label>
                                <textarea class="form-control radius-8 border-2 shadow-none" rows="2" v-model="currentMember.ghi_chu" placeholder="Thông tin thêm..."></textarea>
                            </div>

                            
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button v-if="isEditing" type="button" class="btn btn-outline-danger me-auto radius-8 px-3" @click="handleDelete">Xóa</button>
                        <button v-if="isEditing" type="button" class="btn btn-warning text-dark radius-8 px-3 fw-bold" style="background:#d4af37; border-color:#d4af37;" @click="showQRCardFromModal">Xem Mã QR</button>
                        <button type="button" class="btn btn-light px-4 radius-8" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn px-4 radius-8 fw-bold" :class="isEditing ? 'btn-warning' : 'btn-primary'" @click="saveMember">
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
        <!-- Delete Confirm Modal -->
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content radius-15 shadow-lg border-0">
                    <div class="modal-header border-0 bg-danger text-white radius-top-15 p-3">
                        <h6 class="modal-title fw-bold"><i class="bx bx-trash me-1"></i> Xác nhận xóa</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-3 text-start">
                        <p class="mb-0 text-dark">Bạn có chắc chắn muốn xóa thành viên này? Hành động này không thể hoàn tác.</p>
                    </div>
                    <div class="modal-footer border-0 p-3 justify-content-end">
                        <button type="button" class="btn btn-light radius-8 px-3" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-danger radius-8 px-3 fw-bold" @click="confirmDelete">Xác Nhận Xóa</button>
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
import html2canvas from 'html2canvas';
import { jsPDF } from 'jspdf';

const TreeItem = defineComponent({
    name: 'TreeItem',
    props: ['member', 'listDoiTocHo', 'searchQuery'],
    emits: ['edit', 'show-qr', 'add-child', 'add-spouse'],
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
        
        const generationClass = `gen-${(this.member.doi_thu % 5) + 1}`;
        const isHighlighted = this.searchQuery && this.member.ho_ten.toLowerCase().includes(this.searchQuery.toLowerCase());
        
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
                    onShowQr: (m) => this.$emit('show-qr', m),
                    onAddChild: (m) => this.$emit('add-child', m),
                    onAddSpouse: (m) => this.$emit('add-spouse', m)
                }))
            ) : null;
            return h('li', [nodeGroup, children]);
        }
        
        // Build main card and spouse cards; if exactly two spouses, place main between them
        const makeMainCard = (order = null, extraClass = '') => h('div', { 
            class: ['tree-node-card', generationClass, extraClass, { 
                'principal': !this.member.cha_id, 
                'is-dead': this.member.trang_thai === 'Đã mất',
                'highlighted': isHighlighted
            }],
            style: order !== null ? { order } : undefined,
            onClick: (e) => {
                e.stopPropagation();
                clearTimeout(this.clickTimeout);
                this.isDoubleClick = false;
                this.clickTimeout = setTimeout(() => {
                    if (!this.isDoubleClick) this.$emit('edit', this.member);
                }, 200);
            },
            onDblclick: (e) => { e.stopPropagation(); this.isDoubleClick = true; clearTimeout(this.clickTimeout); this.$emit('show-qr', this.member); }
        }, [
            h('div', { class: 'node-avatar-container' }, [ h('img', { src: this.member.avatar ? this.member.avatar : ('https://ui-avatars.com/api/?name=' + this.member.ho_ten + '&background=d4af37&color=fff'), class: 'node-avatar shadow-sm' }) ]),
            h('div', { class: 'node-content' }, [ h('div', { class: 'node-name' }, this.member.ho_ten), this.member.ngay_sinh ? h('div', { class: 'node-date' }, formatDate(this.member.ngay_sinh)) : null, h('div', { class: 'node-tag' }, `Đời ${this.member.doi_thu}${getTenDoi(this.member.doi_thu)}`) ]),
            h('div', { class: 'node-edit-btn' }, [ h('i', { class: 'bx bx-pencil' }) ]),
            h('div', { class: 'quick-actions' }, [
                h('button', { class: 'btn-action add-child', title: 'Thêm con', onClick: (e) => { e.stopPropagation(); this.$emit('add-child', this.member); } }, [ h('i', { class: 'bx bx-plus' }) ]),
                h('button', { class: 'btn-action add-spouse', title: 'Thêm vợ/chồng', onClick: (e) => { e.stopPropagation(); this.$emit('add-spouse', this.member); } }, [ h('i', { class: 'bx bxs-heart' }) ])
            ])
        ]);

        const makeSpouseChunk = (spouse, order = null) => {
            const spouseClass = order === 1 ? 'spouse-left' : (order === 5 ? 'spouse-right' : '');
            return h('div', { 
                class: ['tree-node-card', 'spouse', spouseClass, { 'is-dead': spouse.trang_thai === 'Đã mất', 'highlighted': this.searchQuery && spouse.ho_ten.toLowerCase().includes(this.searchQuery.toLowerCase()) }],
                style: order !== null ? { order: order } : undefined,
                onClick: (e) => { e.stopPropagation(); clearTimeout(this.clickTimeout); this.isDoubleClick = false; this.clickTimeout = setTimeout(() => { if (!this.isDoubleClick) this.$emit('edit', spouse); }, 200); },
                onDblclick: (e) => { e.stopPropagation(); this.isDoubleClick = true; clearTimeout(this.clickTimeout); this.$emit('show-qr', spouse); }
            }, [
                h('div', { class: 'node-avatar-container' }, [ h('img', { src: spouse.avatar ? spouse.avatar : ('https://ui-avatars.com/api/?name=' + spouse.ho_ten + '&background=d4af37&color=fff'), class: 'node-avatar shadow-sm' }) ]),
                h('div', { class: 'node-content' }, [ h('div', { class: 'node-name' }, spouse.ho_ten), h('div', { class: 'node-tag spouse-tag' }, spouse.gioi_tinh === 'Nữ' ? 'Vợ' : (spouse.gioi_tinh === 'Nam' ? 'Chồng' : 'Vợ/Chồng')) ]),
                h('div', { class: 'node-edit-btn' }, [ h('i', { class: 'bx bx-pencil' }) ]),
                h('div', { class: 'quick-actions' }, [
                    h('button', { class: 'btn-action add-child', title: 'Thêm con', onClick: (e) => { e.stopPropagation(); this.$emit('add-child', spouse); } }, [ h('i', { class: 'bx bx-plus' }) ]),
                    h('button', { class: 'btn-action add-spouse', title: 'Thêm vợ/chồng', onClick: (e) => { e.stopPropagation(); this.$emit('add-spouse', spouse); } }, [ h('i', { class: 'bx bxs-heart' }) ])
                ])
            ]);
        };

        let children = null;
        const coupleChildren = [];
        const hasMultipleSpouses = this.member.spouses && this.member.spouses.length === 2;

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

            const sp0 = this.member.spouses[0];
            const sp1 = this.member.spouses[1];
            const kids0 = hasChildren ? this.member.children.filter(c => c.me_id == sp0.id || c.cha_id == sp0.id) : [];
            const kids1 = hasChildren ? this.member.children.filter(c => c.me_id == sp1.id || c.cha_id == sp1.id) : [];

            const col0 = h('div', { class: ['union-column', 'union-column-0', kids0.length === 0 ? 'union-column-empty' : ''] }, [
                kids0.length > 0
                    ? h('ul', kids0.map(child => h(TreeItem, { 
                        key: child.id,
                        member: child, 
                        listDoiTocHo: this.listDoiTocHo, 
                        searchQuery: this.searchQuery,
                        onEdit: (m) => this.$emit('edit', m),
                        onShowQr: (m) => this.$emit('show-qr', m),
                        onAddChild: (m) => this.$emit('add-child', m),
                        onAddSpouse: (m) => this.$emit('add-spouse', m)
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
                        onShowQr: (m) => this.$emit('show-qr', m),
                        onAddChild: (m) => this.$emit('add-child', m),
                        onAddSpouse: (m) => this.$emit('add-spouse', m)
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
                    onShowQr: (m) => this.$emit('show-qr', m),
                    onAddChild: (m) => this.$emit('add-child', m),
                    onAddSpouse: (m) => this.$emit('add-spouse', m)
                }))
            ) : null;
        }

        const nodeGroup = h('div', { class: 'tree-node-group' }, [ h('div', { class: 'couple-wrapper' }, coupleChildren) ]);
        
        return h('li', { class: ['tree-li', { 'has-multiple-spouses-li': hasMultipleSpouses }] }, [nodeGroup, children]);
    }
});

export default {
    name: 'PartnerGenealogy',
    components: { TreeItem },
    data() {
        return {
            allMembers: [],
            listChiNhanh: [],
            selectedChiNhanhId: null,
            listDoiTocHo: [],
            currentMember: {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, me_id: null, gioi_tinh: 'Nam',
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null
            },
            avatarPreview: null,
            isEditing: false,
            modal: null,
            searchQuery: '',
            showQRModal: false,
            activeMember: {},
            // permissions/roles (removed)
            
            // Zoom & Pan state
            zoom: 1,
            posX: 0,
            posY: 0,
            isPanning: false,
            lastMouseX: 0,
            lastMouseY: 0,
            deleteModal: null,
            
            // Export state
            isExporting: false
        }
    },
    computed: {
        treeData() {
            let list = JSON.parse(JSON.stringify(this.allMembers));
            
            // Filter by branch if selected
            if (this.selectedChiNhanhId) {
                list = list.filter(m => m.chi_nhanh_id === this.selectedChiNhanhId);
            }

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
            return roots;
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
            const delEl = document.getElementById('deleteConfirmModal');
            if (delEl) this.deleteModal = new window.bootstrap.Modal(delEl);
        }
        this.loadDoiTocHo();
        this.loadChiNhanh();
        this.loadData();
    },
    methods: {
        getHeaders() {
            return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
        },
        loadDoiTocHo() {
            axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listDoiTocHo = res.data.data.sort((a, b) => a.so_doi - b.so_doi);
                    }
                });
        },
        loadData() {
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.allMembers = res.data.data;
                    }
                });
        },
        loadChiNhanh() {
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listChiNhanh = res.data.data;
                        if (this.listChiNhanh.length > 0 && !this.selectedChiNhanhId) {
                            this.selectedChiNhanhId = this.listChiNhanh[0].id;
                        }
                    }
                });
        },
        // View Control Methods
        zoomIn() { if (this.zoom < 2) this.zoom += 0.1; },
        zoomOut() { if (this.zoom > 0.3) this.zoom -= 0.1; },
        resetView() {
            this.zoom = 1;
            this.posX = 0;
            this.posY = 0;
        },
        async exportToImage() {
            if (this.listChiNhanh.length === 0 || this.treeData.length === 0) {
                toastr.warning('Không có dữ liệu để xuất!');
                return;
            }
            
            this.isExporting = true;
            
            // Save original view state
            const origZoom = this.zoom;
            const origPosX = this.posX;
            const origPosY = this.posY;
            
            // Reset view to capture everything
            this.resetView();
            
            // Wait for DOM to update
            await this.$nextTick();
            
            try {
                // Give a bit more time for any CSS transitions
                await new Promise(resolve => setTimeout(resolve, 500));
                
                const canvas = document.querySelector('.tree');
                if (!canvas) throw new Error("Không tìm thấy cây gia phả");
                
                const renderedCanvas = await html2canvas(canvas, {
                    scale: 2, // High resolution
                    useCORS: true,
                    backgroundColor: '#faf9f6',
                    logging: false
                });
                
                const imgData = renderedCanvas.toDataURL('image/png');
                
                const link = document.createElement('a');
                link.download = `Cay_Gia_Pha_${this.selectedChiNhanhId || 'Export'}.png`;
                link.href = imgData;
                link.click();
                
                toastr.success('Xuất ảnh thành công!');
            } catch (error) {
                console.error(error);
                toastr.error('Có lỗi xảy ra khi xuất ảnh.');
            } finally {
                // Restore original view state
                this.zoom = origZoom;
                this.posX = origPosX;
                this.posY = origPosY;
                this.isExporting = false;
            }
        },
        async exportToPDF() {
            if (this.listChiNhanh.length === 0 || this.treeData.length === 0) {
                toastr.warning('Không có dữ liệu để xuất!');
                return;
            }
            
            this.isExporting = true;
            
            const origZoom = this.zoom;
            const origPosX = this.posX;
            const origPosY = this.posY;
            
            this.resetView();
            await this.$nextTick();
            
            try {
                await new Promise(resolve => setTimeout(resolve, 500));
                
                const canvas = document.querySelector('.tree');
                if (!canvas) throw new Error("Không tìm thấy cây gia phả");
                
                const renderedCanvas = await html2canvas(canvas, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: '#faf9f6',
                    logging: false
                });
                
                const imgData = renderedCanvas.toDataURL('image/jpeg', 1.0);
                const imgProps = renderedCanvas; // width and height
                
                // Calculate PDF size matching the tree aspect ratio
                const pdf = new jsPDF({
                    orientation: imgProps.width > imgProps.height ? 'landscape' : 'portrait',
                    unit: 'px',
                    format: [imgProps.width, imgProps.height]
                });
                
                pdf.addImage(imgData, 'JPEG', 0, 0, imgProps.width, imgProps.height);
                pdf.save(`Cay_Gia_Pha_${this.selectedChiNhanhId || 'Export'}.pdf`);
                
                toastr.success('Xuất PDF thành công!');
            } catch (error) {
                console.error(error);
                toastr.error('Có lỗi xảy ra khi xuất PDF.');
            } finally {
                this.zoom = origZoom;
                this.posX = origPosX;
                this.posY = origPosY;
                this.isExporting = false;
            }
        },
        handleWheel(e) {
            e.preventDefault();
            if (e.deltaY < 0) this.zoomIn();
            else this.zoomOut();
        },
        startPan(e) {
            if (e.button !== 0) return; // Only left click
            this.isPanning = true;
            this.lastMouseX = e.clientX;
            this.lastMouseY = e.clientY;
        },
        doPan(e) {
            if (!this.isPanning) return;
            const dx = e.clientX - this.lastMouseX;
            const dy = e.clientY - this.lastMouseY;
            this.posX += dx;
            this.posY += dy;
            this.lastMouseX = e.clientX;
            this.lastMouseY = e.clientY;
        },
        endPan() {
            this.isPanning = false;
        },

        // Modal Methods
        getSpousesOfFather(fatherId) {
            if (!fatherId) return [];
            return this.allMembers.filter(m => m.loai_quan_he === 'Vợ/Chồng' && m.spouse_of_id == fatherId);
        },
        openAddModal() {
            if (!this.selectedChiNhanhId) {
                toastr.warning('Vui lòng chọn hoặc khởi tạo dòng họ trước khi thêm thành viên.');
                return;
            }
            this.isEditing = false;
            this.currentMember = {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, me_id: null, gioi_tinh: 'Nam',
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null,
                chi_nhanh_id: this.selectedChiNhanhId
            };
            this.avatarFile = null;
            this.avatarPreview = null;
            if (this.modal) this.modal.show();
        },
        onAddChild(parentMember) {
            this.isEditing = false;
            this.currentMember = {
                id: null, ho_ten: '', doi_thu: parentMember.doi_thu + 1, cha_id: parentMember.id, me_id: null, gioi_tinh: 'Nam',
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null,
                chi_nhanh_id: this.selectedChiNhanhId
            };
            this.avatarFile = null;
            this.avatarPreview = null;
            if (this.modal) this.modal.show();
        },
        onAddSpouse(mainMember) {
            this.isEditing = false;
            this.currentMember = {
                id: null, ho_ten: '', doi_thu: mainMember.doi_thu, cha_id: null, me_id: null, gioi_tinh: mainMember.gioi_tinh === 'Nam' ? 'Nữ' : 'Nam',
                loai_quan_he: 'Vợ/Chồng', spouse_of_id: mainMember.id, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null,
                chi_nhanh_id: this.selectedChiNhanhId
            };
            this.avatarFile = null;
            this.avatarPreview = null;
            if (this.modal) this.modal.show();
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
            if (file) {
                this.avatarFile = file;
                this.avatarPreview = URL.createObjectURL(file);
            }
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
            if (this.avatarFile) {
                formData.set('avatar', this.avatarFile);
            }
            
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
                } else {
                    toastr.error(res.data.message);
                }
            });
        },
        handleDelete() {
            if (this.deleteModal) {
                this.deleteModal.show();
            } else {
                if (confirm('Xóa thành viên này?')) {
                    this.confirmDelete();
                }
            }
        },
        confirmDelete() {
            axios.post('http://127.0.0.1:8000/api/thanh-vien/delete', { id: this.currentMember.id }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                        if (this.deleteModal) this.deleteModal.hide();
                        if (this.modal) this.modal.hide();
                    } else {
                        toastr.error(res.data.message);
                    }
                });
        },
        showQRCardFromModal() {
            if (this.modal) this.modal.hide();
            this.showQRCard(this.currentMember);
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
.has-multiple-spouses-li > ul::before {
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
</style>
