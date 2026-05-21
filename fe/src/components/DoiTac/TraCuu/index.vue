<template>
    <div class="relationship-discovery">
        <div class="sidebar-bg-layer" aria-hidden="true"></div>

        <div class="container py-2 position-relative">
            <div class="text-center mb-4 animate__animated animate__fadeIn">
                <div class="heritage-badge mb-3">HỆ THỐNG GIA PHẢ ĐỐI TÁC</div>
                <h2 class="heritage-title fw-bold">Tra Cứu Bậc Vai Vế</h2>
                <p class="heritage-subtitle text-secondary">Khám phá và phân tích mối liên hệ huyết thống trong dòng tộc</p>
            </div>

            <div v-if="listChiNhanh.length === 0" class="row justify-content-center animate__animated animate__fadeIn">
                <div class="col-lg-8">
                    <div class="luxury-panel p-5 text-center shadow-sm">
                        <div class="mb-4 mt-3 empty-glow-icon">
                            <i class="bx bx-building-house text-muted opacity-20" style="font-size: 80px !important;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Dòng Họ Chưa Được Khởi Tạo!</h4>
                        <p class="text-secondary small">Tính năng tra cứu vai vế chỉ khả dụng sau khi bạn đã thiết lập Dòng Họ (Chi Nhánh).</p>
                        <router-link to="/doi-tac/dong-ho" class="btn btn-luxury-primary px-5 mt-3 shadow-sm mb-3">
                            Khởi Tạo Ngay
                        </router-link>
                    </div>
                </div>
            </div>

            <div v-else-if="allMembers.length === 0" class="row justify-content-center animate__animated animate__fadeIn">
                <div class="col-lg-8">
                    <div class="luxury-panel p-5 text-center shadow-sm">
                        <div class="mb-4 mt-3 empty-glow-icon">
                            <i class="bx bx-git-branch text-muted opacity-20" style="font-size: 80px !important;"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Chưa Có Thành Viên Để Tra Cứu!</h4>
                        <p class="text-secondary small">Bạn cần khởi tạo thành viên vào Cây Gia Phả trước khi có thể thực hiện tra cứu bậc vai vế.</p>
                        <router-link to="/doi-tac/gia-pha" class="btn btn-luxury-primary px-5 mt-3 shadow-sm mb-3">
                            Bắt Đầu Xây Dựng Cây
                        </router-link>
                    </div>
                </div>
            </div>

            <div v-else class="w-100">
                <div class="row mb-5 justify-content-center animate__animated animate__fadeInDown">
                    <div class="col-lg-6">
                        <div class="luxury-panel p-4 text-center shadow-sm">
                            <label class="modern-field-label mb-2">Chọn Dòng Họ Để Tra Cứu</label>
                            <div class="modern-select-box">
                                <select class="form-select modern-select fw-bold text-center" v-model="selectedChiNhanhId" @change="resetSelection">
                                    <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 justify-content-center align-items-stretch" v-if="selectedChiNhanhId">
                    <div class="col-lg-5 animate__animated animate__fadeInLeft">
                        <div class="luxury-panel h-100 p-4 position-relative">
                            <div class="card-label-premium cl-indigo">THÀNH VIÊN THỨ NHẤT</div>

                            <div class="selection-box mt-4">
                                <div class="modern-select-box shadow-none border border-light-subtle">
                                    <select class="form-select modern-select fw-semibold" v-model="idA">
                                        <option :value="null">-- Chọn thành viên --</option>
                                        <option v-for="m in filteredMembers" :key="m.id" :value="m.id">
                                            {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="personA" class="member-profile-card mt-4 text-center p-4">
                                <div class="profile-avatar-container mb-3">
                                    <img :src="personA.avatar || 'https://ui-avatars.com/api/?name=' + personA.ho_ten + '&background=eef2ff&color=4f46e5'" class="profile-avatar">
                                    <div class="profile-gender-badge" :class="personA.gioi_tinh === 'Nam' ? 'male' : 'female'">
                                        <i :class="personA.gioi_tinh === 'Nam' ? 'bx bx-male' : 'bx bx-female'"></i>
                                    </div>
                                    <div class="avatar-ring-glow-light cl-ring-indigo"></div>
                                </div>
                                <h4 class="profile-name fw-bold text-dark mb-2">{{ personA.ho_ten }}</h4>
                                <div class="d-flex justify-content-center gap-2">
                                    <span class="badge status-alive text-indigo bg-light-indigo px-3">Đời {{ personA.doi_thu }}</span>
                                    <span class="badge status-dead px-3" v-if="personA.trang_thai === 'Đã mất'">Đã mất</span>
                                </div>
                            </div>
                            <div v-else class="empty-member-state text-center py-5 opacity-40">
                                <i class="bx bxs-user-circle display-3 text-muted mb-2"></i>
                                <p class="small fw-semibold text-secondary">Vui lòng chọn thành viên đầu tiên</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 d-flex flex-column align-items-center justify-content-center py-3">
                        <div class="action-hub-wrapper">
                            <div class="hub-line-vertical"></div>
                            <button class="hub-btn-premium shadow" @click="calculateRelationship" :disabled="!idA || !idB || idA === idB">
                                <i class="bx bxs-bolt-circle"></i>
                                <span>PHÂN TÍCH</span>
                            </button>
                            <div class="hub-line-vertical"></div>
                        </div>
                    </div>

                    <div class="col-lg-5 animate__animated animate__fadeInRight">
                        <div class="luxury-panel h-100 p-4 position-relative">
                            <div class="card-label-premium cl-rose">THÀNH VIÊN THỨ HAI</div>

                            <div class="selection-box mt-4">
                                <div class="modern-select-box shadow-none border border-light-subtle">
                                    <select class="form-select modern-select fw-semibold" v-model="idB">
                                        <option :value="null">-- Chọn thành viên --</option>
                                        <option v-for="m in filteredMembers" :key="m.id" :value="m.id">
                                            {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="personB" class="member-profile-card mt-4 text-center p-4">
                                <div class="profile-avatar-container mb-3">
                                    <img :src="personB.avatar || 'https://ui-avatars.com/api/?name=' + personB.ho_ten + '&background=fff5f7&color=db2777'" class="profile-avatar">
                                    <div class="profile-gender-badge" :class="personB.gioi_tinh === 'Nam' ? 'male' : 'female'">
                                        <i :class="personB.gioi_tinh === 'Nam' ? 'bx bx-male' : 'bx bx-female'"></i>
                                    </div>
                                    <div class="avatar-ring-glow-light cl-ring-rose"></div>
                                </div>
                                <h4 class="profile-name fw-bold text-dark mb-2">{{ personB.ho_ten }}</h4>
                                <div class="d-flex justify-content-center gap-2">
                                    <span class="badge status-alive text-rose bg-light-rose px-3">Đời {{ personB.doi_thu }}</span>
                                    <span class="badge status-dead px-3" v-if="personB.trang_thai === 'Đã mất'">Đã mất</span>
                                </div>
                            </div>
                            <div v-else class="empty-member-state text-center py-5 opacity-40">
                                <i class="bx bxs-user-circle display-3 text-muted mb-2"></i>
                                <p class="small fw-semibold text-secondary">Vui lòng chọn thành viên thứ hai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="result" class="row mt-5 animate__animated animate__slideInUp">
                    <div class="col-lg-10 mx-auto">
                        <div class="result-master-card shadow-sm overflow-hidden border border-light-subtle">
                            <div class="result-header py-3 text-center border-bottom">
                                <span class="result-badge fw-bold">KẾT QUẢ PHÂN TÍCH PHẢ HỆ</span>
                            </div>
                            <div class="result-body p-0">
                                <div class="row g-0">
                                    <div class="col-md-5 result-term-box p-5 d-flex flex-column align-items-center justify-content-center text-center">
                                        <div class="term-label mb-2">DANH XƯNG BẬC VAI</div>
                                        <div class="term-value fw-bold">{{ result.term }}</div>
                                        <div class="term-glow-light"></div>
                                    </div>
                                    <div class="col-md-7 result-desc-box p-5 bg-white">
                                        <div class="desc-content">
                                            <div class="quote-icon mb-3"><i class="bx bxs-quote-alt-left text-teal"></i></div>
                                            <p class="fs-5 text-dark mb-4 lh-base">
                                                Xét trong tôn ti trật tự gia tộc, <strong class="text-indigo">{{ personA.ho_ten }}</strong> sẽ gọi kính cẩn <strong class="text-rose">{{ personB.ho_ten }}</strong> là:
                                            </p>
                                            <div class="relation-badge-large-premium mb-4">
                                                {{ result.term }}
                                            </div>
                                            <div class="desc-footer p-3 rounded-3 border-start border-4 border-teal bg-light">
                                                <i class="bx bx-info-circle me-2 text-teal"></i> {{ result.description }}
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
            result: null
        }
    },
    computed: {
        filteredMembers() {
            if (!this.selectedChiNhanhId) return [];
            return this.allMembers.filter(m => m.id_chi_nhanh == this.selectedChiNhanhId);
        },
        personA() { return this.allMembers.find(m => m.id == this.idA); },
        personB() { return this.allMembers.find(m => m.id == this.idB); }
    },
    mounted() {
        this.loadData();
        this.loadChiNhanh();
    },
    methods: {
        getHeaders() {
            return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
        },
        loadData() {
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.allMembers = res.data.data;
                    }
                });
        },
        loadChiNhanh() {
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listChiNhanh = res.data.data;
                        if (this.listChiNhanh.length > 0 && !this.selectedChiNhanhId) {
                            this.selectedChiNhanhId = this.listChiNhanh[0].id;
                        }
                    }
                });
        },
        resetSelection() {
            this.idA = null;
            this.idB = null;
            this.result = null;
        },
        calculateRelationship() {
            if (this.idA === this.idB) {
                toastr.warning('Vui lòng chọn hai thành viên khác nhau!');
                return;
            }

            const a = this.personA;
            const b = this.personB;
            const diff = a.doi_thu - b.doi_thu;

            let term = "Họ hàng";
            let description = "Cùng nằm trong hệ thống huyết thống của dòng tộc.";

            if (diff === 0) {
                if (a.cha_id === b.cha_id && a.cha_id !== null) {
                    term = a.gioi_tinh === 'Nam' ? "Anh/Em" : "Chị/Em";
                    description = "Là anh chị em ruột trực hệ, cùng chung một bậc sinh thành.";
                } else {
                    term = "Anh/Chị/Em họ";
                    description = "Cùng chung một thế hệ niên kỷ nhưng thuộc các nhánh chi hoặc đời cha mẹ khác nhau.";
                }
            } else if (diff === 1) {
                if (a.cha_id === b.id) {
                    term = b.gioi_tinh === 'Nam' ? "Bố / Cha" : "Mẹ";
                    description = "Quan hệ cha con/mẹ con trực hệ phụ mẫu, một bậc sinh thành tôn kính.";
                } else {
                    term = b.gioi_tinh === 'Nam' ? "Chú / Bác" : "Cô / Dì";
                    description = b.ho_ten + " là hàng bề trên kính cẩn (thuộc cùng thế hệ dòng đời với cha/mẹ của bạn).";
                }
            } else if (diff === -1) {
                if (b.cha_id === a.id) {
                    term = "Con cái";
                    description = b.ho_ten + " là hậu duệ trực hệ đời nối tiếp trực tiếp (đời con).";
                } else {
                    term = "Cháu";
                    description = b.ho_ten + " là hàng cháu vai dưới, thuộc thế hệ sau một bậc.";
                }
            } else if (diff === 2) {
                term = b.gioi_tinh === 'Nam' ? "Ông Nội/Ngoại" : "Bà Nội/Bà Ngoại";
                description = b.ho_ten + " là bậc đại tiền bối đời thứ hai sinh dưỡng ra cha/mẹ bạn.";
            } else if (diff === -2) {
                term = "Cháu Nội / Cháu Ngoại";
                description = b.ho_ten + " là hậu duệ đời thứ hai kế thừa, vai dưới hai thế hệ bậc phả.";
            } else if (diff >= 3) {
                term = "Cụ / Cố";
                description = b.ho_ten + " là bậc đại tiền bối khai tổ cao niên, khởi nguồn dòng tộc lâu đời.";
            } else if (diff <= -3) {
                term = "Chắt / Chít";
                description = b.ho_ten + " là chắt tử hậu duệ các đời xa nối tiếp phước đức thế hệ sau.";
            }

            this.result = { term, description };
        }
    }
}
</script>

