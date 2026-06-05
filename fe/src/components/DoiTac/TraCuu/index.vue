<template>
    <div class="relationship-discovery">
        <div class="bg-circles" aria-hidden="true">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
            <div class="circle circle-3"></div>
        </div>

        <div class="container py-2 position-relative">
            <div class="text-center mb-4 animate__animated animate__fadeIn">
                <div class="heritage-badge mb-3">HỆ THỐNG GIA PHẢ ĐỐI TÁC</div>
                <h2 class="heritage-title fw-bold">Tra Cứu Bậc Vai Vế</h2>
                <p class="heritage-subtitle text-secondary">Khám phá mối liên hệ huyết thống trong gia tộc</p>
                <div class="heritage-divider">
                    <span class="diamond"></span>
                </div>
            </div>

            <div v-if="listChiNhanh.length === 0" class="row justify-content-center animate__animated animate__fadeIn">
                <div class="col-lg-8">
                    <div class="heritage-card p-5 text-center shadow-sm">
                        <div class="mb-4 mt-3">
                            <i class="bx bx-building-house text-warning opacity-25"
                                style="font-size: 80px !important;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Dòng Họ Chưa Được Khởi Tạo!</h4>
                        <p class="text-muted">Tính năng tra cứu chỉ khả dụng sau khi bạn đã thiết lập Dòng Họ (Chi
                            Nhánh).</p>
                        <router-link to="/doi-tac/dong-ho"
                            class="btn btn-gradient-orange radius-30 px-5 mt-3 shadow-sm mb-3 fw-bold">
                            <i class="bx bx-plus-circle"></i> Khởi Tạo Ngay
                        </router-link>
                    </div>
                </div>
            </div>

            <div v-else-if="allMembers.length === 0"
                class="row justify-content-center animate__animated animate__fadeIn">
                <div class="col-lg-8">
                    <div class="heritage-card p-5 text-center shadow-sm">
                        <div class="mb-4 mt-3">
                            <i class="bx bx-git-branch text-warning opacity-25" style="font-size: 80px !important;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Chưa Có Thành Viên Để Tra Cứu!</h4>
                        <p class="text-muted">Bạn cần thêm thành viên vào Cây Gia Phả trước khi có thể tra cứu vai vế.
                        </p>
                        <router-link to="/doi-tac/gia-pha"
                            class="btn btn-gradient-orange radius-30 px-5 mt-3 shadow-sm mb-3 fw-bold">
                            <i class="bx bx-plus"></i> Bắt Đầu Xây Dựng Cây
                        </router-link>
                    </div>
                </div>
            </div>

            <div v-else class="row mb-4 justify-content-center animate__animated animate__fadeInDown">
                <div class="col-lg-6">
                    <div class="heritage-card p-4 text-center border-1">
                        <label class="fw-bold text-secondary mb-2 text-uppercase font-bold"
                            style="letter-spacing: 0.8px; font-size: 13px;">Chọn Dòng Họ Để Tra Cứu:</label>
                        <select class="form-select select-pill-premium radius-30 border-1 fw-bold text-dark"
                            v-model="selectedChiNhanhId" @change="resetSelection">
                            <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                        </select>
                        <div v-if="!selectedChiNhanhId" class="mt-2 text-danger small style-italic">
                            * Bạn cần chọn dòng họ trước khi tra cứu thành viên
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 justify-content-center align-items-stretch" v-if="selectedChiNhanhId">
                <div class="col-lg-5 animate__animated animate__fadeInLeft">
                    <div class="heritage-card h-100 p-4">
                        <div class="card-label">THÀNH VIÊN THỨ NHẤT</div>

                        <div class="selection-box mt-4">
                            <div class="searchable-select-container searchable-select-container-a position-relative">
                                <!-- Trigger -->
                                <div class="custom-select-wrapper shadow-none border d-flex align-items-center justify-content-between px-3 py-2"
                                    @click="toggleDropdownA"
                                    style="cursor: pointer; background: var(--input-bg); min-height: 48px; border-radius: 30px;">
                                    <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 80%;">
                                        <i class="bx bx-user-pin select-icon position-static"
                                            style="color: #f97316;"></i>
                                        <!-- Inline Search Input when Open -->
                                        <div v-if="isOpenA" class="w-100 me-2" @click.stop>
                                            <input type="text"
                                                class="form-control border-0 p-0 bg-transparent fw-bold text-dark shadow-none"
                                                placeholder="Nhập tên để tìm kiếm..." v-model="searchQueryA"
                                                ref="searchInputA" style="outline: none; box-shadow: none;" />
                                        </div>
                                        <!-- Plain Label when Closed -->
                                        <span v-else class="fw-bold text-dark text-truncate">
                                            {{ personA ? `${personA.ho_ten} (Đời ${personA.doi_thu})` :
                                                '-- Chọn thành viên --' }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Clear button (only closed & selected) -->
                                        <button v-if="idA !== null && !isOpenA"
                                            class="btn-clear-selection border-0 bg-transparent p-0 d-flex align-items-center"
                                            @click.stop="clearSelectionA" title="Xóa tìm kiếm">
                                            <i class="bx bx-x fs-5 text-muted hover-red"></i>
                                        </button>
                                        <!-- Clear search text button (only open & typing) -->
                                        <button v-if="isOpenA && searchQueryA"
                                            class="btn-clear-search-inline border-0 bg-transparent p-0 d-flex align-items-center"
                                            @click.stop="searchQueryA = ''">
                                            <i class="bx bx-x fs-5 text-muted"></i>
                                        </button>
                                        <i class="bx bx-chevron-down fs-5 text-muted transition"
                                            :class="{ 'rotate-180': isOpenA }"></i>
                                    </div>
                                </div>

                                <!-- Dropdown Panel (no duplicate search input!) -->
                                <div v-if="isOpenA"
                                    class="custom-select-dropdown shadow-lg rounded-4 p-3 mt-2 position-absolute w-100 bg-white border"
                                    style="z-index: 1000; left: 0; right: 0;">
                                    <div class="options-list overflow-y-auto" style="max-height: 200px;">
                                        <div v-for="m in filteredMembersA" :key="m.id"
                                            class="option-item p-2 rounded-3 cursor-pointer text-start my-1"
                                            :class="{ 'active-option': idA === m.id }" @click="selectMemberA(m)">
                                            <div class="fw-semibold text-dark">{{ m.ho_ten }}</div>
                                            <div class="small text-muted" style="font-size: 11px;">Đời {{ m.doi_thu }} -
                                                {{ m.gioi_tinh }}</div>
                                        </div>
                                        <div v-if="filteredMembersA.length === 0"
                                            class="text-muted text-center py-3 font-13">
                                            Không tìm thấy thành viên
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="personA"
                            class="member-profile mt-4 text-center p-4 rounded-4 animate__animated animate__zoomIn">
                            <div class="profile-avatar-container mb-3">
                                <img :src="personA.avatar || 'https://ui-avatars.com/api/?name=' + personA.ho_ten + '&background=f97316&color=fff'"
                                    class="profile-avatar border border-2 border-white">
                                <div class="profile-gender" :class="personA.gioi_tinh === 'Nam' ? 'male' : 'female'">
                                    <i :class="personA.gioi_tinh === 'Nam' ? 'bx bx-male' : 'bx bx-female'"></i>
                                </div>
                            </div>
                            <h4 class="profile-name fw-bold m-0">{{ personA.ho_ten }}</h4>
                            <div class="profile-stats d-flex justify-content-center gap-2 mt-2">
                                <span class="stat-tag px-3 py-1">Đời {{ personA.doi_thu }}</span>
                                <span class="stat-tag dead-tag px-3 py-1" v-if="personA.trang_thai === 'Đã mất'">Đã
                                    mất</span>
                            </div>
                        </div>
                        <div v-else class="empty-state text-center py-5 opacity-50 text-muted">
                            <i class="bx bxs-user-circle display-2 mb-2"></i>
                            <p class="font-medium small m-0">Chọn thành viên thứ nhất</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 d-flex flex-column align-items-center justify-content-center my-3 my-lg-0">
                    <div class="action-hub w-100">
                        <div class="hub-line"></div>
                        <button class="hub-btn shadow-sm fw-bold" @click="calculateRelationship"
                            :disabled="!idA || !idB || idA === idB">
                            <i class="bx bxs-bolt"></i>
                            <span>TRA CỨU</span>
                        </button>
                        <div class="hub-line"></div>
                    </div>
                </div>

                <div class="col-lg-5 animate__animated animate__fadeInRight">
                    <div class="heritage-card h-100 p-4">
                        <div class="card-label bg-orange-label">THÀNH VIÊN THỨ HAI</div>

                        <div class="selection-box mt-4">
                            <div class="searchable-select-container searchable-select-container-b position-relative">
                                <!-- Trigger -->
                                <div class="custom-select-wrapper shadow-none border d-flex align-items-center justify-content-between px-3 py-2"
                                    @click="toggleDropdownB"
                                    style="cursor: pointer; background: var(--input-bg); min-height: 48px; border-radius: 30px;">
                                    <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: 80%;">
                                        <i class="bx bx-user-pin select-icon icon-secondary position-static"
                                            style="color: #db2777;"></i>
                                        <!-- Inline Search Input when Open -->
                                        <div v-if="isOpenB" class="w-100 me-2" @click.stop>
                                            <input type="text"
                                                class="form-control border-0 p-0 bg-transparent fw-bold text-dark shadow-none"
                                                placeholder="Nhập tên để tìm kiếm..." v-model="searchQueryB"
                                                ref="searchInputB" style="outline: none; box-shadow: none;" />
                                        </div>
                                        <!-- Plain Label when Closed -->
                                        <span v-else class="fw-bold text-dark text-truncate">
                                            {{ personB ? `${personB.ho_ten} (Đời ${personB.doi_thu})` :
                                                '-- Chọn thành viên --' }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Clear button (only closed & selected) -->
                                        <button v-if="idB !== null && !isOpenB"
                                            class="btn-clear-selection border-0 bg-transparent p-0 d-flex align-items-center"
                                            @click.stop="clearSelectionB" title="Xóa tìm kiếm">
                                            <i class="bx bx-x fs-5 text-muted hover-red"></i>
                                        </button>
                                        <!-- Clear search text button (only open & typing) -->
                                        <button v-if="isOpenB && searchQueryB"
                                            class="btn-clear-search-inline border-0 bg-transparent p-0 d-flex align-items-center"
                                            @click.stop="searchQueryB = ''">
                                            <i class="bx bx-x fs-5 text-muted"></i>
                                        </button>
                                        <i class="bx bx-chevron-down fs-5 text-muted transition"
                                            :class="{ 'rotate-180': isOpenB }"></i>
                                    </div>
                                </div>

                                <!-- Dropdown Panel (no duplicate search input!) -->
                                <div v-if="isOpenB"
                                    class="custom-select-dropdown shadow-lg rounded-4 p-3 mt-2 position-absolute w-100 bg-white border"
                                    style="z-index: 1000; left: 0; right: 0;">
                                    <div class="options-list overflow-y-auto" style="max-height: 200px;">
                                        <div v-for="m in filteredMembersB" :key="m.id"
                                            class="option-item p-2 rounded-3 cursor-pointer text-start my-1"
                                            :class="{ 'active-option': idB === m.id }" @click="selectMemberB(m)">
                                            <div class="fw-semibold text-dark">{{ m.ho_ten }}</div>
                                            <div class="small text-muted" style="font-size: 11px;">Đời {{ m.doi_thu }} -
                                                {{ m.gioi_tinh }}</div>
                                        </div>
                                        <div v-if="filteredMembersB.length === 0"
                                            class="text-muted text-center py-3 font-13">
                                            Không tìm thấy thành viên
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="personB"
                            class="member-profile mt-4 text-center p-4 rounded-4 animate__animated animate__zoomIn">
                            <div class="profile-avatar-container mb-3">
                                <img :src="personB.avatar || 'https://ui-avatars.com/api/?name=' + personB.ho_ten + '&background=db2777&color=fff'"
                                    class="profile-avatar border border-2 border-white">
                                <div class="profile-gender" :class="personB.gioi_tinh === 'Nam' ? 'male' : 'female'">
                                    <i :class="personB.gioi_tinh === 'Nam' ? 'bx bx-male' : 'bx bx-female'"></i>
                                </div>
                            </div>
                            <h4 class="profile-name fw-bold m-0">{{ personB.ho_ten }}</h4>
                            <div class="profile-stats d-flex justify-content-center gap-2 mt-2">
                                <span class="stat-tag px-3 py-1">Đời {{ personB.doi_thu }}</span>
                                <span class="stat-tag dead-tag px-3 py-1" v-if="personB.trang_thai === 'Đã mất'">Đã
                                    mất</span>
                            </div>
                        </div>
                        <div v-else class="empty-state text-center py-5 opacity-50 text-muted">
                            <i class="bx bxs-user-circle display-2 mb-2"></i>
                            <p class="font-medium small m-0">Chọn thành viên thứ hai</p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="result" class="row mt-4 pt-2 animate-premium-result">
                <div class="col-lg-12">
                    <div class="result-master-card shadow-sm overflow-hidden border">
                        <div class="shimmer-line"></div>

                        <div class="result-header p-3 text-center fw-bold">
                            <span class="result-badge">KẾT QUẢ PHÂN TÍCH VAI VẾ</span>
                        </div>
                        <div class="result-body p-0">
                            <div class="row g-0">
                                <div
                                    class="col-md-5 result-term-box p-4 d-flex flex-column align-items-center justify-content-center">
                                    <div class="term-label mb-1">DANH XƯNG GỌI BẬC</div>
                                    <div class="term-value fw-bold text-nowrap pop-text">{{ result.term }}</div>
                                    <div class="term-glow"></div>
                                </div>
                                <div class="col-md-7 result-desc-box p-4 bg-adaptive-card d-flex align-items-center">
                                    <div class="desc-content w-100 px-2">
                                        <div class="quote-icon mb-2 opacity-25"><i
                                                class="bx bxs-quote-alt-left fs-2"></i></div>
                                        <p class="fs-5 mb-3 lh-base theme-text-main">
                                            Trong cùng gia tộc, <strong class="text-warning">{{ personA.ho_ten
                                            }}</strong> sẽ xưng hô với <strong class="text-pink">{{ personB.ho_ten
                                                }}</strong> là:
                                        </p>
                                        <div class="relation-badge-large mb-3 fw-bold pulse-orange">
                                            {{ result.term }}
                                        </div>
                                        <div
                                            class="desc-footer p-3 rounded-3 border-start border-4 border-warning bg-adaptive-input">
                                            <i class="bx bx-info-circle me-2 text-warning fs-5 align-middle"></i> {{
                                                result.description }}
                                        </div>
                                    </div>
                                </div>
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
import toastr from 'toastr';

export default {
    name: 'PartnerTraCuu',
    data() {
        return {
            allMembers: [],
            listChiNhanh: [],
            selectedChiNhanhId: null,
            idA: null,
            idB: null,
            result: null,
            isOpenA: false,
            isOpenB: false,
            searchQueryA: '',
            searchQueryB: ''
        }
    },
    computed: {
        filteredMembers() {
            if (!this.selectedChiNhanhId) return [];
            return this.allMembers.filter(m => m.chi_nhanh_id === this.selectedChiNhanhId);
        },
        personA() { return this.allMembers.find(m => m.id === this.idA); },
        personB() { return this.allMembers.find(m => m.id === this.idB); },
        filteredMembersA() {
            if (!this.searchQueryA) return this.filteredMembers;
            const q = this.searchQueryA.toLowerCase();
            return this.filteredMembers.filter(m => m.ho_ten.toLowerCase().includes(q));
        },
        filteredMembersB() {
            if (!this.searchQueryB) return this.filteredMembers;
            const q = this.searchQueryB.toLowerCase();
            return this.filteredMembers.filter(m => m.ho_ten.toLowerCase().includes(q));
        }
    },
    mounted() {
        this.loadData();
        this.loadChiNhanh();
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        toggleDropdownA() {
            this.isOpenA = !this.isOpenA;
            if (this.isOpenA) {
                this.isOpenB = false;
                this.$nextTick(() => {
                    if (this.$refs.searchInputA) {
                        this.$refs.searchInputA.focus();
                    }
                });
            }
        },
        toggleDropdownB() {
            this.isOpenB = !this.isOpenB;
            if (this.isOpenB) {
                this.isOpenA = false;
                this.$nextTick(() => {
                    if (this.$refs.searchInputB) {
                        this.$refs.searchInputB.focus();
                    }
                });
            }
        },
        selectMemberA(m) {
            this.idA = m.id;
            this.isOpenA = false;
            this.searchQueryA = '';
        },
        selectMemberB(m) {
            this.idB = m.id;
            this.isOpenB = false;
            this.searchQueryB = '';
        },
        clearSelectionA() {
            this.idA = null;
            this.isOpenA = false;
            this.searchQueryA = '';
        },
        clearSelectionB() {
            this.idB = null;
            this.isOpenB = false;
            this.searchQueryB = '';
        },
        handleClickOutside(event) {
            if (!this.$el) return;
            const containerA = this.$el.querySelector('.searchable-select-container-a');
            const containerB = this.$el.querySelector('.searchable-select-container-b');
            if (containerA && !containerA.contains(event.target)) {
                this.isOpenA = false;
            }
            if (containerB && !containerB.contains(event.target)) {
                this.isOpenB = false;
            }
        },
        getHeaders() {
            return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
        },
        loadData() {
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
                .then(res => { if (res.data.status) this.allMembers = res.data.data; });
        },
        loadChiNhanh() {
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listChiNhanh = res.data.data;
                        if (this.listChiNhanh.length > 0 && !this.selectedChiNhanhId)
                            this.selectedChiNhanhId = this.listChiNhanh[0].id;
                    }
                });
        },
        resetSelection() { this.idA = null; this.idB = null; this.result = null; },
        calculateRelationship() {
            if (this.idA === this.idB) { toastr.warning('Vui lòng chọn hai thành viên khác nhau!'); return; }

            axios.post('http://127.0.0.1:8000/api/thanh-vien/xac-dinh-quan-he', {
                id_a: this.idA,
                id_b: this.idB
            }, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.result = {
                            term: res.data.appellation || res.data.term,
                            description: res.data.description,
                            path: res.data.path || []
                        };
                    } else {
                        toastr.error(res.data.message || 'Lỗi xác định mối quan hệ!');
                    }
                })
                .catch(err => {
                    toastr.error(err.response?.data?.message || 'Có lỗi xảy ra khi kết nối máy chủ!');
                });
        }
    }
}
</script>

