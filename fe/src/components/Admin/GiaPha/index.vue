<template>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 radius-10">
                <div class="card-header bg-white py-3 border-0">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-git-branch text-primary"></i> Trực Quan Cây Gia Phả</h5>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <button class="btn btn-primary radius-30 px-4 shadow-sm" @click="openAddModal">
                                <i class="bx bx-plus"></i> Thêm Thành Viên
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-uppercase">Dòng họ</label>
                            <select class="form-select radius-10 border-2 shadow-none" v-model="selectedChiNhanh" @change="filterTree">
                                <option :value="null">-- Tất cả --</option>
                                <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Family Tree Container -->
                    <div class="tree-container overflow-auto p-5 bg-white rounded-4 border">
                        <div class="tree">
                            <ul v-if="treeData.length">
                                <TreeItem 
                                    v-for="member in treeData" 
                                    :key="member.id" 
                                    :member="member" 
                                    :listDoiTocHo="listDoiTocHo"
                                    @edit="onEdit"
                                />
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Thêm/Sửa Thành Viên -->
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
    props: ['member', 'listDoiTocHo'],
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
        const hasChildren = this.member.children && this.member.children.length > 0;
        if (this.member.isDummy) {
            const nodeGroup = h('div', { class: 'tree-node-group' }, [
                h('div', { 
                    class: 'tree-dummy-node',
                    style: 'width: 2px; height: 120px; background-color: #ccc; margin: 0 auto;'
                })
            ]);
            const children = hasChildren ? h('ul', 
                this.member.children.map(child => h(TreeItem, { member: child, listDoiTocHo: this.listDoiTocHo, onEdit: (m) => this.$emit('edit', m) }))
            ) : null;
            return h('li', [nodeGroup, children]);
        }

        const nodeGroup = h('div', { class: 'tree-node-group' }, [
            h('div', { 
                class: ['tree-node-card', { 'principal': !this.member.cha_id, 'is-dead': this.member.trang_thai === 'Đã mất' }],
                onClick: (e) => { e.stopPropagation(); this.$emit('edit', this.member); }
            }, [
                h('div', { class: 'node-avatar-container mb-2 text-center' }, [
                    h('img', { 
                        src: this.member.avatar ? this.member.avatar : ('https://ui-avatars.com/api/?name=' + this.member.ho_ten + '&background=d4af37&color=fff'), 
                        class: 'rounded-circle border border-2 border-warning', 
                        style: 'width: 50px; height: 50px; object-fit: cover;' 
                    })
                ]),
                h('div', { class: 'node-name' }, this.member.ho_ten),
                this.member.ngay_sinh ? h('div', { class: 'node-birth small text-muted' }, formatDate(this.member.ngay_sinh)) : null,
                h('div', { class: 'node-meta text-primary fw-bold mt-1' }, [
                    h('span', `Đời ${this.member.doi_thu}${getTenDoi(this.member.doi_thu)}`),
                    this.member.trang_thai === 'Đã mất' ? h('span', { class: 'status-dead ms-1 text-danger' }, ' (Đã mất)') : null
                ]),
                h('div', { class: 'node-edit-overlay' }, [
                    h('i', { class: 'bx bx-edit-alt' }),
                    h('span', ' Sửa')
                ])
            ]),
            this.member.spouses && this.member.spouses.length ? this.member.spouses.map(spouse => [
                h('div', { class: 'tree-connector-h' }),
                h('div', { 
                    class: ['tree-node-card spouse', { 'is-dead': spouse.trang_thai === 'Đã mất' }],
                    onClick: (e) => { e.stopPropagation(); this.$emit('edit', spouse); }
                }, [
                    h('div', { class: 'node-avatar-container mb-2 text-center' }, [
                        h('img', { 
                            src: spouse.avatar ? spouse.avatar : ('https://ui-avatars.com/api/?name=' + spouse.ho_ten + '&background=d4af37&color=fff'), 
                            class: 'rounded-circle border border-2 border-warning', 
                            style: 'width: 50px; height: 50px; object-fit: cover;' 
                        })
                    ]),
                    h('div', { class: 'node-name' }, spouse.ho_ten),
                    spouse.ngay_sinh ? h('div', { class: 'node-birth small text-muted' }, formatDate(spouse.ngay_sinh)) : null,
                    h('div', { class: 'node-meta fw-bold mt-1' }, 'Vợ/Chồng'),
                    h('div', { class: 'node-edit-overlay' }, [
                        h('i', { class: 'bx bx-edit-alt' }),
                        h('span', ' Sửa')
                    ])
                ])
            ]) : null
        ]);
        const children = hasChildren ? h('ul', 
            this.member.children.map(child => h(TreeItem, { member: child, listDoiTocHo: this.listDoiTocHo, onEdit: (m) => this.$emit('edit', m) }))
        ) : null;
        return h('li', [nodeGroup, children]);
    }
});

