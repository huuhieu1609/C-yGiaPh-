<template>
    <div class="row">
        <div class="col-12">
            <div class="card luxury-panel overflow-hidden border-0 shadow-sm">
                <div class="card-header bg-transparent py-4 position-relative z-2">
                    <div class="row align-items-center g-3">
                        <div class="col-md-3">
                            <h4 class="mb-0 fw-bold panel-title text-dark">
                                <i class="bx bx-git-branch text-emerald"></i> Quản Lý Cây Gia Phả
                            </h4>
                        </div>
                        <div class="col-md-9 text-md-end d-flex align-items-center justify-content-md-end gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <label class="small fw-bold text-uppercase text-secondary text-nowrap mb-0">Dòng họ:</label>
                                <div class="modern-select-box" style="width: 200px;">
                                    <select class="form-select modern-select fw-bold" v-model="selectedChiNhanh" @change="filterTree">
                                        <option :value="null">-- Tất cả --</option>
                                        <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="position-relative" style="width: 220px;">
                                <input type="text" class="form-control modern-search ps-5" v-model="searchQuery" placeholder="Tìm thành viên...">
                                <span class="position-absolute top-50 translate-middle-y start-0 ms-3 search-icon-btn"><i class="bx bx-search"></i></span>
                            </div>
                            
                            <div class="btn-group pill-control-group overflow-hidden">
                                <button class="btn btn-pill-action px-2_5" @click="zoomOut" title="Thu nhỏ"><i class="bx bx-minus"></i></button>
                                <button class="btn btn-pill-display px-2 fw-bold" style="min-width: 60px;">{{ Math.round(zoom * 100) }}%</button>
                                <button class="btn btn-pill-action px-2_5" @click="zoomIn" title="Phóng to"><i class="bx bx-plus"></i></button>
                                <button class="btn btn-pill-action px-2_5" @click="resetView" title="Đặt lại"><i class="bx bx-refresh"></i></button>
                            </div>

                            <button class="btn btn-luxury-primary px-4" @click="openAddModal">
                                <i class="bx bx-plus-circle"></i> Thêm Thành Viên
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 position-relative">
                    <div v-if="listChiNhanh.length === 0" class="text-center py-5 w-100 position-relative z-2">
                        <div class="mb-4 mt-5 empty-glow-icon">
                            <i class="bx bx-building-house text-muted opacity-20" style="font-size: 90px !important;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Dòng Họ Chưa Được Khởi Tạo!</h4>
                        <p class="text-secondary small">Hệ thống chưa ghi nhận dòng họ (chi nhánh) nào khả dụng.</p>
                    </div>
                    <div v-else-if="allMembers.length === 0" class="text-center py-5 w-100 position-relative z-2">
                        <div class="mb-4 mt-5 empty-glow-icon">
                            <i class="bx bx-git-branch text-muted opacity-20" style="font-size: 90px !important;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Cây Gia Phả Đang Trống!</h4>
                        <p class="text-secondary small">Vui lòng khởi tạo thành viên đầu tiên (Thủy Tổ) để xây dựng phả hệ.</p>
                        <button class="btn btn-luxury-primary px-5 mt-3 shadow-sm mb-5" @click="openAddModal">
                            <i class="bx bx-plus"></i> Thêm Thành Viên Ngay
                        </button>
                    </div>
                    <div v-else class="tree-viewport" 
                         ref="viewport"
                         @mousedown="startPan"
                         @mousemove="doPan"
                         @mouseup="endPan"
                         @mouseleave="endPan"
                         :style="{ cursor: isPanning ? 'grabbing' : 'grab' }">
                        
                        <div class="tree-canvas" :style="canvasStyle">
                            <div class="tree" v-if="processedTreeData.length">
                                <ul>
                                    <TreeItem 
                                        v-for="member in processedTreeData" 
                                        :key="member.id" 
                                        :member="member" 
                                        :listDoiTocHo="listDoiTocHo"
                                        :searchQuery="searchQuery"
                                        @edit="onEdit"
                                    />
                                </ul>
                            </div>
                            <div v-else class="text-center py-5 mt-5">
                                <div class="empty-glow-icon mb-3">
                                    <i class="bx bx-git-repo-forked text-muted opacity-20" style="font-size: 70px !important;"></i>
                                </div>
                                <h5 class="text-secondary">Chưa có dữ liệu thành viên cho nhánh này</h5>
                                <p class="text-muted small">Hãy kiểm tra bộ lọc hoặc bắt đầu thêm thành viên.</p>
                                <button class="btn btn-luxury-secondary btn-sm px-4 mt-2" @click="openAddModal">Thêm ngay</button>
                            </div>
                        </div>
                    </div>

                    <div class="view-controls position-absolute bottom-0 end-0 m-4 p-2 bg-luxury-tips rounded-3 small text-muted d-none d-md-block z-2">
                        <i class="bx bx-mouse ms-1 text-emerald"></i> Cuộn để thu phóng • <i class="bx bx-move ms-1 text-teal"></i> Kéo để di chuyển tự do
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content luxury-modal border-0 shadow-lg">
                    <div class="modal-header border-bottom p-4" :class="isEditing ? 'header-edit' : 'header-create'">
                        <h5 class="modal-title fw-bold text-dark">
                            {{ isEditing ? 'Chỉnh Sửa Thông Tin' : 'Thêm Thành Viên Mới' }}
                        </h5>
                        <div class="btn-close" style="cursor: pointer;" data-bs-dismiss="modal"></div>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-12 text-center mb-2">
                                <div class="position-relative d-inline-block">
                                    <img :src="avatarPreview || currentMember.avatar || ('https://ui-avatars.com/api/?name=' + (currentMember.ho_ten || 'A') + '&background=f3f4f6&color=111827')" class="rounded-circle profile-upload-preview" alt="Avatar" width="100" height="100" style="object-fit: cover;">
                                    <label for="member-avatar-upload" class="avatar-upload-trigger position-absolute bottom-0 end-0" title="Chọn ảnh">
                                        <i class="bx bx-camera text-secondary"></i>
                                    </label>
                                    <input type="file" id="member-avatar-upload" @change="onAvatarChange" class="d-none" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="modern-field-label">Thuộc Dòng Họ (Chi Nhánh)</label>
                                <div class="modern-select-box">
                                    <select class="form-select modern-select fw-semibold" v-model="currentMember.chi_nhanh_id">
                                        <option :value="null">-- Không xác định --</option>
                                        <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="modern-field-label">Họ và Tên</label>
                                <input type="text" class="form-control modern-input" v-model="currentMember.ho_ten" placeholder="Nhập họ tên...">
                            </div>
                            <div class="col-md-3">
                                <label class="modern-field-label">Giới tính</label>
                                <select class="form-select modern-input" v-model="currentMember.gioi_tinh">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="modern-field-label">Đời thứ</label>
                                <select class="form-select modern-input" v-model="currentMember.doi_thu">
                                    <option v-for="doi in listDoiTocHo" :key="doi.id" :value="doi.so_doi">
                                        Đời {{ doi.so_doi }} - {{ doi.ten_doi }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="modern-field-label">Ngày sinh</label>
                                <input type="date" class="form-control modern-input" v-model="currentMember.ngay_sinh">
                            </div>

                            <div class="col-md-6">
                                <label class="modern-field-label">Trạng thái hiện tại</label>
                                <div class="d-flex gap-4 pt-2">
                                    <div class="form-check modern-radio">
                                        <input class="form-check-input shadow-none" type="radio" value="Còn sống" v-model="currentMember.trang_thai" id="status1">
                                        <label class="form-check-label text-dark" for="status1">Còn sống</label>
                                    </div>
                                    <div class="form-check modern-radio">
                                        <input class="form-check-input shadow-none" type="radio" value="Đã mất" v-model="currentMember.trang_thai" id="status2">
                                        <label class="form-check-label text-dark" for="status2">Đã mất</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" v-if="currentMember.trang_thai === 'Đã mất'">
                                <label class="modern-field-label">Ngày tạ thế</label>
                                <input type="date" class="form-control modern-input" v-model="currentMember.ngay_mat">
                            </div>

                            <div class="col-md-6">
                                <label class="modern-field-label">Quan hệ với dòng họ</label>
                                <select class="form-select modern-input" v-model="currentMember.loai_quan_he">
                                    <option value="Chính">Thành viên chính (Con cháu)</option>
                                    <option value="Vợ/Chồng">Người phối ngẫu (Vợ/Chồng)</option>
                                </select>
                            </div>

                            <div class="col-md-6" v-if="currentMember.loai_quan_he === 'Chính'">
                                <label class="modern-field-label">Con của ông (Cha)</label>
                                <select class="form-select modern-input" v-model="currentMember.cha_id">
                                    <option :value="null">--- Thủy Tổ (Đời 1) ---</option>
                                    <option v-for="m in allMembers" :key="m.id" :value="m.id" 
                                        v-show="m.id !== currentMember.id && m.loai_quan_he === 'Chính' && m.chi_nhanh_id === currentMember.chi_nhanh_id">
                                        {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6" v-if="currentMember.loai_quan_he === 'Vợ/Chồng'">
                                <label class="modern-field-label">Là Vợ/Chồng của ai?</label>
                                <select class="form-select modern-input" v-model="currentMember.spouse_of_id">
                                    <option v-for="m in allMembers" :key="m.id" :value="m.id" 
                                        v-show="m.loai_quan_he === 'Chính' && m.chi_nhanh_id === currentMember.chi_nhanh_id">
                                        {{ m.ho_ten }}
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-12">
                                <label class="modern-field-label">Ghi chú tộc phả</label>
                                <textarea class="form-control modern-input" rows="2" v-model="currentMember.ghi_chu" placeholder="Thông tin chi tiết..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-4">
                        <button v-if="isEditing" type="button" class="btn btn-modern-danger me-auto px-4" @click="handleDelete">Xóa nút</button>
                        <button type="button" class="btn btn-modern-cancel px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-modern-save px-4 fw-bold" :class="isEditing ? 'btn-warn' : 'btn-save'" @click="saveMember">
                            {{ isEditing ? 'Cập Nhật' : 'Lưu Lại' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent, h, shallowRef, triggerRef } from 'vue';
import axios from 'axios';
import toastr from 'toastr';

const TreeItem = defineComponent({
    name: 'TreeItem',
    props: {
        member: { type: Object, required: true },
        listDoiTocHo: { type: Array, default: () => [] },
        searchQuery: { type: String, default: '' }
    },
    emits: ['edit'],
    setup(props, context) {
        return () => {
            const getTenDoi = (doi_thu) => {
                if (!props.listDoiTocHo || !props.listDoiTocHo.length) return '';
                const doi = props.listDoiTocHo.find(d => d.so_doi == doi_thu);
                return doi && doi.ten_doi ? ` (${doi.ten_doi})` : '';
            };

            const formatDate = (dateString) => {
                if (!dateString) return '';
                return new Date(dateString).toLocaleDateString('vi-VN');
            };

            const hasChildren = props.member.children && props.member.children.length > 0;

            if (props.member.isDummy) {
                const nodeGroup = h('div', { class: 'tree-node-group' }, [
                    h('div', { 
                        class: 'tree-dummy-node',
                        style: 'width: 1.5px; height: 100px; background-color: rgba(0,0,0,0.06); margin: 0 auto;'
                    })
                ]);
                const children = hasChildren ? h('ul', 
                    props.member.children.map(child => h(TreeItem, { 
                        key: child.id,
                        member: child, 
                        listDoiTocHo: props.listDoiTocHo, 
                        searchQuery: props.searchQuery,
                        onEdit: (m) => context.emit('edit', m) 
                    }))
                ) : null;
                return h('li', [nodeGroup, children]);
            }

            const generationClass = `gen-${(props.member.doi_thu % 5) + 1}`;
            const isHighlighted = props.searchQuery && props.member.ho_ten.toLowerCase().includes(props.searchQuery.toLowerCase());
            
            // Render nội dung Avatar bọc thẻ span chống bóp vỡ layout chữ viết tắt
            const renderAvatarContent = (item) => {
                if (item.avatar) {
                    return h('img', { src: item.avatar, class: 'node-avatar-img' });
                }
                const nameParts = item.ho_ten.trim().split(' ');
                let text = 'A';
                if (nameParts.length >= 2) {
                    text = nameParts[nameParts.length - 2].charAt(0) + nameParts[nameParts.length - 1].charAt(0);
                } else if (nameParts.length === 1) {
                    text = nameParts[0].substring(0, 2);
                }
                return h('span', { class: 'avatar-text-initials' }, text.toUpperCase());
            };

            const nodeGroup = h('div', { class: 'tree-node-group' }, [
                h('div', { 
                    class: ['tree-node-card', generationClass, { 
                        'principal': !props.member.cha_id, 
                        'is-dead': props.member.trang_thai === 'Đã mất',
                        'highlighted': isHighlighted
                    }],
                    onClick: (e) => { e.stopPropagation(); context.emit('edit', props.member); }
                }, [
                    h('div', { class: 'node-avatar-container' }, [
                        h('div', { class: 'node-avatar' }, [renderAvatarContent(props.member)]),
                        h('div', { class: 'avatar-neon-ring' })
                    ]),
                    h('div', { class: 'node-content' }, [
                        h('div', { class: 'node-name' }, props.member.ho_ten),
                        props.member.ngay_sinh ? h('div', { class: 'node-date' }, formatDate(props.member.ngay_sinh)) : null,
                        h('div', { class: 'node-tag' }, `Đời ${props.member.doi_thu}${getTenDoi(props.member.doi_thu)}`)
                    ]),
                    h('div', { class: 'node-edit-btn' }, [
                        h('i', { class: 'bx bx-edit-alt' })
                    ])
                ]),
                props.member.spouses && props.member.spouses.length ? props.member.spouses.map(spouse => {
                    const isSpouseHighlighted = props.searchQuery && spouse.ho_ten.toLowerCase().includes(props.searchQuery.toLowerCase());
                    return [
                        h('div', { class: 'tree-connector-h', key: 'conn_' + spouse.id }),
                        h('div', { 
                            key: 'spouse_' + spouse.id,
                            class: ['tree-node-card spouse', { 
                                'is-dead': spouse.trang_thai === 'Đã mất',
                                'highlighted': isSpouseHighlighted
                            }],
                            onClick: (e) => { e.stopPropagation(); context.emit('edit', spouse); }
                        }, [
                            h('div', { class: 'node-avatar-container' }, [
                                h('div', { class: 'node-avatar' }, [renderAvatarContent(spouse)]),
                                h('div', { class: 'avatar-neon-ring' })
                            ]),
                            h('div', { class: 'node-content' }, [
                                h('div', { class: 'node-name' }, spouse.ho_ten),
                                h('div', { class: 'node-tag spouse-tag' }, 'Phối ngẫu')
                            ]),
                            h('div', { class: 'node-edit-btn' }, [
                                h('i', { class: 'bx bx-edit-alt' })
                            ])
                        ])
                    ];
                }) : null
            ]);
            
            const children = hasChildren ? h('ul', 
                props.member.children.map(child => h(TreeItem, { 
                    key: child.id,
                    member: child, 
                    listDoiTocHo: props.listDoiTocHo, 
                    searchQuery: props.searchQuery,
                    onEdit: (m) => context.emit('edit', m) 
                }))
            ) : null;
            
            return h('li', [nodeGroup, children]);
        };
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
            selectedChiNhanh: null,
            currentMember: {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, gioi_tinh: 'Nam', chi_nhanh_id: null,
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null
            },
            avatarPreview: null,
            isEditing: false,
            modal: null,
            searchQuery: '',
            zoom: 1,
            posX: 0,
            posY: 0,
            isPanning: false,
            lastMouseX: 0,
            lastMouseY: 0,
            processedTreeData: shallowRef([])
        }
    },
    watch: {
        allMembers: {
            handler() { this.buildSafeTree(); },
            deep: true
        },
        selectedChiNhanh() {
            this.buildSafeTree();
        }
    },
    computed: {
        canvasStyle() {
            return {
                transform: `translate(${this.posX}px, ${this.posY}px) scale(${this.zoom})`,
                transformOrigin: 'top center'
            };
        }
    },
    mounted() {
        if (window.bootstrap) {
            const modalElement = document.getElementById('memberModal');
            if (modalElement) {
                document.body.appendChild(modalElement);
                this.modal = new window.bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true
                });
            }
        }
        this.loadChiNhanh();
        this.loadDoiTocHo();
        this.loadData();
        if (this.$refs.viewport) {
            this.$refs.viewport.addEventListener('wheel', this.handleWheel, { passive: false });
        }
    },
    beforeUnmount() {
        if (this.$refs.viewport) {
            this.$refs.viewport.removeEventListener('wheel', this.handleWheel);
        }
        const modalElement = document.getElementById('memberModal');
        if (modalElement && modalElement.parentNode === document.body) {
            document.body.removeChild(modalElement);
        }
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(b => b.remove());
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
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
        loadChiNhanh() {
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => { if (res.data.status) this.listChiNhanh = res.data.data; });
        },
        loadData() {
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.allMembers = res.data.data;
                        this.buildSafeTree();
                    }
                });
        },
        buildSafeTree() {
            if (!this.allMembers || !this.allMembers.length) {
                this.processedTreeData = [];
                return;
            }

            let list = JSON.parse(JSON.stringify(this.allMembers));
            if (this.selectedChiNhanh) {
                list = list.filter(item => 
                    item.chi_nhanh_id == this.selectedChiNhanh || item.loai_quan_he === 'Vợ/Chồng'
                );
            }

            const map = {};
            const roots = [];
            
            list.forEach(item => {
                map[item.id] = item;
                item.children = [];
                item.spouses = [];
            });

            list.forEach(item => {
                // ƯU TIÊN 2 QUAN HỆ: Đẩy vào mảng spouses nằm ngang, CHẶN không cho nhảy kép vào nhánh con children
                if (item.loai_quan_he === 'Vợ/Chồng' && item.spouse_of_id && map[item.spouse_of_id]) {
                    map[item.spouse_of_id].spouses.push(item);
                } else if (item.cha_id && map[item.cha_id]) {
                    if (item.id == item.cha_id) return; 

                    let parent = map[item.cha_id];
                    if (item.doi_thu > parent.doi_thu + 1) {
                        let currentParent = parent;
                        for (let d = parent.doi_thu + 1; d < item.doi_thu; d++) {
                            let dummyId = 'dummy_' + currentParent.id + '_' + d;
                            if (!map[dummyId]) {
                                map[dummyId] = { id: dummyId, isDummy: true, doi_thu: d, children: [], spouses: [] };
                                currentParent.children.push(map[dummyId]);
                            }
                            currentParent = map[dummyId];
                        }
                        currentParent.children.push(item);
                    } else {
                        parent.children.push(item);
                    }
                } else if (item.loai_quan_he === 'Chính') {
                    roots.push(item);
                }
            });

            this.processedTreeData = roots;
            triggerRef(this.processedTreeData);
        },
        filterTree() { this.buildSafeTree(); },
        zoomIn() { if (this.zoom < 2) this.zoom += 0.1; },
        zoomOut() { if (this.zoom > 0.3) this.zoom -= 0.1; },
        resetView() { this.zoom = 1; this.posX = 0; this.posY = 0; },
        handleWheel(e) { e.preventDefault(); if (e.deltaY < 0) this.zoomIn(); else this.zoomOut(); },
        startPan(e) { if (e.button !== 0) return; this.isPanning = true; this.lastMouseX = e.clientX; this.lastMouseY = e.clientY; },
        doPan(e) { if (!this.isPanning) return; const dx = e.clientX - this.lastMouseX; const dy = e.clientY - this.lastMouseY; this.posX += dx; this.posY += dy; this.lastMouseX = e.clientX; this.lastMouseY = e.clientY; },
        endPan() { this.isPanning = false; },

        openAddModal() {
            this.isEditing = false;
            this.currentMember = {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, gioi_tinh: 'Nam', chi_nhanh_id: this.selectedChiNhanh,
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null
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
            const url = this.isEditing ? 'http://127.0.0.1:8000/api/thanh-vien/update' : 'http://127.0.0.1:8000/api/thanh-vien/create';
            const formData = new FormData();
            for (let key in this.currentMember) {
                if (this.currentMember[key] !== null && this.currentMember[key] !== undefined) formData.append(key, this.currentMember[key]);
            }
            if (this.avatarFile) formData.set('avatar', this.avatarFile);
            
            axios.post(url, formData, { headers: { 'Content-Type': 'multipart/form-data', Authorization: `Bearer ${localStorage.getItem('access_token')}` } })
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
        }
    }
}
</script>
<style scoped>
/* ─── SYSTEM CORE LIGHT VARIABLES ─── */
.luxury-panel {
  background: rgba(255, 255, 255, 0.85) !important;
  border: 1px solid rgba(0, 0, 0, 0.04) !important;
  border-radius: 24px !important;
  box-shadow: 0 10px 35px rgba(0, 0, 0, 0.02);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  margin: 0 !important;
  width: 100%;
}