<style scoped>
/* ═══════════════════════════════════════════════════
   BIẾN TOÀN CỤC & NỀN TẢNG
═══════════════════════════════════════════════════ */
.relationship-discovery {
    background-color: var(--card-bg);
    min-height: calc(100vh - 60px);
    position: relative;
    border-radius: 20px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: background-color 0.4s ease, border-color 0.4s ease;
}

/* ═══════════════════════════════════════════════════
   HIỆU ỨNG VÒNG TRÒN NỀN — MỀM MẠI & SÂU HƠN
═══════════════════════════════════════════════════ */
.bg-circles .circle {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    z-index: 0;
    pointer-events: none;
    will-change: transform, opacity;
}

.circle-1 {
    width: 380px;
    height: 380px;
    background: radial-gradient(circle, rgba(249, 115, 22, 0.07), transparent 70%);
    top: -80px;
    left: -80px;
    animation: floatOrb 12s ease-in-out infinite;
}

.circle-2 {
    width: 420px;
    height: 420px;
    background: radial-gradient(circle, rgba(219, 39, 119, 0.05), transparent 70%);
    bottom: -100px;
    right: -80px;
    animation: floatOrb 15s ease-in-out infinite reverse;
}

.circle-3 {
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(250, 204, 21, 0.04), transparent 70%);
    top: 45%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation: floatOrb 18s ease-in-out infinite 3s;
}

