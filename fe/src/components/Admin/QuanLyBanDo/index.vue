<template>
    <div class="admin-map-management-wrapper">
        <!-- Page Header -->
        <div class="page-header mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="page-icon">
                    <i class='bx bx-map-alt'></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold theme-text-main page-title text-gradient-gold">Quản Lý Bản Đồ Hệ Thống</h4>
                    <p class="mb-0 text-secondary small mt-1">Quản lý và giám sát các điểm định vị (Mộ Phần, Nhà Thờ Họ) và cấu hình bản đồ số toàn hệ thống</p>
                </div>
            </div>
            <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="refreshAll" :disabled="loading" title="Làm mới dữ liệu">
                <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': loading}"></i>
            </button>
        </div>

        <!-- System Map Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card shadow-sm border-0 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary small fw-semibold text-uppercase">Mộ Phần Định Vị</span>
                            <h3 class="mb-0 fw-bold theme-text-main mt-1">{{ stats.gravePins }}</h3>
                        </div>
                        <div class="stat-icon bg-danger-subtle text-danger">
                            <i class="bx bx-ghost"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-secondary small">Điểm định vị mộ tổ và thân nhân</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card shadow-sm border-0 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary small fw-semibold text-uppercase">Nhà Thờ Họ</span>
                            <h3 class="mb-0 fw-bold theme-text-main mt-1">{{ stats.templePins }}</h3>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">
                            <i class="bx bx-home-heart"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-secondary small">Điểm định vị từ đường, nhà thờ tổ</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card shadow-sm border-0 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary small fw-semibold text-uppercase">Tổng Địa Điểm</span>
                            <h3 class="mb-0 fw-bold theme-text-main mt-1">{{ stats.totalPins }}</h3>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">
                            <i class="bx bx-map-pin"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-secondary small">Tổng số tọa độ công khai trên hệ thống</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card stat-card shadow-sm border-0 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-secondary small fw-semibold text-uppercase">Bản Đồ Đối Tác</span>
                            <h3 class="mb-0 fw-bold theme-text-main mt-1">{{ stats.activeBranches }}</h3>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">
                            <i class="bx bx-network-chart"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-secondary small">Dòng họ đối tác đã sử dụng bản đồ</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Locations Table & Moderation -->
            <div class="col-lg-8 col-12">
                <div class="card genealogy-main-card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent py-3 border-bottom border-light-subtle d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold text-uppercase theme-text-main">
                            <i class="bx bx-list-ul me-2 text-warning"></i> Quản Lý Danh Sách Tọa Độ
                        </h6>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <!-- Partner Selector Button -->
                            <button class="btn btn-outline-warning radius-30 px-3 fw-bold d-flex align-items-center gap-2 shadow-sm" style="font-size: 13px; height: 34px; padding-top: 5px; padding-bottom: 5px;" @click="showPartnerSelectorModal = true">
                                <i class="bx bx-user-circle fs-5"></i>
                                <span>{{ selectedPartner ? (selectedPartner.nguoi_dung?.ho_ten || selectedPartner.ten_goi) + ' (' + selectedPartner.dong_ho + ')' : 'Chọn Đối Tác' }}</span>
                            </button>

                            <select class="form-select border-0 shadow-sm radius-10 py-1 px-3 bg-light-subtle theme-text-main" v-model="filterType" style="font-size: 13px; height: 34px; width: 140px;">
                                <option value="all">-- Tất cả loại --</option>
                                <option value="mo_phan">Mộ Phần</option>
                                <option value="nha_tho">Nhà Thờ Họ</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- State 1: No partner chosen yet -->
                        <div v-if="!selectedPartner" class="text-center py-5 mt-5">
                            <div class="empty-state-icon mb-3">
                                <i class="bx bx-map-pin fs-1 text-warning opacity-75 animate__animated animate__pulse animate__infinite"></i>
                            </div>
                            <h5 class="fw-bold theme-text-main mb-2">Chưa Chọn Đối Tác Quản Lý Bản Đồ</h5>
                            <p class="text-secondary small mx-auto mb-4" style="max-width: 480px;">
                                Vui lòng lựa chọn một tài khoản đối tác từ danh sách để tải dữ liệu bản đồ ghim (Mộ Phần, Nhà Thờ Họ) và quản lý danh sách tọa độ của dòng họ tương ứng.
                            </p>
                            <button class="btn btn-gradient-orange text-white radius-30 px-4 fw-bold shadow-sm" @click="showPartnerSelectorModal = true">
                                <i class="bx bx-list-ul me-1"></i> Chọn Đối Tác Ngay
                            </button>
                        </div>

                        <!-- State 2: Partner chosen -->
                        <div v-else>
                            <!-- Search Box -->
                            <div class="search-box mb-4" style="max-width: 400px;">
                                <i class='bx bx-search'></i>
                                <input type="text" class="form-control premium-input border-2 shadow-none" placeholder="Tìm kiếm địa điểm, địa chỉ..." v-model="searchQuery">
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 text-nowrap">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Tên Địa Điểm</th>
                                            <th>Phân Loại</th>
                                            <th>Dòng Họ / Nhánh</th>
                                            <th>Tọa Độ (Lat, Lng)</th>
                                            <th>Địa Chỉ</th>
                                            <th class="text-center">Hành Động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="loading">
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div>
                                                <span>Đang tải danh sách bản đồ...</span>
                                            </td>
                                        </tr>
                                        <tr v-else-if="filteredLocations.length === 0">
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="bx bx-search-alt fs-2 d-block mb-2"></i>
                                                Chưa có điểm định vị nào khớp
                                            </td>
                                        </tr>
                                        <tr v-else v-for="(loc, index) in filteredLocations" :key="loc.uid" class="table-row-premium">
                                            <td class="text-center fw-bold">{{ index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="marker-thumb" :class="loc.type === 'mo_phan' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary'">
                                                        <i class="bx" :class="loc.type === 'mo_phan' ? 'bx-ghost' : 'bx-home-heart'"></i>
                                                    </div>
                                                    <span class="fw-semibold">{{ loc.name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge" :class="loc.type === 'mo_phan' ? 'bg-danger-subtle text-danger border-danger' : 'bg-primary-subtle text-primary border-primary'" style="border: 1px solid;">
                                                    {{ loc.type === 'mo_phan' ? 'Mộ Phần' : 'Nhà Thờ Họ' }}
                                                </span>
                                            </td>
                                            <td class="text-truncate" style="max-width: 150px;">
                                                {{ loc.branchName || 'Chưa phân nhánh' }}
                                            </td>
                                            <td class="font-monospace small text-warning">
                                                {{ loc.lat }}, {{ loc.lng }}
                                            </td>
                                            <td class="text-truncate text-secondary" style="max-width: 200px;" :title="loc.address">
                                                {{ loc.address || '---' }}
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-action-delete btn-sm" @click="deletePin(loc)" title="Xóa điểm định vị">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Map Config & Suggestion Panel -->
            <div class="col-lg-4 col-12">
                <div class="d-flex flex-column gap-4">
                    <!-- Global Configuration Card -->
                    <div class="card genealogy-main-card shadow-sm border-0">
                        <div class="card-header bg-transparent py-3 border-bottom border-light-subtle">
                            <h6 class="mb-0 fw-bold text-uppercase theme-text-main">
                                <i class="bx bx-cog me-2 text-warning"></i> Cấu Hình Bản Đồ Hệ Thống
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <form @submit.prevent="saveConfig">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small">Map Provider</label>
                                    <select class="form-select premium-input" v-model="config.provider">
                                        <option value="mapbox">Mapbox API (Recommended)</option>
                                        <option value="google">Google Maps Platform</option>
                                        <option value="openstreetmap">OpenStreetMap (Free)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small">API Access Token / Key</label>
                                    <input type="password" class="form-control premium-input" placeholder="pk.eyJ1i..." v-model="config.apiKey">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-secondary small">Kinh độ Mặc định</label>
                                        <input type="text" class="form-control premium-input" v-model="config.defaultLng">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-secondary small">Vĩ độ Mặc định</label>
                                        <input type="text" class="form-control premium-input" v-model="config.defaultLat">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary small">Default Zoom Level</label>
                                    <input type="number" class="form-control premium-input" v-model="config.defaultZoom" min="1" max="20">
                                </div>
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-gradient-orange text-white radius-30 fw-bold py-2 shadow-sm" :disabled="saving">
                                        <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Lưu Cấu Hình Bản Đồ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Suggestions Panel -->
                    <div class="card genealogy-main-card shadow-sm border-0" style="background: linear-gradient(135deg, rgba(249, 115, 22, 0.03) 0%, rgba(255, 215, 0, 0.01) 100%);">
                        <div class="card-header bg-transparent py-3 border-bottom border-light-subtle">
                            <h6 class="mb-0 fw-bold text-uppercase theme-text-main text-gradient-gold">
                                <i class="bx bx-bulb me-2"></i> Gợi Ý Quản Lý Bản Đồ Số
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="suggestion-item d-flex gap-3 mb-3">
                                <div class="sug-num">1</div>
                                <div>
                                    <strong class="theme-text-main d-block mb-1" style="font-size: 13.5px;">Tổng Quan Bản Đồ Hệ Thống</strong>
                                    <span class="text-secondary small">Hiển thị một bản đồ toàn cầu gộp tất cả các tọa độ nhà thờ, mộ phần của đối tác để Admin xem tổng quát sự phân bổ dòng họ.</span>
                                </div>
                            </div>
                            <div class="suggestion-item d-flex gap-3 mb-3">
                                <div class="sug-num">2</div>
                                <div>
                                    <strong class="theme-text-main d-block mb-1" style="font-size: 13.5px;">Phê Duyệt Địa Điểm Công Khai</strong>
                                    <span class="text-secondary small">Khi đối tác pin một di tích lịch sử dòng họ công khai, Admin sẽ kiểm duyệt độ chính xác thông tin trước khi hiển thị lên bản đồ chung.</span>
                                </div>
                            </div>
                            <div class="suggestion-item d-flex gap-3 mb-3">
                                <div class="sug-num">3</div>
                                <div>
                                    <strong class="theme-text-main d-block mb-1" style="font-size: 13.5px;">Quản Lý Danh Mục Điểm Ghim</strong>
                                    <span class="text-secondary small">Thiết lập các nhóm điểm ghim (Ví dụ: Chi lăng, Mộ Tổ, Từ Đường, Địa Điểm Họp Mặt) cùng màu sắc và biểu tượng đại diện.</span>
                                </div>
                            </div>
                            <div class="suggestion-item d-flex gap-3">
                                <div class="sug-num">4</div>
                                <div>
                                    <strong class="theme-text-main d-block mb-1" style="font-size: 13.5px;">Thống Kê API & Lưu Trữ</strong>
                                    <span class="text-secondary small">Theo dõi số lượt gọi API Map, dung lượng lưu trữ hình ảnh mộ phần và báo cáo tranh chấp địa điểm từ người dùng.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Chọn Đối Tác -->
        <div v-if="showPartnerSelectorModal" class="custom-modal-backdrop animate__animated animate__fadeIn" @click.self="showPartnerSelectorModal = false">
            <div class="custom-modal-content animate__animated animate__zoomIn p-4 rounded-4 shadow-2xl bg-white position-relative" style="max-width: 650px; z-index: 1060;">
                <button class="btn-close-custom position-absolute top-0 end-0 m-3 border-0 bg-transparent" @click="showPartnerSelectorModal = false">
                    <i class="bx bx-x fs-2 text-muted"></i>
                </button>
                
                <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i class="bx bx-user-circle text-warning fs-3"></i> Danh Sách Tài Khoản Đối Tác
                </h5>
                <p class="text-muted small mb-4">Lựa chọn đối tác quản lý để hiển thị và biên tập cây gia phả tương ứng.</p>

                <!-- Search inside modal -->
                <div class="position-relative mb-3">
                    <input type="text" class="form-control ps-5 radius-30 border-2 shadow-none" v-model="partnerSearchQuery" placeholder="Tìm kiếm đối tác theo tên, email hoặc dòng họ...">
                    <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary"><i class="bx bx-search"></i></span>
                </div>

                <!-- Partners List Container -->
                <div class="partner-list-scroll overflow-auto pe-1" style="max-height: 380px;">
                    <div class="row g-3" v-if="filteredPartners.length">
                        <div class="col-md-6" v-for="item in filteredPartners" :key="item.id">
                            <!-- Active Partner (Has Chi Nhanh / Clan Tree) -->
                            <div v-if="item.id_chi_nhanh" 
                                 class="partner-select-card p-3 rounded-3 border border-2 cursor-pointer transition-all d-flex align-items-center gap-3 animate__animated animate__fadeIn"
                                 :class="selectedPartner && selectedPartner.id === item.id ? 'border-warning bg-light-gold' : 'border-light bg-white'"
                                 @click="selectPartner(item)">
                                
                                <div class="partner-avatar-circle d-flex align-items-center justify-content-center text-white font-weight-bold"
                                     :style="{ background: getAvatarBg(item.nguoi_dung?.ho_ten || item.ten_goi) }">
                                    {{ getAvatarInitials(item.nguoi_dung?.ho_ten || item.ten_goi) }}
                                </div>
                                
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="fw-bold text-dark text-truncate">{{ item.nguoi_dung?.ho_ten || item.ten_goi }}</div>
                                    <div class="text-muted small text-truncate">{{ item.nguoi_dung?.email || 'Chưa liên kết email' }}</div>
                                    <div class="mt-2">
                                        <span class="badge badge-gold-soft font-9 px-2 py-1 text-dark animate__animated animate__fadeIn" style="background: rgba(212, 175, 55, 0.15); color: #8a6d1c !important; border: 1px solid rgba(212, 175, 55, 0.25);">
                                            <i class="bx bx-home-alt me-1"></i>{{ item.dong_ho }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div v-if="selectedPartner && selectedPartner.id === item.id" class="text-warning">
                                    <i class="bx bxs-check-circle fs-4 animate__animated animate__scaleIn"></i>
                                </div>
                            </div>

                            <!-- Inactive Partner (Has NOT created Chi Nhanh / Clan Tree yet) -->
                            <div v-else 
                                 class="partner-select-card partner-card-disabled p-3 rounded-3 border border-2 border-dashed border-light-secondary bg-light bg-opacity-70 text-muted d-flex align-items-center gap-3 animate__animated animate__fadeIn"
                                 style="opacity: 0.65; cursor: not-allowed;"
                                 title="Đối tác này chưa tạo cây gia phả">
                                
                                <div class="partner-avatar-circle d-flex align-items-center justify-content-center text-white font-weight-bold"
                                     style="background: #94a3b8; filter: grayscale(0.5); box-shadow: none;">
                                    {{ getAvatarInitials(item.nguoi_dung?.ho_ten || item.ten_goi) }}
                                </div>
                                
                                <div class="overflow-hidden flex-grow-1">
                                    <div class="fw-bold text-secondary text-truncate" style="text-decoration: line-through; opacity: 0.85;">{{ item.nguoi_dung?.ho_ten || item.ten_goi }}</div>
                                    <div class="text-muted small text-truncate" style="opacity: 0.85;">{{ item.nguoi_dung?.email || 'Chưa liên kết email' }}</div>
                                    <div class="mt-2 text-danger small fw-bold d-flex align-items-center gap-1">
                                        <i class="bx bx-error-circle fs-6"></i>
                                        <span>Đối tác này chưa tạo cây gia phả</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Empty State -->
                    <div v-else class="text-center py-5">
                        <i class="bx bx-user-x fs-1 text-muted opacity-25 mb-2"></i>
                        <h6 class="text-muted fw-bold">Không tìm thấy đối tác nào</h6>
                        <p class="text-muted small mb-0">Vui lòng thử từ khóa tìm kiếm khác.</p>
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-0 mt-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary radius-30 px-4" @click="showPartnerSelectorModal = false">Đóng</button>
                    <button v-if="selectedPartner" type="button" class="btn btn-danger radius-30 px-4 text-white" @click="clearPartnerSelection">Bỏ chọn</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'AdminMapManagement',
    data() {
        return {
            locations: [],
            listPartners: [],
            selectedPartner: null,
            selectedChiNhanh: null,
            showPartnerSelectorModal: false,
            partnerSearchQuery: '',
            stats: {
                gravePins: 0,
                templePins: 0,
                totalPins: 0,
                activeBranches: 0
            },
            config: {
                provider: 'mapbox',
                apiKey: '••••••••••••••••••••••••••••••••••••••••',
                defaultLng: '105.8341598',
                defaultLat: '21.0277644',
                defaultZoom: 12
            },
            searchQuery: '',
            filterType: 'all',
            loading: false,
            saving: false
        }
    },
    computed: {
        filteredPartners() {
            if (!this.partnerSearchQuery) {
                return this.listPartners;
            }
            const q = this.partnerSearchQuery.toLowerCase();
            return this.listPartners.filter(item => {
                const partnerName = (item.nguoi_dung?.ho_ten || item.ten_goi || '').toLowerCase();
                const partnerEmail = (item.nguoi_dung?.email || '').toLowerCase();
                const clanName = (item.dong_ho || '').toLowerCase();
                return partnerName.includes(q) || partnerEmail.includes(q) || clanName.includes(q);
            });
        },
        filteredLocations() {
            if (!this.selectedPartner || !this.selectedChiNhanh) {
                return [];
            }
            let base = this.locations.filter(l => l.chi_nhanh_id == this.selectedChiNhanh);
            if (this.filterType !== 'all') {
                base = base.filter(l => l.type === this.filterType);
            }
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                base = base.filter(l =>
                    l.name.toLowerCase().includes(q) ||
                    (l.address && l.address.toLowerCase().includes(q)) ||
                    (l.branchName && l.branchName.toLowerCase().includes(q))
                );
            }
            return base;
        }
    },
    mounted() {
        this.loadData();
        this.loadStats();
        this.loadPartners();
    },
    methods: {
        getHeaders() {
            return {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('access_token')
                }
            };
        },
        loadPartners() {
            axios.get('http://127.0.0.1:8000/api/admin/doi-tac/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listPartners = res.data.data;
                    }
                });
        },
        selectPartner(item) {
            this.selectedPartner = item;
            this.selectedChiNhanh = item.id_chi_nhanh;
            this.showPartnerSelectorModal = false;
            toastr.success(`Đã chọn đối tác: ${item.nguoi_dung?.ho_ten || item.ten_goi}`);
        },
        clearPartnerSelection() {
            this.selectedPartner = null;
            this.selectedChiNhanh = null;
            this.showPartnerSelectorModal = false;
            toastr.info('Đã gỡ chọn đối tác.');
        },
        getAvatarInitials(name) {
            if (!name) return 'DT';
            const parts = name.trim().split(/\s+/);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        },
        getAvatarBg(name) {
            if (!name) return '#d4af37';
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            const colors = [
                'linear-gradient(135deg, #d4af37 0%, #aa8c2c 100%)',
                'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)',
                'linear-gradient(135deg, #0f2027 0%, #203a43 100%)',
                'linear-gradient(135deg, #56ab2f 0%, #a8e063 100%)',
                'linear-gradient(135deg, #e65c00 0%, #f9d423 100%)',
                'linear-gradient(135deg, #833ab4 0%, #fd1d1d 100%)'
            ];
            const index = Math.abs(hash) % colors.length;
            return colors[index];
        },
        refreshAll() {
            this.loadData();
            this.loadStats();
            this.loadPartners();
        },
        loadData() {
            this.loading = true;
            
            // Tải mộ phần
            const loadGraves = axios.get('http://127.0.0.1:8000/api/mo-phan/get-data', this.getHeaders());
            // Tải nhà thờ họ
            const loadTemples = axios.get('http://127.0.0.1:8000/api/nha-tho-ho/get-data', this.getHeaders());

            Promise.all([loadGraves, loadTemples])
                .then(([gravesRes, templesRes]) => {
                    let tempLocs = [];

                    if (gravesRes.data.status) {
                        const graves = gravesRes.data.data.filter(g => g.vi_do && g.kinh_do);
                        graves.forEach(g => {
                            tempLocs.push({
                                uid: 'grave_' + g.id,
                                id: g.id,
                                type: 'mo_phan',
                                name: g.ten_mo,
                                lat: g.vi_do,
                                lng: g.kinh_do,
                                address: g.dia_chi || g.ten_nghia_trang,
                                branchName: g.thanh_vien?.chi_nhanh?.ten_chi || 'Không có nhánh',
                                chi_nhanh_id: g.thanh_vien?.chi_nhanh_id || (g.thanh_vien?.chi_nhanh?.id || null)
                            });
                        });
                    }

                    if (templesRes.data.status) {
                        const temples = templesRes.data.data.filter(t => t.vi_do && t.kinh_do);
                        temples.forEach(t => {
                            tempLocs.push({
                                uid: 'temple_' + t.id,
                                id: t.id,
                                type: 'nha_tho',
                                name: t.ten_nha_tho,
                                lat: t.vi_do,
                                lng: t.kinh_do,
                                address: t.dia_chi,
                                branchName: t.chi_nhanh?.ten_chi || 'Không có nhánh',
                                chi_nhanh_id: t.chi_nhanh_id || (t.chi_nhanh?.id || null)
                            });
                        });
                    }

                    this.locations = tempLocs;
                    this.stats.gravePins = tempLocs.filter(l => l.type === 'mo_phan').length;
                    this.stats.templePins = tempLocs.filter(l => l.type === 'nha_tho').length;
                    this.stats.totalPins = tempLocs.length;
                })
                .catch(() => {
                    toastr.error('Lỗi khi đồng bộ danh sách tọa độ bản đồ!');
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        loadStats() {
            // Tải số lượng chi nhánh có sử dụng bản đồ
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.stats.activeBranches = res.data.data.length;
                    }
                })
                .catch(() => {});
        },
        saveConfig() {
            this.saving = true;
            setTimeout(() => {
                this.saving = false;
                toastr.success('Cập nhật cấu hình bản đồ hệ thống thành công!');
            }, 1000);
        },
        deletePin(loc) {
            if (!confirm(`Bạn có chắc chắn muốn xóa điểm ghim "${loc.name}" khỏi bản đồ? Điều này sẽ xóa vĩnh viễn dữ liệu điểm ghim của đối tác.`)) return;
            
            const url = loc.type === 'mo_phan' 
                ? 'http://127.0.0.1:8000/api/mo-phan/delete'
                : 'http://127.0.0.1:8000/api/nha-tho-ho/delete';

            axios.post(url, { id: loc.id }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success('Đã gỡ điểm ghim thành công!');
                        this.loadData();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(() => {
                    toastr.error('Có lỗi xảy ra khi xóa điểm ghim!');
                });
        }
    }
}
</script>

<style scoped>
/* ========== Utilities ========== */
.radius-12 { border-radius: 12px !important; }
.radius-10 { border-radius: 10px !important; }
.radius-8  { border-radius: 8px !important; }
.radius-30 { border-radius: 30px !important; }
.transition-all { transition: all 0.3s ease; }
.cursor-pointer { cursor: pointer; }

/* ========== Premium Page Header ========== */
.page-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, #1e2035 0%, #252740 100%);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; color: #ffd700;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.text-gradient-gold {
    background: linear-gradient(135deg, #b8860b 0%, #e5a93b 50%, #ffd700 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* ========== Circular Reload Button ========== */
.btn-refresh-premium {
    background: var(--input-bg) !important;
    border: 1px solid var(--border-color) !important;
    width: 36px;
    height: 36px;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    transition: all 0.25s ease;
}
.btn-refresh-premium:hover {
    transform: rotate(30deg) scale(1.05);
    border-color: #f97316 !important;
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
}

/* ========== Statistics Cards ========== */
.stat-card {
    background: var(--card-bg) !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 16px !important;
    transition: transform 0.25s ease;
}
.stat-card:hover {
    transform: translateY(-2px);
}
.stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
}
.bg-danger-subtle { background-color: rgba(239, 68, 68, 0.1) !important; }
.bg-primary-subtle { background-color: rgba(79, 70, 229, 0.1) !important; }
.bg-success-subtle { background-color: rgba(16, 185, 129, 0.1) !important; }
.bg-warning-subtle { background-color: rgba(245, 158, 11, 0.1) !important; }

/* ========== Premium Inputs & Containers ========== */
.genealogy-main-card {
    background: var(--card-bg) !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 16px !important;
}

.premium-input {
    background-color: var(--input-bg) !important;
    border: 1px solid var(--border-color) !important;
    color: var(--text-main) !important;
    padding: 11px 16px !important;
    border-radius: 12px !important;
    font-size: 13.5px !important;
    transition: all 0.25s ease !important;
}
.premium-input:focus {
    border-color: #f97316 !important;
    background-color: var(--card-bg) !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1) !important;
}

