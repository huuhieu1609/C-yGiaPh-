<template>
    <div class="client-tra-cuu">
        <!-- Hero Section with Heritage Theme -->
        <div class="hero-heritage py-5">
            <div class="container text-center pt-5 pb-5 mb-4">
                <div class="heritage-label mb-3 animate__animated animate__fadeInDown">HỆ THỐNG TRUY XUẤT NGUỒN GỐC</div>
                <h1 class="display-2 fw-bold text-white mb-3 animate__animated animate__fadeIn">Máy Tính Xưng Hô</h1>
                <p class="fs-5 text-white-50 max-w-700 mx-auto animate__animated animate__fadeInUp mb-5">
                    Dựa trên thuật toán phả hệ chuẩn mực để xác định vai vế và cách xưng hô truyền thống trong dòng họ Heritage Archivist.
                </p>
            </div>
        </div>

        <div class="container pb-5 mt-n5-custom">
            <div class="main-card shadow-2xl rounded-5 bg-white border-0 overflow-hidden">
                <div class="p-4 p-lg-5">
                    <div class="row g-4 g-lg-5 align-items-center">
                        <!-- Person 1 Selection -->
                        <div class="col-lg-5">
                            <div class="selection-card p-4 rounded-4 text-center" :class="idA ? 'selected-a' : 'empty'">
                                <h5 class="text-uppercase tracking-wider fw-bold mb-4">Thành viên thứ nhất</h5>
                                <div class="custom-select-box mb-4 shadow-sm">
                                    <select class="form-select border-0 bg-transparent py-3 fw-bold" v-model="idA">
                                        <option :value="null">-- Tìm kiếm theo tên --</option>
                                        <option v-for="m in allMembers" :key="m.id" :value="m.id">
                                            {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                        </option>
                                    </select>
                                </div>
                                <div v-if="personA" class="member-display animate__animated animate__fadeIn">
                                    <div class="avatar-ring mx-auto mb-3">
                                        <img :src="personA.avatar || 'https://ui-avatars.com/api/?name=' + personA.ho_ten + '&background=1a1a1a&color=fff'" class="rounded-circle shadow-sm">
                                        <div class="gender-dot" :class="personA.gioi_tinh === 'Nam' ? 'bg-primary' : 'bg-danger'">
                                            <i :class="personA.gioi_tinh === 'Nam' ? 'bx bx-male' : 'bx bx-female'"></i>
                                        </div>
                                    </div>
                                    <h3 class="fw-bold mb-1">{{ personA.ho_ten }}</h3>
                                    <div class="mb-2">
                                        <span class="badge bg-dark-soft text-dark px-3 py-2 rounded-pill">Đời tộc họ: {{ personA.doi_thu }}</span>
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <i class="bx bx-calendar me-1"></i> Ngày sinh: {{ formatDate(personA.ngay_sinh) }}
                                    </div>
                                    <div :class="personA.trang_thai === 'Còn sống' ? 'text-success' : 'text-danger'" class="fw-bold small">
                                        <i class="bx bx-info-circle me-1"></i> {{ personA.trang_thai }}
                                    </div>
                                </div>
                                <div v-else class="selection-placeholder py-5 opacity-25">
                                    <i class="bx bxs-user-circle display-1"></i>
                                    <p class="mt-2 fw-bold">CHƯA CHỌN</p>
                                </div>
                            </div>
                        </div>

                        <!-- VS / Action Hub -->
                        <div class="col-lg-2 text-center py-4 py-lg-0 d-flex flex-column align-items-center justify-content-center">
                            <div class="action-hub mx-auto">
                                <div class="hub-icon-container shadow-lg mx-auto" @click="calculateRelationship" :class="{'clickable': idA && idB && idA !== idB}">
                                    <i class="bx bxs-bolt-circle fs-1" :class="idA && idB ? 'text-warning' : 'text-muted'"></i>
                                </div>
                                <button class="btn btn-heritage mt-3 w-100 py-3" @click="calculateRelationship" :disabled="!idA || !idB || idA === idB">
                                    XÁC NHẬN
                                </button>
                            </div>
                        </div>

                        <!-- Person 2 Selection -->
                        <div class="col-lg-5">
                            <div class="selection-card p-4 rounded-4 text-center" :class="idB ? 'selected-b' : 'empty'">
                                <h5 class="text-uppercase tracking-wider fw-bold mb-4">Thành viên thứ hai</h5>
                                <div class="custom-select-box mb-4 shadow-sm">
                                    <select class="form-select border-0 bg-transparent py-3 fw-bold" v-model="idB">
                                        <option :value="null">-- Tìm kiếm theo tên --</option>
                                        <option v-for="m in allMembers" :key="m.id" :value="m.id">
                                            {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                                        </option>
                                    </select>
                                </div>
                                <div v-if="personB" class="member-display animate__animated animate__fadeIn">
                                    <div class="avatar-ring mx-auto mb-3 border-gold">
                                        <img :src="personB.avatar || 'https://ui-avatars.com/api/?name=' + personB.ho_ten + '&background=d4af37&color=fff'" class="rounded-circle shadow-sm">
                                        <div class="gender-dot" :class="personB.gioi_tinh === 'Nam' ? 'bg-primary' : 'bg-danger'">
                                            <i :class="personB.gioi_tinh === 'Nam' ? 'bx bx-male' : 'bx bx-female'"></i>
                                        </div>
                                    </div>
                                    <h3 class="fw-bold mb-1">{{ personB.ho_ten }}</h3>
                                    <div class="mb-2">
                                        <span class="badge bg-gold-soft text-warning px-3 py-2 rounded-pill">Đời tộc họ: {{ personB.doi_thu }}</span>
                                    </div>
                                    <div class="small text-muted mb-1">
                                        <i class="bx bx-calendar me-1"></i> Ngày sinh: {{ formatDate(personB.ngay_sinh) }}
                                    </div>
                                    <div :class="personB.trang_thai === 'Còn sống' ? 'text-success' : 'text-danger'" class="fw-bold small">
                                        <i class="bx bx-info-circle me-1"></i> {{ personB.trang_thai }}
                                    </div>
                                </div>
                                <div v-else class="selection-placeholder py-5 opacity-25">
                                    <i class="bx bxs-user-circle display-1 text-warning"></i>
                                    <p class="mt-2 fw-bold">CHƯA CHỌN</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Result Panel -->
                    <div v-if="result" class="result-panel mt-5 animate__animated animate__zoomIn">
                        <div class="premium-card overflow-hidden shadow-2xl rounded-4">
                            <div class="row g-0">
                                <div class="col-md-5 p-5 text-white bg-heritage-dark d-flex flex-column justify-content-center text-center text-md-start">
                                    <span class="text-warning text-uppercase fw-bold tracking-widest small mb-2">Cách xưng hô</span>
                                    <h1 class="display-3 fw-bold text-gold mb-0">{{ result.term }}</h1>
                                </div>
                                <div class="col-md-7 p-5 bg-white">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="icon-box bg-gold-soft text-warning me-3">
                                            <i class="bx bxs-detail fs-4"></i>
                                        </div>
                                        <h3 class="fw-bold mb-0 text-dark">Chi tiết quan hệ</h3>
                                    </div>
                                    <p class="fs-4 lh-base text-dark mb-4">
                                        Trong truyền thống gia tộc, <strong>{{ personA.ho_ten }}</strong> sẽ gọi <strong>{{ personB.ho_ten }}</strong> là 
                                        <span class="text-gold fw-bold">{{ result.term }}</span>.
                                    </p>
                                    <div class="info-footer p-3 rounded-3 bg-light border-start border-4 border-warning">
                                        <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Ghi chú:</span>
                                        <span class="text-muted">{{ result.description }}</span>
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
    name: 'ClientTraCuuHarmonious',
    data() {
        return {
            allMembers: [],
            idA: null,
            idB: null,
            result: null
        }
    },
    computed: {
        personA() { return this.allMembers.find(m => m.id === this.idA); },
        personB() { return this.allMembers.find(m => m.id === this.idB); }
    },
    mounted() {
        this.loadData();
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
        formatDate(date) {
            if (!date) return '---';
            const d = new Date(date);
            return d.toLocaleDateString('vi-VN');
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
                term = "Cháu Nội";
                description = b.ho_ten + " là hậu duệ đời thứ hai (cháu nội).";
            } else if (diff >= 3) {
                term = "Cụ / Cố";
                description = b.ho_ten + " là bậc đại tiền bối khởi nguồn lâu đời.";
            } else if (diff <= -3) {
                term = "Chắt / Chít";
                description = b.ho_ten + " là hậu duệ đời thứ 3, 4 về sau.";
            }

            this.result = { term, description };
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Playfair+Display:wght@700;900&display=swap');

.client-tra-cuu {
    font-family: 'Inter', sans-serif;
    background-color: #f8fafc;
    min-height: 100vh;
}

.hero-heritage {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    position: relative;
    padding-bottom: 120px;
}

.hero-heritage::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 100px;
    background: linear-gradient(to top, #f8fafc, transparent);
}

.heritage-label {
    color: #d4af37;
    font-weight: 800;
    letter-spacing: 3px;
    font-size: 13px;
    text-transform: uppercase;
}

.display-2 {
    font-family: 'Playfair Display', serif;
    font-weight: 900;
    letter-spacing: -1px;
}

.max-w-700 { max-width: 700px; }
.mt-n5-custom { margin-top: -80px; position: relative; z-index: 10; }

.main-card {
    border: 1px solid rgba(0,0,0,0.05);
}

.selection-card {
    background: #fdfdfd;
    border: 2px solid #f1f5f9;
    transition: all 0.3s ease;
}

.selected-a { border-color: #0f172a; background: #fff; box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05); }
.selected-b { border-color: #d4af37; background: #fff; box-shadow: 0 10px 20px rgba(212, 175, 55, 0.05); }

.tracking-wider { letter-spacing: 2px; font-size: 14px; color: #64748b; }

.custom-select-box {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.avatar-ring {
    width: 140px; height: 140px;
    border-radius: 50%;
    padding: 8px;
    border: 3px solid #0f172a;
    position: relative;
}
.avatar-ring.border-gold { border-color: #d4af37; }
.avatar-ring img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

.gender-dot {
    position: absolute;
    bottom: 5px; right: 5px;
    width: 35px; height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    border: 3px solid white;
}

.bg-dark-soft { background: #e2e8f0; }
.bg-gold-soft { background: #fdf6e3; }
.text-gold { color: #d4af37; }

.action-hub { max-width: 150px; }
.hub-icon-container {
    width: 70px; height: 70px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: default;
    transition: all 0.3s ease;
}
.hub-icon-container.clickable { cursor: pointer; }
.hub-icon-container.clickable:hover { transform: rotate(15deg) scale(1.1); }

.btn-heritage {
    background: #0f172a;
    color: white;
    border-radius: 12px;
    font-weight: 800;
    letter-spacing: 2px;
    border: none;
    transition: all 0.3s ease;
}
.btn-heritage:hover:not(:disabled) { background: #1e293b; transform: translateY(-2px); }
.btn-heritage:disabled { background: #cbd5e1; cursor: not-allowed; }

.bg-heritage-dark { background: #0f172a; }

.icon-box {
    width: 50px; height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

.radius-20 { border-radius: 20px; }

@media (max-width: 991px) {
    .display-2 { font-size: 2.5rem; }
    .mt-n5 { margin-top: -60px; }
}
</style>