@keyframes floatOrb {

    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }

    33% {
        transform: translate(15px, -20px) scale(1.04);
    }

    66% {
        transform: translate(-10px, 12px) scale(0.97);
    }
}

/* ═══════════════════════════════════════════════════
   TIÊU ĐỀ & BADGE PHONG CÁCH DI SẢN
═══════════════════════════════════════════════════ */
.heritage-badge {
    display: inline-block;
    padding: 5px 20px;
    background: var(--input-bg);
    color: #f97316;
    font-weight: 800;
    font-size: 11px;
    letter-spacing: 2px;
    border-radius: 50px;
    border: 1px solid rgba(249, 115, 22, 0.2);
    box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.04);
    transition: box-shadow 0.3s ease;
}

.heritage-badge:hover {
    box-shadow: 0 0 0 8px rgba(249, 115, 22, 0.06);
}

.heritage-title {
    font-size: 2.3rem;
    color: var(--text-main);
    margin-top: 8px;
    letter-spacing: -0.5px;
}

.heritage-subtitle {
    font-size: 14px;
    font-weight: 500;
}

.heritage-divider {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 14px 0;
    gap: 0;
}

.heritage-divider::before,
.heritage-divider::after {
    content: "";
    height: 1px;
    width: 55px;
    background: linear-gradient(to right, transparent, var(--border-color));
}

