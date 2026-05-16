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

                            <button class="btn btn-primary radius-30 px-3 shadow-sm" @click="openAddModal">
                                <i class="bx bx-plus"></i> Thêm
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 position-relative">
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
                        <button type="button" class="btn btn-light px-4 radius-8" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn px-4 radius-8 fw-bold" :class="isEditing ? 'btn-warning' : 'btn-primary'" @click="saveMember">
                            {{ isEditing ? 'Cập Nhật' : 'Lưu Lại' }}
                        </button>
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

const TreeItem = defineComponent({
    name: 'TreeItem',
    props: ['member', 'listDoiTocHo', 'searchQuery'],
    emits: ['edit'],
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
                    onEdit: (m) => this.$emit('edit', m) 
                }))
            ) : null;
            return h('li', [nodeGroup, children]);
        }
        
        const nodeGroup = h('div', { class: 'tree-node-group' }, [
            h('div', { 
                class: ['tree-node-card', generationClass, { 
                    'principal': !this.member.cha_id, 
                    'is-dead': this.member.trang_thai === 'Đã mất',
                    'highlighted': isHighlighted
                }],
                onClick: (e) => { e.stopPropagation(); this.$emit('edit', this.member); }
            }, [
                h('div', { class: 'node-avatar-container' }, [
                    h('img', { 
                        src: this.member.avatar ? this.member.avatar : ('https://ui-avatars.com/api/?name=' + this.member.ho_ten + '&background=d4af37&color=fff'), 
                        class: 'node-avatar shadow-sm'
                    })
                ]),
                h('div', { class: 'node-content' }, [
                    h('div', { class: 'node-name' }, this.member.ho_ten),
                    this.member.ngay_sinh ? h('div', { class: 'node-date' }, formatDate(this.member.ngay_sinh)) : null,
                    h('div', { class: 'node-tag' }, `Đời ${this.member.doi_thu}${getTenDoi(this.member.doi_thu)}`)
                ]),
                h('div', { class: 'node-edit-btn' }, [
                    h('i', { class: 'bx bx-pencil' })
                ])
            ]),
            this.member.spouses && this.member.spouses.length ? this.member.spouses.map(spouse => {
                const isSpouseHighlighted = this.searchQuery && spouse.ho_ten.toLowerCase().includes(this.searchQuery.toLowerCase());
                return [
                    h('div', { class: 'tree-connector-h' }),
                    h('div', { 
                        class: ['tree-node-card spouse', { 
                            'is-dead': spouse.trang_thai === 'Đã mất',
                            'highlighted': isSpouseHighlighted
                        }],
                        onClick: (e) => { e.stopPropagation(); this.$emit('edit', spouse); }
                    }, [
                        h('div', { class: 'node-avatar-container' }, [
                            h('img', { 
                                src: spouse.avatar ? spouse.avatar : ('https://ui-avatars.com/api/?name=' + spouse.ho_ten + '&background=d4af37&color=fff'), 
                                class: 'node-avatar shadow-sm'
                            })
                        ]),
                        h('div', { class: 'node-content' }, [
                            h('div', { class: 'node-name' }, spouse.ho_ten),
                            h('div', { class: 'node-tag spouse-tag' }, 'Vợ/Chồng')
                        ]),
                        h('div', { class: 'node-edit-btn' }, [
                            h('i', { class: 'bx bx-pencil' })
                        ])
                    ])
                ];
            }) : null
        ]);
        const children = hasChildren ? h('ul', 
            this.member.children.map(child => h(TreeItem, { 
                member: child, 
                listDoiTocHo: this.listDoiTocHo, 
                searchQuery: this.searchQuery,
                onEdit: (m) => this.$emit('edit', m) 
            }))
        ) : null;
        
        return h('li', [nodeGroup, children]);
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
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, gioi_tinh: 'Nam',
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null
            },
            avatarPreview: null,
            isEditing: false,
            modal: null,
            searchQuery: '',
            
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
        }
        this.loadDoiTocHo();
        this.loadChiNhanh();
        this.loadData();
        
        // Add wheel listener for zoom
        this.$refs.viewport.addEventListener('wheel', this.handleWheel, { passive: false });
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
        openAddModal() {
            this.isEditing = false;
            this.currentMember = {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, gioi_tinh: 'Nam',
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
                headers: { 'Content-Type': 'multipart/form-data' }
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
            if (confirm('Xóa thành viên này?')) {
                axios.post('http://127.0.0.1:8000/api/thanh-vien/delete', { id: this.currentMember.id })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                            this.modal.hide();
                        }
                    });
            }
        }
    }
}
</script>

<style>
.tree-viewport {
    height: 700px;
    background: #fdfdfd;
    background-image: radial-gradient(#e0e0e0 1px, transparent 1px);
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

/* Connecting Lines */
.tree li::before, .tree li::after { content: ''; position: absolute; top: 0; right: 50%; border-top: 2px solid #ddd; width: 50%; height: 50px; }
.tree li::after { right: auto; left: 50%; border-left: 2px solid #ddd; }
.tree li:only-child::after, .tree li:only-child::before { display: none; }
.tree li:only-child { padding-top: 0; }
.tree li:first-child::before, .tree li:last-child::after { border: 0 none; }
.tree li:last-child::before { border-right: 2px solid #ddd; border-radius: 0 10px 0 0; }
.tree li:first-child::after { border-radius: 10px 0 0 0; }
.tree ul ul::before { content: ''; position: absolute; top: 0; left: 50%; border-left: 2px solid #ddd; width: 0; height: 50px; }

/* Node Styling */
.tree-node-group { display: inline-flex; align-items: center; justify-content: center; position: relative; z-index: 10; }
.tree-connector-h { width: 30px; height: 2px; background: #ddd; }

.tree-node-card {
    background: #fff;
    border: 2px solid #ddd;
    padding: 10px;
    border-radius: 15px;
    min-width: 200px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(5px);
}

.tree-node-card:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    z-index: 100;
}

.tree-node-card.highlighted {
    border-color: #ffc107 !important;
    background: #fffbeb !important;
    animation: pulse-border 1.5s infinite;
}

@keyframes pulse-border {
    0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
    70% { box-shadow: 0 0 0 15px rgba(255, 193, 7, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}

/* Generation Colors */
.gen-1 { border-color: #4285f4; border-left-width: 6px; }
.gen-2 { border-color: #34a853; border-left-width: 6px; }
.gen-3 { border-color: #fbbc05; border-left-width: 6px; }
.gen-4 { border-color: #ea4335; border-left-width: 6px; }
.gen-5 { border-color: #a142f4; border-left-width: 6px; }

.tree-node-card.is-dead {
    filter: grayscale(0.8);
    opacity: 0.8;
    background: #f8f9fa;
}

.node-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
}

.node-content {
    text-align: left;
    flex-grow: 1;
}

.node-name {
    font-weight: 700;
    font-size: 14px;
    color: #2c3e50;
    margin-bottom: 2px;
}

.node-date {
    font-size: 11px;
    color: #7f8c8d;
}

.node-tag {
    font-size: 10px;
    font-weight: 600;
    color: #2980b9;
    text-transform: uppercase;
    margin-top: 4px;
}

.spouse-tag {
    color: #e67e22;
}

.tree-node-card.spouse {
    border-style: dashed;
    border-left-width: 2px;
    min-width: 180px;
}

.node-edit-btn {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 28px;
    height: 28px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    opacity: 0;
    transition: 0.2s;
    color: #666;
}

.tree-node-card:hover .node-edit-btn {
    opacity: 1;
}

.node-edit-btn:hover {
    background: #4285f4;
    color: #fff;
}
</style>