.panel-title {
  font-size: 15px;
  letter-spacing: 0.3px;
}
.text-emerald { color: #059669 !important; }

/* HỆ MÀU SẮC ĐỊNH DANH THEO ĐỜI (PASTEL LUXURY) */
.tree-node-card {
  --gen-1-color: #4f46e5; /* Đời 1: Tím Indigo */
  --gen-2-color: #db2777; /* Đời 2: Hồng Rose */
  --gen-3-color: #0d9488; /* Đời 3: Xanh Teal */
  --gen-4-color: #d97706; /* Đời 4: Hổ Phách */
  --gen-5-color: #059669; /* Đời 5: Lục Bảo */
}

/* ─── CONTROLS TOOLBAR (Bộ lọc & Tìm kiếm) ─── */
.modern-select-box {
  background: #ffffff;
  border-radius: 30px;
  padding: 1px;
  border: 1px solid rgba(0, 0, 0, 0.08);
}
.modern-select {
  background-color: transparent !important;
  border: none !important;
  color: #4b5563 !important; 
  padding-left: 18px;
  font-size: 13.5px;
  box-shadow: none !important;
  height: 38px;
}
.modern-select option { background: #ffffff; color: #111827; }

.modern-search {
  background: #ffffff !important;
  border: 1px solid rgba(0, 0, 0, 0.08) !important;
  color: #111827 !important;
  border-radius: 30px;
  height: 38px;
  font-size: 13.5px;
}
.modern-search::placeholder { color: #9ca3af; }
.modern-search:focus {
  border-color: #059669 !important;
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.08) !important;
}
.search-icon-btn { color: #9ca3af; }

.pill-control-group {
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 30px;
  padding: 2px;
}
.btn-pill-action {
  background: transparent;
  border: none;
  color: #6b7280;
  width: 32px;
  height: 32px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s ease;
}
.btn-pill-action:hover { color: #111827; background: #f3f4f6; }
.btn-pill-display { background: transparent; border: none; color: #111827; font-size: 13px; pointer-events: none; }

.btn-luxury-primary {
  background: linear-gradient(135deg, #059669 0%, #10b981 50%, #34d399 100%);
  border: none;
  color: #ffffff;
  font-weight: 600;
  font-size: 13.5px;
  border-radius: 30px;
  height: 38px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
  transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
}
.btn-luxury-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(5, 150, 105, 0.35);
}

/* ─── MA TRẬN VIEWPORT CÂY PHẢ HỆ ─── */
.tree-viewport {
  height: calc(100vh - 210px); 
  min-height: 580px;
  background: rgba(248, 250, 252, 0.4);
  background-image: linear-gradient(rgba(0, 0, 0, 0.012) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(0, 0, 0, 0.012) 1px, transparent 1px);
  background-size: 40px 40px;
  position: relative;
  overflow: hidden;
  user-select: none;
  display: block;
  border-bottom-left-radius: 24px;
  border-bottom-right-radius: 24px;
}

.tree-canvas {
  padding: 60px 100px 100px 100px;
  transition: transform 0.15s cubic-bezier(0.25, 1, 0.5, 1);
  position: absolute;
  left: 0;
  top: 0;
  width: max-content;
  height: max-content;
}

/* ─── 🌟 TÁI CẤU TRÚC ĐƯỜNG KẺ CHỈ CHUẨN KỸ THUẬT SỐ 🌟 ─── */
.tree, .tree ul, .tree li {
  position: relative;
  margin: 0;
  padding: 0;
  list-style-type: none;
}

.tree ul { 
  padding-top: 40px; 
  display: flex !important; 
  flex-direction: row !important;
  flex-wrap: nowrap !important;
  justify-content: center !important; 
}

.tree li { 
  text-align: center; 
  padding: 40px 20px 0 20px !important; 
  flex: 0 0 auto !important;
  position: relative;
}

/* Khóa chặt đường chỉ kẻ ngang nối nhánh anh em */
.tree li::before, .tree li::after {
  content: '' !important;
  position: absolute !important;
  top: 0 !important;
  right: 50% !important;
  border-top: 2px solid rgba(0, 0, 0, 0.15) !important; /* Tăng độ đậm nét lên 2px */
  width: 50% !important;
  height: 40px !important;
  z-index: 1;
}
.tree li::after { 
  right: auto !important; 
  left: 50% !important; 
  border-left: 2px solid rgba(0, 0, 0, 0.15) !important; /* Đường chỉ dọc đi xuống */
}

/* Khử đường chỉ thừa đối với nhánh con một độc nhất */
.tree li:only-child::after, .tree li:only-child::before { display: none !important; }
.tree li:only-child { padding-top: 0 !important; }
.tree li:first-child::before, .tree li:last-child::after { border: 0 none !important; }

/* Tạo cua cong mượt mà ở các góc rẽ biên của dòng tộc */
.tree li:last-child::before { 
  border-right: 2px solid rgba(0, 0, 0, 0.15) !important; 
  border-radius: 0 16px 0 0 !important; 
}
.tree li:first-child::after { 
  border-radius: 16px 0 0 0 !important; 
}

/* Trục dọc trung tâm nối thẳng từ lề dưới của Cha xuống */
.tree ul ul::before { 
  content: '' !important; 
  position: absolute !important;
  top: 0 !important; 
  left: 50% !important; 
  border-left: 2px solid rgba(0, 0, 0, 0.15) !important; 
  width: 0 !important; 
  height: 40px !important;
  transform: translateX(-50%) !important;
  z-index: 1;
}

/* Khối bọc kén cụm Node: Giữ hồng tâm trục dọc luôn chuẩn xác */
.tree-node-group { 
  display: inline-flex !important; 
  align-items: center !important; 
  justify-content: center !important; 
  position: relative; 
  z-index: 10;
  margin: 0 auto !important;
}

/* Đường chỉ ngang nhỏ nối giữa Vợ và Chồng */
.tree-connector-h { 
  width: 24px; 
  height: 2px; 
  background: rgba(0, 0, 0, 0.15) !important; 
}

/* ─── THẺ CARD NHÂN KHẨU (Luxury Glassmorphic Cards) ─── */
.tree-node-card {
  background: #ffffff !important;
  border: 1px solid rgba(0, 0, 0, 0.05) !important;
  padding: 14px 16px !important;
  border-radius: 20px !important; 
  width: 220px !important;
  min-width: 220px !important;
  display: flex !important;
  align-items: center !important;
  gap: 14px !important;
  box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.04) !important;
  cursor: pointer;
  position: relative;
  transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1) !important;
}

/* Hiệu ứng nhấc khối phát sáng khi rà chuột */
.tree-node-card:hover {
  transform: translateY(-6px) scale(1.03) !important;
  border-color: rgba(0, 0, 0, 0.08) !important;
  box-shadow: 0 20px 35px -8px rgba(0, 0, 0, 0.06), 0 0 25px var(--glow-light) !important;
  z-index: 100;
}

.gen-1 { border-left: 4.5px solid var(--gen-1-color) !important; --glow-light: rgba(79, 70, 229, 0.12); }
.gen-2 { border-left: 4.5px solid var(--gen-2-color) !important; --glow-light: rgba(219, 39, 119, 0.12); }
.gen-3 { border-left: 4.5px solid var(--gen-3-color) !important; --glow-light: rgba(13, 148, 136, 0.12); }
.gen-4 { border-left: 4.5px solid var(--gen-4-color) !important; --glow-light: rgba(217, 119, 6, 0.12); }
.gen-5 { border-left: 4.5px solid var(--gen-5-color) !important; --glow-light: rgba(5, 150, 105, 0.12); }

.tree-node-card.spouse {
  border-style: dashed !important;
  border-left-width: 1.5px !important;
  border-color: rgba(0, 0, 0, 0.12) !important;
  background: rgba(255, 255, 255, 0.8) !important;
  width: 195px !important;
  min-width: 195px !important;
  --glow-light: rgba(249, 115, 22, 0.06);
}

.tree-node-card.highlighted {
  border-color: #059669 !important;
  background: rgba(5, 150, 105, 0.05) !important;
  animation: light-pulse 2s infinite;
}
@keyframes light-pulse {
  0% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.25); }
  70% { box-shadow: 0 0 0 12px rgba(5, 150, 105, 0); }
  100% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); }
}

