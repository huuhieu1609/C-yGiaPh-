<template>
    <div>
        <!-- Clan Name Management (Always available) -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card radius-10 border-0 shadow-sm overflow-hidden" :style="partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);' : 'background: #fff;'">
                    <div class="card-body p-4" :class="partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'text-white' : 'text-dark'">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-2">
                                    {{ partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'Chào mừng bạn đến với Hệ Thống Gia Phả!' : 'Quản Lý Thông Tin Dòng Họ' }}
                                </h4>
                                <p class="mb-0 opacity-75">Tên Dòng Họ sẽ được sử dụng làm tên Chi Nhánh chính của bạn trên hệ thống.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="input-group">
                                    <input type="text" class="form-control radius-8" :class="partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'border-0' : 'border-2'" v-model="newTenGoi" placeholder="Tên Dòng Họ (VD: Trần Gia)">
                                    <button class="btn fw-bold radius-8 ms-2 px-4" :class="partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'btn-warning' : 'btn-primary'" @click="updatePartnerInfo">
                                        {{ partnerProfile && (partnerProfile.ten_goi === 'Gói Đối Tác' || !partnerProfile.ten_goi) ? 'Cập Nhật Ngay' : 'Đổi Tên' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Thành Viên Dòng Họ</p>
                                <h4 class="my-1 text-info">{{ stats.total_members || 0 }}</h4>
                                <p class="mb-0 font-13">Tổng số thành viên đã thêm</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
                                <i class='bx bxs-group'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Số Đời Tộc Họ</p>
                                <h4 class="my-1 text-danger">{{ stats.max_generation || 0 }}</h4>
                                <p class="mb-0 font-13">Thế hệ cao nhất ghi nhận</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
                                <i class='bx bxs-share-alt'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Sự Kiện Sắp Tới</p>
                                <h4 class="my-1 text-success">{{ stats.upcoming_events || 0 }}</h4>
                                <p class="mb-0 font-13">Sự kiện trong dòng họ</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                                <i class='bx bxs-calendar-event'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->

        <div class="row">
            <div class="col-12">
                <div class="card radius-10">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold"><i class="bx bx-time-five me-1"></i> Cập Nhật Thành Viên Gần Đây</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Họ và Tên</th>
                                        <th>Mối Quan Hệ</th>
                                        <th>Ngày Cập Nhật</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!stats.recent_members || stats.recent_members.length === 0">
                                        <td colspan="4" class="text-center py-4 text-muted">Chưa có dữ liệu thành viên mới cập nhật.</td>
                                    </tr>
                                    <tr v-for="item in stats.recent_members" :key="item.id">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img :src="item.avatar || 'https://ui-avatars.com/api/?name=' + item.ho_ten" class="rounded-circle me-2" width="35" height="35">
                                                <div class="fw-bold">{{ item.ho_ten }}</div>
                                            </div>
                                        </td>
                                        <td>{{ item.loai_quan_he }}</td>
                                        <td>{{ formatDate(item.updated_at) }}</td>
                                        <td>
                                            <span class="badge bg-gradient-quepal text-white shadow-sm w-100">Đã cập nhật</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
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
    name: 'PartnerDashboard',
    data() {
        return {
            partnerProfile: null,
            stats: {
                total_members: 0,
                max_generation: 0,
                upcoming_events: 0,
                recent_members: []
            },
            newTenGoi: '',
            isLoading: false
        }
    },
    mounted() {
        this.loadProfile();
        this.loadStats();
    },
    methods: {
        getHeaders() {
            return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
        },
        loadProfile() {
            axios.get('http://127.0.0.1:8000/api/doi-tac/get-profile', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.partnerProfile = res.data.data;
                        this.newTenGoi = this.partnerProfile.ten_goi !== 'Gói Đối Tác' ? this.partnerProfile.ten_goi : '';
                    }
                });
        },
        loadStats() {
            axios.get('http://127.0.0.1:8000/api/doi-tac/statistics', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.stats = res.data.data;
                    }
                });
        },
        updatePartnerInfo() {
            if (!this.newTenGoi) {
                toastr.warning('Vui lòng nhập tên dòng họ/tổ chức!');
                return;
            }
            axios.post('http://127.0.0.1:8000/api/doi-tac/update-profile', { ten_goi: this.newTenGoi }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadProfile();
                    }
                });
        },
        formatDate(dateString) {
            if (!dateString) return '---';
            return new Date(dateString).toLocaleDateString('vi-VN');
        }
    }
}
</script>

<style scoped>
.radius-8 { border-radius: 8px; }
.bg-gradient-scooter { background: linear-gradient(135deg, #36d1dc, #5b86e5) !important; }
.bg-gradient-bloody { background: linear-gradient(135deg, #f5515f, #a1051d) !important; }
.bg-gradient-ohhappiness { background: linear-gradient(135deg, #00b09b, #96c93d) !important; }
</style>