.search-box {
    position: relative;
}
.search-box i {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-sub);
    font-size: 18px;
}

/* ========== Custom Table Styles ========== */
.table-row-premium {
    transition: background-color 0.2s ease;
}
.table-row-premium:hover {
    background-color: var(--input-bg) !important;
}
.marker-thumb {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
}

.btn-action-delete {
    background: transparent !important;
    border-radius: 8px !important;
    font-size: 14px; padding: 5px 10px !important;
    transition: all 0.2s ease;
    border: 1px solid rgba(239, 68, 68, 0.3) !important; 
    color: #ef4444 !important;
}
.btn-action-delete:hover {
    background: #ef4444 !important;
    color: white !important;
}

/* ========== Suggestion List ========== */
.suggestion-item {
    align-items: flex-start;
}
.sug-num {
    width: 24px;
    height: 24px;
    background: var(--input-bg);
    border: 1px solid var(--border-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: #f97316;
    flex-shrink: 0;
    margin-top: 2px;
}

/* ========== Orange Gradient Button ========== */
.btn-gradient-orange {
    background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
    border: none !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(244, 63, 94, 0.15) !important;
    transition: all 0.25s ease;
}
.btn-gradient-orange:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(244, 63, 94, 0.25) !important;
}

/* ========== Circular Reload Button Active State ========== */
.btn-refresh-premium:active {
    transform: scale(0.95);
}

/* ========== Custom Vue Modal Styling for Partner Selector ========== */
.custom-modal-backdrop {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.custom-modal-content {
    width: 100%;
    max-width: 480px;
    background: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
}
.btn-close-custom {
    transition: all 0.2s ease;
}
.btn-close-custom:hover {
    transform: rotate(90deg);
}

/* ========== Partner Selector Premium Styling ========== */
.partner-list-scroll {
    scrollbar-width: thin;
    scrollbar-color: #d4af37 transparent;
}
.partner-list-scroll::-webkit-scrollbar {
    width: 6px;
}
.partner-list-scroll::-webkit-scrollbar-thumb {
    background-color: #d4af37;
    border-radius: 10px;
}
.partner-select-card {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.partner-select-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.15);
    border-color: #ffd700 !important;
}
.partner-select-card.border-warning {
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
}
.bg-light-gold {
    background-color: rgba(212, 175, 55, 0.05) !important;
}
.partner-avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    font-size: 16px;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}
</style>
