<template>
    <div class="client-gia-pha container-fluid pt-5 mt-5 mb-5">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="title-giapha" style="color: #d4af37; font-family: 'Playfair Display', serif; font-weight: 700;">Cây Gia Phả Dòng Họ</h2>
                <p class="text-muted">Trực quan hóa sơ đồ phả hệ các thế hệ</p>
            </div>
        </div>

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="tree-container overflow-auto p-5 bg-white">
                    <div class="tree" v-if="!isLoading">
                        <ul v-if="treeData.length">
                            <TreeItem 
                                v-for="member in treeData" 
                                :key="member.id" 
                                :member="member" 
                                @view="onView"
                            />
                        </ul>
                        <div v-else class="text-center p-5 text-muted">
                            Không có dữ liệu gia phả.
                        </div>
                    </div>
                    <div v-else class="text-center p-5">
                        <i class="bx bx-loader-alt bx-spin fs-1 text-warning"></i>
                        <p class="mt-2 text-muted">Đang tải dữ liệu cây gia phả...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Xem Chi Tiết Thành Viên -->
        <div class="modal fade" id="viewMemberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content radius-15 shadow-lg border-0">
                    <div class="modal-header border-0 bg-dark text-white radius-top-15">
                        <h5 class="modal-title fw-bold" style="color: #d4af37;">
                            Thông Tin Thành Viên
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3">
                            <img :src="currentMember.avatar ? currentMember.avatar : ('https://ui-avatars.com/api/?name=' + currentMember.ho_ten + '&background=d4af37&color=fff')" class="rounded-circle border border-3 border-warning" width="100" height="100" alt="Avatar" style="object-fit: cover;">
                        </div>
                        <h4 class="fw-bold mb-1">{{ currentMember.ho_ten }}</h4>
                        <span class="badge bg-light text-dark border mb-3">Đời thứ {{ currentMember.doi_thu }}</span>
                        
                        <div class="row text-start mt-3">
                            <div class="col-6 mb-2">
                                <span class="text-muted small d-block">Giới tính:</span>
                                <span class="fw-semibold">{{ currentMember.gioi_tinh }}</span>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted small d-block">Trạng thái:</span>
                                <span class="fw-semibold" :class="currentMember.trang_thai === 'Đã mất' ? 'text-danger' : 'text-success'">
                                    {{ currentMember.trang_thai }}
                                </span>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted small d-block">Ngày sinh:</span>
                                <span class="fw-semibold">{{ currentMember.ngay_sinh || 'Không rõ' }}</span>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted small d-block">Vai trò trong họ:</span>
                                <span class="fw-semibold">{{ currentMember.loai_quan_he === 'Chính' ? 'Con Cháu' : 'Người phối ngẫu' }}</span>
                            </div>
                            <div class="col-6 mb-2" v-if="currentMember.trang_thai === 'Đã mất'">
                                <span class="text-muted small d-block">Ngày mất:</span>
                                <span class="fw-semibold">{{ currentMember.ngay_mat || 'Không rõ' }}</span>
                            </div>
                            <div class="col-12 mt-2" v-if="currentMember.ghi_chu">
                                <span class="text-muted small d-block">Ghi chú:</span>
                                <p class="mb-0 text-dark">{{ currentMember.ghi_chu }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light px-4 radius-8" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent, h } from 'vue';
import axios from 'axios';

const formatDate = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('vi-VN');
};

const TreeItem = defineComponent({
    name: 'TreeItem',
    props: ['member'],
    emits: ['view'],
    render() {
        const hasChildren = this.member.children && this.member.children.length > 0;
        const nodeGroup = h('div', { class: 'tree-node-group' }, [
            h('div', { 
                class: ['tree-node-card', { 'principal': !this.member.cha_id, 'is-dead': this.member.trang_thai === 'Đã mất' }],
                onClick: (e) => { e.stopPropagation(); this.$emit('view', this.member); }
            }, [
                h('div', { class: 'node-avatar-container mb-2' }, [
                    h('img', { 
                        src: this.member.avatar ? this.member.avatar : ('https://ui-avatars.com/api/?name=' + this.member.ho_ten + '&background=d4af37&color=fff'), 
                        class: 'rounded-circle border border-2 border-warning', 
                        style: 'width: 50px; height: 50px; object-fit: cover;' 
                    })
                ]),
                h('div', { class: 'node-name' }, this.member.ho_ten),
                this.member.ngay_sinh ? h('div', { class: 'node-birth small text-muted', style: 'font-size: 11px;' }, formatDate(this.member.ngay_sinh)) : null,
                h('div', { class: 'node-meta' }, [
                    h('span', `Đời ${this.member.doi_thu}`),
                    this.member.trang_thai === 'Đã mất' ? h('span', { class: 'status-dead ms-1' }, ' (Đã mất)') : null
                ]),
                h('div', { class: 'node-view-overlay' }, [
                    h('i', { class: 'bx bx-info-circle' }),
                    h('span', ' Chi tiết')
                ])
            ]),
            this.member.spouses && this.member.spouses.length ? this.member.spouses.map(spouse => [
                h('div', { class: 'tree-connector-h' }),
                h('div', { 
                    class: ['tree-node-card spouse', { 'is-dead': spouse.trang_thai === 'Đã mất' }],
                    onClick: (e) => { e.stopPropagation(); this.$emit('view', spouse); }
                }, [
                    h('div', { class: 'node-avatar-container mb-2' }, [
                        h('img', { 
                            src: spouse.avatar ? spouse.avatar : ('https://ui-avatars.com/api/?name=' + spouse.ho_ten + '&background=d4af37&color=fff'), 
                            class: 'rounded-circle border border-2 border-warning', 
                            style: 'width: 50px; height: 50px; object-fit: cover;' 
                        })
                    ]),
                    h('div', { class: 'node-name' }, spouse.ho_ten),
                    spouse.ngay_sinh ? h('div', { class: 'node-birth small text-muted', style: 'font-size: 11px;' }, formatDate(spouse.ngay_sinh)) : null,
                    h('div', { class: 'node-meta' }, 'Vợ/Chồng'),
                    h('div', { class: 'node-view-overlay' }, [
                        h('i', { class: 'bx bx-info-circle' }),
                        h('span', ' Chi tiết')
                    ])
                ])
            ]) : null
        ]);
        const children = hasChildren ? h('ul', 
            this.member.children.map(child => h(TreeItem, { member: child, onView: (m) => this.$emit('view', m) }))
        ) : null;
        return h('li', [nodeGroup, children]);
    }
});