export default {
    name: 'GiaPhaManagement',
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
            modal: null
        }
    },
    computed: {
        treeData() {
            let list = JSON.parse(JSON.stringify(this.allMembers));
            
            // Nếu có chọn chi nhánh, lọc danh sách thành viên thuộc chi nhánh đó
            if (this.selectedChiNhanh) {
                // Ta chỉ lấy các thành viên chính thuộc chi nhánh, và các vợ/chồng của họ
                list = list.filter(item => 
                    item.chi_nhanh_id == this.selectedChiNhanh || 
                    (item.loai_quan_he === 'Vợ/Chồng') // vợ/chồng sẽ được map vào spouse_of_id sau
                );
            }
            
            const map = {};
            const roots = [];
            list.forEach(item => { map[item.id] = item; item.children = []; item.spouses = []; });
            
            // Hàm trợ giúp tạo node ẩn (dummy) để nhảy đời
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
                    // Kiểm tra khoảng cách đời
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
        }
    },
    mounted() {
        if (window.bootstrap) {
            this.modal = new window.bootstrap.Modal(document.getElementById('memberModal'));
        }
        this.loadChiNhanh();
        this.loadDoiTocHo();
        this.loadData();
    },
    methods: {
        loadDoiTocHo() {
            axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.listDoiTocHo = res.data.data.sort((a, b) => a.so_doi - b.so_doi);
                    }
                });
        },
        loadChiNhanh() {
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.listChiNhanh = res.data.data;
                        if (this.listChiNhanh.length > 0 && !this.selectedChiNhanh) {
                            this.selectedChiNhanh = this.listChiNhanh[0].id;
                        }
                    }
                });
        },
        filterTree() {
            // Vue computed property (treeData) will reactively update when selectedChiNhanh changes.
        },
        loadData() {
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.allMembers = res.data.data;
                    }
                });
        },
        openAddModal() {
            this.isEditing = false;
            this.currentMember = {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, gioi_tinh: 'Nam', chi_nhanh_id: this.selectedChiNhanh,
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null
            };
            this.avatarFile = null;
            this.avatarPreview = null;
            document.getElementById('member-avatar-upload').value = '';
            this.modal.show();
        },
        onEdit(member) {
            this.isEditing = true;
            this.currentMember = { ...member };
            this.avatarFile = null;
            this.avatarPreview = null;
            document.getElementById('member-avatar-upload').value = '';
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
                    'Content-Type': 'multipart/form-data'
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
                })
                .catch(err => {
                    toastr.error('Có lỗi xảy ra!');
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
.tree-container {
    background: #f8f9fa;
    background-image: linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px);
    background-size: 20px 20px;
}
.tree { display: flex; justify-content: center; }
.tree ul { padding-top: 40px; position: relative; display: flex !important; justify-content: center; padding-left: 0; margin-bottom: 0; }
.tree li { text-align: center; list-style-type: none; position: relative; padding: 40px 10px 0 10px; transition: all 0.5s; flex: 0 1 auto; }
.tree li::before, .tree li::after { content: ''; position: absolute; top: 0; right: 50%; border-top: 2px solid #ccc; width: 50%; height: 40px; }
.tree li::after { right: auto; left: 50%; border-left: 2px solid #ccc; }
.tree li:only-child::after, .tree li:only-child::before { display: none; }
.tree li:only-child { padding-top: 0; }
.tree li:first-child::before, .tree li:last-child::after { border: 0 none; }
.tree li:last-child::before { border-right: 2px solid #ccc; border-radius: 0 5px 0 0; }
.tree li:first-child::after { border-radius: 5px 0 0 0; }
.tree ul ul::before { content: ''; position: absolute; top: 0; left: 50%; border-left: 2px solid #ccc; width: 0; height: 40px; }
.tree-node-group { display: inline-flex; align-items: center; justify-content: center; position: relative; z-index: 10; }
.tree-connector-h { width: 25px; height: 2px; background: #d4af37; }
.tree-node-card { background: #fff; border: 1px solid #ddd; padding: 12px 15px; border-radius: 10px; min-width: 140px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); cursor: pointer; position: relative; overflow: hidden; transition: 0.3s; }
.tree-node-card.principal { border: 2px solid #d4af37; background: #fffdf5; }
.tree-node-card.is-dead { border-color: #636e72; background-color: #f1f2f6; opacity: 0.85; }
.status-dead { color: #d63031; font-weight: bold; }
.tree-node-card.spouse { border-style: dashed; background: #fafafa; }
.node-name { font-weight: 700; font-size: 14px; color: #333; }
.node-meta { font-size: 11px; color: #888; text-transform: uppercase; }
.node-edit-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(212, 175, 55, 0.9); color: #fff; display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s; font-weight: bold; }
.tree-node-card:hover .node-edit-overlay { opacity: 1; }
.tree-node-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
</style>
