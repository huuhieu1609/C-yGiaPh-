<template>
    <div class="row">
        <div class="col-12">
            <div class="card genealogy-main-card shadow-sm border-0 radius-10 overflow-hidden">
                <div class="card-header py-3 border-0 mt-2 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold theme-text-main"><i class="bx bx-group text-warning"></i> Quản Lý Thành Viên Dòng Họ</h5>
                    <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="refreshData" :disabled="isLoading" title="Làm mới dữ liệu">
                        <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                    </button>
                </div>
                <div class="card-body px-4 pb-4">
                    <div v-if="listChiNhanh.length === 0" class="text-center py-5">
                        <div class="mb-4 mt-5">
                            <i class="bx bx-building-house text-warning opacity-25" style="font-size: 100px !important;"></i>
                        </div>
                        <h4 class="fw-bold">Dòng Họ Chưa Được Khởi Tạo!</h4>
                        <p class="text-muted">Bạn cần khởi tạo Dòng Họ (Chi Nhánh) trước khi quản lý thành viên.</p>
                        <router-link to="/doi-tac/dong-ho" class="btn btn-gradient-orange radius-30 px-5 mt-3 shadow-sm mb-5 fw-bold">
                            <i class="bx bx-plus-circle"></i> Khởi Tạo Ngay
                        </router-link>
                    </div>
                    
                    <div v-else-if="allMembers.length === 0" class="text-center py-5">
                        <div class="mb-4 mt-5">
                            <i class="bx bx-git-branch text-warning opacity-25" style="font-size: 100px !important;"></i>
                        </div>
                        <h4 class="fw-bold">Cây Gia Phả Đang Trống!</h4>
                        <p class="text-muted">Vui lòng thêm thành viên đầu tiên (Thủy Tổ) tại trang <b>Cây Gia Phả</b>.</p>
                        <router-link to="/doi-tac/gia-pha" class="btn btn-gradient-orange radius-30 px-5 mt-3 shadow-sm mb-5 fw-bold">
                            <i class="bx bx-plus"></i> Đi Đến Cây Gia Phả
                        </router-link>
                    </div>
                    
                    <template v-else>
                        <div class="row mb-4 g-3">
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <input type="text" class="form-control ps-5 radius-30 premium-input" v-model="searchQuery" placeholder="Tìm tên thành viên...">
                                    <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"><i class="bx bx-search fs-5"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select radius-30 filter-select border-1 fw-bold" v-model="selectedChiNhanhId">
                                    <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select radius-30 filter-select border-1 fw-bold" v-model="filterDoi">
                                    <option :value="null">-- Tất cả các đời --</option>
                                    <option v-for="doi in listDoiTocHo" :key="doi.id" :value="doi.so_doi">Đời {{ doi.so_doi }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive table-container-premium">
                            <table class="table align-middle mb-0 text-nowrap">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-start ps-4 cursor-pointer" @click="sortBy('ho_ten')">Thành Viên <i class="bx" :class="sortKey === 'ho_ten' ? (sortOrder === 'asc' ? 'bx-sort-up' : 'bx-sort-down') : 'bx-sort'"></i></th>
                                        <th class="cursor-pointer" @click="sortBy('doi_thu')">Đời Thứ <i class="bx" :class="sortKey === 'doi_thu' ? (sortOrder === 'asc' ? 'bx-sort-up' : 'bx-sort-down') : 'bx-sort'"></i></th>
                                        <th>Mối Quan Hệ</th>
                                        <th class="cursor-pointer" @click="sortBy('ngay_sinh')">Ngày Sinh <i class="bx" :class="sortKey === 'ngay_sinh' ? (sortOrder === 'asc' ? 'bx-sort-up' : 'bx-sort-down') : 'bx-sort'"></i></th>
                                        <th>Trạng Thái</th>
                                        <th class="pe-4">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="filteredMembers.length === 0">
                                        <td colspan="6" class="text-center py-5 text-muted fw-medium bg-transparent">
                                            <i class="bx bx-git-branch fs-2 d-block mb-2 text-warning opacity-50"></i>
                                            Chưa có thành viên nào khớp với bộ lọc.<br>
                                            Vui lòng điều chỉnh lại điều kiện tìm kiếm.
                                        </td>
                                    </tr>
                                    <tr v-else v-for="item in filteredMembers" :key="item.id" class="table-row-premium">
                                        <td class="ps-4 bg-transparent">
                                            <div class="d-flex align-items-center">
                                                <img :src="item.avatar || ('https://ui-avatars.com/api/?name=' + item.ho_ten + '&background=f97316&color=fff')"
                                                     class="rounded-circle border border-2 border-white shadow-sm me-3" width="42" height="42"
                                                     style="object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold row-member-name">{{ item.ho_ten }}</div>
                                                    <div class="small text-secondary">{{ item.gioi_tinh }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center bg-transparent">
                                            <span class="badge badge-generation px-3 py-2 fw-bold">Đời {{ item.doi_thu }}</span>
                                        </td>
                                        <td class="text-center bg-transparent fw-semibold text-secondary">{{ item.loai_quan_he }}</td>
                                        <td class="text-center bg-transparent text-muted font-medium">{{ formatDate(item.ngay_sinh) }}</td>
                                        <td class="text-center bg-transparent">
                                            <span v-if="item.trang_thai === 'Còn sống'" class="badge bg-success-premium px-3 py-2 fw-bold">Còn sống</span>
                                            <span v-else class="badge bg-danger-premium px-3 py-2 fw-bold">Đã mất</span>
                                        </td>
                                        <td class="text-center bg-transparent pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-action-edit" @click="onEdit(item)" title="Chỉnh sửa"><i class="bx bx-edit-alt"></i></button>
                                                <button class="btn btn-sm btn-action-delete" @click="handleDelete(item.id)" title="Xóa bỏ"><i class="bx bx-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content radius-24 shadow-lg border-0 overflow-hidden bg-white">
                    <div class="modal-header border-0 p-4 pb-2" :class="isEditing ? 'bg-light-warning' : 'bg-light-primary'">
                        <h5 class="modal-title fw-bold text-dark">
                            {{ isEditing ? 'Chỉnh Sửa Thông Tin' : 'Thêm Thành Viên Mới' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-12 text-center mb-1">
                                <div class="position-relative d-inline-block">
                                    <img :src="avatarPreview || currentMember.avatar || ('https://ui-avatars.com/api/?name=' + (currentMember.ho_ten || 'A') + '&background=f97316&color=fff')" class="rounded-circle border border-3 border-white shadow-sm" alt="Avatar" width="100" height="100" style="object-fit: cover;">
                                    <label for="member-avatar-upload" class="btn btn-sm btn-orange-avatar rounded-circle position-absolute bottom-0 end-0 shadow-sm" title="Chọn ảnh">
                                        <i class="bx bx-camera text-white"></i>
                                    </label>
                                    <input type="file" id="member-avatar-upload" @change="onAvatarChange" class="d-none" accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Họ và Tên</label>
                                <input type="text" class="form-control premium-input" v-model="currentMember.ho_ten" placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary">Giới tính</label>
                                <select class="form-select premium-input" v-model="currentMember.gioi_tinh">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-secondary">Đời thứ</label>
                                <select class="form-select premium-input" v-model="currentMember.doi_thu">
                                    <option v-for="doi in listDoiTocHo" :key="doi.id" :value="doi.so_doi">
                                        Đời {{ doi.so_doi }} - {{ doi.ten_doi }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Ngày sinh</label>
                                <input type="date" class="form-control premium-input" v-model="currentMember.ngay_sinh">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Trạng thái</label>
                                <div class="d-flex gap-4 pt-2">
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" value="Còn sống" v-model="currentMember.trang_thai" id="status1">
                                        <label class="form-check-label fw-medium" for="status1">Còn sống</label>
                                    </div>
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" value="Đã mất" v-model="currentMember.trang_thai" id="status2">
                                        <label class="form-check-label fw-medium" for="status2">Đã mất</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" v-if="currentMember.trang_thai === 'Đã mất'">
                                <label class="form-label fw-bold text-danger">Ngày mất</label>
                                <input type="date" class="form-control premium-input border-danger border-opacity-50" v-model="currentMember.ngay_mat">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary">Quan hệ với dòng họ</label>
                                <select class="form-select premium-input" v-model="currentMember.loai_quan_he">
                                    <option value="Chính">Thành viên chính (Con cháu)</option>
                                    <option value="Vợ/Chồng">Người phối ngẫu (Vợ/Chồng)</option>
                                </select>
                            </div>
                            <div class="col-md-6" v-if="currentMember.loai_quan_he === 'Chính'">
                                <label class="form-label fw-bold text-secondary">Con của ông (Cha)</label>
                                <select class="form-select premium-input" v-model="currentMember.cha_id">
                                    <option :value="null">--- Thủy Tổ ---</option>
                                    <option v-for="m in allMembers" :key="m.id" :value="m.id" 
                                        v-show="m.id !== currentMember.id && m.loai_quan_he === 'Chính'">
                                        {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6" v-if="currentMember.loai_quan_he === 'Vợ/Chồng'">
                                <label class="form-label fw-bold text-secondary">Là Vợ/Chồng của ai?</label>
                                <select class="form-select premium-input" v-model="currentMember.spouse_of_id">
                                    <option v-for="m in allMembers" :key="m.id" :value="m.id" 
                                        v-show="m.loai_quan_he === 'Chính'">
                                        {{ m.ho_ten }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-secondary">Ghi chú</label>
                                <textarea class="form-control premium-input" rows="2" v-model="currentMember.ghi_chu" placeholder="Thông tin thêm..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4 radius-30 fw-medium" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn px-4 radius-30 fw-bold" :class="isEditing ? 'btn-warning-premium' : 'btn-orange-premium'" @click="saveMember">
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
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'PartnerMemberManagement',
    data() {
        return {
            allMembers: [],
            listChiNhanh: [],
            selectedChiNhanhId: null,
            listDoiTocHo: [],
            searchQuery: '',
            filterDoi: null,
            sortKey: 'ho_ten',
            sortOrder: 'asc',
            isEditing: false,
            modal: null,
            currentMember: {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, gioi_tinh: 'Nam', chi_nhanh_id: null,
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null,
                ngay_mat_al_ngay: null, ngay_mat_al_thang: null, ngay_mat_al_nam: null, ngay_mat_al_nhuan: 0
            },
            avatarFile: null,
            avatarPreview: null,
            showQRModal: false,
            activeMember: {}
        }
    },
    computed: {
        filteredMembers() {
            let temp = this.allMembers.filter(m => {
                const matchSearch = m.ho_ten.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchDoi = this.filterDoi === null || m.doi_thu == this.filterDoi;
                const matchChiNhanh = this.selectedChiNhanhId === null || m.chi_nhanh_id == this.selectedChiNhanhId;
                return matchSearch && matchDoi && matchChiNhanh;
            });

            return temp.sort((a, b) => {
                let valA = a[this.sortKey] || '';
                let valB = b[this.sortKey] || '';
                if (this.sortKey === 'doi_thu') {
                    valA = parseInt(valA);
                    valB = parseInt(valB);
                }
                if (valA < valB) return this.sortOrder === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortOrder === 'asc' ? 1 : -1;
                return 0;
            });
        }
    },
    mounted() {
        if (window.bootstrap) {
            this.modal = new window.bootstrap.Modal(document.getElementById('memberModal'));
        }
        this.loadDoiTocHo();
        this.loadChiNhanh();
        this.loadData();
    },
    methods: {
        sortBy(key) {
            if (this.sortKey === key) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortOrder = 'asc';
            }
        },
        getHeaders() {
            return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
        },
        loadDoiTocHo() {
            axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data', this.getHeaders())
                .then(res => { if (res.data.status) this.listDoiTocHo = res.data.data; });
        },
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
                .then(res => { if (res.data.status) this.allMembers = res.data.data; })
                .finally(() => { this.isLoading = false; });
        },
        loadChiNhanh() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listChiNhanh = res.data.data;
                        if (this.listChiNhanh.length > 0 && !this.selectedChiNhanhId) {
                            this.selectedChiNhanhId = this.listChiNhanh[0].id;
                        }
                    }
                })
                .finally(() => { this.isLoading = false; });
        },
        refreshData() {
            this.isLoading = true;
            Promise.all([
                axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data', this.getHeaders()),
                axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders()),
                axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
            ]).then(([resG, resB, resM]) => {
                if (resG.data.status) this.listDoiTocHo = resG.data.data;
                if (resB.data.status) {
                    this.listChiNhanh = resB.data.data;
                    if (this.listChiNhanh.length > 0 && !this.selectedChiNhanhId) {
                        this.selectedChiNhanhId = this.listChiNhanh[0].id;
                    }
                }
                if (resM.data.status) this.allMembers = resM.data.data;
                toastr.success('Dữ liệu thành viên đã được làm mới!');
            }).catch(() => {
                toastr.error('Lỗi khi tải lại dữ liệu thành viên.');
            }).finally(() => {
                this.isLoading = false;
            });
        },
        formatDate(date) {
            if (!date) return '---';
            return new Date(date).toLocaleDateString('vi-VN');
        },
        openAddModal() {
            this.isEditing = false;
            this.currentMember = {
                id: null, ho_ten: '', doi_thu: 1, cha_id: null, gioi_tinh: 'Nam', chi_nhanh_id: null,
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null,
                ngay_mat_al_ngay: null, ngay_mat_al_thang: null, ngay_mat_al_nam: null, ngay_mat_al_nhuan: 0
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
                } else {
                    toastr.error(res.data.message);
                }
            });
        },
        handleDelete(id) {
            if (confirm('Xác nhận xóa thành viên này?')) {
                axios.post('http://127.0.0.1:8000/api/thanh-vien/delete', { id }, this.getHeaders())
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        }
                    });
            }
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
                    <title>In mã QR - \${this.activeMember.ho_ten}</title>
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
                            <img src="\${this.activeMember.avatar || ('https://ui-avatars.com/api/?name=' + this.activeMember.ho_ten + '&background=d4af37&color=fff')}" style="border: 2px solid #d4af37; border-radius: 50%; width: 55px; height: 55px; object-fit: cover;">
                            <div>
                                <h4 class="drop-shadow" style="font-weight: bold; margin: 0; color: white;">\${this.activeMember.ho_ten}</h4>
                                <div style="margin-top: 5px;">
                                    <span class="badge-gold-soft">Đời \${this.activeMember.doi_thu}</span>
                                    <span class="badge-gold-soft">\${this.activeMember.gioi_tinh}</span>
                                    <span class="badge-gold-soft">\${this.activeMember.trang_thai}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="qr-frame-royal">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=\${encodeURIComponent(this.getMemberQRUrl(this.activeMember))}" width="180" height="180">
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