export default {
    name: 'ClientGiaPha',
    components: { TreeItem },
    data() {
        return {
            allMembers: [],
            currentMember: {},
            modal: null,
            isLoading: true
        }
    },
    computed: {
        treeData() {
            const list = JSON.parse(JSON.stringify(this.allMembers));
            const map = {};
            const roots = [];
            list.forEach(item => { map[item.id] = item; item.children = []; item.spouses = []; });
            list.forEach(item => {
                if (item.loai_quan_he === 'Vợ/Chồng' && item.spouse_of_id && map[item.spouse_of_id]) {
                    map[item.spouse_of_id].spouses.push(item);
                } else if (item.cha_id && map[item.cha_id]) {
                    map[item.cha_id].children.push(item);
                } else if (item.loai_quan_he === 'Chính') {
                    roots.push(item);
                }
            });
            return roots;
        }
    },
    mounted() {
        if (window.bootstrap) {
            this.modal = new window.bootstrap.Modal(document.getElementById('viewMemberModal'));
        }
        this.loadData();
    },
    methods: {
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.allMembers = res.data.data;
                    }
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        onView(member) {
            this.currentMember = member;
            this.modal.show();
        }
    }
}
</script>

<style>
.client-gia-pha .tree-container {
    background: #fdfdfd;
    background-image: radial-gradient(circle, #e9ecef 1px, transparent 1px);
    background-size: 20px 20px;
    min-height: 60vh;
    cursor: grab;
}
.client-gia-pha .tree-container:active {
    cursor: grabbing;
}
.tree { display: flex; justify-content: center; }
.tree ul { padding-top: 40px; position: relative; display: flex !important; justify-content: center; padding-left: 0; margin-bottom: 0; }
.tree li { text-align: center; list-style-type: none; position: relative; padding: 40px 10px 0 10px; transition: all 0.5s; flex: 0 1 auto; }
.tree li::before, .tree li::after { content: ''; position: absolute; top: 0; right: 50%; border-top: 2px solid #bba14d; width: 50%; height: 40px; }
.tree li::after { right: auto; left: 50%; border-left: 2px solid #bba14d; }
.tree li:only-child::after, .tree li:only-child::before { display: none; }
.tree li:only-child { padding-top: 0; }
.tree li:first-child::before, .tree li:last-child::after { border: 0 none; }
.tree li:last-child::before { border-right: 2px solid #bba14d; border-radius: 0 5px 0 0; }
.tree li:first-child::after { border-radius: 5px 0 0 0; }
.tree ul ul::before { content: ''; position: absolute; top: 0; left: 50%; border-left: 2px solid #bba14d; width: 0; height: 40px; }
.tree-node-group { display: inline-flex; align-items: center; justify-content: center; position: relative; z-index: 10; }
.tree-connector-h { width: 25px; height: 2px; background: #bba14d; }
.tree-node-card { background: #fff; border: 1px solid #ddd; padding: 15px 20px; border-radius: 12px; min-width: 150px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); cursor: pointer; position: relative; overflow: hidden; transition: 0.3s; }
.tree-node-card.principal { border: 2px solid #d4af37; background: #fffdf5; }
.tree-node-card.is-dead { border-color: #636e72; background-color: #f1f2f6; opacity: 0.85; }
.status-dead { color: #d63031; font-weight: bold; }
.tree-node-card.spouse { border-style: dashed; background: #fafafa; }
.node-name { font-weight: 700; font-size: 15px; color: #1a1a1a; margin-bottom: 3px; }
.node-meta { font-size: 12px; color: #888; text-transform: uppercase; font-weight: 500; }
.node-view-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(26, 26, 26, 0.85); color: #d4af37; display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s; font-weight: bold; font-size: 14px; gap: 5px; }
.tree-node-card:hover .node-view-overlay { opacity: 1; }
.tree-node-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2); }
</style>
