<template>
    <div class="public-profile-container py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <!-- Loading State -->
                    <div v-if="isLoading" class="text-center py-5">
                        <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Đang tải...</span>
                        </div>
                        <h5 class="mt-3 text-muted fw-bold">Đang tải hồ sơ gia tộc...</h5>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="errorMsg" class="card shadow-lg border-0 radius-15 text-center p-5 bg-glass-error">
                        <div class="mb-4">
                            <i class="bx bx-error-circle text-danger" style="font-size: 80px;"></i>
                        </div>
                        <h3 class="fw-bold text-dark">Không Tìm Thấy Thông Tin!</h3>
                        <p class="text-muted fs-5">{{ errorMsg }}</p>
                        <router-link to="/" class="btn btn-warning px-5 radius-30 fw-bold shadow-sm mt-3">
                            <i class="bx bx-home-alt"></i> Quay Về Trang Chủ
                        </router-link>
                    </div>

                    <!-- Profile Content -->
                    <div v-else class="profile-card-wrapper">
                        <!-- Parallax Gold Header -->
                        <div class="profile-header radius-top-15 shadow-sm position-relative overflow-hidden" :class="member.trang_thai === 'Đã mất' ? 'header-memorial' : 'header-alive'">
                            <div class="header-overlay"></div>
                            
                            <!-- Main Header Info -->
                            <div class="d-flex flex-column align-items-center text-center text-white position-relative z-index-2 py-5 px-4">
                                <div class="avatar-holder mb-3 position-relative">
                                    <img :src="member.avatar || ('https://ui-avatars.com/api/?name=' + member.ho_ten + '&background=d4af37&color=fff&size=120')" 
                                        class="rounded-circle border border-4 shadow-lg border-gold profile-avatar" 
                                        alt="Avatar">
                                    <span class="status-badge position-absolute bottom-0 end-0 px-2 py-1 radius-30 text-white font-12 fw-bold"
                                        :class="member.trang_thai === 'Còn sống' ? 'bg-success' : 'bg-danger'">
                                        <i class="bx" :class="member.trang_thai === 'Còn sống' ? 'bx-heart' : 'bx-bookmark-heart'"></i>
                                        {{ member.trang_thai }}
                                    </span>
                                </div>
                                <h2 class="fw-bold text-white mb-1 drop-shadow">{{ member.ho_ten }}</h2>
                                <p v-if="member.ten_goi && member.ten_goi !== member.ho_ten" class="fs-5 text-warning mb-2 fw-medium italic-nickname">"{{ member.ten_goi }}"</p>
                                
                                <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                                    <span class="badge badge-glass radius-30 px-3 py-2 font-13">
                                        <i class="bx bx-git-branch text-warning me-1"></i>
                                        Đời thứ {{ member.doi_thu }}
                                    </span>
                                    <span class="badge badge-glass radius-30 px-3 py-2 font-13">
                                        <i class="bx" :class="member.gioi_tinh === 'Nam' ? 'bx-male-sign text-info' : 'bx-female-sign text-pink'"></i>
                                        {{ member.gioi_tinh }}
                                    </span>
                                    <span v-if="member.chi_nhanh" class="badge badge-glass radius-30 px-3 py-2 font-13">
                                        <i class="bx bx-home-alt text-success me-1"></i>
                                        {{ member.chi_nhanh.ten_chi }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Main Details Grid -->
                        <div class="card bg-white shadow-lg border-0 radius-bottom-15 p-4 mt-0 pt-5">
                            
                            <!-- UNIFIED RELATIONSHIP PANEL -->
                            <div v-if="relationship || dynamicRelationship" class="rel-finder-card p-4 mb-4 radius-12 border border-2 border-dashed-gold position-relative overflow-hidden">
                                <div class="rel-finder-bg-decor"></div>
                                <div class="position-relative z-index-2">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <div class="rel-icon-box">
                                            <i class="bx bx-dna fs-4 text-warning"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-extrabold text-dark mb-0">Mối Quan Hệ Với Bạn</h6>
                                            <p class="text-muted font-12 mb-0">
                                                <template v-if="isLoggedIn">Dựa trên email tài khoản của bạn trong hệ thống gia phả</template>
                                                <template v-else>Thông tin từ hệ thống nhận diện huyết thống</template>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Loading state -->
                                    <div v-if="isLoadingRelationship" class="text-center py-4">
                                        <div class="spinner-border spinner-border-sm text-warning me-2"></div>
                                        <span class="text-muted font-13">Hệ thống đang xác định mối quan hệ huyết thống...</span>
                                    </div>

                                    <!-- Display result (auto-detected for logged-in, or static for others) -->
                                    <div v-else-if="dynamicRelationship || relationship" class="dyn-result mt-0 p-4 radius-12 animate__animated animate__fadeIn" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
                                        <div class="row align-items-center g-3">
                                            <div class="col-md-4 text-center">
                                                <div class="font-11 text-warning text-uppercase fw-bold mb-1">
                                                    <template v-if="isLoggedIn">Bạn gọi người này là</template>
                                                    <template v-else>Mối quan hệ</template>
                                                </div>
                                                <div class="dyn-term-value">{{ (dynamicRelationship || relationship).term }}</div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="font-13 text-white-50 mb-2">{{ (dynamicRelationship || relationship).description }}</div>
                                                <div v-if="(dynamicRelationship || relationship).path && (dynamicRelationship || relationship).path.length" class="pt-2 border-top border-secondary">
                                                    <div class="font-11 text-warning mb-2"><i class="bx bx-git-branch me-1"></i>Đường kết nối huyết thống:</div>
                                                    <div class="d-flex flex-wrap align-items-center gap-1">
                                                        <div v-for="(node, idx) in (dynamicRelationship || relationship).path" :key="idx" class="d-flex align-items-center gap-1">
                                                            <div class="dyn-path-node d-flex align-items-center gap-1 px-2 py-1 radius-8"
                                                                 :style="idx === 0 && isLoggedIn ? 'background:rgba(212,175,55,0.25); border:1px solid #d4af37;' : idx === (dynamicRelationship || relationship).path.length - 1 && isLoggedIn ? 'background:rgba(59,130,246,0.25); border:1px solid #60a5fa;' : 'background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15);'">
                                                                <img :src="node.avatar || 'https://ui-avatars.com/api/?name=' + node.ho_ten + '&background=d4af37&color=fff'" class="rounded-circle" width="18" height="18">
                                                                <span class="font-11 fw-bold">{{ node.ho_ten }}</span>
                                                                <span v-if="isLoggedIn && idx === 0" class="badge ms-1" style="font-size:8px; background:#d4af37; color:#000;">BẠN</span>
                                                                <span v-else-if="isLoggedIn && idx === (dynamicRelationship || relationship).path.length - 1" class="badge ms-1" style="font-size:8px; background:#3b82f6;">ĐT</span>
                                                            </div>
                                                            <i v-if="idx < (dynamicRelationship || relationship).path.length - 1" class="bx bx-chevron-right" style="font-size:14px; opacity:0.5; color:#d4af37;"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Not found in family tree (for logged-in users) -->
                                    <div v-else-if="isLoadingRelationship === false && dynamicRelError" class="text-center py-3">
                                        <i class="bx bx-unlink text-muted fs-2 mb-2 d-block"></i>
                                        <p class="text-muted font-13 mb-0">{{ dynamicRelError }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Gợi ý đăng nhập nếu chưa đăng nhập và không có relationship từ backend -->
                            <div v-if="!isLoggedIn && !relationship" class="text-center py-3 mb-4 p-3 radius-12 bg-light border border-dashed">
                                <i class="bx bx-lock-alt text-muted fs-3 mb-2 d-block"></i>
                                <p class="text-muted font-13 mb-2">Đăng nhập để xem mối quan hệ huyết thống với thành viên này</p>
                                <router-link to="/login" class="btn btn-sm btn-dark px-4 radius-30 fw-bold">
                                    <i class="bx bx-log-in me-1"></i> Đăng Nhập Ngay
                                </router-link>
                            </div>

                            <!-- Memorial Panel (Tưởng niệm người đã khuất) -->
                            <div v-if="member.trang_thai === 'Đã mất'" class="memorial-panel p-4 mb-4 radius-12 text-center text-dark-rose border border-rose">
                                <div class="d-flex justify-content-center gap-3 mb-2">
                                    <i class="bx bxs-flame text-warning animate-candle fs-2"></i>
                                    <h4 class="fw-bold tracking-wide text-rose mb-0">TƯỞNG NIỆM & GIỖ CHẠP</h4>
                                    <i class="bx bxs-flame text-warning animate-candle fs-2"></i>
                                </div>
                                <p class="fst-italic opacity-75 font-14 mb-3">"Sinh ký tử quy - Nơi lưu giữ ngọn lửa tri ân tiên tổ"</p>
                                
                                <div class="row g-3 justify-content-center">
                                    <div class="col-md-5">
                                        <div class="memorial-info-box p-3 radius-10 border border-rose-light bg-glass-rose">
                                            <div class="font-12 text-muted-rose text-uppercase fw-bold">Ngày Giỗ Âm Lịch</div>
                                            <div class="fs-4 fw-extrabold text-rose">
                                                {{ getLunarDateString() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5" v-if="member.ngay_mat">
                                        <div class="memorial-info-box p-3 radius-10 border border-rose-light bg-glass-rose">
                                            <div class="font-12 text-muted-rose text-uppercase fw-bold">Ngày Mất Dương Lịch</div>
                                            <div class="fs-4 fw-extrabold text-rose">
                                                {{ formatDate(member.ngay_mat) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabs Switcher -->
                            <ul class="nav nav-tabs nav-tabs-custom mb-4" id="profileTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-bold" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">
                                        <i class="bx bx-user me-1"></i> Thông Tin Tiểu Sử
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="family-tab" data-bs-toggle="tab" data-bs-target="#family-pane" type="button" role="tab">
                                        <i class="bx bx-git-repo-forked me-1"></i> Mắt Xích Gia Đình
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="profileTabsContent">
                                <!-- Info Tab Pane -->
                                <div class="tab-pane fade show active" id="info-pane" role="tabpanel" tabindex="0">
                                    <div class="row g-4">
                                        <!-- Biography & Story -->
                                        <div class="col-md-12">
                                            <div class="p-3 bg-light radius-10 border border-light-grey height-100">
                                                <h5 class="fw-bold mb-3 text-dark text-border-bottom"><i class="bx bx-book-open text-warning me-1"></i> Cuộc Đời & Sự Nghiệp</h5>
                                                <div v-if="member.ghi_chu" class="quote-story p-3 radius-8 mb-3 bg-white border-start border-4 border-warning italic font-15 text-dark lh-base">
                                                    {{ member.ghi_chu }}
                                                </div>
                                                <div v-if="member.thong_tin_them" class="text-secondary font-14 lh-relaxed" style="white-space: pre-line;">
                                                    {{ member.thong_tin_them }}
                                                </div>
                                                <p v-if="!member.ghi_chu && !member.thong_tin_them" class="text-muted italic text-center py-4">Chưa có thông tin ghi chép về cuộc đời thành viên này.</p>
                                            </div>
                                        </div>

                                        <!-- Timeline / Vital Stats -->
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light radius-10 border border-light-grey height-100">
                                                <h5 class="fw-bold mb-3 text-dark text-border-bottom"><i class="bx bx-time text-primary me-1"></i> Mốc Thời Gian</h5>
                                                <ul class="timeline-vital list-unstyled mb-0">
                                                    <li class="d-flex align-items-center mb-3">
                                                        <div class="timeline-icon rounded-circle bg-success text-white me-3"><i class="bx bx-plus"></i></div>
                                                        <div>
                                                            <div class="font-12 text-muted">Ngày Sinh</div>
                                                            <div class="fw-bold text-dark">{{ formatDate(member.ngay_sinh) }}</div>
                                                        </div>
                                                    </li>
                                                    <li v-if="member.trang_thai === 'Đã mất'" class="d-flex align-items-center mb-0">
                                                        <div class="timeline-icon rounded-circle bg-danger text-white me-3"><i class="bx bx-minus"></i></div>
                                                        <div>
                                                            <div class="font-12 text-muted">Ngày Mất</div>
                                                            <div class="fw-bold text-dark">{{ formatDate(member.ngay_mat) }}</div>
                                                        </div>
                                                    </li>
                                                    <li v-else class="d-flex align-items-center mb-0">
                                                        <div class="timeline-icon rounded-circle bg-primary text-white me-3"><i class="bx bx-check"></i></div>
                                                        <div>
                                                            <div class="font-12 text-muted">Trạng Thái Hiện Tại</div>
                                                            <div class="fw-bold text-success">Còn sống (Hưởng dương / Thọ)</div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Basic Attributes -->
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light radius-10 border border-light-grey height-100">
                                                <h5 class="fw-bold mb-3 text-dark text-border-bottom"><i class="bx bx-id-card text-info me-1"></i> Thông Tin Cơ Bản</h5>
                                                <table class="table table-sm table-borderless mb-0 align-middle">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-muted py-2" style="width: 120px;">Họ và Tên:</td>
                                                            <td class="fw-bold text-dark py-2">{{ member.ho_ten }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted py-2">Giới Tính:</td>
                                                            <td class="fw-bold text-dark py-2">{{ member.gioi_tinh }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted py-2">Mối Quan Hệ:</td>
                                                            <td class="fw-bold text-dark py-2">
                                                                <span class="badge" :class="member.loai_quan_he === 'Chính' ? 'bg-light-primary text-primary' : 'bg-light-warning text-warning'">
                                                                    {{ member.loai_quan_he === 'Chính' ? 'Thành viên chính (Huyết thống)' : 'Người phối ngẫu (Dâu/Rể)' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr v-if="member.email">
                                                            <td class="text-muted py-2">Email liên hệ:</td>
                                                            <td class="fw-bold text-dark py-2 font-13">{{ member.email }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Family Tab Pane -->
                                <div class="tab-pane fade" id="family-pane" role="tabpanel" tabindex="0">
                                    <div class="p-3 bg-light radius-10 border border-light-grey">
                                        <h5 class="fw-bold mb-3 text-dark text-border-bottom"><i class="bx bx-network-chart text-warning me-1"></i> Liên Kết Huyết Thống Gần Nhất</h5>
                                        
                                        <!-- Parents -->
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-secondary mb-2"><i class="bx bx-chevron-right text-warning"></i> Đấng Sinh Thành (Cha & Mẹ)</h6>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="family-relation-card p-2 bg-white radius-8 border d-flex align-items-center gap-3">
                                                        <div class="relation-role-tag bg-primary text-white font-10 px-2 py-1 rounded">CHA</div>
                                                        <template v-if="father">
                                                            <img :src="father.avatar || 'https://ui-avatars.com/api/?name=' + father.ho_ten" class="rounded-circle border" width="35" height="35" style="object-fit: cover;">
                                                            <div class="flex-grow-1 overflow-hidden">
                                                                <router-link :to="'/thanh-vien/detail/' + father.id" class="fw-bold text-dark text-decoration-none d-block text-truncate hover-primary">{{ father.ho_ten }}</router-link>
                                                                <span class="font-11 text-muted">Đời {{ father.doi_thu }} - {{ father.trang_thai }}</span>
                                                            </div>
                                                        </template>
                                                        <div v-else class="text-muted italic font-13 py-1">Chưa cập nhật cha ruột</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="family-relation-card p-2 bg-white radius-8 border d-flex align-items-center gap-3">
                                                        <div class="relation-role-tag bg-pink text-white font-10 px-2 py-1 rounded">MẸ</div>
                                                        <template v-if="mother">
                                                            <img :src="mother.avatar || 'https://ui-avatars.com/api/?name=' + mother.ho_ten" class="rounded-circle border" width="35" height="35" style="object-fit: cover;">
                                                            <div class="flex-grow-1 overflow-hidden">
                                                                <router-link :to="'/thanh-vien/detail/' + mother.id" class="fw-bold text-dark text-decoration-none d-block text-truncate hover-primary">{{ mother.ho_ten }}</router-link>
                                                                <span class="font-11 text-muted">Đời {{ mother.doi_thu }} - {{ mother.trang_thai }}</span>
                                                            </div>
                                                        </template>
                                                        <div v-else class="text-muted italic font-13 py-1">Chưa cập nhật mẹ ruột</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Spouses (Bạn đời) -->
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-secondary mb-2"><i class="bx bx-chevron-right text-warning"></i> Bạn Đời (Vợ / Chồng)</h6>
                                            <div class="row g-3">
                                                <div v-if="spouses.length === 0" class="col-12">
                                                    <div class="p-3 bg-white radius-8 border text-center text-muted italic font-13">Chưa kết hôn / chưa cập nhật thông tin bạn đời</div>
                                                </div>
                                                <div v-else v-for="sp in spouses" :key="sp.id" class="col-md-6">
                                                    <div class="family-relation-card p-2 bg-white radius-8 border d-flex align-items-center gap-3">
                                                        <div class="relation-role-tag bg-warning text-dark font-10 px-2 py-1 rounded">PHỐI NGẪU</div>
                                                        <img :src="sp.avatar || 'https://ui-avatars.com/api/?name=' + sp.ho_ten" class="rounded-circle border" width="35" height="35" style="object-fit: cover;">
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <router-link :to="'/thanh-vien/detail/' + sp.id" class="fw-bold text-dark text-decoration-none d-block text-truncate hover-primary">{{ sp.ho_ten }}</router-link>
                                                            <span class="font-11 text-muted">{{ sp.gioi_tinh }} - Đời {{ sp.doi_thu }} - {{ sp.trang_thai }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Children -->
                                        <div>
                                            <h6 class="fw-bold text-secondary mb-2"><i class="bx bx-chevron-right text-warning"></i> Thế Hệ Hậu Duệ (Con Cái)</h6>
                                            <div class="row g-3">
                                                <div v-if="children.length === 0" class="col-12">
                                                    <div class="p-3 bg-white radius-8 border text-center text-muted italic font-13">Chưa có hậu duệ / chưa cập nhật thông tin con cái</div>
                                                </div>
                                                <div v-else v-for="child in children" :key="child.id" class="col-md-6">
                                                    <div class="family-relation-card p-2 bg-white radius-8 border d-flex align-items-center gap-3">
                                                        <div class="relation-role-tag bg-success text-white font-10 px-2 py-1 rounded">CON CÁI</div>
                                                        <img :src="child.avatar || 'https://ui-avatars.com/api/?name=' + child.ho_ten" class="rounded-circle border" width="35" height="35" style="object-fit: cover;">
                                                        <div class="flex-grow-1 overflow-hidden">
                                                            <router-link :to="'/thanh-vien/detail/' + child.id" class="fw-bold text-dark text-decoration-none d-block text-truncate hover-primary">{{ child.ho_ten }}</router-link>
                                                            <span class="font-11 text-muted">{{ child.gioi_tinh }} - Đời {{ child.doi_thu }} - {{ child.trang_thai }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Footer CTAs -->
                            <div class="text-center mt-5 pt-4 border-top border-light-grey">
                                <p class="text-muted font-13">Bản quyền thuộc về Hệ Thống Quản Lý Gia Phả Số</p>
                                <div class="d-flex flex-wrap justify-content-center gap-3">
                                    <router-link to="/gia-pha" class="btn btn-outline-primary px-4 radius-30 font-14">
                                        <i class="bx bx-git-branch"></i> Xem Cây Gia Phả
                                    </router-link>
                                    <router-link to="/tra-cuu" class="btn btn-outline-secondary px-4 radius-30 font-14">
                                        <i class="bx bx-search"></i> Tra Cứu Dòng Họ
                                    </router-link>
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

export default {
    name: 'PublicMemberDetail',
    data() {
        return {
            isLoading: true,
            errorMsg: null,
            member: {},
            father: null,
            mother: null,
            spouses: [],
            children: [],
            relationship: null,
            // Manual relationship finder
            dynamicRelationship: null,
            dynamicRelError: null,
            isLoadingRelationship: false,
            isLoggedIn: !!localStorage.getItem('access_token')
        }
    },
    watch: {
        '$route.params.id': {
            immediate: true,
            handler() {
                this.loadMemberProfile();
                this.dynamicRelationship = null;
                this.dynamicRelError = null;
                this.isLoadingRelationship = false;
                if (this.isLoggedIn) {
                    this.$nextTick(() => this.autoDetectMyRelationship());
                }
            }
        }
    },
    mounted() {
        if (this.isLoggedIn) {
            this.autoDetectMyRelationship();
        }
    },
    methods: {
        getHeaders() {
            const token = localStorage.getItem('access_token');
            return token ? { headers: { Authorization: `Bearer ${token}` } } : {};
        },
        loadMemberProfile() {
            this.isLoading = true;
            this.errorMsg = null;
            const id = this.$route.params.id;

            axios.get(`http://127.0.0.1:8000/api/thanh-vien/public-detail/${id}`, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        const payload = res.data.data;
                        this.member = payload.member;
                        this.father = payload.father;
                        this.mother = payload.mother;
                        this.spouses = payload.spouses || [];
                        this.children = payload.children || [];
                        this.relationship = payload.relationship || null;
                    } else {
                        this.errorMsg = res.data.message || 'Không tìm thấy hồ sơ thành viên.';
                    }
                })
                .catch(err => {
                    this.errorMsg = err.response?.data?.message || 'Không tìm thấy hồ sơ hoặc máy chủ không phản hồi.';
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        // Auto-detect logged-in user's member profile by email, then compute relationship
        autoDetectMyRelationship() {
            const token = localStorage.getItem('access_token');
            if (!token) return;
            const targetId = parseInt(this.$route.params.id);

            // 1. Lay thong tin user hien tai tu /api/me
            axios.get('http://127.0.0.1:8000/api/me', {
                headers: { Authorization: `Bearer ${token}` }
            })
            .then(meRes => {
                const userEmail = meRes.data?.user?.email;
                if (!userEmail) return;

                // 2. Tim thanh vien co email trung khop
                return axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data')
                    .then(tvRes => {
                        if (!tvRes.data.status) return;
                        const myMember = tvRes.data.data.find(m => m.email === userEmail);

                        if (!myMember) {
                            // Nguoi dung chua co trong cay gia pha
                            this.dynamicRelError = 'Tài khoản của bạn chưa được liên kết với thành viên nào trong cây gia phả.';
                            return;
                        }

                        if (myMember.id === targetId) {
                            // Dang xem chinh minh
                            this.dynamicRelError = 'Đây chính là hồ sơ của bạn trong cây gia phả.';
                            return;
                        }

                        // 3. Goi API xac dinh quan he BFS
                        this.isLoadingRelationship = true;
                        return axios.post('http://127.0.0.1:8000/api/thanh-vien/xac-dinh-quan-he', {
                            id_a: myMember.id,
                            id_b: targetId
                        })
                        .then(relRes => {
                            if (relRes.data.status) {
                                this.dynamicRelationship = {
                                    term: relRes.data.term,
                                    description: relRes.data.description,
                                    path: relRes.data.path || []
                                };
                            } else {
                                this.dynamicRelError = 'Không tìm thấy mối quan hệ huyết thống giữa bạn và người này.';
                            }
                        });
                    });
            })
            .catch(() => {
                // Silent fail — khong hien loi
            })
            .finally(() => {
                this.isLoadingRelationship = false;
            });
        },
        formatDate(dateString) {
            if (!dateString) return '---';
            return new Date(dateString).toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        getLunarDateString() {
            const m = this.member;
            if (!m.ngay_mat_al_ngay || !m.ngay_mat_al_thang) {
                return 'Đang cập nhật';
            }
            let str = `Ngày ${m.ngay_mat_al_ngay} tháng ${m.ngay_mat_al_thang}`;
            if (m.ngay_mat_al_nhuan) {
                str += ' (Nhuận)';
            }
            if (m.ngay_mat_al_nam) {
                str += `, năm ${m.ngay_mat_al_nam}`;
            }
            return str;
        }
    }
}
</script>

<style scoped>
.public-profile-container {
    background-color: #f7f9fc;
    min-height: 100vh;
}
.radius-15 { border-radius: 15px !important; }
.radius-bottom-15 { 
    border-bottom-left-radius: 15px !important; 
    border-bottom-right-radius: 15px !important; 
}
.radius-top-15 {
    border-top-left-radius: 15px !important;
    border-top-right-radius: 15px !important;
}
.radius-12 { border-radius: 12px !important; }
.radius-30 { border-radius: 30px !important; }
.radius-8 { border-radius: 8px !important; }
.z-index-2 { z-index: 2; }
.shadow-xs { box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important; }

/* Parallax Headers */
.profile-header {
    background-size: cover;
    background-position: center;
    position: relative;
    border-bottom: 5px solid #d4af37;
}
.header-alive {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
}
.header-memorial {
    background: linear-gradient(135deg, #4b121a 0%, #7d1f2b 100%);
    border-bottom-color: #c5a059 !important;
}
.header-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle, transparent 20%, rgba(0,0,0,0.3) 100%);
    z-index: 1;
}

/* Avatar Border Styling */
.avatar-holder {
    display: inline-block;
}
.profile-avatar {
    width: 120px;
    height: 120px;
    object-fit: cover;
    transition: transform 0.3s ease;
}
.profile-avatar:hover {
    transform: scale(1.05);
}
.border-gold {
    border-color: #d4af37 !important;
}
.header-memorial .border-gold {
    border-color: #c5a059 !important;
}
.drop-shadow {
    text-shadow: 0 2px 4px rgba(0,0,0,0.4);
}
.italic-nickname {
    font-style: italic;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.badge-glass {
    background: rgba(255, 255, 255, 0.15) !important;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
}

/* Dynamic Relation Card */
.relationship-card {
    background: linear-gradient(135deg, #fffcf5 0%, #fff8e8 100%);
    border-color: rgba(212, 175, 55, 0.3) !important;
}
.border-light-gold {
    border-color: rgba(212, 175, 55, 0.15) !important;
}
.bg-light-gold {
    background-color: #fff9e6;
}
.text-warning-gold {
    color: #e5a93b !important;
}
.bg-dark-gold {
    background-color: #3b2c0c;
    color: #ffd891 !important;
}
.decor-light {
    position: absolute;
    top: -20px;
    right: -20px;
    width: 100px;
    height: 100px;
    background: radial-gradient(circle, rgba(212,175,55,0.2) 0%, transparent 70%);
    pointer-events: none;
    z-index: 1;
}

/* Memorial styling */
.memorial-panel {
    background: linear-gradient(135deg, #fff5f5 0%, #ffe9e9 100%);
    border-color: rgba(245, 81, 95, 0.2) !important;
}
.text-dark-rose { color: #5c181b; }
.text-rose { color: #a11d23; }
.border-rose { border-color: rgba(161, 29, 35, 0.2) !important; }
.border-rose-light { border-color: rgba(161, 29, 35, 0.1) !important; }
.bg-glass-rose { background-color: rgba(255,255,255,0.6); }
.text-muted-rose { color: #8a484c; }

@keyframes flicker {
    0%, 100% { transform: scale(1); opacity: 0.9; }
    50% { transform: scale(1.1) rotate(2deg); opacity: 1; }
}
.animate-candle {
    display: inline-block;
    animation: flicker 1.5s infinite ease-in-out;
}

/* Tabs */
.nav-tabs-custom {
    border-bottom: 2px solid #eaedf1;
}
.nav-tabs-custom .nav-link {
    border: none;
    color: #5c6873;
    padding: 12px 20px;
    background: transparent;
    transition: all 0.3s ease;
}
.nav-tabs-custom .nav-link.active {
    color: #d4af37;
    border-bottom: 3px solid #d4af37;
}

/* Quote story block */
.quote-story {
    box-shadow: inset 0 0 10px rgba(0,0,0,0.01);
}
.text-border-bottom {
    border-bottom: 2px solid #eaedf1;
    padding-bottom: 8px;
}
.lh-relaxed { line-height: 1.7; }
.italic { font-style: italic; }

/* Timeline Vital */
.timeline-vital li {
    position: relative;
}
.timeline-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

/* Family relation cards */
.family-relation-card {
    transition: all 0.25s ease;
}
.family-relation-card:hover {
    border-color: #d4af37 !important;
    box-shadow: 0 4px 10px rgba(212, 175, 55, 0.1) !important;
    transform: translateY(-2px);
}
.relation-role-tag {
    font-weight: 800;
    letter-spacing: 0.5px;
    font-size: 9px !important;
}
.hover-primary:hover {
    color: #d4af37 !important;
}
.text-pink { color: #e83e8c; }
.bg-pink { background-color: #e83e8c; }
.bg-light-primary { background-color: rgba(13, 110, 253, 0.08); }
.bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }

/* === RELATIONSHIP FINDER PANEL === */
.border-dashed-gold {
    border-color: rgba(212, 175, 55, 0.4) !important;
    border-style: dashed !important;
}
.rel-finder-card {
    background: linear-gradient(135deg, #fffdf5 0%, #fdf8e8 100%);
}
.rel-finder-bg-decor {
    position: absolute;
    top: 0; right: 0;
    width: 200px; height: 200px;
    background: radial-gradient(circle at top right, rgba(212,175,55,0.12) 0%, transparent 70%);
    pointer-events: none;
}
.rel-icon-box {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: rgba(212,175,55,0.15);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.btn-dark-gold {
    background: #0f172a;
    color: #d4af37;
    border: 1px solid #d4af37;
    transition: all 0.2s ease;
}
.btn-dark-gold:hover:not(:disabled) {
    background: #d4af37;
    color: #0f172a;
    transform: translateY(-1px);
}
.btn-dark-gold:disabled { opacity: 0.55; }

.dyn-result { border: 1px solid rgba(212,175,55,0.3); }
.dyn-term-value {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    font-weight: 900;
    color: #d4af37;
    line-height: 1.1;
    text-shadow: 0 0 30px rgba(212,175,55,0.3);
}
.dyn-path-node { transition: all 0.2s ease; }
.dyn-path-node:hover { transform: translateY(-1px); }
</style>
