<template>
    <div class="relationship-discovery">
        <!-- Elegant Background Elements -->
        <div class="bg-circles">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
        </div>

        <div class="container py-5 position-relative">
            <!-- Header: Heritage Style -->
            <div class="text-center mb-4 animate__animated animate__fadeIn">
                <div class="heritage-badge mb-3">HỆ THỐNG GIA PHẢ ĐỐI TÁC</div>
                <h1 class="heritage-title">Tra Cứu Bậc Vai Vế</h1>
                <p class="heritage-subtitle">Khám phá mối liên hệ huyết thống trong gia tộc</p>
                <div class="heritage-divider">
                    <span class="diamond"></span>
                </div>
            </div>

            <!-- Global Branch Selector -->
            <div class="row mb-5 justify-content-center animate__animated animate__fadeInDown">
                <div class="col-lg-6">
                    <div class="heritage-card p-3 text-center border-2 border-warning">
                        <label class="fw-bold text-dark mb-2 text-uppercase" style="letter-spacing: 1px;">Chọn Dòng Họ Để Tra Cứu:</label>
                        <select class="form-select form-select-lg radius-15 border-2 shadow-none" v-model="selectedChiNhanhId" @change="resetSelection">
                            <option :value="null">-- Vui lòng chọn dòng họ --</option>
                            <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                        </select>
                        <div v-if="!selectedChiNhanhId" class="mt-2 text-danger small italic">
                            * Bạn cần chọn dòng họ trước khi tra cứu thành viên
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 justify-content-center align-items-stretch" v-if="selectedChiNhanhId">
                <!-- Person A: The Seeker -->
                <div class="col-lg-5 animate__animated animate__fadeInLeft">
                    <div class="heritage-card h-100 p-4">
                        <div class="card-label">THÀNH VIÊN THỨ NHẤT</div>
                        
                        <div class="selection-box mt-4">
                            <div class="custom-select-wrapper shadow-sm">
                                <i class="bx bx-user-pin select-icon"></i>
                                <select class="custom-select" v-model="idA">
                                    <option :value="null">-- Chọn thành viên --</option>
                                    <option v-for="m in filteredMembers" :key="m.id" :value="m.id">
                                        {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div v-if="personA" class="member-profile mt-4 text-center p-4 rounded-4 animate__animated animate__zoomIn">
                            <div class="profile-avatar-container mb-3">
                                <img :src="personA.avatar || 'https://ui-avatars.com/api/?name=' + personA.ho_ten + '&background=0f172a&color=fff'" class="profile-avatar">
                                <div class="profile-gender" :class="personA.gioi_tinh === 'Nam' ? 'male' : 'female'">
                                    <i :class="personA.gioi_tinh === 'Nam' ? 'bx bx-male' : 'bx bx-female'"></i>
                                </div>
                            </div>
                            <h3 class="profile-name">{{ personA.ho_ten }}</h3>
                            <div class="profile-stats d-flex justify-content-center gap-2 mt-2">
                                <span class="stat-tag">Đời {{ personA.doi_thu }}</span>
                                <span class="stat-tag" v-if="personA.trang_thai === 'Đã mất'">Đã mất</span>
                            </div>
                        </div>
                        <div v-else class="empty-state text-center py-5 opacity-50">
                            <i class="bx bxs-user-circle display-1 mb-3"></i>
                            <p>Chọn thành viên đầu tiên</p>
                        </div>
                    </div>
                </div>

                <!-- Central Action -->
                <div class="col-lg-2 d-flex flex-column align-items-center justify-content-center">
                    <div class="action-hub">
                        <div class="hub-line"></div>
                        <button class="hub-btn shadow-lg" @click="calculateRelationship" :disabled="!idA || !idB || idA === idB">
                            <i class="bx bxs-bolt"></i>
                            <span>TRA CỨU</span>
                        </button>
                        <div class="hub-line"></div>
                    </div>
                </div>

                <!-- Person B: The Reference -->
                <div class="col-lg-5 animate__animated animate__fadeInRight">
                    <div class="heritage-card h-100 p-4">
                        <div class="card-label">THÀNH VIÊN THỨ HAI</div>

                        <div class="selection-box mt-4">
                            <div class="custom-select-wrapper shadow-sm">
                                <i class="bx bx-user-pin select-icon icon-secondary"></i>
                                <select class="custom-select" v-model="idB">
                                    <option :value="null">-- Chọn thành viên --</option>
                                    <option v-for="m in filteredMembers" :key="m.id" :value="m.id">
                                        {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div v-if="personB" class="member-profile mt-4 text-center p-4 rounded-4 animate__animated animate__zoomIn">
                            <div class="profile-avatar-container mb-3">
                                <img :src="personB.avatar || 'https://ui-avatars.com/api/?name=' + personB.ho_ten + '&background=d4af37&color=fff'" class="profile-avatar border-gold">
                                <div class="profile-gender" :class="personB.gioi_tinh === 'Nam' ? 'male' : 'female'">
                                    <i :class="personB.gioi_tinh === 'Nam' ? 'bx bx-male' : 'bx bx-female'"></i>
                                </div>
                            </div>
                            <h3 class="profile-name">{{ personB.ho_ten }}</h3>
                            <div class="profile-stats d-flex justify-content-center gap-2 mt-2">
                                <span class="stat-tag">Đời {{ personB.doi_thu }}</span>
                                <span class="stat-tag" v-if="personB.trang_thai === 'Đã mất'">Đã mất</span>
                            </div>
                        </div>
                        <div v-else class="empty-state text-center py-5 opacity-50">
                            <i class="bx bxs-user-circle display-1 mb-3"></i>
                            <p>Chọn thành viên thứ hai</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result View -->
            <div v-if="result" class="row mt-5 animate__animated animate__slideInUp">
                <div class="col-lg-10 mx-auto">
                    <div class="result-master-card shadow-lg overflow-hidden">
                        <div class="result-header p-4 text-center">
                            <span class="result-badge">KẾT QUẢ PHÂN TÍCH</span>
                        </div>
                        <div class="result-body p-0">
                            <div class="row g-0">
                                <div class="col-md-5 result-term-box p-5 d-flex flex-column align-items-center justify-content-center">
                                    <div class="term-label mb-2">VAI VẾ</div>
                                    <div class="term-value">{{ result.term }}</div>
                                    <div class="term-glow"></div>
                                </div>
                                <div class="col-md-7 result-desc-box p-5 bg-white">
                                    <div class="desc-content">
                                        <div class="quote-icon mb-3"><i class="bx bxs-quote-alt-left"></i></div>
                                        <p class="fs-4 text-dark mb-4 lh-base">
                                            Trong cùng gia tộc, <strong class="text-primary">{{ personA.ho_ten }}</strong> sẽ gọi <strong class="text-gold">{{ personB.ho_ten }}</strong> là:
                                        </p>
                                        <div class="relation-badge-large mb-4">
                                            {{ result.term }}
                                        </div>
                                        <div class="desc-footer p-3 rounded-3 bg-light border-start border-4 border-warning">
                                            <i class="bx bx-info-circle me-2 text-warning"></i> {{ result.description }}
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
            return this.allMembers.filter(m => m.chi_nhanh_id === this.selectedChiNhanhId);
        },
        personA() { return this.allMembers.find(m => m.id === this.idA); },
        personB() { return this.allMembers.find(m => m.id === this.idB); }
    },
    mounted() {
        this.loadData();
        this.loadChiNhanh();
    },
    methods: {
        loadData() {
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.allMembers = res.data.data;
                    }
                });
        },
        loadChiNhanh() {
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data')
                .then(res => {
                    if (res.data.status) {
                        this.listChiNhanh = res.data.data;
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
                    description = "Là anh chị em ruột, cùng chung huyết thống trực hệ.";
                } else {
                    term = "Anh/Chị/Em họ";
                    description = "Cùng một thế hệ nhưng khác nhánh phụ hoặc đời cha mẹ khác nhau.";
                }
            } else if (diff === 1) {
                if (a.cha_id === b.id) {
                    term = b.gioi_tinh === 'Nam' ? "Bố / Cha" : "Mẹ";
                    description = "Quan hệ cha con/mẹ con trực hệ, một bậc sinh thành.";
                } else {
                    term = b.gioi_tinh === 'Nam' ? "Chú / Bác" : "Cô / Dì";
                    description = b.ho_ten + " là hàng bề trên (cùng thế hệ với cha/mẹ).";
                }
            } else if (diff === -1) {
                if (b.cha_id === a.id) {
                    term = "Con cái";
                    description = b.ho_ten + " là hậu duệ trực hệ (đời con).";
                } else {
                    term = "Cháu";
                    description = b.ho_ten + " là hàng cháu, vai dưới một bậc.";
                }
            } else if (diff === 2) {
                term = b.gioi_tinh === 'Nam' ? "Ông" : "Bà";
                description = b.ho_ten + " là bậc tiền bối đời thứ hai (ông bà).";
            } else if (diff === -2) {
                term = "Cháu Nội/Ngoại";
                description = b.ho_ten + " là hậu duệ đời thứ hai (cháu).";
            } else if (diff >= 3) {
                term = "Cụ / Cố";
                description = b.ho_ten + " là bậc đại tiền bối khởi nguồn lâu đời.";
            } else if (diff <= -3) {
                term = "Chắt / Chít";
                description = b.ho_ten + " là hậu duệ các đời tiếp theo.";
            }

            this.result = { term, description };
        }
    }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Quicksand:wght@400;600;700&display=swap');

