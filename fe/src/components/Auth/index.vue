<template>
    <div class="row">
        <div class="col-12">
            <!-- Khi người dùng chưa có cây gia phả nào (Không có email khớp) -->
            <div v-if="!isLoading && listChiNhanh.length === 0" class="card border-0 shadow-sm radius-15 p-5 text-center mt-4">
                <div class="mb-4">
                    <i class='bx bx-ghost display-1 text-muted opacity-25' style="font-size: 80px !important;"></i>
                </div>
                <h3 class="fw-bold text-dark mb-3">Hiện tại chưa có cây gia phả nào!</h3>
                <p class="text-muted fs-6 mx-auto" style="max-width: 600px;">
                    Tài khoản của bạn chưa được liên kết với bất kỳ dòng họ nào.<br>
                    Hãy liên hệ với <strong>Trưởng Tộc (Người quản lý gia phả)</strong> để họ thêm Email của bạn vào danh sách thành viên.
                </p>
            </div>

            <!-- Khi đã có dữ liệu Cây gia phả -->
            <div v-else-if="!isLoading && listChiNhanh.length > 0">
                <div class="card border-0 shadow-sm radius-15 mb-4">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <h5 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                            <i class="bx bx-sitemap fs-4"></i> Phả Hệ Dòng Tộc Của Bạn
                        </h5>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted fw-semibold">Gia phả:</span>
                            <select class="form-select radius-10 border-2 shadow-none font-weight-bold text-dark" v-model="selectedBranch" @change="loadMembers" style="min-width: 250px;">
                                <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Danh sách thông tin người của dòng họ -->
                <div class="card border-0 shadow-sm radius-15">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-uppercase">Danh sách thành viên (Hiển thị Tóm tắt)</h6>
                        <div class="row g-4">
                            <div v-for="member in listMembers" :key="member.id" class="col-md-4 col-lg-3 col-xl-2">
                                <div class="member-card p-3 text-center border radius-15 h-100 position-relative">
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <i v-if="member.gioi_tinh === 'Nam'" class='bx bx-male-sign text-primary fs-5'></i>
                                        <i v-else class='bx bx-female-sign text-danger fs-5'></i>
                                    </div>
                                    <img :src="member.avatar || 'https://ui-avatars.com/api/?name=' + member.ho_ten" class="rounded-circle mb-3 border border-3 border-light shadow-sm" width="70" height="70">
                                    <h6 class="fw-bold mb-1 text-dark">{{ member.ho_ten }}</h6>
                                    <div class="badge bg-light text-dark border rounded-pill mb-3">Đời thứ {{ member.doi_thu || 1 }}</div>
                                    <div class="text-start bg-light p-2 rounded-3 small">
                                        <div class="text-truncate" title="Trạng thái"><i class='bx bx-pulse text-success me-1'></i> {{ member.trang_thai || 'Còn sống' }}</div>
                                        <div class="text-truncate" title="Ngày sinh"><i class='bx bx-calendar text-warning me-1'></i> {{ member.ngay_sinh || 'Đang cập nhật' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="listMembers.length === 0" class="col-12 text-center py-4 text-muted">
                                Đang chờ cập nhật dữ liệu thành viên...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    name: 'NguoiDungGiaPha',
    data() {
        return {
            isLoading: true,
            listChiNhanh: [],
            selectedBranch: null,
            listMembers: []
        }
    },
    mounted() { this.fetchBranches(); },
    methods: {
        getHeaders() { return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } }; },
        fetchBranches() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listChiNhanh = res.data.data;
                        if (this.listChiNhanh.length > 0) {
                            this.selectedBranch = this.listChiNhanh[0].id;
                            this.loadMembers();
                        }
                    }
                }).finally(() => { this.isLoading = false; });
        },
        loadMembers() {
            if (!this.selectedBranch) return;
            axios.get(`http://127.0.0.1:8000/api/thanh-vien/chi-nhanh/${this.selectedBranch}`, this.getHeaders())
                .then(res => { if (res.data.status) { this.listMembers = res.data.data; } });
        }
    }
}
</script>
<style scoped>.member-card { transition: all 0.3s ease; } .member-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); border-color: #0d6efd !important; }</style>