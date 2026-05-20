<template>
    <div class="row">
        <!-- Left Column: Add/Edit Form -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-plus-circle me-1"></i> {{ isEditing ? 'Cập Nhật Đời Họ' : 'Thêm Đời Họ Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số Đời</label>
                            <input type="number" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập số đời (VD: 1, 2, 3...)" v-model="formData.so_doi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên Đời</label>
                            <input type="text" class="form-control radius-8 border-2 shadow-none" placeholder="Nhập tên đời (VD: Đời thứ nhất)" v-model="formData.ten_doi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tình Trạng</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.trang_thai">
                                <option value="1">Hoạt động</option>
                                <option value="0">Tạm dừng</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mô Tả</label>
                            <textarea class="form-control radius-8 border-2 shadow-none" rows="4" placeholder="Nhập mô tả chi tiết..." v-model="formData.mo_ta"></textarea>
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

        <!-- Right Column: Data Table -->
        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #333;">
                        <i class="bx bx-list-ul me-1"></i> Danh Sách Đời Họ
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-8 overflow-hidden border-2">
                        <input type="text" class="form-control border-0 shadow-none ps-4" placeholder="Tìm kiếm đời họ..." v-model="searchQuery">
                        <button class="btn btn-success px-5 fw-bold" type="button" style="background-color: #00c853; border-color: #00c853;">Tìm kiếm</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="15%" class="py-3">Tên Đời</th>
                                    <th width="10%" class="py-3">Số Đời</th>
                                    <th width="40%" class="py-3">Mô Tả</th>
                                    <th width="15%" class="py-3">Tình Trạng</th>
                                    <th width="15%" class="py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredList.length === 0">
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Chưa có dữ liệu
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td class="fw-semibold">{{ item.ten_doi }}</td>
                                    <td class="text-center"><span class="badge bg-light text-primary border border-primary rounded-pill px-3 py-2">Đời {{ item.so_doi }}</span></td>
                                    <td>{{ item.mo_ta || '---' }}</td>
                                    <td class="text-center">
                                        <span v-if="item.trang_thai == 1" class="badge bg-success rounded-pill px-3 py-2">Hoạt động</span>
                                        <span v-else class="badge bg-danger rounded-pill px-3 py-2">Tạm dừng</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary radius-8" @click="editItem(item)" title="Sửa">
                                                <i class="bx bx-edit-alt m-0"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger radius-8" data-bs-toggle="modal" data-bs-target="#deleteModal" @click="deleteId = item.id" title="Xóa">
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
        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content radius-10 border-0 shadow">
                    <div class="modal-header bg-light border-bottom-0">
                        <h5 class="modal-title text-danger fw-bold" id="deleteModalLabel">
                            <i class="bx bx-trash me-1"></i> Xác Nhận Xóa Đời Họ
                        </h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-danger border-0 d-flex align-items-center radius-8 mb-3" role="alert" style="background-color: #fde1e1; color: #d32f2f;">
                            <i class="bx bx-error-circle fs-1 me-3"></i>
                            <div>
                                <h6 class="alert-heading fw-bold mb-1">Cảnh báo nghiêm trọng!</h6>
                                <span class="small">Dữ liệu đời họ này sẽ bị xóa vĩnh viễn và không thể khôi phục lại được.</span>
                            </div>
                        </div>
                        <p class="mb-0 text-center fs-6 fw-semibold text-dark">Bạn có chắc chắn muốn tiếp tục xóa?</p>
                    </div>
                    <div class="modal-footer border-top-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-light radius-8 px-4 border" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="button" class="btn btn-danger radius-8 px-4 fw-bold shadow-sm" data-bs-dismiss="modal" @click="executeDelete">Đồng ý Xóa</button>
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
    name: 'DoiTocHoManagement',
    data() {
        return {
            listData: [],
            formData: {
                id: null,
                so_doi: '',
                ten_doi: '',
                mo_ta: '',
                trang_thai: 1
            },
            isEditing: false,
            isLoading: false,
            searchQuery: '',
            deleteId: null
        }
    },
    computed: {
        filteredList() {
            if (!this.searchQuery) return this.listData;
            const q = this.searchQuery.toLowerCase();
            return this.listData.filter(item => 
                (item.ten_doi && item.ten_doi.toLowerCase().includes(q)) ||
                (item.so_doi && item.so_doi.toString().includes(q))
            );
        }
    },
    mounted() {
        this.loadData();
    },
    methods: {
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                })
                .catch(err => console.log(err))
                .finally(() => {
                    this.isLoading = false;
                });
        },
        saveData() {
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/doi-toc-ho/update'
                : 'http://127.0.0.1:8000/api/doi-toc-ho/create';
            
            axios.post(url, this.formData)
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                        this.resetForm();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    toastr.error('Có lỗi xảy ra, vui lòng thử lại!');
                });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
        },
        executeDelete() {
            if (!this.deleteId) return;
            axios.delete(`http://127.0.0.1:8000/api/doi-toc-ho/delete/${this.deleteId}`)
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    toastr.error('Có lỗi xảy ra!');
                })
                .finally(() => {
                    this.deleteId = null;
                });
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                so_doi: '',
                ten_doi: '',
                mo_ta: '',
                trang_thai: 1
            };
        }
    }
}
</script>

<style scoped>
.radius-10 {
    border-radius: 10px;
}
.radius-8 {
    border-radius: 8px;
}
.table thead th {
    border-bottom: none;
    font-weight: 600;
}
</style>