.heritage-divider::after {
    background: linear-gradient(to left, transparent, var(--border-color));
}

.diamond {
    width: 7px;
    height: 7px;
    background: #f97316;
    transform: rotate(45deg);
    margin: 0 12px;
    box-shadow: 0 0 8px rgba(249, 115, 22, 0.5);
    animation: diamondPulse 3s ease-in-out infinite;
}

@keyframes diamondPulse {

    0%,
    100% {
        box-shadow: 0 0 6px rgba(249, 115, 22, 0.4);
    }

    50% {
        box-shadow: 0 0 14px rgba(249, 115, 22, 0.7);
    }
}

/* ═══════════════════════════════════════════════════
   THẺ HERITAGE — NỀN VÀ VIỀN THÍCH ỨNG
═══════════════════════════════════════════════════ */
.heritage-card {
    background: var(--card-bg);
    border-radius: 20px;
    border: 1px solid var(--border-color);
    position: relative;
    z-index: 1;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.heritage-card:hover {
    border-color: rgba(249, 115, 22, 0.2);
    box-shadow: 0 8px 32px rgba(249, 115, 22, 0.06) !important;
}

.card-label {
    position: absolute;
    top: -11px;
    left: 20px;
    background: linear-gradient(135deg, #f43f5e, #f97316);
    color: white;
    padding: 3px 14px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.5px;
    border-radius: 50px;
    box-shadow: 0 2px 10px rgba(249, 115, 22, 0.25);
}

.bg-orange-label {
    background: linear-gradient(135deg, #ff8c00, #ea580c);
    box-shadow: 0 2px 10px rgba(234, 88, 12, 0.25);
}

/* ═══════════════════════════════════════════════════
   SELECT BOX — DROPDOWN TINH TẾ
═══════════════════════════════════════════════════ */
.select-pill-premium {
    border-radius: 30px !important;
    border: 1px solid var(--border-color) !important;
    padding: 10px 18px !important;
    font-size: 14.5px;
    background-color: var(--input-bg) !important;
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

.select-pill-premium:focus {
    border-color: #f97316 !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12) !important;
    outline: none !important;
}

.custom-select-wrapper {
    position: relative;
    background: var(--input-bg);
    border-radius: 30px;
    border-color: var(--border-color) !important;
    transition: border-color 0.25s ease, box-shadow 0.25s ease;
}

.custom-select-wrapper:focus-within {
    border-color: rgba(249, 115, 22, 0.4) !important;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.08);
}

.select-icon {
    position: absolute;
    margin-top: 20px;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    color: #f97316;
    pointer-events: none;
    transition: color 0.2s ease;
}

.icon-secondary {
    color: #db2777;
}

.custom-select {
    width: 100%;
    padding: 11px 16px 11px 44px;
    border: none;
    background: transparent;
    font-size: 14px;
    color: var(--text-main) !important;
    outline: none;
    cursor: pointer;
}

/* ═══════════════════════════════════════════════════
   CARD HỒ SƠ THÀNH VIÊN
═══════════════════════════════════════════════════ */
.member-profile {
    background: var(--input-bg);
    border: 1px solid var(--border-color);
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.3s ease;
}

.member-profile:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.profile-avatar-container {
    position: relative;
    display: inline-block;
}

.profile-avatar {
    width: 85px;
    height: 85px;
    border-radius: 50%;
    object-fit: cover;
    transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.3s ease;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.profile-avatar:hover {
    transform: scale(1.06);
    box-shadow: 0 8px 24px rgba(249, 115, 22, 0.2);
}

.profile-gender {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    border: 2px solid var(--card-bg);
    transition: transform 0.2s ease;
}

.profile-gender:hover {
    transform: scale(1.15);
}

.male {
    background: linear-gradient(135deg, #fb923c, #f97316);
}

.female {
    background: linear-gradient(135deg, #f472b6, #db2777);
}

.stat-tag {
    background: var(--card-bg);
    color: var(--text-sub);
    border: 1px solid var(--border-color);
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
    transition: background 0.2s ease, border-color 0.2s ease;
}

.dead-tag {
    background-color: rgba(239, 68, 68, 0.06);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.15);
}

.empty-state i {
    transition: opacity 0.3s ease;
}

/* ═══════════════════════════════════════════════════
   ACTION HUB — NÚT TRA CỨU TRUNG TÂM
═══════════════════════════════════════════════════ */
.action-hub {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.hub-line {
    width: 2px;
    flex-grow: 1;
    min-height: 30px;
    background: linear-gradient(to bottom, transparent, var(--border-color) 50%, transparent);
    border-radius: 2px;
    transition: background 0.3s ease;
}

.hub-btn {
    width: 84px;
    height: 84px;
    border-radius: 50%;
    background: var(--card-bg);
    color: var(--text-sub);
    border: 2px solid var(--border-color);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 9.5px;
    letter-spacing: 0.6px;
    margin: 14px 0;
    cursor: default;
    transition:
        transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
        background 0.3s ease,
        border-color 0.3s ease,
        color 0.3s ease,
        box-shadow 0.3s ease;
    will-change: transform;
}

.hub-btn:not(:disabled) {
    border-color: #f97316;
    color: #f97316;
    cursor: pointer;
    box-shadow: 0 0 0 6px rgba(249, 115, 22, 0.06);
    animation: hubReadyPulse 3s ease-in-out infinite;
}

@keyframes hubReadyPulse {

    0%,
    100% {
        box-shadow: 0 0 0 6px rgba(249, 115, 22, 0.06);
    }

    50% {
        box-shadow: 0 0 0 10px rgba(249, 115, 22, 0.02);
    }
}

.hub-btn:not(:disabled):hover {
    transform: scale(1.1) rotate(5deg);
    background: linear-gradient(135deg, #f43f5e, #f97316);
    color: white;
    border-color: transparent;
    box-shadow: 0 8px 28px rgba(249, 115, 22, 0.35);
    animation: none;
}

.hub-btn:not(:disabled):active {
    transform: scale(0.96) rotate(0deg);
    box-shadow: 0 2px 8px rgba(249, 115, 22, 0.25);
}

.hub-btn i {
    font-size: 22px;
    margin-bottom: 3px;
}

.hub-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}

/* ═══════════════════════════════════════════════════
   KẾT QUẢ — ANIMATION VÀO MƯỢT MÀ
═══════════════════════════════════════════════════ */
.animate-premium-result {
    animation: resultReveal 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
    will-change: transform, opacity;
}

@keyframes resultReveal {
    0% {
        opacity: 0;
        transform: translateY(32px) scale(0.98);
        filter: blur(3px);
    }

    60% {
        filter: blur(0);
    }

    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
        filter: blur(0);
    }
}

/* ═══════════════════════════════════════════════════
   THẺ KẾT QUẢ MASTER
═══════════════════════════════════════════════════ */
.result-master-card {
    background: var(--card-bg) !important;
    border-radius: 20px;
    border-color: var(--border-color) !important;
    position: relative;
    transition: box-shadow 0.4s ease;
}

.result-master-card:hover {
    box-shadow: 0 12px 40px rgba(249, 115, 22, 0.08) !important;
}

/* Dải ánh sáng chạy ngang — sửa lỗi 100-percent */
.shimmer-line {
    position: absolute;
    top: 0;
    left: -200%;
    width: 60%;
    height: 100%;
    background: linear-gradient(105deg,
            transparent 20%,
            rgba(249, 115, 22, 0.05) 50%,
            transparent 80%);
    transform: skewX(-20deg);
    z-index: 5;
    pointer-events: none;
    animation: shimmerGlide 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    animation-delay: 0.6s;
}

@keyframes shimmerGlide {
    0% {
        left: -200%;
    }

    40% {
        left: 200%;
    }

    100% {
        left: 200%;
    }
}

.result-header {
    background: var(--input-bg);
    color: var(--text-main);
    border-bottom: 1px solid var(--border-color);
}

.result-badge {
    font-weight: 800;
    letter-spacing: 2.5px;
    font-size: 11px;
    color: #f97316;
    text-shadow: 0 0 20px rgba(249, 115, 22, 0.3);
}

/* Ô danh xưng trái tối */
.result-term-box {
    background: linear-gradient(145deg, #1a1c2e 0%, #141625 100%);
    color: white;
    position: relative;
    min-height: 190px;
    z-index: 1;
    overflow: hidden;
}

.result-term-box::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 50% 120%, rgba(249, 115, 22, 0.12) 0%, transparent 65%);
    pointer-events: none;
}

.term-label {
    font-weight: 700;
    opacity: 0.35;
    font-size: 10.5px;
    letter-spacing: 1px;
    position: relative;
    z-index: 2;
}

/* Chữ danh xưng lớn — nảy từ tâm, mượt hơn */
.term-value {
    font-size: 2.7rem;
    color: #f97316;
    text-shadow:
        0 2px 12px rgba(249, 115, 22, 0.25),
        0 0 40px rgba(249, 115, 22, 0.1);
    position: relative;
    z-index: 2;
    line-height: 1.15;
}

.pop-text {
    animation: popTextIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.4) 0.2s both;
}

@keyframes popTextIn {
    0% {
        transform: scale(0.65) translateY(8px);
        opacity: 0;
    }

    70% {
        transform: scale(1.06) translateY(-2px);
        opacity: 1;
    }

    100% {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
}

/* Hào quang mờ ảo chuyển động chậm */
.term-glow {
    position: absolute;
    width: 160px;
    height: 160px;
    background: radial-gradient(circle, rgba(249, 115, 22, 0.14) 0%, transparent 70%);
    z-index: 0;
    animation: pulseGlow 5s ease-in-out infinite;
    pointer-events: none;
}

@keyframes pulseGlow {

    0%,
    100% {
        transform: scale(0.9);
        opacity: 0.4;
    }

    50% {
        transform: scale(1.25);
        opacity: 0.85;
    }
}

/* Ô mô tả phải */
.bg-adaptive-card {
    background-color: var(--card-bg) !important;
}

.bg-adaptive-input {
    background-color: var(--input-bg) !important;
    border-color: var(--border-color) !important;
}

.theme-text-main {
    color: var(--text-main) !important;
}

.text-pink {
    color: #db2777 !important;
}

/* Badge danh xưng lớn — nhịp thở phát quang */
.relation-badge-large {
    display: inline-block;
    padding: 7px 26px;
    background: rgba(249, 115, 22, 0.07);
    color: #f97316;
    font-weight: 800;
    font-size: 18px;
    border-radius: 14px;
    border: 1.5px solid rgba(249, 115, 22, 0.18);
    transition: background 0.3s ease, transform 0.3s ease;
}

.relation-badge-large:hover {
    background: rgba(249, 115, 22, 0.12);
    transform: scale(1.02);
}

.pulse-orange {
    animation: badgePulse 3s ease-in-out infinite;
}

@keyframes badgePulse {

    0%,
    100% {
        box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.1),
            inset 0 0 0 0 rgba(249, 115, 22, 0.0);
    }

    50% {
        box-shadow: 0 0 16px 4px rgba(249, 115, 22, 0.07),
            inset 0 0 10px 0 rgba(249, 115, 22, 0.04);
    }
}

/* ═══════════════════════════════════════════════════
   NÚT CTA
═══════════════════════════════════════════════════ */
.btn-gradient-orange {
    background: linear-gradient(135deg, #f43f5e, #f97316) !important;
    border: none !important;
    color: #fff !important;
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1),
        box-shadow 0.25s ease,
        filter 0.25s ease;
}

.btn-gradient-orange:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 8px 24px rgba(249, 115, 22, 0.3) !important;
    filter: brightness(1.05);
}

.btn-gradient-orange:active {
    transform: translateY(0) scale(0.98);
}

/* Custom Searchable Select Styles */
.options-list {
    max-height: 200px;
    overflow-y: auto;
}

.options-list::-webkit-scrollbar {
    width: 4px;
}

.options-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.option-item {
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 1px solid var(--border-color, #f1f5f9);
}

.option-item:hover {
    background-color: rgba(249, 115, 22, 0.08);
}

.option-item.active-option {
    background-color: #f97316 !important;
}

.option-item.active-option .text-dark,
.option-item.active-option .text-muted {
    color: #fff !important;
}

.hover-red:hover {
    color: #ef4444 !important;
}

.rotate-180 {
    transform: rotate(180deg);
}

.transition {
    transition: transform 0.25s ease;
}
</style>