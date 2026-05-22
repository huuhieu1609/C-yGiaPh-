<template>
    <div class="row">
        <div class="col-12">
            <div class="card luxury-panel overflow-hidden border-0 shadow-sm">
                <div class="card-header bg-transparent py-4 border-0 position-relative z-2">
                    <h4 class="mb-0 fw-bold panel-title">
                        <i class="bx bx-group text-teal"></i> Quản Lý Thành Viên Dòng Họ
                    </h4>
                </div>
<<<<<<< HEAD

                <div class="card-body p-4 pt-2">
                    <div v-if="listChiNhanh.length === 0" class="text-center py-5">
                        <div class="mb-4 mt-5 empty-glow-icon">
                            <i class="bx bx-building-house text-muted opacity-20"
                                style="font-size: 90px !important;"></i>
=======
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
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
                        </div>
                        <h4 class="fw-bold text-dark">Dòng Họ Chưa Được Khởi Tạo!</h4>
                        <p class="text-secondary small">Bạn cần khởi tạo Dòng Họ (Chi Nhánh) trước khi quản lý thành
                            viên.</p>
                        <router-link to="/doi-tac/dong-ho" class="btn btn-luxury-primary px-5 mt-3 shadow-sm mb-5">
                            Khởi Tạo Ngay
                        </router-link>
                    </div>
<<<<<<< HEAD

                    <div v-else-if="allMembers.length === 0" class="text-center py-5">
                        <div class="mb-4 mt-5 empty-glow-icon">
                            <i class="bx bx-git-branch text-muted opacity-20" style="font-size: 90px !important;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Cây Gia Phả Đang Trống!</h4>
                        <p class="text-secondary small">Vui lòng thêm thành viên đầu tiên (Thủy Tổ) để bắt đầu xây dựng
                            phả hệ dòng tộc.</p>
                        <button class="btn btn-luxury-primary px-5 mt-3 shadow-sm mb-5" @click="openAddModal">
                            <i class="bx bx-plus"></i> Thêm Thành Viên Ngay
                        </button>
                    </div>