<style scoped>
/* KHUNG CARD CHÍNH THÍCH ỨNG CHÂN THỰC */
.genealogy-main-card {
  background: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 16px !important;
}

.theme-text-main {
  color: var(--text-main) !important;
}

/* ─── CÁC Ô NHẬP DỮ LIỆU VÀ BỘ LỌC VIÊN THUỐC ─── */
.filter-select, .premium-input {
  border-radius: 30px !important;
  border: 1px solid var(--border-color) !important;
  padding: 8px 16px !important;
  font-size: 14px;
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  box-shadow: none !important;
  transition: all 0.25s ease;
}
.filter-select:focus, .premium-input:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg) !important;
}

/* NÚT BẤM KHỞI TẠO GRADIENT ĐỒNG BỘ */
.btn-gradient-orange {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
  border: none !important; color: #ffffff !important;
  box-shadow: 0 4px 12px rgba(244, 63, 94, 0.2) !important;
}

/* ─── CẤU TRÚC BẢNG PREMIUM THÍCH ỨNG ADAPTIVE ─── */
.table-container-premium {
  border: 1px solid var(--border-color);
  border-radius: 12px;
  overflow: hidden;
}

.table thead th {
  background-color: var(--input-bg) !important;
  color: var(--text-sub) !important;
  border-bottom: 1px solid var(--border-color) !important;
  padding: 14px 10px !important;
}