.relationship-discovery {
    font-family: 'Quicksand', sans-serif;
    background-color: #fcfaf5;
    min-height: calc(100vh - 100px);
    overflow: hidden;
    position: relative;
    border-radius: 15px;
}

.bg-circles .circle {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: 0;
}
.circle-1 { width: 300px; height: 300px; background: rgba(212, 175, 55, 0.1); top: -50px; left: -50px; }
.circle-2 { width: 400px; height: 400px; background: rgba(15, 23, 42, 0.05); bottom: -100px; right: -100px; }

.heritage-badge {
    display: inline-block;
    padding: 5px 20px;
    background: #0f172a;
    color: #d4af37;
    font-weight: 800;
    font-size: 11px;
    letter-spacing: 2px;
    border-radius: 50px;
}
.heritage-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.8rem;
    color: #0f172a;
    font-weight: 900;
    margin-top: 10px;
}
.heritage-subtitle { font-size: 1.1rem; color: #64748b; font-style: italic; }
.heritage-divider { display: flex; align-items: center; justify-content: center; margin: 15px 0; }
.heritage-divider::before, .heritage-divider::after { content: ""; height: 1px; width: 80px; background: #d4af37; }
.diamond { width: 8px; height: 8px; background: #d4af37; transform: rotate(45deg); margin: 0 12px; }

.heritage-card {
    background: white;
    border-radius: 25px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    position: relative;
}
.card-label {
    position: absolute;
    top: -10px;
    left: 20px;
    background: #d4af37;
    color: white;
    padding: 2px 12px;
    font-size: 10px;
    font-weight: 800;
    border-radius: 4px;
}

.custom-select-wrapper { position: relative; background: #f8fafc; border-radius: 12px; }
.select-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 20px; color: #0f172a; }
.icon-secondary { color: #d4af37; }
.custom-select { width: 100%; padding: 12px 12px 12px 40px; border: none; background: transparent; font-weight: 700; color: #0f172a; outline: none; }

.profile-avatar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
.profile-avatar-container { position: relative; display: inline-block; }
.profile-gender {
    position: absolute;
    bottom: 2px; right: 2px;
    width: 30px; height: 30px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 16px; border: 2px solid white;
}
.male { background: #008bf8; }
.female { background: #ff4d6d; }
.profile-name { font-family: 'Playfair Display', serif; font-weight: 900; color: #0f172a; }
.stat-tag { background: #f1f5f9; color: #475569; padding: 2px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; }

.action-hub { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.hub-line { width: 2px; flex-grow: 1; background: linear-gradient(to bottom, transparent, #d4af37, transparent); }
.hub-btn {
    width: 90px; height: 90px; border-radius: 50%;
    background: #0f172a; color: white; border: 4px solid #d4af37;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    font-weight: 900; font-size: 10px; transition: 0.3s; margin: 15px 0;
}
.hub-btn:hover:not(:disabled) { transform: scale(1.1); background: #1e293b; }
.hub-btn i { font-size: 26px; color: #d4af37; margin-bottom: 2px; }

.result-master-card { background: white; border-radius: 30px; border: 1px solid #eee; }
.result-header { background: #0f172a; color: #d4af37; }
.result-badge { font-weight: 800; letter-spacing: 3px; font-size: 12px; }
.result-term-box { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; position: relative; min-height: 250px; }
.term-label { font-weight: 700; opacity: 0.5; font-size: 12px; }
.term-value { font-family: 'Playfair Display', serif; font-size: 4rem; font-weight: 900; color: #d4af37; text-align: center; }
.quote-icon { font-size: 30px; color: #d4af37; opacity: 0.3; }
.relation-badge-large {
    display: inline-block; padding: 8px 30px;
    background: rgba(212, 175, 55, 0.1); color: #0f172a;
    font-weight: 900; font-size: 20px; border-radius: 12px; border: 2px solid #d4af37;
}
</style>
