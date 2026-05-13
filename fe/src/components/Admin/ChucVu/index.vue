<template>
    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-plus-circle me-1"></i> {{ isEditing ? 'Cập Nhật Chức Vụ' : 'Thêm Chức Vụ Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên Chức Vụ</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập tên chức vụ" v-model="formData.ten_chuc_vu" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mô Tả</label>
                            <textarea class="form-control radius-8 border-2 shadow-none" rows="4" placeholder="Nhập mô tả..." v-model="formData.mo_ta"></textarea>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" class="btn btn-light radius-8 px-4" v-if="isEditing" @click="resetForm">Hủy</button>
                            <button type="submit" class="btn text-white radius-8 px-4 fw-bold shadow-sm" style="background-color: #008bf8; border-color: #008bf8;">
                                {{ isEditing ? 'Cập Nhật' : 'Thêm Mới' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #333;">
                        <i class="bx bx-list-ul me-1"></i> Danh Sách Chức Vụ
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="25%" class="py-3">Tên Chức Vụ</th>
                                    <th width="35%" class="py-3">Mô Tả</th>
                                    <th width="10%" class="py-3">Trạng Thái</th>
                                    <th width="25%" class="py-3">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in listData" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-semibold">{{ item.ten_chuc_vu }}</td>
                                    <td>{{ item.mo_ta || '---' }}</td>
                                    <td class="text-center">
                                        <button @click="changeStatus(item.id)" :class="item.trang_thai == 'Hoạt động' ? 'btn-success' : 'btn-danger'" class="btn btn-sm radius-8 w-100">
                                            {{ item.trang_thai }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-info text-white radius-8" @click="openPhanQuyen(item)" title="Phân Quyền">
                                                <i class="bx bx-shield-quarter m-0"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-primary radius-8" @click="editItem(item)" title="Sửa">
                                                <i class="bx bx-edit-alt m-0"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger radius-8" @click="deleteItem(item.id)" title="Xóa">
                                                <i class="bx bx-trash m-0"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Phân Quyền -->
        <div class="modal fade" id="phanQuyenModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 radius-10 shadow-lg">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">PHÂN QUYỀN CHỨC VỤ: <span class="text-warning">{{ selectedRole.ten_chuc_vu }}</span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3" v-for="cn in listChucNang" :key="cn.id">
                                <div class="form-check form-switch p-3 border rounded-3 hover-bg-light transition-all">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" :id="'cn' + cn.id" :value="cn.id" v-model="selectedPermissions">
                                    <label class="form-check-label fw-bold cursor-pointer" :for="'cn' + cn.id">
                                        {{ cn.ten_chuc_nang }}
                                        <p class="small text-muted mb-0 fw-normal">{{ cn.mo_ta }}</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary radius-8 px-4" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary radius-8 px-4 fw-bold" @click="savePhanQuyen">Lưu Thay Đổi</button>
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
    name: 'ChucVuManagement',
    data() {
        return {
            listData: [],
            formData: {
                id: null,
                ten_chuc_vu: '',
                mo_ta: '',
                trang_thai: 'Hoạt động'
            },
            isEditing: false,
            // Phan Quyen
            selectedRole: {},
            listChucNang: [],
            selectedPermissions: [],
            modalPhanQuyen: null
        }
    },
    mounted() {
        this.loadData();
        if (window.bootstrap) {
            this.modalPhanQuyen = new window.bootstrap.Modal(document.getElementById('phanQuyenModal'));
        }
    },
    methods: {
        loadData() {
            axios.get('http://127.0.0.1:8000/api/chuc-vu/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                });
        },
        saveData() {
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/chuc-vu/update'
                : 'http://127.0.0.1:8000/api/chuc-vu/create';
            
            axios.post(url, this.formData)
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                        this.resetForm();
                    } else {
                        toastr.error(res.data.message);
                    }
                });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
        },
        deleteItem(id) {
            if (confirm('Bạn có chắc chắn muốn xóa?')) {
                axios.post('http://127.0.0.1:8000/api/chuc-vu/delete', { id })
                    .then(res => {
                        if (res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        }
                    });
            }
        },
        changeStatus(id) {
            axios.post('http://127.0.0.1:8000/api/chuc-vu/status', { id })
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                    }
                });
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                ten_chuc_vu: '',
                mo_ta: '',
                trang_thai: 'Hoạt động'
            };
        },
        // Phan Quyen methods
        openPhanQuyen(item) {
            this.selectedRole = item;
            axios.post('http://127.0.0.1:8000/api/phan-quyen/get-chuc-nang', { chuc_vu_id: item.id })
                .then(res => {
                    if (res.data.status) {
                        this.listChucNang = res.data.data;
                        this.selectedPermissions = res.data.da_chon;
                        this.modalPhanQuyen.show();
                    }
                });
        },
        savePhanQuyen() {
            const payload = {
                chuc_vu_id: this.selectedRole.id,
                list_chuc_nang: this.selectedPermissions
            };
            axios.post('http://127.0.0.1:8000/api/phan-quyen/update', payload)
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.modalPhanQuyen.hide();
                    } else {
                        toastr.error(res.data.message);
                    }
                });
        }
    }
}
</script>

<style scoped>
.radius-10 { border-radius: 10px; }
.radius-8 { border-radius: 8px; }
.hover-bg-light:hover { background-color: #f8f9fa; }
.transition-all { transition: all 0.2s ease; }
.cursor-pointer { cursor: pointer; }
</style>
