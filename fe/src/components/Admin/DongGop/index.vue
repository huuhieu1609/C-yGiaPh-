<template>
    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-gift me-1"></i>
                        {{ isEditing ? 'Cập Nhật Đóng Góp' : 'Thêm Đóng Góp Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Người Đóng Góp</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.nguoi_dung_id"
                                required>
                                <option :value="null" disabled>Chọn người đóng góp</option>
                                <option v-for="user in listUsers" :key="user.id" :value="user.id">
                                    {{ user.ho_ten || user.email }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nội Dung</label>
                            <textarea rows="5" class="form-control radius-8 border-2 shadow-none"
                                placeholder="Nhập nội dung đóng góp" v-model="formData.noi_dung" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Trạng Thái</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.trang_thai">
                                <option value="Chờ duyệt">Chờ duyệt</option>
                                <option value="Đã duyệt">Đã duyệt</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" class="btn btn-light radius-8 px-4" v-if="isEditing"
                                @click="resetForm">Hủy</button>
                            <button type="submit" class="btn text-white radius-8 px-4 fw-bold shadow-sm"
                                style="background-color: #008bf8; border-color: #008bf8;">
                                {{ isEditing ? 'Cập Nhật' : 'Thêm Mới' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div
                    class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #333;">
                        <i class="bx bx-list-ul me-1"></i> Danh Sách Đóng Góp
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-8 overflow-hidden border-2">
                        <input type="text" class="form-control border-0 shadow-none ps-4"
                            placeholder="Tìm kiếm đóng góp..." v-model="searchQuery" />
                        <button class="btn btn-success px-5 fw-bold" type="button"
                            style="background-color: #00c853; border-color: #00c853;">Tìm kiếm</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="25%" class="py-3">Người Đóng Góp</th>
                                    <th width="40%" class="py-3">Nội Dung</th>
                                    <th width="15%" class="py-3">Trạng Thái</th>
                                    <th width="15%" class="py-3">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="5" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredList.length === 0">
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Chưa có dữ liệu đóng góp
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td>{{ getUserName(item.nguoi_dung_id) }}</td>
                                    <td>{{ item.noi_dung }}</td>
                                    <td class="text-center">
                                        <button @click="toggleStatus(item)"
                                            :class="item.trang_thai === 'Đã duyệt' ? 'btn-success' : 'btn-warning'"
                                            class="btn btn-sm radius-8 w-100">
                                            {{ item.trang_thai }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary radius-8"
                                                @click="editItem(item)" title="Sửa">
                                                <i class="bx bx-edit-alt m-0"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger radius-8"
                                                @click="deleteItem(item.id)" title="Xóa">
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
    </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'DongGopManagement',
    data() {
        return {
            listData: [],
            listUsers: [],
            isLoading: false,
            searchQuery: '',
            formData: {
                id: null,
                nguoi_dung_id: null,
                noi_dung: '',
                trang_thai: 'Chờ duyệt'
            },
            isEditing: false
        }
    },
    computed: {
        filteredList() {
            if (!this.searchQuery) return this.listData;
            const q = this.searchQuery.toLowerCase();
            return this.listData.filter(item =>
                item.noi_dung.toLowerCase().includes(q) ||
                this.getUserName(item.nguoi_dung_id).toLowerCase().includes(q) ||
                item.trang_thai.toLowerCase().includes(q)
            );
        }
    },
    mounted() {
        this.loadUsers();
        this.loadData();
    },
    methods: {
        getHeaders() {
            const token = localStorage.getItem('access_token');
            return {
                headers: {
                    Authorization: token ? `Bearer ${token}` : ''
                }
            };
        },
        loadUsers() {
            axios.get('http://127.0.0.1:8000/api/nguoi-dung/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listUsers = res.data.data;
                    }
                });
        },
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/dong-gop/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                })
                .catch(err => {
                    console.error(err);
                    toastr.error('Không thể tải dữ liệu đóng góp');
                })
                .finally(() => { this.isLoading = false; });
        },
        getUserName(id) {
            const user = this.listUsers.find(user => user.id === id);
            return user ? (user.ho_ten || user.email) : 'Không xác định';
        },
        saveData() {
            const url = this.isEditing
                ? 'http://127.0.0.1:8000/api/dong-gop/update'
                : 'http://127.0.0.1:8000/api/dong-gop/create';

            axios.post(url, this.formData, this.getHeaders())
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
                    const message = err.response?.data?.message || 'Có lỗi khi lưu đóng góp';
                    toastr.error(message);
                });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
        },
        deleteItem(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa đóng góp này?')) return;
            axios.post('http://127.0.0.1:8000/api/dong-gop/delete', { id }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    const message = err.response?.data?.message || 'Lỗi khi xóa đóng góp';
                    toastr.error(message);
                });
        },
        toggleStatus(item) {
            axios.post('http://127.0.0.1:8000/api/dong-gop/status', { id: item.id }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    const message = err.response?.data?.message || 'Lỗi khi cập nhật trạng thái';
                    toastr.error(message);
                });
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                nguoi_dung_id: null,
                noi_dung: '',
                trang_thai: 'Chờ duyệt'
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
