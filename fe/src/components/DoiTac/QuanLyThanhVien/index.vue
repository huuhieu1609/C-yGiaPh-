<template>
    <div class="card shadow-sm border-0 radius-10">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-group text-primary"></i> Quản Lý Thành Viên Dòng Họ</h5>
        </div>
        <div class="card-body">
            <div v-if="listChiNhanh.length === 0" class="text-center py-5">
                <div class="mb-4 mt-5">
                    <i class="bx bx-building-house fs-1 text-muted opacity-25" style="font-size: 100px !important;"></i>
                </div>
                <h4 class="fw-bold text-dark">Dòng Họ Chưa Được Khởi Tạo!</h4>
                <p class="text-muted">Bạn cần khởi tạo Dòng Họ (Chi Nhánh) trước khi quản lý thành viên.</p>
                <router-link to="/doi-tac/dong-ho" class="btn btn-primary radius-30 px-5 mt-3 shadow-sm mb-5">
                    <i class="bx bx-plus-circle"></i> Khởi Tạo Ngay
                </router-link>
            </div>
            <div v-else-if="allMembers.length === 0" class="text-center py-5">
                <div class="mb-4 mt-5">
                    <i class="bx bx-git-branch fs-1 text-muted opacity-25" style="font-size: 100px !important;"></i>
                </div>
                <h4 class="fw-bold text-dark">Cây Gia Phả Đang Trống!</h4>
                <p class="text-muted">Vui lòng thêm thành viên đầu tiên (Thủy Tổ) tại trang <b>Cây Gia Phả</b>.</p>
                <router-link to="/doi-tac/gia-pha" class="btn btn-primary radius-30 px-5 mt-3 shadow-sm mb-5">
                    <i class="bx bx-plus"></i> Đi Đến Cây Gia Phả
                </router-link>
            </div>
            <template v-else>
                <div class="row mb-3 g-3">
                    <div class="col-md-4">
                        <div class="position-relative">
                            <input type="text" class="form-control ps-5 radius-10" v-model="searchQuery" placeholder="Tìm tên thành viên...">
                            <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary"><i class="bx bx-search"></i></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select radius-10" v-model="selectedChiNhanhId">
                            <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select radius-10" v-model="filterDoi">
                            <option :value="null">-- Tất cả các đời --</option>
                            <option v-for="doi in listDoiTocHo" :key="doi.id" :value="doi.so_doi">Đời {{ doi.so_doi }}</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light text-uppercase small fw-bold">
                            <tr class="text-center">
                                <th>Thành Viên</th>
                                <th>Đời Thứ</th>
                                <th>Mối Quan Hệ</th>
                                <th>Ngày Sinh</th>
                                <th>Trạng Thái</th>
                                <th class="text-center">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="filteredMembers.length === 0">
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bx bx-git-branch fs-2 d-block mb-2"></i>
                                    Chưa có thành viên nào trong dòng họ này.<br>
                                    Vui lòng thêm thành viên (Thủy Tổ) tại trang <b>Cây Gia Phả</b> để bắt đầu.
                                </td>
                            </tr>
                            <tr v-else v-for="item in filteredMembers" :key="item.id">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img :src="item.avatar || ('https://ui-avatars.com/api/?name=' + item.ho_ten + '&background=d4af37&color=fff')"
                                            class="rounded-circle border me-3" width="45" height="45"
                                            style="object-fit: cover;">
                                        <div>
                                            <div class="fw-bold">{{ item.ho_ten }}</div>
                                            <div class="small text-muted">{{ item.gioi_tinh }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light-primary text-primary px-3">Đời {{ item.doi_thu }}</span>
                                </td>
                                <td class="text-center">{{ item.loai_quan_he }}</td>
                                <td class="text-center">{{ formatDate(item.ngay_sinh) }}</td>
                                <td class="text-center">
                                    <span v-if="item.trang_thai === 'Còn sống'" class="badge bg-success">Còn sống</span>
                                    <span v-else class="badge bg-danger">Đã mất</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-info" @click="showQRCard(item)" title="Xem Mã QR"><i class="bx bx-qr"></i></button>
                                        <button class="btn btn-sm btn-outline-warning" @click="onEdit(item)"><i class="bx bx-edit-alt"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" @click="handleDelete(item.id)"><i class="bx bx-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>
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
                                <label class="form-label fw-bold">Ngày mất (Dương lịch)</label>
                                <input type="date" class="form-control radius-8 border-2 shadow-none" v-model="currentMember.ngay_mat">
                            </div>

                            <div class="col-md-12" v-if="currentMember.trang_thai === 'Đã mất'">
                                <div class="p-3 rounded-3 mt-2" style="background-color: rgba(212, 175, 55, 0.08); border: 1px dashed rgba(212, 175, 55, 0.3) !important;">
                                    <h6 class="fw-bold mb-3" style="color: #8a6d1c;"><i class="bx bx-calendar-star me-1"></i> Ngày Giỗ Âm Lịch</h6>
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label font-12 fw-bold text-dark">Ngày âm lịch</label>
                                            <select class="form-select font-13 radius-8 border-2 shadow-none text-dark" v-model="currentMember.ngay_mat_al_ngay">
                                                <option :value="null">-- Chọn --</option>
                                                <option v-for="d in 30" :key="d" :value="d">Ngày {{ d }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label font-12 fw-bold text-dark">Tháng âm lịch</label>
                                            <select class="form-select font-13 radius-8 border-2 shadow-none text-dark" v-model="currentMember.ngay_mat_al_thang">
                                                <option :value="null">-- Chọn --</option>
                                                <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label font-12 fw-bold text-dark">Năm âm lịch (Tùy chọn)</label>
                                            <input type="number" class="form-control font-13 radius-8 border-2 shadow-none text-dark" v-model="currentMember.ngay_mat_al_nam" placeholder="Ví dụ: 2026">
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end pb-2">
                                            <div class="form-check">
                                                <input class="form-check-input border-2" type="checkbox" id="lunarNhuan" v-model="currentMember.ngay_mat_al_nhuan" :true-value="1" :false-value="0">
                                                <label class="form-check-label font-12 fw-bold text-dark" for="lunarNhuan">
                                                    Tháng nhuận
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
            return this.allMembers.filter(m => {
                const matchSearch = m.ho_ten.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchDoi = this.filterDoi === null || m.doi_thu == this.filterDoi;
                const matchChiNhanh = this.selectedChiNhanhId === null || m.chi_nhanh_id == this.selectedChiNhanhId;
                return matchSearch && matchDoi && matchChiNhanh;
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
        getHeaders() {
            return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
        },
        loadDoiTocHo() {
            axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data', this.getHeaders())
                .then(res => { if (res.data.status) this.listDoiTocHo = res.data.data; });
        },
        loadData() {
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
                .then(res => { if (res.data.status) this.allMembers = res.data.data; });
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

<style>
.radius-10 { border-radius: 10px; }
.radius-15 { border-radius: 15px; }
.radius-30 { border-radius: 30px; }
.radius-8 { border-radius: 8px; }
.radius-top-15 { border-top-left-radius: 15px; border-top-right-radius: 15px; }
.bg-light-primary { background-color: rgba(13, 110, 253, 0.1); }

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
</style>