.table-row-premium {
  border-bottom: 1px solid var(--border-color) !important;
  transition: background-color 0.2s ease;
}
.table-row-premium:hover {
  background-color: var(--input-bg) !important;
}

.row-member-name { font-size: 14px; color: var(--text-main); }
.font-medium { font-weight: 500; }

/* ─── BADGE VÀ NÚT TƯƠNG TÁC TINH TẾ ─── */
.badge-generation {
  background-color: rgba(249, 115, 22, 0.08) !important;
  color: #f97316 !important;
  border: 1px solid rgba(249, 115, 22, 0.15);
  border-radius: 30px !important;
  font-size: 11.5px !important;
}

.bg-success-premium {
  background-color: rgba(16, 185, 129, 0.08) !important;
  color: #10b981 !important;
  border: 1px solid rgba(16, 185, 129, 0.15);
  border-radius: 30px !important;
}

.bg-danger-premium {
  background-color: rgba(239, 68, 68, 0.08) !important;
  color: #ef4444 !important;
  border: 1px solid rgba(239, 68, 68, 0.15);
  border-radius: 30px !important;
}

/* Hệ thống nút hành động nhỏ */
.btn-action-edit, .btn-action-delete {
  background: transparent !important;
  border-radius: 8px !important;
  font-size: 15px; padding: 5px 10px !important;
  transition: all 0.2s ease;
}
.btn-action-edit { border: 1px solid rgba(245, 158, 11, 0.3) !important; color: #f59e0b !important; }
.btn-action-edit:hover { background: #f59e0b !important; color: white !important; }

.btn-action-delete { border: 1px solid rgba(239, 68, 68, 0.3) !important; color: #ef4444 !important; }
.btn-action-delete:hover { background: #ef4444 !important; color: white !important; }

/* ─── HỘP THOẠI MODAL ĐỒNG BỘ ─── */
.radius-24 { border-radius: 24px !important; }
.bg-light-primary { background-color: rgba(249, 115, 22, 0.04) !important; }
.bg-light-warning { background-color: rgba(245, 158, 11, 0.04) !important; }
.btn-orange-avatar { background: #f97316 !important; width: 32px; height: 32px; }
.custom-radio .form-check-input:checked { background-color: #f97316 !important; border-color: #f97316 !important; }
.btn-orange-premium { background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important; color: white !important; border: none; }
.btn-warning-premium { background: #f59e0b !important; color: #111827 !important; border: none; }

.btn-refresh-premium {
  background: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
  width: 36px;
  height: 36px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-refresh-premium:hover {
  transform: rotate(30deg) scale(1.05);
  border-color: #f97316 !important;
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
}
.btn-refresh-premium:active {
  transform: scale(0.95);
}
</style>