<style scoped>
/* ─── SYSTEM CORE LIGHT VARIABLES ─── */
.relationship-discovery {
    background-color: transparent !important; /* Trong suốt hoàn toàn để lộ Aura Mesh của Layout cha */
    min-height: calc(100vh - 40px);
    position: relative;
    border-radius: 20px;
}

/* Lưới hạt tinh thảo mờ mịn */
.sidebar-bg-layer {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background-image: linear-gradient(rgba(0, 0, 0, 0.01) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(0, 0, 0, 0.01) 1px, transparent 1px);
    background-size: 30px 30px;
    z-index: 0;
}

.heritage-badge {
    display: inline-block;
    padding: 6px 20px;
    background: #ffffff;
    border: 1px solid rgba(0,0,0,0.06);
    color: #db2777;
    font-weight: 700;
    font-size: 11px;
    letter-spacing: 1.5px;
    border-radius: 50px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.01);
}

.heritage-title {
    font-size: 2.2rem;
    color: #111827;
    margin-top: 5px;
}

/* KHỐI KÉN TRẮNG THỦY TINH */
.luxury-panel {
    background: rgba(255, 255, 255, 0.85) !important;
    border: 1px solid rgba(0, 0, 0, 0.04) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    z-index: 1;
}

/* Thẻ tag nhãn của kén chọn thành viên */
.card-label-premium {
    position: absolute;
    top: -11px;
    left: 24px;
    color: white;
    padding: 3px 16px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    border-radius: 30px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
}
.cl-indigo { background: linear-gradient(135deg, #4f46e5, #6366f1); }
.cl-rose { background: linear-gradient(135deg, #db2777, #ec4899); }

/* ─── CONTROLS INPUT & SELECT ─── */
.modern-select-box {
    background: #ffffff;
    border-radius: 30px;
    padding: 1px;
    border: 1px solid rgba(0, 0, 0, 0.07);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01);
}
.modern-select {
    background-color: transparent !important;
    border: none !important;
    color: #4b5563 !important; 
    padding-left: 16px;
    font-size: 14px;
    box-shadow: none !important;
    height: 42px;
}
.modern-select option { background: #ffffff; color: #111827; }

.btn-luxury-primary {
    background: linear-gradient(135deg, #db2777 0%, #f97316 50%, #f59e0b 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    border-radius: 30px;
    box-shadow: 0 4px 12px rgba(219, 39, 119, 0.15);
    transition: all 0.3s ease;
}

/* ─── MEMBER PROFILE CARD ─── */
.member-profile-card {
    background: rgba(255, 255, 255, 0.6) !important;
    border: 1px solid rgba(0,0,0,0.03);
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.01);
}
.profile-avatar-container {
    position: relative;
    display: inline-block;
}
.profile-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    position: relative;
    z-index: 2;
}
.avatar-ring-glow-light {
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    border: 1.5px solid transparent;
    z-index: 1;
}
.cl-ring-indigo { background: linear-gradient(135deg, #4f46e5, transparent) border-box; }
.cl-ring-rose { background: linear-gradient(135deg, #db2777, transparent) border-box; }

.profile-gender-badge {
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
    border: 2px solid white;
    z-index: 3;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.male { background: #3b82f6; }
.female { background: #ec4899; }

.bg-light-indigo { background-color: #eef2ff !important; }
.bg-light-rose { background-color: #fff5f7 !important; }
.text-indigo { color: #4f46e5 !important; }
.text-rose { color: #db2777 !important; }
.status-alive { font-weight: 600; font-size: 12px; border-radius: 30px; }
.status-dead { background-color: #f3f4f6 !important; color: #9ca3af !important; font-weight: 600; font-size: 12px; border-radius: 30px; }

/* ─── ACTION HUB TRUNG TÂM PHÂN TÍCH ─── */
.action-hub-wrapper {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.hub-line-vertical {
    width: 1.5px;
    flex-grow: 1;
    background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.04), transparent);
}
.hub-btn-premium {
    width: 86px;
    height: 86px;
    border-radius: 50%;
    background: linear-gradient(135deg, #db2777 0%, #f97316 50%, #f59e0b 100%);
    color: white;
    border: 4px solid #ffffff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 10px;
    letter-spacing: 0.5px;
    box-shadow: 0 8px 20px rgba(219, 39, 119, 0.2);
    transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    margin: 12px 0;
    padding: 0;
}
.hub-btn-premium:hover:not(:disabled) {
    transform: scale(1.06) rotate(4deg);
    box-shadow: 0 10px 25px rgba(219, 39, 119, 0.35);
}
.hub-btn-premium:disabled {
    background: #eaebed;
    box-shadow: none;
    color: #a3a3a3;
    cursor: not-allowed;
}
.hub-btn-premium i { font-size: 24px; margin-bottom: 2px; }

/* ─── RESULT MASTER CARD ─── */
.result-master-card {
    background: rgba(255, 255, 255, 0.9) !important;
    border-radius: 24px;
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.02);
}
.result-header {
    background: rgba(0,0,0,0.01);
    border-color: rgba(0, 0, 0, 0.04) !important;
}
.result-badge { letter-spacing: 2px; font-size: 12px; color: #4b5563; }
.result-term-box {
    background: linear-gradient(135deg, #090b10 0%, #161822 100%);
    color: white;
    min-height: 240px;
    position: relative;
}
.term-label { font-weight: 700; opacity: 0.4; font-size: 11.5px; letter-spacing: 1px; }
.term-value {
    font-size: 2.8rem;
    background: linear-gradient(135deg, #ffffff 0%, #fff1cc 50%, #d4af37 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.quote-icon { font-size: 28px; opacity: 0.15; }

.relation-badge-large-premium {
    display: inline-block;
    padding: 6px 24px;
    background: #fff5f7;
    color: #db2777;
    font-weight: 700;
    font-size: 18px;
    border-radius: 30px;
    border: 1px solid rgba(219, 39, 119, 0.12);
}
.text-teal { color: #0d9488 !important; }
.border-teal { border-color: #0d9488 !important; }
.empty-glow-icon i { animation: icon-float 3.5s ease-in-out infinite; }
@keyframes icon-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
</style>
