<template>
    <div class="row g-4">
        <!-- Left Column: Add/Edit Form -->
        <div class="col-lg-5 col-md-12">
            <div class="card luxury-panel border-0 shadow-sm">
                <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center">
                    <h5 class="mb-0 fw-bold panel-title text-dark text-gradient-gold">
                        <i class="bx bx-plus-circle me-2"></i> {{ isEditing ? 'Cập Nhật Gói Dịch Vụ' : 'Tạo Gói Dịch Vụ Mới' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary-custom">Tên Gói Dịch Vụ</label>
                            <input type="text" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="VD: Gói Gia Đình Nâng Cao" v-model="formData.ten_goi" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary-custom">Giá Cả (VNĐ)</label>
                            <input type="number" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="VD: 100000" v-model="formData.gia_ca" required>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold text-secondary-custom">Thời Hạn (Tháng)</label>
                                <input type="number" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="VD: 12" v-model="formData.thoi_han" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold text-secondary-custom">Trạng Thái</label>
                                <select class="form-select premium-input radius-10 border-2 shadow-none" v-model="formData.trang_thai" required>
                                    <option value="Hoạt động">Hoạt động</option>
                                    <option value="Tạm dừng">Tạm dừng</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-4 mb-3">
                                <label class="form-label fw-bold text-secondary-custom">Giới Hạn Đời</label>
                                <input type="number" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="999 = Vô hạn" v-model="formData.max_doi" required>
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label fw-bold text-secondary-custom">Thành Viên</label>
                                <input type="number" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="99999 = Vô hạn" v-model="formData.max_thanh_vien" required>
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label fw-bold text-secondary-custom">Chi Nhánh</label>
                                <input type="number" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="VD: 1" v-model="formData.max_chi_nhanh" required>
                            </div>
                        </div>

                        <!-- FEATURE SELECTOR -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary-custom d-flex align-items-center justify-content-between">
                                <span><i class="bx bx-check-shield me-1 text-warning"></i> Tính Năng Đặc Quyền</span>
                                <span class="badge bg-warning text-dark fw-bold" style="font-size: 11px;">{{ selectedFeatureCount }}/{{ totalFeatureCount }} đã chọn</span>
                            </label>

                            <div class="feature-panel">
                                <div v-for="group in featureGroups" :key="group.key" class="feature-group mb-2">
                                    <!-- Group Header -->
                                    <div class="feature-group-header d-flex align-items-center gap-2 py-2 px-3" @click="toggleGroup(group.key)">
                                        <input type="checkbox"
                                            class="feature-master-check"
                                            :checked="isGroupAllChecked(group)"
                                            :indeterminate.prop="isGroupIndeterminate(group)"
                                            @change="toggleGroupAll(group, $event)"
                                            @click.stop>
                                        <i :class="group.icon" class="text-warning fs-6"></i>
                                        <span class="fw-bold" style="font-size: 12.5px;">{{ group.label }}</span>
                                        <i class="bx ms-auto" :class="openGroups[group.key] ? 'bx-chevron-up' : 'bx-chevron-down'" style="font-size: 14px; color: #94a3b8;"></i>
                                    </div>
                                    <!-- Feature List -->
                                    <div v-if="openGroups[group.key]" class="feature-list px-3 pb-2">
                                        <label v-for="feat in group.features" :key="feat.key"
                                            class="feature-item d-flex align-items-start gap-2 py-2 px-2 rounded mb-1"
                                            :class="{'feature-item-checked': formData.features.includes(feat.key)}">
                                            <input type="checkbox"
                                                class="feature-check mt-1"
                                                :value="feat.key"
                                                v-model="formData.features">
                                            <div>
                                                <div class="fw-semibold" style="font-size: 12.5px; color: #1e293b;">{{ feat.label }}</div>
                                                <div class="text-muted" style="font-size: 11px; line-height: 1.4;">{{ feat.desc }}</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary-custom">Mô Tả Gói</label>
                            <textarea rows="3" class="form-control premium-input radius-10 border-2 shadow-none" placeholder="Nhập mô tả ngắn về gói..." v-model="formData.mo_ta"></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <button type="button" class="btn btn-outline-secondary radius-30 px-4" v-if="isEditing" @click="resetForm">Hủy</button>
                            <button type="submit" class="btn btn-filter-submit text-white radius-30 px-4 fw-bold shadow-sm">
                                {{ isEditing ? 'Cập Nhật' : 'Tạo Mới' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Data Table -->
        <div class="col-lg-7 col-md-12">
            <div class="card luxury-panel border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-4 border-0 border-bottom border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <h5 class="mb-0 fw-bold panel-title text-dark">
                        <i class="bx bx-package me-2 text-warning"></i> Cấu Hình Gói Dịch Vụ Hệ Thống
                    </h5>
                    <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="loadData" :disabled="isLoading" title="Làm mới dữ liệu">
                        <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-10 overflow-hidden border-2 search-box-premium shadow-sm">
                        <input type="text" class="form-control border-0 shadow-none ps-4 bg-transparent" placeholder="Tìm kiếm gói dịch vụ..." v-model="searchQuery">
                        <span class="input-group-text border-0 bg-transparent pe-4"><i class="bx bx-search text-secondary"></i></span>
                    </div>

                    <div class="table-responsive rounded-3 border border-light-subtle">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="22%">Tên Gói</th>
                                    <th width="13%" class="text-end">Giá Cả</th>
                                    <th width="11%" class="text-center">Thời Hạn</th>
                                    <th width="32%">Tính Năng</th>
                                    <th width="10%" class="text-center">Trạng Thái</th>
                                    <th width="7%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="7" class="text-center py-5">
                                        <div class="spinner-border text-warning" role="status"><span class="visually-hidden">Loading...</span></div>
                                    </td>
                                </tr>
                                <tr v-else-if="filteredList.length === 0">
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block opacity-50"></i>
                                        Chưa có gói dịch vụ nào
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in filteredList" :key="item.id">
                                    <td class="text-center fw-bold text-muted" style="font-size:12px;">{{ index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size: 13px;">{{ item.ten_goi }}</div>
                                        <div class="small text-muted mt-1" style="font-size: 11px;">{{ item.mo_ta || 'Không có mô tả' }}</div>
                                    </td>
                                    <td class="text-end fw-bold text-success">{{ formatCurrency(item.gia_ca) }}</td>
                                    <td class="text-center fw-semibold text-secondary" style="font-size: 12px;">{{ item.thoi_han }} tháng</td>
                                    <td>
                                        <!-- Limits -->
                                        <div class="d-flex gap-2 flex-wrap mb-1">
                                            <span class="feat-badge feat-badge-limit">
                                                <i class="bx bx-layer me-1"></i>{{ item.max_doi >= 999 ? 'Vô hạn đời' : item.max_doi + ' đời' }}
                                            </span>
                                            <span class="feat-badge feat-badge-limit">
                                                <i class="bx bx-group me-1"></i>{{ item.max_thanh_vien >= 99999 ? 'Vô hạn TV' : item.max_thanh_vien + ' TV' }}
                                            </span>
                                            <span class="feat-badge feat-badge-limit">
                                                <i class="bx bx-git-branch me-1"></i>{{ item.max_chi_nhanh >= 999 ? 'Vô hạn CN' : (item.max_chi_nhanh || 1) + ' CN' }}
                                            </span>
                                        </div>
                                        <!-- Features -->
                                        <div class="d-flex gap-1 flex-wrap">
                                            <template v-if="getItemFeatures(item).length > 0">
                                                <span v-for="feat in getItemFeatures(item).slice(0, 4)" :key="feat.key"
                                                    class="feat-badge feat-badge-feature"
                                                    :title="feat.label + ': ' + feat.desc">
                                                    <i :class="feat.icon" class="me-1"></i>{{ feat.label }}
                                                </span>
                                                <span v-if="getItemFeatures(item).length > 4"
                                                    class="feat-badge feat-badge-more"
                                                    :title="getItemFeatures(item).slice(4).map(f => f.label).join(', ')">
                                                    +{{ getItemFeatures(item).length - 4 }} khác
                                                </span>
                                            </template>
                                            <span v-else class="text-muted" style="font-size: 11px; font-style: italic;">Chưa cấu hình tính năng</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button @click="changeStatus(item.id)"
                                            :class="item.trang_thai === 'Hoạt động' ? 'btn-status-active' : 'btn-status-locked'"
                                            class="btn-status-toggle w-100 fw-bold">
                                            {{ item.trang_thai }}
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-action-edit" @click="editItem(item)" title="Sửa">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                            <button class="btn btn-action-delete" @click="deleteItem(item.id)" title="Xóa">
                                                <i class="bx bx-trash"></i>
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

// ============================================================
// ĐỊNH NGHĨA TẤT CẢ TÍNH NĂNG HỆ THỐNG (CÓ THỂ MỞ RỘNG)
// ============================================================
const FEATURE_GROUPS = [
    {
        key: 'gia_pha',
        label: 'Gia Phả & Cây Dòng Họ',
        icon: 'bx bx-sitemap',
        features: [
            { key: 'tao_cay_gia_pha',    label: 'Tạo Cây Gia Phả',         icon: 'bx bx-sitemap',     desc: 'Khởi tạo và quản lý cây gia phả dòng họ riêng' },
            { key: 'them_thanh_vien',     label: 'Thêm Thành Viên',          icon: 'bx bx-user-plus',   desc: 'Thêm mới thành viên không giới hạn vào cây' },
            { key: 'sua_xoa_thanh_vien',  label: 'Sửa/Xóa Thành Viên',      icon: 'bx bx-edit',        desc: 'Chỉnh sửa và xóa thông tin thành viên dòng họ' },
            { key: 'quan_ly_vo_chong',    label: 'Quản Lý Vợ/Chồng',        icon: 'bx bx-heart',       desc: 'Thiết lập và quản lý quan hệ hôn nhân' },
            { key: 'quan_ly_con_nuoi',    label: 'Quản Lý Con Nuôi',         icon: 'bx bx-user',        desc: 'Thêm và quản lý con nuôi trong gia đình' },
            { key: 'xuat_pdf',            label: 'Xuất PDF Gia Phả',         icon: 'bx bx-file-pdf',    desc: 'Xuất toàn bộ cây gia phả ra file PDF' },
        ]
    },
    {
        key: 'quan_ly',
        label: 'Quản Lý & Điều Hành',
        icon: 'bx bx-cog',
        features: [
            { key: 'phe_duyet_de_xuat',   label: 'Phê Duyệt Đề Xuất',       icon: 'bx bx-check-circle', desc: 'Tiếp nhận và phê duyệt đề xuất chỉnh sửa từ thành viên' },
            { key: 'tu_dong_phe_duyet',   label: 'Tự Động Phê Duyệt',       icon: 'bx bx-check-double', desc: 'Bật/tắt chế độ phê duyệt tự động cho đề xuất' },
            { key: 'quan_ly_chi_nhanh',   label: 'Quản Lý Chi Nhánh',       icon: 'bx bx-git-branch',   desc: 'Tạo và quản lý nhiều chi nhánh dòng họ' },
            { key: 'phan_quyen_thanh_vien', label: 'Phân Quyền Thành Viên', icon: 'bx bx-shield',        desc: 'Cấp quyền quản lý cho các thành viên khác' },
            { key: 'nhat_ky_hoat_dong',   label: 'Nhật Ký Hoạt Động',       icon: 'bx bx-history',      desc: 'Xem lịch sử toàn bộ thao tác trong hệ thống' },
        ]
    },
    {
        key: 'su_kien',
        label: 'Sự Kiện & Tưởng Niệm',
        icon: 'bx bx-calendar-event',
        features: [
            { key: 'quan_ly_su_kien',     label: 'Quản Lý Sự Kiện',         icon: 'bx bx-calendar',     desc: 'Tạo, quản lý sự kiện họ tộc và lịch gặp mặt' },
            { key: 'dang_ky_su_kien',     label: 'Đăng Ký Sự Kiện',         icon: 'bx bx-calendar-check', desc: 'Thành viên đăng ký tham gia sự kiện' },
            { key: 'tuong_niem',          label: 'Tưởng Niệm',               icon: 'bx bx-moon',         desc: 'Thêm, xem tưởng niệm ngày giỗ, kỷ niệm' },
            { key: 'nhac_gio_tu',         label: 'Nhắc Ngày Giỗ Tự',        icon: 'bx bx-bell',         desc: 'Nhắc nhở tự động ngày giỗ âm lịch hàng năm' },
        ]
    },
    {
        key: 'tai_lieu',
        label: 'Tài Liệu & Lưu Trữ',
        icon: 'bx bx-book-open',
        features: [
            { key: 'quan_ly_tai_lieu',    label: 'Tủ Sách Tài Liệu',        icon: 'bx bx-folder',       desc: 'Upload và quản lý tài liệu, hình ảnh dòng họ' },
            { key: 'upload_hinh_anh',     label: 'Upload Hình Ảnh',          icon: 'bx bx-image',        desc: 'Đăng tải hình ảnh thành viên và sự kiện' },
            { key: 'album_gia_dinh',      label: 'Album Gia Đình',           icon: 'bx bx-images',       desc: 'Tạo và quản lý album ảnh theo từng gia đình' },
        ]
    },
    {
        key: 'ban_do',
        label: 'Bản Đồ & Địa Điểm',
        icon: 'bx bx-map',
        features: [
            { key: 'ban_do_mo_phan',      label: 'Bản Đồ Mộ Phần',          icon: 'bx bx-map-pin',      desc: 'Đánh dấu và quản lý vị trí mộ phần GPS' },
            { key: 'ban_do_nha_tho',      label: 'Bản Đồ Nhà Thờ Họ',       icon: 'bx bx-map',          desc: 'Lưu vị trí và thông tin nhà thờ họ' },
            { key: 'tra_cuu_ban_do',      label: 'Tra Cứu Bản Đồ',          icon: 'bx bx-search-alt',   desc: 'Tìm kiếm địa điểm liên quan trên bản đồ' },
        ]
    },
    {
        key: 'tai_chinh',
        label: 'Tài Chính & Đóng Góp',
        icon: 'bx bx-dollar-circle',
        features: [
            { key: 'quan_ly_dong_gop',    label: 'Quản Lý Đóng Góp',        icon: 'bx bx-donate-heart', desc: 'Ghi nhận và theo dõi đóng góp tài chính dòng họ' },
            { key: 'bao_cao_tai_chinh',   label: 'Báo Cáo Tài Chính',       icon: 'bx bx-bar-chart',    desc: 'Xem báo cáo thu chi quỹ dòng họ' },
        ]
    },
    {
        key: 'cao_cap',
        label: 'Tính Năng Cao Cấp',
        icon: 'bx bx-crown',
        features: [
            { key: 'tra_cuu_quan_he',     label: 'Tra Cứu Quan Hệ',         icon: 'bx bx-search',       desc: 'Tính toán và hiển thị mối quan hệ giữa hai thành viên' },
            { key: 'xuat_csv',            label: 'Xuất Dữ Liệu CSV',        icon: 'bx bx-spreadsheet',  desc: 'Xuất toàn bộ danh sách thành viên ra file Excel/CSV' },
            { key: 'thong_ke_nang_cao',   label: 'Thống Kê Nâng Cao',       icon: 'bx bx-analyse',      desc: 'Xem thống kê chi tiết về dòng họ theo thế hệ' },
            { key: 'api_tich_hop',        label: 'API Tích Hợp',             icon: 'bx bx-code-alt',     desc: 'Truy cập dữ liệu qua API dành cho developer' },
        ]
    }
];

// Flat map để tra cứu nhanh theo key
const FEATURE_MAP = {};
FEATURE_GROUPS.forEach(g => g.features.forEach(f => { FEATURE_MAP[f.key] = { ...f, groupKey: g.key }; }));

export default {
    name: 'GoiDichVuManagement',
    data() {
        return {
            listData: [],
            formData: {
                id: null,
                ten_goi: '',
                gia_ca: 0,
                thoi_han: 12,
                max_doi: 5,
                max_thanh_vien: 100,
                max_chi_nhanh: 1,
                mo_ta: '',
                trang_thai: 'Hoạt động',
                features: []
            },
            isEditing: false,
            isLoading: false,
            searchQuery: '',
            featureGroups: FEATURE_GROUPS,
            openGroups: Object.fromEntries(FEATURE_GROUPS.map(g => [g.key, g.key === 'gia_pha']))
        };
    },
    computed: {
        filteredList() {
            if (!this.searchQuery) return this.listData;
            const q = this.searchQuery.toLowerCase();
            return this.listData.filter(item =>
                (item.ten_goi && item.ten_goi.toLowerCase().includes(q)) ||
                (item.mo_ta && item.mo_ta.toLowerCase().includes(q))
            );
        },
        selectedFeatureCount() {
            return (this.formData.features || []).length;
        },
        totalFeatureCount() {
            return FEATURE_GROUPS.reduce((sum, g) => sum + g.features.length, 0);
        }
    },
    mounted() {
        this.loadData();
    },
    methods: {
        getHeaders() {
            return { headers: { Authorization: 'Bearer ' + localStorage.getItem('access_token') } };
        },
        toggleGroup(key) {
            this.openGroups[key] = !this.openGroups[key];
        },
        isGroupAllChecked(group) {
            return group.features.every(f => (this.formData.features || []).includes(f.key));
        },
        isGroupIndeterminate(group) {
            const checked = group.features.filter(f => (this.formData.features || []).includes(f.key)).length;
            return checked > 0 && checked < group.features.length;
        },
        toggleGroupAll(group, event) {
            const allKeys = group.features.map(f => f.key);
            if (event.target.checked) {
                const newSet = new Set([...(this.formData.features || []), ...allKeys]);
                this.formData.features = [...newSet];
            } else {
                this.formData.features = (this.formData.features || []).filter(k => !allKeys.includes(k));
            }
        },
        getItemFeatures(item) {
            const feats = item.features || [];
            const arr = typeof feats === 'string' ? (feats ? feats.split(',').map(s => s.trim()) : []) : feats;
            return arr.map(k => FEATURE_MAP[k]).filter(Boolean);
        },
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/goi-dich-vu/get-data', this.getHeaders())
            .then(res => {
                if (res.data.status) this.listData = res.data.data;
            })
            .catch(() => toastr.error('Lỗi tải dữ liệu gói dịch vụ!'))
            .finally(() => this.isLoading = false);
        },
        saveData() {
            const payload = {
                ...this.formData,
                features: (this.formData.features || []).join(',')
            };
            const url = this.isEditing
                ? 'http://127.0.0.1:8000/api/goi-dich-vu/update'
                : 'http://127.0.0.1:8000/api/goi-dich-vu/create';

            axios.post(url, payload, this.getHeaders())
            .then(res => {
                if (res.data.status) {
                    toastr.success(res.data.message);
                    this.loadData();
                    this.resetForm();
                } else {
                    toastr.error(res.data.message);
                }
            })
            .catch(() => toastr.error('Đã có lỗi xảy ra, vui lòng thử lại!'));
        },
        editItem(item) {
            this.isEditing = true;
            const feats = item.features || [];
            const arr = typeof feats === 'string' ? (feats ? feats.split(',').map(s => s.trim()) : []) : feats;
            this.formData = { ...item, features: arr };
            // Mở các nhóm có tính năng đang được chọn
            FEATURE_GROUPS.forEach(g => {
                if (g.features.some(f => arr.includes(f.key))) this.openGroups[g.key] = true;
            });
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        deleteItem(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa gói dịch vụ này?')) return;
            axios.post('http://127.0.0.1:8000/api/goi-dich-vu/delete', { id }, this.getHeaders())
            .then(res => {
                if (res.data.status) { toastr.success(res.data.message); this.loadData(); }
                else toastr.error(res.data.message);
            })
            .catch(() => toastr.error('Lỗi khi xóa gói dịch vụ!'));
        },
        changeStatus(id) {
            axios.post('http://127.0.0.1:8000/api/goi-dich-vu/status', { id }, this.getHeaders())
            .then(res => { if (res.data.status) { toastr.success(res.data.message); this.loadData(); } })
            .catch(() => toastr.error('Lỗi khi thay đổi trạng thái gói!'));
        },
        resetForm() {
            this.isEditing = false;
            this.formData = { id: null, ten_goi: '', gia_ca: 0, thoi_han: 12, max_doi: 5, max_thanh_vien: 100, max_chi_nhanh: 1, mo_ta: '', trang_thai: 'Hoạt động', features: [] };
            this.openGroups = Object.fromEntries(FEATURE_GROUPS.map(g => [g.key, g.key === 'gia_pha']));
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value || 0);
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap');

.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02) !important;
}
.panel-title { font-size: 14.5px; letter-spacing: 0.3px; font-family: 'Outfit', sans-serif; }
.text-gradient-gold {
    background: linear-gradient(135deg, #d4af37 0%, #f39c12 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.text-secondary-custom { color: #4b5563; font-size: 12.5px; letter-spacing: 0.3px; font-family: 'Outfit', sans-serif; }
.radius-10 { border-radius: 10px !important; }
.radius-30 { border-radius: 30px !important; }
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

/* ---- FEATURE PANEL ---- */
.feature-panel {
    background: #f8fafc;
    border: 1px solid rgba(0,0,0,0.06);
    border-radius: 12px;
    overflow: hidden;
    max-height: 340px;
    overflow-y: auto;
}
.feature-panel::-webkit-scrollbar { width: 4px; }
.feature-panel::-webkit-scrollbar-track { background: transparent; }
.feature-panel::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 2px; }

.feature-group { border-bottom: 1px solid rgba(0,0,0,0.04); }
.feature-group:last-child { border-bottom: none; }
.feature-group-header {
    cursor: pointer;
    background: #f1f5f9;
    user-select: none;
    transition: background 0.2s;
    font-family: 'Outfit', sans-serif;
}
.feature-group-header:hover { background: #e8f0fe; }

.feature-master-check {
    width: 15px; height: 15px;
    cursor: pointer;
    accent-color: #f39c12;
}
.feature-check {
    width: 14px; height: 14px;
    cursor: pointer;
    accent-color: #4f46e5;
    flex-shrink: 0;
}
.feature-item {
    cursor: pointer;
    transition: background 0.15s;
    border: 1px solid transparent;
}
.feature-item:hover { background: #f1f5f9; }
.feature-item-checked {
    background: #eef2ff !important;
    border-color: rgba(79, 70, 229, 0.12) !important;
}

/* ---- FEATURE BADGES in table ---- */
.feat-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 10.5px;
    font-weight: 600;
    white-space: nowrap;
    line-height: 1.6;
}
.feat-badge-limit {
    background: #f0f9ff;
    color: #0369a1;
    border: 1px solid rgba(3, 105, 161, 0.15);
}
.feat-badge-feature {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid rgba(21, 128, 61, 0.15);
}
.feat-badge-more {
    background: #fef9ec;
    color: #b45309;
    border: 1px solid rgba(180, 83, 9, 0.15);
}

.btn-filter-submit {
    background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%) !important;
    color: #ffffff !important;
    border: none !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    padding: 10px 24px !important;
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15) !important;
    transition: all 0.2s ease !important;
}
.btn-filter-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(79, 70, 229, 0.25) !important; }

.modern-table thead { background: #f8fafc; }
.modern-table thead th {
    color: #475569; font-weight: 700; font-size: 11.5px; text-transform: uppercase;
    letter-spacing: 0.5px; border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 12px 14px; font-family: 'Outfit', sans-serif;
}
.modern-table tbody tr { transition: background 0.2s ease; }
.modern-table tbody tr:hover { background-color: #f8fafc; }
.modern-table tbody td { padding: 12px 14px; border-bottom: 1px solid rgba(0,0,0,0.03); font-size: 13px; color: #1e293b; }

.search-box-premium { background: #f8fafc; border: 1px solid rgba(0,0,0,0.05); }
.search-box-premium input { font-size: 13px; color: #334155; }

.btn-refresh-premium {
    background: rgba(0,0,0,0.03) !important;
    border: 1px solid rgba(0,0,0,0.05) !important;
    width: 36px; height: 36px; cursor: pointer;
    transition: all 0.25s ease;
}
.btn-refresh-premium:hover { transform: rotate(30deg) scale(1.05); border-color: #f39c12 !important; background: rgba(243,156,18,0.05) !important; }

.btn-action-edit, .btn-action-delete {
    background: #ffffff; border: 1px solid rgba(0,0,0,0.06);
    font-size: 14px; border-radius: 6px; width: 30px; height: 30px;
    display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s ease;
}
.btn-action-edit { color: #d97706; }
.btn-action-edit:hover { background: #d97706; color: #fff; border-color: transparent; }
.btn-action-delete { color: #dc3545; }
.btn-action-delete:hover { background: #dc3545; color: #fff; border-color: transparent; }
.btn-status-toggle { border-radius: 30px; padding: 5px 12px; font-size: 11.5px; cursor: pointer; transition: all 0.2s ease; border: 1px solid transparent; }
.btn-status-active { background: #f0fdf4 !important; color: #16a34a !important; border-color: rgba(22,163,74,0.15) !important; }
.btn-status-active:hover { background: #dcfce7 !important; }
.btn-status-locked { background: #fef2f2 !important; color: #ef4444 !important; border-color: rgba(239,68,68,0.15) !important; }
.btn-status-locked:hover { background: #fee2e2 !important; }
</style>
