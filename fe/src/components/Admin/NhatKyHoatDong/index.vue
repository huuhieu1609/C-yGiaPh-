<template>
    <div class="row g-4">
        <div class="col-12">
            <div class="card luxury-panel border-0 shadow-sm">
                <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h5 class="mb-0 fw-bold panel-title text-dark"><i class="bx bx-history me-2 text-warning"></i> Nhật Ký Hoạt Động Hệ Thống</h5>
                        <p class="mb-0 text-secondary small mt-1">Giám sát các thao tác quản trị viên và nhật ký bảo mật của hệ thống.</p>
                    </div>
                    <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="fetchLogs" :disabled="isLoading" title="Làm mới nhật ký">
                        <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    <!-- Filters Grid -->
                    <div class="row g-3 mb-4 p-3 bg-light-subtle rounded-3 border border-light-subtle">
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label fw-bold text-secondary-custom small text-uppercase">Tìm kiếm</label>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" v-model="searchQuery" placeholder="Nhập từ khóa...">
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label fw-bold text-secondary-custom small text-uppercase">Người dùng</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="selectedUser">
                                <option :value="null">Tất cả người dùng</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label fw-bold text-secondary-custom small text-uppercase">Loại hoạt động</label>
                            <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="selectedType">
                                <option value="all">Tất cả</option>
                                <option v-for="type in types" :key="type.value" :value="type.value">{{ type.label }}</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label fw-bold text-secondary-custom small text-uppercase">Khoảng thời gian</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control premium-input radius-10 border-2 shadow-none" v-model="startDate">
                                <input type="date" class="form-control premium-input radius-10 border-2 shadow-none" v-model="endDate">
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div class="card stat-card radius-15 border-0 shadow-sm p-4 h-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1 text-secondary-custom small">Số bản ghi lọc</p>
                                        <h3 class="mb-0 fw-bold text-dark">{{ filteredLogs.length }}</h3>
                                    </div>
                                    <div class="icon-box-premium rounded-circle bg-blue-subtle">
                                        <i class="bx bx-list-check text-primary fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stat-card radius-15 border-0 shadow-sm p-4 h-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="overflow-hidden me-2">
                                        <p class="mb-1 text-secondary-custom small">Hoạt động gần nhất</p>
                                        <h5 class="mb-0 fw-bold text-dark text-truncate" :title="latestAction">{{ latestAction }}</h5>
                                    </div>
                                    <div class="icon-box-premium rounded-circle bg-emerald-subtle">
                                        <i class="bx bx-time-five text-success fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card stat-card radius-15 border-0 shadow-sm p-4 h-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1 text-secondary-custom small">Người dùng thao tác</p>
                                        <h3 class="mb-0 fw-bold text-dark">{{ uniqueUsers }}</h3>
                                    </div>
                                    <div class="icon-box-premium rounded-circle bg-amber-subtle">
                                        <i class="bx bx-user-circle text-warning fs-3"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline List -->
                    <div class="timeline-list">
                        <div v-if="isLoading" class="text-center py-5">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        
                        <template v-else>
                            <div v-for="item in filteredLogs" :key="item.id" class="timeline-item mb-4 animate-fade-in">
                                <div class="timeline-dot" :class="itemTypeClass(item.type)"></div>
                                <div class="timeline-content p-4 bg-white border radius-15 shadow-sm">
                                    <div class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3">
                                        <div>
                                            <span class="badge badge-type px-3 py-1.5 fw-bold" :class="badgeTypeClass(item.type)">{{ item.type }}</span>
                                            <h6 class="mt-3 mb-2 fw-bold text-dark">{{ item.title }}</h6>
                                            <p class="mb-2 text-secondary small">{{ item.description }}</p>
                                        </div>
                                        <div class="text-md-end">
                                            <p class="mb-1 text-dark fw-bold small"><i class="bx bx-user me-1"></i>{{ item.user }}</p>
                                            <p class="mb-0 text-muted small"><i class="bx bx-calendar me-1"></i>{{ item.date }} {{ item.timestamp }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex flex-wrap gap-2 pt-2 border-top border-light-subtle align-items-center justify-content-between">
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-light text-dark border border-light-subtle">IP: {{ item.ip }}</span>
                                            <span class="badge" :class="item.status === 'Thành công' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'">
                                                Trạng thái: {{ item.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="!filteredLogs.length" class="text-center py-5 text-muted">
                                <i class="bx bx-info-circle fs-1 opacity-50 mb-2"></i>
                                <p class="mb-0">Không có nhật ký phù hợp với bộ lọc hiện tại.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'NhatKyHoatDong',
    data() {
        return {
            searchQuery: '',
            selectedUser: null,
            selectedType: 'all',
            startDate: '',
            endDate: '',
            isLoading: false,
            types: [
                { value: 'Cập nhật', label: 'Cập nhật/Chỉnh sửa' },
                { value: 'Tạo mới', label: 'Tạo mới/Thêm' },
                { value: 'Xóa', label: 'Xóa' },
                { value: 'Hệ thống', label: 'Hệ thống' }
            ],
            logs: []
        };
    },
    mounted() {
        this.fetchLogs();
    },
    computed: {
        users() {
            const userMap = {};
            this.logs.forEach(log => {
                if (log.userId && log.user) {
                    userMap[log.userId] = log.user;
                }
            });
            return Object.keys(userMap).map(id => ({
                id: parseInt(id),
                name: userMap[id]
            }));
        },
        filteredLogs() {
            return this.logs.filter(item => {
                if (item.type === 'Đăng nhập') return false;
                const matchesSearch = this.searchQuery
                    ? (item.title && item.title.toLowerCase().includes(this.searchQuery.toLowerCase())) ||
                      (item.description && item.description.toLowerCase().includes(this.searchQuery.toLowerCase()))
                    : true;
                const matchesUser = this.selectedUser ? item.userId === this.selectedUser : true;
                const matchesType = this.selectedType !== 'all' ? item.type === this.selectedType : true;
                const matchesDate = (() => {
                    if (!this.startDate && !this.endDate) return true;
                    if (!item.date) return true;
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
    },
    methods: {
        getHeaders() {
            return {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            };
        },
        async fetchLogs() {
            this.isLoading = true;
            try {
                const res = await axios.get('http://127.0.0.1:8000/api/nhat-ky-hoat-dong/get-data', this.getHeaders());
                if (res.data.status) {
                    this.logs = res.data.data.map(item => {
                        const dateObj = new Date(item.created_at || item.thoi_gian || new Date());
                        const hours = String(dateObj.getHours()).padStart(2, '0');
                        const minutes = String(dateObj.getMinutes()).padStart(2, '0');
                        const year = dateObj.getFullYear();
                        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                        const day = String(dateObj.getDate()).padStart(2, '0');

                        // Phân loại hoạt động
                        let actionType = 'Hệ thống';
                        const actionLower = (item.hanh_dong || '').toLowerCase();
                        if (actionLower.includes('đăng nhập') || actionLower.includes('login')) {
                          actionType = 'Đăng nhập';
                        } else if (actionLower.includes('tạo') || actionLower.includes('thêm') || actionLower.includes('create') || actionLower.includes('insert')) {
                          actionType = 'Tạo mới';
                        } else if (actionLower.includes('cập nhật') || actionLower.includes('sửa') || actionLower.includes('update') || actionLower.includes('edit')) {
                          actionType = 'Cập nhật';
                        } else if (actionLower.includes('xóa') || actionLower.includes('delete') || actionLower.includes('destroy')) {
                          actionType = 'Xóa';
                        }

                        return {
                            id: item.id,
                            userId: item.nguoi_dung_id || 999,
                            user: item.nguoi_dung ? item.nguoi_dung.ho_ten : 'Hệ thống',
                            type: actionType,
                            title: item.hanh_dong || 'Hoạt động hệ thống',
                            description: `${item.nguoi_dung ? item.nguoi_dung.ho_ten : 'Hệ thống'} đã thực hiện hành động: "${item.hanh_dong || ''}"`,
                            timestamp: `${hours}:${minutes}`,
                            ip: item.ip || '127.0.0.1',
                            status: item.trang_thai || 'Thành công',
                            date: `${year}-${month}-${day}`,
                            chi_tiet: item.chi_tiet
                        };
                    });
                }
            } catch (error) {
                console.error('Error fetching activity logs:', error);
            } finally {
                this.isLoading = false;
            }
        },
        itemTypeClass(type) {
            if (type === 'Đăng nhập') return 'dot-login';
            if (type === 'Tạo mới') return 'dot-create';
            if (type === 'Cập nhật') return 'dot-update';
            if (type === 'Xóa') return 'dot-delete';
            return 'dot-system';
        },
        badgeTypeClass(type) {
            if (type === 'Đăng nhập') return 'badge-login';
            if (type === 'Tạo mới') return 'badge-create';
            if (type === 'Cập nhật') return 'badge-update';
            if (type === 'Xóa') return 'badge-delete';
            return 'badge-system';
        }
    }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap');

.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02) !important;
    transition: background-color 0.3s, border-color 0.3s;
}

.panel-title {
    font-size: 14.5px;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
}

.text-secondary-custom {
    color: #4b5563;
    font-size: 12px;
    letter-spacing: 0.3px;
    font-family: 'Outfit', sans-serif;
}

.radius-10 {
    border-radius: 10px !important;
}

.radius-15 {
    border-radius: 15px !important;
}

.radius-30 {
    border-radius: 30px !important;
}

.premium-input {
    background-color: #f8fafc !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    font-family: 'Inter', sans-serif;
    color: #334155 !important;
    padding: 10px 14px !important;
    font-size: 13px !important;
    transition: all 0.25s ease !important;
}

.premium-input:focus {
    border-color: #f39c12 !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1) !important;
}

/* Timeline lists */
.timeline-list {
    position: relative;
    padding-left: 24px;
}

.timeline-list::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 10px;
    width: 2px;
    height: calc(100% - 20px);
    background: #f1f5f9;
}

.timeline-item {
    position: relative;
}

.timeline-dot {
    position: absolute;
    left: 2px;
    top: 22px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #64748b;
    border: 4px solid #fff;
    box-shadow: 0 0 0 3px rgba(100, 116, 139, 0.1);
    z-index: 2;
}

.dot-login { background: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
.dot-create { background: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
.dot-update { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
.dot-delete { background: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1); }
.dot-system { background: #06b6d4; box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1); }

.timeline-content {
    margin-left: 20px;
    border-radius: 16px !important;
    border-color: rgba(0,0,0,0.04) !important;
    transition: all 0.25s ease;
}
.timeline-content:hover {
    transform: translateX(4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.03) !important;
}

.icon-box-premium {
    width: 48px;
    height: 48px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.bg-blue-subtle { background-color: #eef2ff !important; }
.bg-emerald-subtle { background-color: #f0fdf4 !important; }
.bg-amber-subtle { background-color: #fffbeb !important; }

/* Badges styling */
.badge-type {
    border-radius: 20px;
}

.badge-login { background: rgba(79, 70, 229, 0.08) !important; color: #4f46e5 !important; border: 1px solid rgba(79, 70, 229, 0.15); }
.badge-create { background: rgba(16, 185, 129, 0.08) !important; color: #10b981 !important; border: 1px solid rgba(16, 185, 129, 0.15); }
.badge-update { background: rgba(245, 158, 11, 0.08) !important; color: #d97706 !important; border: 1px solid rgba(245, 158, 11, 0.15); }
.badge-delete { background: rgba(239, 68, 68, 0.08) !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.15); }
.badge-system { background: rgba(6, 182, 212, 0.08) !important; color: #0891b2 !important; border: 1px solid rgba(6, 182, 212, 0.15); }

.bg-success-subtle { background: #f0fdf4 !important; color: #16a34a !important; }

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Circular reload button */
.btn-refresh-premium {
    background: rgba(0, 0, 0, 0.03) !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    width: 36px;
    height: 36px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    transition: all 0.25s ease;
}
.btn-refresh-premium:hover {
    transform: rotate(30deg) scale(1.05);
    border-color: #f39c12 !important;
    background: rgba(243, 156, 18, 0.05) !important;
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