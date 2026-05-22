<template>
    <div class="row">
        <div class="col-12">
            <div class="card genealogy-main-card shadow-sm border-0 radius-10 overflow-hidden">
                <div class="card-header py-3 border-0 mt-2 px-4">
                    <h5 class="mb-0 fw-bold theme-text-main"><i class="bx bx-group text-warning"></i> Quản Lý Thành Viên Dòng Họ</h5>
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
                                        <th class="text-start ps-4">Thành Viên</th>
                                        <th>Đời Thứ</th>
                                        <th>Mối Quan Hệ</th>
                                        <th>Ngày Sinh</th>
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
                loai_quan_he: 'Chính', spouse_of_id: null, trang_thai: 'Còn sống', ngay_mat: null, ngay_sinh: null, ghi_chu: '', avatar: null
            },
            avatarFile: null,
            avatarPreview: null,
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
</style>