.tree-node-card.is-dead { filter: grayscale(0.4) contrast(0.9); opacity: 0.65; background: rgba(249, 250, 251, 0.85) !important; }
.tree-node-card.is-dead .node-name { color: #4b5563 !important; }

/* VÒNG TRÒN AVATAR */
.node-avatar-container { position: relative; width: 48px; height: 48px; flex-shrink: 0; }
.node-avatar { 
  width: 48px !important; 
  height: 48px !important; 
  border-radius: 50% !important; 
  border: 2px solid #ffffff !important; 
  box-shadow: 0 4px 10px rgba(0,0,0,0.04) !important; 
  position: relative; 
  z-index: 2; 
  background-color: #f3f4f6 !important;
  display: flex !important; 
  align-items: center !important; 
  justify-content: center !important;
}
.avatar-text-initials {
  font-weight: 700 !important;
  font-size: 13.5px !important;
  color: #4b5563 !important;
  letter-spacing: -0.3px;
}
.node-avatar-img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.avatar-neon-ring { 
  position: absolute; 
  inset: -3px; 
  border-radius: 50%; 
  border: 1.5px solid transparent; 
  opacity: 0.25; 
  z-index: 1; 
}
.gen-1 .avatar-neon-ring { border-color: var(--gen-1-color); }
.gen-2 .avatar-neon-ring { border-color: var(--gen-2-color); }
.gen-3 .avatar-neon-ring { border-color: var(--gen-3-color); }
.gen-4 .avatar-neon-ring { border-color: var(--gen-4-color); }
.gen-5 .avatar-neon-ring { border-color: var(--gen-5-color); }

.node-content { text-align: left !important; flex-grow: 1; display: flex; flex-direction: column; gap: 2px; }
.node-name { font-weight: 700 !important; font-size: 14.5px !important; color: #111827 !important; letter-spacing: -0.2px; line-height: 1.3; }
.node-date { font-size: 11px !important; color: #6b7280 !important; font-weight: 500; }
.node-tag { font-size: 10px !important; font-weight: 700 !important; color: #9ca3af !important; text-transform: uppercase; margin-top: 3px; letter-spacing: 0.5px; }
.spouse-tag { color: #ea580c !important; background: #fff7ed; padding: 1px 6px; border-radius: 4px; display: inline-block; width: max-content; }

/* BÚT CHÌ EDIT */
.node-edit-btn {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 26px;
  height: 26px;
  background: #ffffff !important;
  border: 1px solid rgba(0, 0, 0, 0.08) !important;
  border-radius: 50% !important;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06) !important;
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
  color: #4b5563 !important;
  font-size: 13px;
}
.tree-node-card:hover .node-edit-btn { opacity: 1; transform: scale(1); }
.node-edit-btn:hover { background: #059669 !important; color: #ffffff !important; transform: scale(1.1) rotate(15deg); border-color: transparent !important; }

/* FORM MODAL */
.luxury-modal {
  background: #ffffff !important; 
  border-radius: 24px !important; 
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15) !important;
  border: none !important;
}
.header-create { border-left: 5px solid #059669; }
.header-edit { border-left: 5px solid #d97706; }
.profile-upload-preview { border: 3px solid #f3f4f6; box-shadow: 0 4px 12px rgba(0,0,0,0.03); object-fit: cover; }
.avatar-upload-trigger { width: 30px; height: 30px; background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; }
.avatar-upload-trigger:hover { background: #059669; color: #fff !important; }

.modern-field-label { font-size: 11.5px; font-weight: 700; color: #6b7280; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; display: block; }
.modern-input { background: #f9fafb !important; border: 1px solid rgba(0, 0, 0, 0.06) !important; color: #111827 !important; border-radius: 12px; padding: 10px 14px; font-size: 14px; }
.modern-radio .form-check-input:checked { background-color: #059669; border-color: #059669; }

.btn-modern-danger { background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; border-radius: 12px; }
.btn-modern-danger:hover { background: #ef4444; color: #fff; }
.btn-modern-cancel { background: transparent; border: 1px solid rgba(0,0,0,0.06); color: #6b7280; border-radius: 12px; }
.btn-modern-save { border: none; border-radius: 12px; color: #ffffff; }
.btn-modern-save.btn-save { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
.btn-modern-save.btn-warn { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }

.bg-luxury-tips { background: rgba(255, 255, 255, 0.85); border: 1px solid rgba(0,0,0,0.04); backdrop-filter: blur(4px); }
</style>