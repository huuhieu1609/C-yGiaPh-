<template>
    <div class="public-profile-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-11">
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
                        <!-- Premium Hero Header Card -->
                        <div class="profile-hero-card" :class="member.trang_thai === 'Đã mất' ? 'hero-memorial' : 'hero-alive'">
                            <div class="hero-bg-accent"></div>
                            <div class="row align-items-center g-4 position-relative z-index-2">
                                <div class="col-md-auto text-center text-md-start">
                                    <div class="avatar-holder-premium position-relative">
                                        <img :src="member.avatar || ('https://ui-avatars.com/api/?name=' + member.ho_ten + '&background=d4af37&color=fff&size=150')" 
                                            class="profile-avatar-img shadow-lg" 
                                            alt="Avatar">
                                        <span class="status-indicator shadow-sm" :class="member.trang_thai === 'Còn sống' ? 'status-alive' : 'status-deceased'">
                                            <i class="bx" :class="member.trang_thai === 'Còn sống' ? 'bx-heart' : 'bx-bookmark-heart'"></i>
                                            {{ member.trang_thai }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md text-center text-md-start text-white">
                                    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                                        <span class="badge badge-generation"><i class="bx bx-git-branch"></i> Đời thứ {{ member.doi_thu }}</span>
                                        <span class="badge badge-gender" :class="member.gioi_tinh === 'Nam' ? 'gender-male' : 'gender-female'">
                                            <i class="bx" :class="member.gioi_tinh === 'Nam' ? 'bx-male' : 'bx-female'"></i>
                                            {{ member.gioi_tinh }}
                                        </span>
                                        <span v-if="member.chi_nhanh" class="badge badge-branch">
                                            <i class="bx bx-home-alt"></i> {{ member.chi_nhanh.ten_chi }}
                                        </span>
                                    </div>
                                    <h1 class="profile-name">{{ member.ho_ten }}</h1>
                                    <p v-if="member.ten_goi && member.ten_goi !== member.ho_ten" class="profile-nickname">
                                        Tên thường gọi: <span class="fw-bold">"{{ member.ten_goi }}"</span>
                                    </p>
                                    <div v-if="member.email" class="profile-email-badge mt-2">
                                        <i class="bx bx-envelope"></i> {{ member.email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Two-Column Grid Layout -->
                        <div class="row g-4">
                            <!-- Left Sidebar Column (4 Cols) -->
                            <div class="col-lg-4">
                                <!-- Kinship Relationship Finder Card -->
                                <div v-if="relationship || dynamicRelationship" class="sidebar-card relationship-card-premium animate__animated animate__fadeInUp">
                                    <div class="d-flex align-items-center gap-3 mb-4">
                                        <div class="rel-icon-box-premium">
                                            <i class="bx bx-dna fs-4 text-warning"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0 text-dark">Mối Quan Hệ</h5>
                                            <p class="text-muted font-11 mb-0">Hệ thống phả hệ số hóa</p>
                                        </div>
                                    </div>

                                    <div v-if="isLoadingRelationship" class="text-center py-4">
                                        <div class="spinner-border spinner-border-sm text-warning"></div>
                                    </div>

                                    <div v-else class="dyn-result-premium">
                                        <div class="text-center py-2">
                                            <div class="relationship-title text-muted text-uppercase fw-bold font-11">
                                                <template v-if="isLoggedIn">Bạn gọi người này là</template>
                                                <template v-else>Mối quan hệ</template>
                                            </div>
                                            <div class="relationship-term">
                                                {{ (dynamicRelationship || relationship).term }}
                                            </div>
                                        </div>
                                        <div class="relationship-desc p-3 rounded bg-light border-start border-3 border-warning font-13 text-secondary mt-2">
                                            {{ (dynamicRelationship || relationship).description }}
                                        </div>

                                        <!-- Lineage Path (mini) -->
                                        <div v-if="(dynamicRelationship || relationship).members && (dynamicRelationship || relationship).members.length" class="mt-4 pt-3 border-top border-light-grey">
                                            <div class="path-title mb-3 font-12 text-muted fw-bold text-uppercase">
                                                <i class="bx bx-git-branch text-warning me-1"></i> Sơ đồ kết nối huyết thống:
                                            </div>
                                            <div class="d-flex flex-column gap-3">
                                                <div v-for="(node, idx) in (dynamicRelationship || relationship).members" :key="idx" class="d-flex align-items-center gap-2 position-relative path-item">
                                                    <div class="path-node-avatar shadow-sm border border-gold">
                                                        <img :src="node.avatar || ('https://ui-avatars.com/api/?name=' + node.ho_ten + '&background=d4af37&color=fff')" class="rounded-circle">
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <div class="fw-bold font-12 text-truncate text-dark">{{ node.ho_ten }}</div>
                                                        <div class="font-10 text-muted">Đời {{ node.doi_thu }}</div>
                                                    </div>
                                                    <span v-if="isLoggedIn && node.id === myMemberId" class="badge bg-gold-badge text-dark font-9">BẠN</span>
                                                    <span v-else-if="isLoggedIn && node.id === member.id" class="badge bg-primary-badge text-white font-9">ĐT</span>
                                                    
                                                    <!-- Connecting vertical indicator -->
                                                    <div v-if="idx < (dynamicRelationship || relationship).members.length - 1" class="path-connector-line"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Login Prompt if guest -->
                                <div v-if="!isLoggedIn && !relationship" class="sidebar-card text-center p-4 guest-prompt-card animate__animated animate__fadeInUp">
                                    <div class="lock-icon-holder mb-3 mx-auto">
                                        <i class="bx bx-lock-alt text-warning display-5"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Xem Quan Hệ Huyết Thống</h6>
                                    <p class="text-muted font-12 mb-4">Đăng nhập tài khoản của bạn để khám phá vai vế xưng hô với thành viên này.</p>
                                    <router-link to="/login" class="btn btn-warning btn-sm w-100 radius-30 fw-bold shadow-sm py-2">
                                        <i class="bx bx-log-in me-1"></i> Đăng Nhập Ngay
                                    </router-link>
                                </div>

                                <!-- Memorial Panel (Deceased only) -->
                                <div v-if="member.trang_thai === 'Đã mất'" class="sidebar-card memorial-card-premium animate__animated animate__fadeInUp">
                                    <div class="text-center mb-3">
                                        <div class="d-flex justify-content-center gap-2 align-items-center">
                                            <i class="bx bxs-flame text-warning animate-candle fs-4"></i>
                                            <h5 class="fw-bold text-rose mb-0">TƯỞNG NIỆM HƯƠNG HỎA</h5>
                                            <i class="bx bxs-flame text-warning animate-candle fs-4"></i>
                                        </div>
                                        <p class="fst-italic text-muted-rose font-11 mt-1">"Nơi lưu giữ ngọn lửa tri ân tiên tổ"</p>
                                    </div>
                                    <div class="memorial-info-box-premium p-3 mb-3 text-center">
                                        <div class="font-10 text-uppercase fw-bold text-muted-rose mb-1">Ngày Giỗ Âm Lịch</div>
                                        <div class="fs-4 fw-extrabold text-rose">{{ getLunarDateString() }}</div>
                                    </div>
                                    <div v-if="member.ngay_mat" class="memorial-info-box-premium p-3 text-center">
                                        <div class="font-10 text-uppercase fw-bold text-muted-rose mb-1">Ngày Mất Dương Lịch</div>
                                        <div class="fs-4 fw-extrabold text-rose">{{ formatDate(member.ngay_mat) }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Content Column (8 Cols) -->
                            <div class="col-lg-8">
                                <div class="content-card-premium animate__animated animate__fadeInUp">
                                    <!-- Custom Navigation Tabs -->
                                    <div class="tabs-header-premium border-bottom pb-2">
                                        <ul class="nav nav-pills custom-pills" id="profileTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">
                                                    <i class="bx bx-user me-1"></i> Tiểu Sử Cuộc Đời
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="family-tab" data-bs-toggle="tab" data-bs-target="#family-pane" type="button" role="tab">
                                                    <i class="bx bx-git-repo-forked me-1"></i> Mắt Xích Gia Đình
                                                </button>
                                            </li>
                                            <li v-if="member.trang_thai === 'Đã mất'" class="nav-item" role="presentation">
                                                <button class="nav-link" id="memorial-tab" data-bs-toggle="tab" data-bs-target="#memorial-pane" type="button" role="tab" @click="loadTributes">
                                                    <i class="bx bxs-flame me-1"></i> Tưởng Niệm & Tri Ân
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery-pane" type="button" role="tab" @click="loadGallery">
                                                    <i class="bx bx-images me-1"></i> Hình Ảnh Kỷ Niệm
                                                </button>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="tab-content mt-4" id="profileTabsContent">
                                        <!-- Biography Tab -->
                                        <div class="tab-pane fade show active" id="info-pane" role="tabpanel" tabindex="0">
                                            <div class="biography-section">
                                                <h5 class="section-title mb-3"><i class="bx bx-book-open text-warning me-1"></i> Cuộc Đời & Sự Nghiệp</h5>
                                                
                                                <div v-if="member.ghi_chu" class="quote-story-premium p-4 radius-12 bg-light border-start border-4 border-warning mb-4">
                                                    <i class="bx bxs-quote-left text-warning fs-3 mb-2 d-block opacity-50"></i>
                                                    <p class="mb-0 fst-italic text-dark font-15 lh-relaxed">{{ member.ghi_chu }}</p>
                                                </div>
                                                
                                                <div v-if="member.thong_tin_them" class="story-content-premium text-secondary font-14 lh-relaxed mb-4" style="white-space: pre-line;">
                                                    {{ member.thong_tin_them }}
                                                </div>
                                                
                                                <p v-if="!member.ghi_chu && !member.thong_tin_them" class="text-muted italic text-center py-5">
                                                    Chưa có thông tin ghi chép về cuộc đời thành viên này.
                                                </p>

                                                <!-- Timeline and Basics in Grid inside content card -->
                                                <div class="row g-4 mt-2 pt-4 border-top border-light-grey">
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold mb-3 text-dark"><i class="bx bx-time text-primary me-1"></i> Mốc Thời Gian</h6>
                                                        <div class="timeline-item-premium d-flex align-items-center mb-3">
                                                            <div class="timeline-indicator-premium bg-success"></div>
                                                            <div class="timeline-details ms-3">
                                                                <span class="text-muted font-11 text-uppercase fw-bold">Ngày Sinh</span>
                                                                <div class="fw-bold font-14 text-dark">{{ formatDate(member.ngay_sinh) }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="timeline-item-premium d-flex align-items-center">
                                                            <div class="timeline-indicator-premium" :class="member.trang_thai === 'Đã mất' ? 'bg-danger' : 'bg-primary'"></div>
                                                            <div class="timeline-details ms-3">
                                                                <span class="text-muted font-11 text-uppercase fw-bold">Trạng Thái Sinh Mệnh</span>
                                                                <div class="fw-bold font-14" :class="member.trang_thai === 'Đã mất' ? 'text-danger' : 'text-success'">
                                                                    {{ member.trang_thai === 'Đã mất' ? 'Đã mất (' + formatDate(member.ngay_mat) + ')' : 'Còn sống' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="fw-bold mb-3 text-dark"><i class="bx bx-id-card text-info me-1"></i> Thông Tin Cơ Bản</h6>
                                                        <div class="info-row-premium d-flex justify-content-between py-2 border-bottom border-light">
                                                            <span class="label text-muted font-13">Họ và Tên</span>
                                                            <span class="value fw-bold text-dark font-13">{{ member.ho_ten }}</span>
                                                        </div>
                                                        <div class="info-row-premium d-flex justify-content-between py-2 border-bottom border-light">
                                                            <span class="label text-muted font-13">Giới Tính</span>
                                                            <span class="value fw-bold text-dark font-13">{{ member.gioi_tinh }}</span>
                                                        </div>
                                                        <div class="info-row-premium d-flex justify-content-between py-2 border-bottom border-light">
                                                            <span class="label text-muted font-13">Hôn Nhân</span>
                                                            <span class="value fw-bold font-13" :class="{
                                                              'text-danger': member.tinh_trang_hon_nhan === 'Đã kết hôn',
                                                              'text-secondary': member.tinh_trang_hon_nhan === 'Độc thân',
                                                              'text-warning': member.tinh_trang_hon_nhan === 'Ly hôn',
                                                              'text-muted': !member.tinh_trang_hon_nhan || member.tinh_trang_hon_nhan === 'Góa'
                                                            }">
                                                              <i class="bx me-1" :class="{
                                                                'bx-heart-circle': member.tinh_trang_hon_nhan === 'Đã kết hôn',
                                                                'bx-user': member.tinh_trang_hon_nhan === 'Độc thân',
                                                                'bx-x-circle': member.tinh_trang_hon_nhan === 'Ly hôn',
                                                                'bx-heart': member.tinh_trang_hon_nhan === 'Góa',
                                                                'bx-minus-circle': !member.tinh_trang_hon_nhan
                                                              }"></i>
                                                              {{ member.tinh_trang_hon_nhan || 'Chưa rõ' }}
                                                            </span>
                                                        </div>
                                                        <div class="info-row-premium d-flex justify-content-between py-2 border-bottom border-light">
                                                            <span class="label text-muted font-13">Hệ Phả Hệ</span>
                                                            <span class="value">
                                                                <span class="badge" :class="member.loai_quan_he === 'Chính' ? 'bg-light-primary text-primary' : 'bg-light-warning text-warning'">
                                                                    {{ member.loai_quan_he === 'Chính' ? 'Huyết thống chính' : 'Người phối ngẫu (Dâu/Rể)' }}
                                                                </span>
                                                            </span>
                                                        </div>
                                                        <div class="info-row-premium d-flex justify-content-between py-2 border-bottom border-light">
                                                            <span class="label text-muted font-13">Email</span>
                                                            <span class="value fw-bold text-dark font-13">{{ member.email || 'Chưa cập nhật' }}</span>
                                                        </div>
                                                        <div class="info-row-premium d-flex justify-content-between py-2 border-bottom border-light">
                                                            <span class="label text-muted font-13">Số điện thoại</span>
                                                            <span class="value fw-bold text-dark font-13">{{ member.so_dien_thoai || 'Chưa cập nhật' }}</span>
                                                        </div>
                                                        <div class="info-row-premium d-flex justify-content-between py-2 border-bottom border-light">
                                                            <span class="label text-muted font-13">Nghề nghiệp</span>
                                                            <span class="value fw-bold text-dark font-13">{{ member.nghe_nghiep || 'Chưa cập nhật' }}</span>
                                                        </div>
                                                        <div class="info-row-premium d-flex justify-content-between py-2">
                                                            <span class="label text-muted font-13">Địa chỉ</span>
                                                            <span class="value fw-bold text-dark font-13">{{ member.dia_chi || 'Chưa cập nhật' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Family Connections Tab -->
                                        <div class="tab-pane fade" id="family-pane" role="tabpanel" tabindex="0">
                                            <div class="family-tree-section">
                                                <h5 class="section-title mb-4"><i class="bx bx-network-chart text-warning me-1"></i> Quan Hệ Gia Đình</h5>
                                                
                                                <!-- Parents -->
                                                <div class="relation-group mb-4">
                                                    <h6 class="relation-group-title text-muted text-uppercase fw-bold font-12 mb-3"><i class="bx bx-chevron-right text-warning"></i> Đấng Sinh Thành (Cha & Mẹ)</h6>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <div class="relation-card-premium p-3 radius-12 border d-flex align-items-center gap-3">
                                                                <div class="relation-badge bg-primary text-white font-9 px-2 py-0.5 rounded position-absolute top-0 end-0 m-2">CHA</div>
                                                                <template v-if="father">
                                                                    <img :src="father.avatar || 'https://ui-avatars.com/api/?name=' + father.ho_ten" class="relation-avatar rounded-circle border shadow-sm">
                                                                    <div class="relation-info overflow-hidden">
                                                                        <router-link :to="'/thanh-vien/detail/' + father.id" class="relation-name d-block text-truncate">{{ father.ho_ten }}</router-link>
                                                                        <span class="relation-sub text-muted font-11">Đời {{ father.doi_thu }} - {{ father.trang_thai }}</span>
                                                                    </div>
                                                                </template>
                                                                <div v-else class="relation-empty text-muted italic font-13 py-2 px-1">Chưa cập nhật thông tin cha</div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="relation-card-premium p-3 radius-12 border d-flex align-items-center gap-3">
                                                                <div class="relation-badge bg-pink text-white font-9 px-2 py-0.5 rounded position-absolute top-0 end-0 m-2">MẸ</div>
                                                                <template v-if="mother">
                                                                    <img :src="mother.avatar || 'https://ui-avatars.com/api/?name=' + mother.ho_ten" class="relation-avatar rounded-circle border shadow-sm">
                                                                    <div class="relation-info overflow-hidden">
                                                                        <router-link :to="'/thanh-vien/detail/' + mother.id" class="relation-name d-block text-truncate">{{ mother.ho_ten }}</router-link>
                                                                        <span class="relation-sub text-muted font-11">Đời {{ mother.doi_thu }} - {{ mother.trang_thai }}</span>
                                                                    </div>
                                                                </template>
                                                                <div v-else class="relation-empty text-muted italic font-13 py-2 px-1">Chưa cập nhật thông tin mẹ</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Spouses -->
                                                <div class="relation-group mb-4">
                                                    <h6 class="relation-group-title text-muted text-uppercase fw-bold font-12 mb-3"><i class="bx bx-chevron-right text-warning"></i> Bạn Đời (Vợ / Chồng)</h6>
                                                    <div class="row g-3">
                                                        <div v-if="spouses.length === 0" class="col-12">
                                                            <div class="relation-empty-large text-center py-4 border border-dashed rounded-12 bg-light text-muted italic font-13">
                                                                Chưa kết hôn hoặc chưa cập nhật thông tin bạn đời
                                                            </div>
                                                        </div>
                                                        <div v-else-if="spouses" v-for="sp in spouses" :key="sp.id" class="col-md-6">
                                                            <div class="relation-card-premium p-3 radius-12 border d-flex align-items-center gap-3">
                                                                <div class="relation-badge bg-warning text-dark font-9 px-2 py-0.5 rounded position-absolute top-0 end-0 m-2">BẠN ĐỜI</div>
                                                                <img :src="sp.avatar || 'https://ui-avatars.com/api/?name=' + sp.ho_ten" class="relation-avatar rounded-circle border shadow-sm">
                                                                <div class="relation-info overflow-hidden">
                                                                    <router-link :to="'/thanh-vien/detail/' + sp.id" class="relation-name d-block text-truncate">{{ sp.ho_ten }}</router-link>
                                                                    <span class="relation-sub text-muted font-11">{{ sp.gioi_tinh }} - Đời {{ sp.doi_thu }} - {{ sp.trang_thai }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Children -->
                                                <div class="relation-group">
                                                    <h6 class="relation-group-title text-muted text-uppercase fw-bold font-12 mb-3"><i class="bx bx-chevron-right text-warning"></i> Thế Hệ Hậu Duệ (Con Cái)</h6>
                                                    <div class="row g-3">
                                                        <div v-if="children.length === 0" class="col-12">
                                                            <div class="relation-empty-large text-center py-4 border border-dashed rounded-12 bg-light text-muted italic font-13">
                                                                Chưa có hậu duệ hoặc chưa cập nhật thông tin con cái
                                                            </div>
                                                        </div>
                                                        <div v-else v-for="child in children" :key="child.id" class="col-md-6">
                                                            <div class="relation-card-premium p-3 radius-12 border d-flex align-items-center gap-3">
                                                                <div class="relation-badge bg-success text-white font-9 px-2 py-0.5 rounded position-absolute top-0 end-0 m-2">CON CÁI</div>
                                                                <img :src="child.avatar || 'https://ui-avatars.com/api/?name=' + child.ho_ten" class="relation-avatar rounded-circle border shadow-sm">
                                                                <div class="relation-info overflow-hidden">
                                                                    <router-link :to="'/thanh-vien/detail/' + child.id" class="relation-name d-block text-truncate">{{ child.ho_ten }}</router-link>
                                                                    <span class="relation-sub text-muted font-11">{{ child.gioi_tinh }} - Đời {{ child.doi_thu }} - {{ child.trang_thai }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer CTAs -->
                                    <div class="card-footer-premium mt-5 pt-4 border-top border-light-grey text-center">
                                        <p class="text-muted font-12 mb-3">Bản quyền thuộc về Hệ Thống Quản Lý Gia Phả Số</p>
                                        <div class="d-flex flex-wrap justify-content-center gap-3">
                                            <router-link to="/gia-pha" class="btn btn-premium-outline-primary px-4 py-2 radius-30 font-14 shadow-xs">
                                                <i class="bx bx-git-branch me-1"></i> Xem Cây Gia Phả
                                            </router-link>
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

    <!-- Upload Image Modal -->
    <div class="modal fade" id="uploadImageModal" tabindex="-1" aria-hidden="true" ref="uploadModalEl">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 radius-24 shadow-lg overflow-hidden bg-white">
          <div class="modal-header py-4 px-4 border-0 d-flex align-items-center justify-content-between text-white bg-dark-gradient">
            <h5 class="modal-title fw-bold"><i class="bx bx-image-add text-warning me-1"></i>Thêm Ảnh Kỷ Niệm</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" @click="closeUploadModal"></button>
          </div>
          <form @submit.prevent="submitImage">
            <div class="modal-body p-4">
              <div class="mb-3">
                <label class="form-label fw-bold text-dark font-xs">ĐƯỜNG DẪN HÌNH ẢNH (URL) <span class="text-danger">*</span></label>
                <input type="url" class="form-control radius-12 border-2 py-2 px-3 shadow-none-focus" v-model="newImageUrl" placeholder="https://example.com/image.jpg" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold text-dark font-xs">MÔ TẢ ẢNH</label>
                <input type="text" class="form-control radius-12 border-2 py-2 px-3 shadow-none-focus" v-model="newImageDesc" placeholder="Nhập mô tả ảnh kỷ niệm (ví dụ: Chụp ngày giỗ tổ 2025...)">
              </div>
            </div>
            <div class="modal-footer py-3 px-4 border-0 bg-light">
              <button type="button" class="btn btn-secondary radius-12 px-4" data-bs-dismiss="modal" @click="closeUploadModal">Đóng</button>
              <button type="submit" :disabled="isSubmittingImage" class="btn btn-warning radius-12 px-4 fw-bold">Tải Lên</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View Full Image Modal -->
    <div class="modal fade" id="viewImageModal" tabindex="-1" aria-hidden="true" ref="viewModalEl">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 radius-24 shadow-lg overflow-hidden bg-black-90">
          <div class="modal-header py-2 px-4 border-0 d-flex align-items-center justify-content-between text-white bg-transparent">
            <span class="font-sm text-white">{{ selectedImage?.mo_ta || 'Hình ảnh kỷ niệm' }}</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" @click="closeViewModal"></button>
          </div>
          <div class="modal-body p-0 text-center bg-dark">
            <img :src="selectedImage?.duong_dan" class="img-fluid max-h-80vh" style="object-fit: contain;">
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
            isLoggedIn: !!localStorage.getItem('access_token'),
            myMemberId: null,

            // Memorial & Tributes
            tributes: [],
            tributeStats: { Nhang: 0, Hoa: 0, Nen: 0, TraiCay: 0 },
            activeOffering: 'Nhang',
            tributeNotes: '',
            isSubmittingTribute: false,

            // Gallery
            galleryImages: [],
            isLoadingGallery: false,
            canManageGallery: false,
            newImageUrl: '',
            newImageDesc: '',
            isSubmittingImage: false,
            selectedImage: null,
            uploadBsModal: null,
            viewBsModal: null,
            isAdmin: false,
            isDoiTac: false
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
                this.tributes = [];
                this.galleryImages = [];
                if (this.isLoggedIn) {
                    this.$nextTick(() => this.autoDetectMyRelationship());
                }
            }
        }
    },
    mounted() {
        // Load User roles
        const userData = JSON.parse(localStorage.getItem('user'));
        if (userData) {
            const user = userData.user || userData;
            const roleName = user.vai_tro?.toLowerCase() || '';
            const chucVuName = user.chuc_vu?.ten_chuc_vu?.toLowerCase() || '';
            const isSubAdmin = chucVuName.includes('quản trị') || roleName.includes('admin');
            this.isAdmin = roleName === 'admin' || isSubAdmin;
            this.isDoiTac = user.is_doi_tac == 1 || user.vai_tro === 'Đối tác';
            this.canManageGallery = this.isAdmin || this.isDoiTac;
        }

        // Initialize Modals
        if (window.bootstrap) {
            const uploadEl = this.$refs.uploadModalEl;
            const viewEl = this.$refs.viewModalEl;
            if (uploadEl) this.uploadBsModal = new window.bootstrap.Modal(uploadEl);
            if (viewEl) this.viewBsModal = new window.bootstrap.Modal(viewEl);
        }

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

                        this.myMemberId = myMember.id;

                        if (myMember.id === targetId) {
                            // Dang xem chinh minh
                            this.dynamicRelError = 'Đây chính là hồ sơ của bạn trong cây gia phả.';
                            return;
                        }

                        // 3. Goi API xac dinh quan he BFS (Dao chieu de xem nguoi nay la gi cua BAN)
                        this.isLoadingRelationship = true;
                        return axios.post('http://127.0.0.1:8000/api/thanh-vien/xac-dinh-quan-he', {
                            id_a: targetId,
                            id_b: myMember.id
                        })
                        .then(relRes => {
                            if (relRes.data.status) {
                                this.dynamicRelationship = {
                                    term: relRes.data.term,
                                    description: relRes.data.description,
                                    path: relRes.data.path || [],
                                    members: relRes.data.members || []
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
        },
        // Tributes & Memorial
        loadTributes() {
            const memberId = this.$route.params.id;
            axios.get(`http://127.0.0.1:8000/api/tuong-niem/thanh-vien/${memberId}`, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.tributes = res.data.tributes || [];
                        this.tributeStats = res.data.stats || { Nhang: 0, Hoa: 0, Nen: 0, TraiCay: 0 };
                    }
                })
                .catch(err => {
                    console.error('Lỗi tải dữ liệu tưởng niệm:', err);
                });
        },
        submitTribute() {
            if (!this.activeOffering) {
                toastr.warning('Vui lòng chọn loại lễ vật!');
                return;
            }
            this.isSubmittingTribute = true;
            const payload = {
                thanh_vien_id: parseInt(this.$route.params.id),
                loai_le_vat: this.activeOffering,
                loi_nhan: this.tributeNotes.trim()
            };

            axios.post('http://127.0.0.1:8000/api/tuong-niem/create', payload, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message || 'Đã dâng lễ vật tôn kính tổ tiên!');
                        this.tributeNotes = '';
                        this.loadTributes();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    toastr.error(err.response?.data?.message || 'Lỗi khi dâng hương hoa.');
                })
                .finally(() => {
                    this.isSubmittingTribute = false;
                });
        },
        getOfferingEmoji(type) {
            const emojis = { Nhang: '🕯️', Hoa: '💐', Nen: '🏮', TraiCay: '🍎' };
            return emojis[type] || '🕯️';
        },
        getOfferingLabel(type) {
            const labels = { Nhang: 'Nhang hương', Hoa: 'Hoa cúng', Nen: 'Nến sáng', TraiCay: 'Quả ngọt' };
            return labels[type] || type;
        },

        // Gallery Methods
        loadGallery() {
            this.isLoadingGallery = true;
            axios.get('http://127.0.0.1:8000/api/hinh-anh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        const targetId = parseInt(this.$route.params.id);
                        this.galleryImages = (res.data.data || []).filter(img => img.thanh_vien_id === targetId);
                    }
                })
                .catch(err => {
                    console.error('Lỗi tải album ảnh kỷ niệm:', err);
                })
                .finally(() => {
                    this.isLoadingGallery = false;
                });
        },
        openUploadModal() {
            this.newImageUrl = '';
            this.newImageDesc = '';
            if (this.uploadBsModal) this.uploadBsModal.show();
        },
        closeUploadModal() {
            if (this.uploadBsModal) this.uploadBsModal.hide();
        },
        submitImage() {
            if (!this.newImageUrl.trim()) {
                toastr.warning('Vui lòng nhập URL hình ảnh!');
                return;
            }
            this.isSubmittingImage = true;
            const payload = {
                thanh_vien_id: parseInt(this.$route.params.id),
                duong_dan: this.newImageUrl.trim(),
                mo_ta: this.newImageDesc.trim()
            };

            axios.post('http://127.0.0.1:8000/api/hinh-anh/create', payload, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success('Thêm hình ảnh kỷ niệm thành công!');
                        this.closeUploadModal();
                        this.loadGallery();
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    toastr.error(err.response?.data?.message || 'Có lỗi xảy ra khi tải ảnh lên.');
                })
                .finally(() => {
                    this.isSubmittingImage = false;
                });
        },
        deleteImage(imageId) {
            if (confirm('Bạn có chắc chắn muốn xóa hình ảnh kỷ niệm này?')) {
                axios.post('http://127.0.0.1:8000/api/hinh-anh/delete', { id: imageId }, this.getHeaders())
                    .then(res => {
                        if (res.data.status) {
                            toastr.success('Đã xóa hình ảnh kỷ niệm.');
                            this.loadGallery();
                        } else {
                            toastr.error(res.data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        toastr.error('Lỗi khi xóa hình ảnh.');
                    });
            }
        },
        viewFullImage(image) {
            this.selectedImage = image;
            if (this.viewBsModal) this.viewBsModal.show();
        },
        closeViewModal() {
            if (this.viewBsModal) this.viewBsModal.hide();
            this.selectedImage = null;
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;0,900;1,700&display=swap');

.public-profile-container {
    background: radial-gradient(circle at 10% 20%, rgba(253, 244, 215, 0.4) 0%, rgba(248, 250, 252, 1) 90%);
    min-height: 100vh;
    padding-top: 110px; /* CRITICAL: spacing to ensure no header overlap */
    padding-bottom: 60px;
    font-family: 'Inter', sans-serif;
}

.radius-15 { border-radius: 15px !important; }
.z-index-2 { z-index: 2; }
.shadow-xs { box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important; }
.border-light-grey { border: 1px solid #eaedf1; }

/* Premium Hero Profile Card */
.profile-hero-card {
    border-radius: 24px;
    padding: 40px;
    margin-bottom: 35px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
}
.hero-alive {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-bottom: 5px solid #d4af37;
}
.hero-memorial {
    background: linear-gradient(135deg, #3b0c11 0%, #5c1218 100%);
    border-bottom: 5px solid #c5a059;
}
.hero-bg-accent {
    position: absolute;
    top: -50px;
    right: -50px;
    width: 350px;
    height: 350px;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.12) 0%, transparent 70%);
    pointer-events: none;
    z-index: 1;
}

/* Avatar Holder */
.avatar-holder-premium {
    display: inline-block;
}
.profile-avatar-img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid #ffffff;
    transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.profile-hero-card:hover .profile-avatar-img {
    transform: scale(1.04);
}
.status-indicator {
    position: absolute;
    bottom: 5px;
    right: 5px;
    padding: 4px 12px;
    border-radius: 30px;
    color: white;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.5px;
    border: 2px solid white;
}
.status-alive { background-color: #10b981; }
.status-deceased { background-color: #ef4444; }

/* Badges and Text */
.badge-generation {
    background: rgba(212, 175, 55, 0.2) !important;
    border: 1px solid rgba(212, 175, 55, 0.4);
    color: #ffd891 !important;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 20px;
}
.badge-gender {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 700;
}
.gender-male {
    background: rgba(59, 130, 246, 0.2) !important;
    border: 1px solid rgba(59, 130, 246, 0.4);
    color: #93c5fd !important;
}
.gender-female {
    background: rgba(236, 72, 153, 0.2) !important;
    border: 1px solid rgba(236, 72, 153, 0.4);
    color: #fbcfe8 !important;
}
.badge-branch {
    background: rgba(16, 185, 129, 0.2) !important;
    border: 1px solid rgba(16, 185, 129, 0.4);
    color: #a7f3d0 !important;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 700;
}

.profile-name {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    font-weight: 900;
    letter-spacing: -0.5px;
    margin-top: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}
.profile-nickname {
    font-style: italic;
    color: rgba(255, 255, 255, 0.85);
    margin-bottom: 5px;
    font-size: 1.05rem;
}
.profile-email-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.1);
    padding: 4px 14px;
    border-radius: 30px;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.9);
}

/* Sidebar Styling */
.sidebar-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.04);
    margin-bottom: 24px;
}

/* Relationship Card */
.relationship-card-premium {
    background: linear-gradient(135deg, #ffffff 0%, #fffcf3 100%);
    border: 1px dashed rgba(212, 175, 55, 0.4);
}
.rel-icon-box-premium {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(212, 175, 55, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
}
.relationship-term {
    font-family: 'Playfair Display', serif;
    font-size: 2.2rem;
    font-weight: 900;
    color: #b58d1e;
    line-height: 1.1;
    margin-top: 4px;
    text-shadow: 0 4px 12px rgba(212, 175, 55, 0.15);
}
.bg-gold-badge {
    background-color: #d4af37;
}
.bg-primary-badge {
    background-color: #0d6efd;
}
.font-9 { font-size: 9px !important; font-weight: 800; }
.font-11 { font-size: 11px !important; }
.font-10 { font-size: 10px !important; }
.font-12 { font-size: 12px !important; }
.path-node-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
    z-index: 2;
}
.path-node-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.path-connector-line {
    position: absolute;
    left: 15px;
    top: 32px;
    width: 2px;
    height: 18px;
    background-color: rgba(212, 175, 55, 0.25);
    z-index: 1;
}

/* Guest Prompt */
.lock-icon-holder {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background-color: rgba(212, 175, 55, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Memorial Sidebar Panel */
.memorial-card-premium {
    background: linear-gradient(135deg, #ffffff 0%, #fff6f6 100%);
    border: 1px dashed rgba(239, 68, 68, 0.3);
}
.text-rose { color: #9f1c24; }
.text-muted-rose { color: #82474b; }
.memorial-info-box-premium {
    background: rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    border: 1px solid rgba(239, 68, 68, 0.1);
}
@keyframes flicker {
    0%, 100% { transform: scale(1); opacity: 0.9; }
    50% { transform: scale(1.1) rotate(2deg); opacity: 1; }
}
.animate-candle {
    display: inline-block;
    animation: flicker 1.6s infinite ease-in-out;
}

/* Right Content Area */
.content-card-premium {
    background: #ffffff;
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 15px 35px rgba(15, 23, 42, 0.03);
    border: 1px solid rgba(0, 0, 0, 0.03);
    min-height: 500px;
}

/* Premium Pills */
.custom-pills .nav-link {
    border-radius: 30px;
    color: #64748b;
    font-weight: 700;
    padding: 10px 24px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}
.custom-pills .nav-link:hover {
    color: #0f172a;
    background-color: #f1f5f9;
}
.custom-pills .nav-link.active {
    background-color: #0f172a;
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.15);
}

/* Biography Pane */
.section-title {
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.2px;
    border-bottom: 2px solid #f1f5f9;
    padding-bottom: 12px;
}
.quote-story-premium {
    position: relative;
    box-shadow: inset 0 0 15px rgba(0,0,0,0.005);
}
.story-content-premium {
    font-size: 0.95rem;
    line-height: 1.75;
    color: #475569;
}
.timeline-indicator-premium {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    flex-shrink: 0;
    border: 3px solid #ffffff;
    box-shadow: 0 0 0 2px currentColor;
}
.info-row-premium {
    border-bottom: 1px solid #f8fafc;
}

/* Family Relations Tree */
.relation-group-title {
    letter-spacing: 0.5px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 6px;
}
.relation-card-premium {
    background: #f8fafc;
    border: 1px solid #eaedf1;
    position: relative;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.relation-card-premium:hover {
    border-color: #d4af37;
    background: #ffffff;
    box-shadow: 0 10px 24px rgba(212, 175, 55, 0.08);
    transform: translateY(-2px);
}
.relation-avatar {
    width: 50px;
    height: 50px;
    object-fit: cover;
}
.relation-name {
    font-weight: 700;
    color: #0f172a;
    font-size: 14px;
    text-decoration: none;
    transition: color 0.2s ease;
}
.relation-name:hover {
    color: #d4af37;
}
.relation-badge {
    font-size: 8px !important;
    font-weight: 800;
    letter-spacing: 0.5px;
}
.bg-pink { background-color: #ec4899 !important; }

/* CTAs Outlines */
.btn-premium-outline-primary {
    background-color: transparent;
    border: 1px solid #0f172a;
    color: #0f172a;
    font-weight: 700;
    transition: all 0.3s ease;
    border-radius: 30px;
}
.btn-premium-outline-primary:hover {
    background-color: #0f172a;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
}
.btn-premium-outline-secondary {
    background-color: transparent;
    border: 1px solid #64748b;
    color: #64748b;
    font-weight: 700;
    transition: all 0.3s ease;
    border-radius: 30px;
}
.btn-premium-outline-secondary:hover {
    background-color: #64748b;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(100, 116, 139, 0.1);
}

@media (max-width: 991px) {
    .public-profile-container {
        padding-top: 120px;
    }
    .profile-hero-card {
        padding: 30px 20px;
    }
    .profile-name {
        font-size: 2rem;
    }
    .content-card-premium {
        padding: 25px;
    }
}

/* Memorial styling */
.text-rose { color: #be123c !important; }
.badge-rose-subtle { background-color: rgba(225, 29, 72, 0.1) !important; color: #e11d48 !important; }
.btn-rose { background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); color: #ffffff; border: none; }
.btn-rose:hover { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); color: #ffffff; }
.btn-outline-rose { border-color: #e11d48; color: #e11d48; background-color: transparent; transition: all 0.2s; }
.btn-outline-rose:hover, .btn-outline-rose.active { background-color: #e11d48; color: white; border-color: #e11d48; }

.offering-stat-card {
  background: #fff5f5;
  border-color: #ffe3e3 !important;
  transition: all 0.3s;
}
.offering-stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(225, 29, 72, 0.05);
}

/* Gallery styling */
.gallery-thumbnail-img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  cursor: pointer;
  transition: transform 0.4s ease;
}
.gallery-thumbnail-img:hover {
  transform: scale(1.05);
}
.gallery-img-hover-overlay {
  background: rgba(0, 0, 0, 0.6);
  pointer-events: none;
  font-weight: 500;
}
.btn-delete-img {
  opacity: 0;
  transition: opacity 0.2s ease;
  z-index: 5;
}
.gallery-img-wrapper:hover .btn-delete-img {
  opacity: 1;
}

.bg-black-90 {
  background-color: rgba(0, 0, 0, 0.9) !important;
}
.max-h-80vh {
  max-height: 80vh;
}
</style>