=======
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
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de

                    <div v-else class="w-100">
                        <div class="row mb-4 g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <input type="text" class="form-control modern-search ps-5" v-model="searchQuery"
                                        placeholder="Tìm tên thành viên...">
                                    <span
                                        class="position-absolute top-50 translate-middle-y start-0 ms-3 search-icon-btn"><i
                                            class="bx bx-search"></i></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="modern-select-box">
                                    <select class="form-select modern-select fw-semibold" v-model="selectedChiNhanhId">
                                        <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="modern-select-box">
                                    <select class="form-select modern-select fw-semibold" v-model="filterDoi">
                                        <option :value="null">-- Tất cả các đời --</option>
                                        <option v-for="doi in listDoiTocHo" :key="doi.id" :value="doi.so_doi">Đời {{
                                            doi.so_doi }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-luxury-primary w-100 fw-bold" @click="openAddModal">
                                    <i class="bx bx-plus-circle"></i> Thêm Mới
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive rounded-3 border border-light-subtle">
                            <table class="table modern-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="30%">Thành Viên</th>
                                        <th width="15%" class="text-center">Đời Thứ</th>
                                        <th width="20%" class="text-center">Mối Quan Hệ</th>
                                        <th width="15%" class="text-center">Ngày Sinh</th>
                                        <th width="10%" class="text-center">Trạng Thái</th>
                                        <th width="10%" class="text-center">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="filteredMembers.length === 0">
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bx bx-git-branch fs-2 d-block mb-2 opacity-20 text-rose"></i>
                                            <span class="small text-secondary fw-medium">Không tìm thấy thành viên phù
                                                hợp với bộ lọc.</span>
                                        </td>
                                    </tr>
                                    <tr v-else v-for="item in filteredMembers" :key="item.id"
                                        :class="'node-row-gen-' + ((item.doi_thu % 5) + 1)">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-table-wrapper">
                                                    <img :src="item.avatar || ('https://ui-avatars.com/api/?name=' + item.ho_ten + '&background=f3f4f6&color=111827')"
                                                        class="rounded-circle shadow-sm" width="44" height="44"
                                                        style="object-fit: cover;">
                                                    <span class="avatar-neon-ring-light"></span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">{{ item.ho_ten }}</div>
                                                    <div class="small text-muted fw-medium">{{ item.gioi_tinh }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-generation px-3 py-1_5">Đời {{ item.doi_thu
                                                }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-relationship"
                                                :class="item.loai_quan_he === 'Chính' ? 'rel-main' : 'rel-spouse'">
                                                {{ item.loai_quan_he === 'Chính' ? 'Con cháu chính' : 'Người phối ngẫu'
                                                }}
                                            </span>
                                        </td>
                                        <td class="text-center text-secondary small fw-medium">{{
                                            formatDate(item.ngay_sinh) }}</td>
                                        <td class="text-center">
                                            <span v-if="item.trang_thai === 'Còn sống'" class="badge status-alive">Còn
                                                sống</span>
                                            <span v-else class="badge status-dead">Đã mất</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-action-edit" @click="onEdit(item)" title="Sửa"><i
                                                        class="bx bx-edit-alt"></i></button>
                                                <button class="btn btn-action-delete" @click="handleDelete(item.id)"
                                                    title="Xóa"><i class="bx bx-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content luxury-modal border-0 shadow-lg">
                    <div class="modal-header border-bottom p-4" :class="isEditing ? 'header-edit' : 'header-create'">
                        <h5 class="modal-title fw-bold text-dark">
                            {{ isEditing ? 'Chỉnh Sửa Thông Tin' : 'Thêm Thành Viên Mới' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-12 text-center mb-2">
                                <div class="position-relative d-inline-block">
                                    <img :src="avatarPreview || currentMember.avatar || ('https://ui-avatars.com/api/?name=' + (currentMember.ho_ten || 'A') + '&background=f3f4f6&color=111827')"
                                        class="rounded-circle profile-upload-preview" alt="Avatar" width="100"
                                        height="100" style="object-fit: cover;">
                                    <label for="member-avatar-upload"
                                        class="avatar-upload-trigger position-absolute bottom-0 end-0" title="Chọn ảnh">
                                        <i class="bx bx-camera text-secondary"></i>
                                    </label>
                                    <input type="file" id="member-avatar-upload" @change="onAvatarChange" class="d-none"
                                        accept="image/*">
                                </div>
                            </div>
                            <div class="col-md-6">
<<<<<<< HEAD
                                <label class="modern-field-label">Họ và Tên <span class="text-rose">*</span></label>
                                <input type="text" class="form-control modern-input" v-model="currentMember.ho_ten"
                                    placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-6">
                                <label class="modern-field-label">Email (Quyền truy cập xem cây)</label>
                                <input type="email" class="form-control modern-input" v-model="currentMember.email"
                                    placeholder="example@gmail.com">
                            </div>
                            <div class="col-md-6">
                                <label class="modern-field-label">Giới tính</label>
                                <select class="form-select modern-input" v-model="currentMember.gioi_tinh">
=======
                                <label class="form-label fw-bold">Họ và Tên</label>
                                <input type="text" class="form-control radius-8 border-2 shadow-none" v-model="currentMember.ho_ten" placeholder="Nguyễn Văn A">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Giới tính</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.gioi_tinh">
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
<<<<<<< HEAD
                            <div class="col-md-6">
                                <label class="modern-field-label">Đời thứ</label>
                                <select class="form-select modern-input" v-model="currentMember.doi_thu">
=======
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Đời thứ</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.doi_thu">
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
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
                                        <input class="form-check-input shadow-none" type="radio" value="Còn sống"
                                            v-model="currentMember.trang_thai" id="status1">
                                        <label class="form-check-label text-dark" for="status1">Còn sống</label>
                                    </div>
                                    <div class="form-check modern-radio">
                                        <input class="form-check-input shadow-none" type="radio" value="Đã mất"
                                            v-model="currentMember.trang_thai" id="status2">
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
<<<<<<< HEAD
                                <label class="modern-field-label">Con của ông (Cha)</label>
                                <select class="form-select modern-input" v-model="currentMember.cha_id">
                                    <option :value="null">--- Thủy Tổ (Đời 1) ---</option>
                                    <template v-for="m in allMembers" :key="'cha_' + m.id">
                                        <option :value="m.id"
                                            v-if="m.id !== currentMember.id && m.loai_quan_he === 'Chính'">
                                            {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                        </option>
                                    </template>
=======
                                <label class="form-label fw-bold">Con của ông (Cha)</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.cha_id">
                                    <option :value="null">--- Thủy Tổ ---</option>
                                    <option v-for="m in allMembers" :key="m.id" :value="m.id" 
                                        v-show="m.id !== currentMember.id && m.loai_quan_he === 'Chính'">
                                        {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                    </option>
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
                                </select>
                            </div>

                            <div class="col-md-6" v-if="currentMember.loai_quan_he === 'Vợ/Chồng'">
<<<<<<< HEAD
                                <label class="modern-field-label">Là Vợ/Chồng của ai?</label>
                                <select class="form-select modern-input" v-model="currentMember.spouse_of_id">
                                    <template v-for="m in allMembers" :key="'vochong_' + m.id">
                                        <option :value="m.id" v-if="m.loai_quan_he === 'Chính'">
                                            {{ m.ho_ten }}
                                        </option>
                                    </template>
=======
                                <label class="form-label fw-bold">Là Vợ/Chồng của ai?</label>
                                <select class="form-select radius-8 border-2 shadow-none" v-model="currentMember.spouse_of_id">
                                    <option v-for="m in allMembers" :key="m.id" :value="m.id" 
                                        v-show="m.loai_quan_he === 'Chính'">
                                        {{ m.ho_ten }}
                                    </option>
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="modern-field-label">Ghi chú tộc phả</label>
                                <textarea class="form-control modern-input" rows="2" v-model="currentMember.ghi_chu"
                                    placeholder="Thông tin tiểu sử, chức vụ, đóng góp nổi bật..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-4">
                        <button type="button" class="btn btn-modern-cancel px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-modern-save px-4 fw-bold"
                            :class="isEditing ? 'btn-warn' : 'btn-save'" @click="saveMember">
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
<<<<<<< HEAD
            if (this.modal) this.modal.show();
            else toastr.error('Lỗi tải giao diện Modal!');
=======
            this.modal.show();
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
        },
        onEdit(member) {
            this.isEditing = true;
            this.currentMember = { ...member };
            this.avatarFile = null;
            this.avatarPreview = null;
<<<<<<< HEAD
            if (this.modal) this.modal.show();
            else toastr.error('Lỗi tải giao diện Modal!');
=======
            this.modal.show();
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
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
<<<<<<< HEAD

            axios.post(url, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    Authorization: `Bearer ${localStorage.getItem('access_token')}`
=======
            
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
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
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
                }).catch(() => toastr.error("Có lỗi xảy ra!"));
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
/* ─── SYSTEM LIGHT THEME COLORS ─── */
.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
    margin: 0 !important;
    width: 100%;
}

.panel-title {
    color: #111827;
    letter-spacing: 0.3px;
}

.text-teal {
    color: #0d9488;
}

.text-rose {
    color: #db2777;
}

.py-1_5 {
    padding-top: 6px !important;
    padding-bottom: 6px !important;
}

/* ĐỒNG BỘ ĐỊNH DANH MÀU ĐỜI THẾ HỆ GIA PHẢ CHUẨN PASTEL */
.node-row-gen-1 {
    --row-accent: #4f46e5;
}

/* Đời 1: Royal Indigo */
.node-row-gen-2 {
    --row-accent: #db2777;
}

/* Đời 2: Rose Pink */
.node-row-gen-3 {
    --row-accent: #0d9488;
}

/* Đời 3: Dark Teal */
.node-row-gen-4 {
    --row-accent: #d97706;
}

/* Đời 4: Amber */
.node-row-gen-5 {
    --row-accent: #059669;
}

/* Đời 5: Emerald */

/* ─── CONTROLS HẠT KÉN NỀN SÁNG ─── */
.modern-search {
    background: #f9fafb !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    color: #111827 !important;
    border-radius: 30px;
    height: 40px;
    font-size: 13.5px;
}

.modern-search::placeholder {
    color: #9ca3af;
}

.modern-search:focus {
    border-color: #0d9488 !important;
    box-shadow: 0 4px 12px rgba(13, 148, 136, 0.1) !important;
}

.search-icon-btn {
    color: #9ca3af;
}

.modern-select-box {
    background: #f9fafb;
    border-radius: 30px;
    padding: 1px;
    border: 1px solid rgba(0, 0, 0, 0.06);
}

.modern-select {
    background-color: transparent !important;
    border: none !important;
    color: #4b5563 !important;
    padding-left: 16px;
    font-size: 14px;
    box-shadow: none !important;
}

.modern-select option {
    background: #ffffff;
    color: #111827;
}

/* Nút bấm viên thuốc rực rỡ */
.btn-luxury-primary {
    background: linear-gradient(135deg, #db2777 0%, #f97316 50%, #f59e0b 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    font-size: 13.5px;
    border-radius: 30px;
    height: 40px;
    box-shadow: 0 4px 12px rgba(219, 39, 119, 0.2);
    transition: all 0.3s ease;
}

.btn-luxury-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(219, 39, 119, 0.35);
    filter: brightness(1.04);
}

/* ─── MODERN LUXURY TABLE DESIGN ─── */
.modern-table thead {
    background: #f4f5f7;
}

.modern-table thead th {
    color: #4b5563;
    font-weight: 700;
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    padding: 14px 16px;
}

.modern-table tbody tr {
    transition: background 0.2s ease;
}

.modern-table tbody tr:hover {
    background-color: #f9fafb;
}

.modern-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    color: #374151;
    font-size: 14px;
}

/* Khung Avatar tròn chứa viền sáng mờ mỏng */
.avatar-table-wrapper {
    position: relative;
    width: 44px;
    height: 44px;
}

.avatar-table-wrapper img {
    border: 2px solid #ffffff;
    position: relative;
    z-index: 2;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
}

.avatar-neon-ring-light {
    position: absolute;
    inset: -1px;
    border-radius: 50%;
    border: 1px solid var(--row-accent, #e5e7eb);
    opacity: 0.4;
    z-index: 1;
}

/* Badge Đời Pastel */
.badge-generation {
    background-color: #ffffff !important;
    border: 1px solid var(--row-accent) !important;
    color: var(--row-accent) !important;
    font-weight: 600;
    border-radius: 30px;
    font-size: 12px;
}

/* Thẻ badge quan hệ tộc */
.badge-relationship {
    font-size: 12.5px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
}

.rel-main {
    background: #eef2ff;
    color: #4f46e5;
}

.rel-spouse {
    background: #fff7ed;
    color: #ea580c;
}

/* Trạng thái Sinh Tử gọn gàng */
.status-alive {
    background-color: #f0fdf4 !important;
    color: #16a34a !important;
    font-weight: 600;
    font-size: 12px;
    border-radius: 6px;
}

.status-dead {
    background-color: #f3f4f6 !important;
    color: #9ca3af !important;
    font-weight: 600;
    font-size: 12px;
    border-radius: 6px;
}

/* Nút thao tác tinh xảo */
.btn-action-edit,
.btn-action-delete {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.06);
    font-size: 13px;
    border-radius: 8px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

.btn-action-edit {
    color: #d97706;
}

.btn-action-edit:hover {
    background: #d97706;
    color: #fff;
    border-color: transparent;
}

.btn-action-delete {
    color: #dc3545;
}

.btn-action-delete:hover {
    background: #dc3545;
    color: #fff;
    border-color: transparent;
}

/* ─── MODAL PREMIUM LIGHT ─── */
.luxury-modal {
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
}

.header-create {
    border-left: 5px solid #0d9488;
}

.header-edit {
    border-left: 5px solid #d97706;
}

.profile-upload-preview {
    border: 3px solid #f3f4f6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

.avatar-upload-trigger {
    width: 30px;
    height: 30px;
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.avatar-upload-trigger:hover {
    background: #db2777;
    color: #fff !important;
    border-color: transparent;
}

.modern-field-label {
    font-size: 11.5px;
    font-weight: 700;
    color: #6b7280;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 6px;
    display: block;
}

.modern-input {
    background: #f9fafb !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    color: #111827 !important;
    border-radius: 12px;
    padding: 10px 14px;
    font-size: 14px;
}

.modern-input:focus {
    border-color: rgba(0, 0, 0, 0.15) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02) !important;
}

.modern-radio .form-check-input:checked {
    background-color: #db2777;
    border-color: #db2777;
}

.btn-modern-cancel {
    background: transparent;
    border: 1px solid rgba(0, 0, 0, 0.06);
    color: #6b7280;
    border-radius: 12px;
}

.btn-modern-cancel:hover {
    background: #f9fafb;
    color: #111827;
}

.btn-modern-save {
    border: none;
    border-radius: 12px;
    color: #ffffff;
}

.btn-modern-save.btn-save {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
}

.btn-modern-save.btn-warn {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
}

.btn-modern-save:hover {
    filter: brightness(1.05);
    transform: translateY(-0.5px);
}

.empty-glow-icon i {
    animation: icon-float 3.5s ease-in-out infinite;
}

@keyframes icon-float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-8px);
    }
}
</style>