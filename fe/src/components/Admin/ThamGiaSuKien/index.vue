<template>
    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-user-check me-1"></i>
                        {{ isEditing ? 'Cập Nhật Tham Gia Sự Kiện' : 'Đăng Ký Tham Gia Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sự Kiện</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.su_kien_id"
                                required>
                                <option :value="null" disabled>Chọn sự kiện</option>
                                <option v-for="event in listEvents" :key="event.id" :value="event.id">
                                    {{ event.tieu_de }}
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Thành Viên</label>
                            <select class="form-select radius-8 border-2 shadow-none" v-model="formData.thanh_vien_id"
                                required>
                                <option :value="null" disabled>Chọn thành viên</option>
                                <option v-for="member in listMembers" :key="member.id" :value="member.id">
                                    {{ member.ho_ten || member.email }}
                                </option>
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" class="btn btn-light radius-8 px-4" v-if="isEditing"
                                @click="resetForm">Hủy</button>
                            <button type="submit" class="btn text-white radius-8 px-4 fw-bold shadow-sm"
                                style="background-color: #008bf8; border-color: #008bf8;">
                                {{ isEditing ? 'Cập Nhật' : 'Lưu đăng ký' }}
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
                        <i class="bx bx-list-ul me-1"></i> Danh Sách Tham Gia
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-8 overflow-hidden border-2">
                        <input type="text" class="form-control border-0 shadow-none ps-4"
                            placeholder="Tìm kiếm người tham gia..." v-model="searchQuery" />
                        <button class="btn btn-success px-5 fw-bold" type="button"
                            style="background-color: #00c853; border-color: #00c853;">Tìm kiếm</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="30%" class="py-3">Thành Viên</th>
                                    <th width="45%" class="py-3">Sự Kiện</th>
                                    <th width="20%" class="py-3">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="4" class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredList.length === 0">
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block"></i>
                                        Chưa có dữ liệu tham gia sự kiện
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold">{{ index + 1 }}</td>
                                    <td>{{ getMemberName(item.thanh_vien_id) }}</td>
                                    <td>{{ getEventTitle(item.su_kien_id) }}</td>
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
    name: 'ThamGiaSuKienManagement',
    data() {
        return {
            listData: [],
            listEvents: [],
            listMembers: [],
            isLoading: false,
            searchQuery: '',
            isEditing: false,
            formData: {
                id: null,
                su_kien_id: null,
                thanh_vien_id: null
            }
        }
    },
    computed: {
        filteredList() {
            if (!this.searchQuery) return this.listData;
            const q = this.searchQuery.toLowerCase();
            return this.listData.filter(item =>
                this.getMemberName(item.thanh_vien_id).toLowerCase().includes(q) ||
                this.getEventTitle(item.su_kien_id).toLowerCase().includes(q)
            );
        }
    },
    mounted() {
        this.loadMembers();
        this.loadEvents();
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
        loadMembers() {
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) this.listMembers = res.data.data;
                });
        },
        loadEvents() {
            axios.get('http://127.0.0.1:8000/api/su-kien/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) this.listEvents = res.data.data;
                });
        },
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/tham-gia-su-kien/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                })
                .catch(err => {
                    console.error(err);
                    toastr.error('Không thể tải dữ liệu tham gia');
                })
                .finally(() => { this.isLoading = false; });
        },
        getMemberName(id) {
            const member = this.listMembers.find(member => member.id === id);
            return member ? (member.ho_ten || member.email) : 'Không xác định';
        },
        getEventTitle(id) {
            const event = this.listEvents.find(event => event.id === id);
            return event ? event.tieu_de : 'Không xác định';
        },
        saveData() {
            const url = this.isEditing
                ? 'http://127.0.0.1:8000/api/tham-gia-su-kien/update'
                : 'http://127.0.0.1:8000/api/tham-gia-su-kien/create';

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
                    const message = err.response?.data?.message || 'Lỗi khi lưu đăng ký tham gia';
                    toastr.error(message);
                });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
        },
        deleteItem(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa mục tham gia này?')) return;
            axios.post('http://127.0.0.1:8000/api/tham-gia-su-kien/delete', { id }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    const message = err.response?.data?.message || 'Lỗi khi xóa đăng ký tham gia';
                    toastr.error(message);
                });
        },
        resetForm() {
            this.isEditing = false;
            this.formData = {
                id: null,
                su_kien_id: null,
                thanh_vien_id: null
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
