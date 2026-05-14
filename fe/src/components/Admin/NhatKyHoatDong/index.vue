<template>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 radius-10">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-history text-primary"></i> Nhật Ký Hoạt
                                Động</h5>
                            <p class="mb-0 text-secondary small">Tổng hợp các hoạt động quản trị và sự kiện hệ thống.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <button class="btn btn-outline-primary radius-30 px-4">Xuất báo cáo</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label fw-semibold small text-uppercase">Tìm kiếm</label>
                            <input type="text" class="form-control radius-10 border-2 shadow-none" v-model="searchQuery"
                                placeholder="Nhập từ khóa...">
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label fw-semibold small text-uppercase">Người dùng</label>
                            <select class="form-select radius-10 border-2 shadow-none" v-model="selectedUser">
                                <option :value="null">Tất cả người dùng</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label fw-semibold small text-uppercase">Loại hoạt động</label>
                            <select class="form-select radius-10 border-2 shadow-none" v-model="selectedType">
                                <option value="all">Tất cả</option>
                                <option v-for="type in types" :key="type.value" :value="type.value">{{ type.label }}
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label fw-semibold small text-uppercase">Khoảng thời gian</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control radius-10 border-2 shadow-none"
                                    v-model="startDate">
                                <input type="date" class="form-control radius-10 border-2 shadow-none"
                                    v-model="endDate">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card radius-15 border-0 shadow-sm p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <p class="mb-1 text-secondary small">Số bản ghi</p>
                                        <h4 class="mb-0 fw-bold">{{ filteredLogs.length }}</h4>
                                    </div>
                                    <div class="icon-box bg-light rounded-circle p-3">
                                        <i class="bx bx-list-check text-primary fs-4"></i>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small">Dữ liệu hiện tại hiển thị dựa trên bộ lọc.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card radius-15 border-0 shadow-sm p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <p class="mb-1 text-secondary small">Hoạt động mới nhất</p>
                                        <h4 class="mb-0 fw-bold">{{ latestAction }}</h4>
                                    </div>
                                    <div class="icon-box bg-light rounded-circle p-3">
                                        <i class="bx bx-time-five text-success fs-4"></i>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small">Hiển thị sự kiện mới nhất trong nhật ký.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card radius-15 border-0 shadow-sm p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <p class="mb-1 text-secondary small">Người dùng hoạt động</p>
                                        <h4 class="mb-0 fw-bold">{{ uniqueUsers }}</h4>
                                    </div>
                                    <div class="icon-box bg-light rounded-circle p-3">
                                        <i class="bx bx-user-circle text-danger fs-4"></i>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small">Số người dùng khác nhau trong nhật ký.</p>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-list">
                        <div v-for="item in filteredLogs" :key="item.id" class="timeline-item mb-4">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content p-4 bg-white border radius-15 shadow-sm">
                                <div
                                    class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3">
                                    <div>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-semibold">{{
                                                item.type }}</span>
                                        <h6 class="mt-3 mb-2 fw-bold">{{ item.title }}</h6>
                                        <p class="mb-2 text-secondary">{{ item.description }}</p>
                                    </div>
                                    <div class="text-md-end">
                                        <p class="mb-1 text-secondary small">{{ item.user }}</p>
                                        <p class="mb-0 text-muted small">{{ item.timestamp }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark">IP: {{ item.ip }}</span>
                                    <span class="badge bg-light text-dark">Trạng thái: {{ item.status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="!filteredLogs.length" class="text-center py-5 text-muted">
                        <i class="bx bx-info-circle fs-1"></i>
                        <p class="mb-0 mt-3">Không có nhật ký phù hợp với bộ lọc hiện tại.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NhatKyHoatDong',
    data() {
        return {
            searchQuery: '',
            selectedUser: null,
            selectedType: 'all',
            startDate: '',
            endDate: '',
            users: [
                { id: 1, name: 'Admin Nguyễn' },
                { id: 2, name: 'Quản trị viên Linh' },
                { id: 3, name: 'Người dùng Nam' }
            ],
            types: [
                { value: 'Đăng nhập', label: 'Đăng nhập' },
                { value: 'Cập nhật', label: 'Cập nhật' },
                { value: 'Xóa', label: 'Xóa' },
                { value: 'Tạo mới', label: 'Tạo mới' }
            ],
            logs: [
                {
                    id: 1,
                    userId: 1,
                    user: 'Admin Nguyễn',
                    type: 'Đăng nhập',
                    title: 'Đăng nhập vào hệ thống',
                    description: 'Admin Nguyễn đã đăng nhập thành công từ trình duyệt Chrome.',
                    timestamp: '09:12',
                    ip: '192.168.1.10',
                    status: 'Thành công',
                    date: '2026-05-14'
                },
                {
                    id: 2,
                    userId: 2,
                    user: 'Quản trị viên Linh',
                    type: 'Cập nhật',
                    title: 'Cập nhật thông tin thành viên',
                    description: 'Đã chỉnh sửa hồ sơ thành viên Nguyễn Văn A.',
                    timestamp: '08:45',
                    ip: '192.168.1.12',
                    status: 'Thành công',
                    date: '2026-05-14'
                },
                {
                    id: 3,
                    userId: 3,
                    user: 'Người dùng Nam',
                    type: 'Tạo mới',
                    title: 'Thêm mới hồ sơ thành viên',
                    description: 'Thêm thành viên mới vào dòng họ Nguyễn.',
                    timestamp: '17:30',
                    ip: '192.168.1.22',
                    status: 'Thành công',
                    date: '2026-05-13'
                },
                {
                    id: 4,
                    userId: 1,
                    user: 'Admin Nguyễn',
                    type: 'Xóa',
                    title: 'Xóa bản ghi nhật ký',
                    description: 'Xóa một bản ghi lỗi hệ thống để dọn sạch dữ liệu thử nghiệm.',
                    timestamp: '14:50',
                    ip: '192.168.1.10',
                    status: 'Thành công',
                    date: '2026-05-12'
                }
            ]
        };
    },
    computed: {
        filteredLogs() {
            return this.logs.filter(item => {
                const matchesSearch = this.searchQuery
                    ? item.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    item.description.toLowerCase().includes(this.searchQuery.toLowerCase())
                    : true;
                const matchesUser = this.selectedUser ? item.userId === this.selectedUser : true;
                const matchesType = this.selectedType !== 'all' ? item.type === this.selectedType : true;
                const matchesDate = (() => {
                    if (!this.startDate && !this.endDate) return true;
                    const [year, month, day] = item.date.split('-').map(Number);
                    const itemDate = new Date(year, month - 1, day);
                    const from = this.startDate ? new Date(this.startDate) : null;
                    const to = this.endDate ? new Date(this.endDate) : null;
                    if (from && itemDate < from) return false;
                    if (to && itemDate > to) return false;
                    return true;
                })();
                return matchesSearch && matchesUser && matchesType && matchesDate;
            }).sort((a, b) => {
                const parseDateTime = entry => {
                    const [year, month, day] = entry.date.split('-').map(Number);
                    const [hour, minute] = entry.timestamp.split(':').map(Number);
                    return new Date(year, month - 1, day, hour, minute);
                };
                return parseDateTime(b) - parseDateTime(a);
            });
        },
        latestAction() {
            return this.filteredLogs.length ? this.filteredLogs[0].title : 'Không có hoạt động';
        },
        uniqueUsers() {
            return [...new Set(this.filteredLogs.map(item => item.userId))].length;
        }
    }
};
</script>

<style scoped>
.timeline-list {
    position: relative;
    padding-left: 20px;
}

.timeline-list::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    width: 2px;
    height: 100%;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
}

.timeline-dot {
    position: absolute;
    left: 2px;
    top: 18px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #0d6efd;
    border: 3px solid #fff;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
}

.timeline-content {
    margin-left: 35px;
}

.icon-box {
    width: 54px;
    height: 54px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 767px) {
    .timeline-content {
        margin-left: 0;
    }

    .timeline-item {
        padding-left: 0;
    }
}
</style>