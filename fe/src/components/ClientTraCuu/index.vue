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
                    <!-- Section Header with QR Upload Button -->
                    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3 border-bottom pb-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-calculator text-dark fs-3"></i>
                            <h4 class="fw-bold mb-0 text-dark">Máy Tính Xưng Hô & Tra Cứu</h4>
                        </div>
                        <button class="btn btn-gold-glass px-4 py-2.5 rounded-pill fw-bold text-dark d-flex align-items-center gap-2 transition" @click="openQRModal" style="background: rgba(212, 175, 55, 0.15); border: 1px solid rgba(212, 175, 55, 0.3); color: #8a6d1c !important;">
                            <i class="bx bx-qr-scan fs-4"></i>
                            Quét QR Từ Ảnh
                        </button>
                    </div>

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

                        <!-- Visual Lineage Path Flowchart -->
                        <div v-if="result.path && result.path.length > 0" class="lineage-path-card mt-4 p-4 p-lg-5 rounded-4 shadow-sm bg-white border">
                            <div class="d-flex align-items-center mb-4">
                                <div class="icon-box bg-dark text-white me-3">
                                    <i class="bx bx-git-commit fs-4"></i>
                                </div>
                                <h4 class="fw-bold mb-0 text-dark">Sơ đồ đường đi huyết thống</h4>
                            </div>
                            
                            <div class="path-flow-container py-3">
                                <div class="row g-3 justify-content-center align-items-center text-center">
                                    <template v-for="(node, idx) in result.path" :key="node.id">
                                        <!-- Member Node -->
                                        <div class="col-auto">
                                            <div class="path-node p-3 rounded-3 border d-flex flex-column align-items-center" 
                                                 :class="{'border-primary bg-primary-soft': idx === 0, 'border-warning bg-gold-soft': idx === result.path.length - 1, 'bg-light': idx > 0 && idx < result.path.length - 1}">
                                                <div class="avatar-mini-ring mb-2">
                                                    <img :src="node.avatar || 'https://ui-avatars.com/api/?name=' + node.ho_ten + '&background=' + (idx === 0 ? '0d6efd' : idx === result.path.length - 1 ? 'd4af37' : '6c757d') + '&color=fff'" 
                                                         class="rounded-circle" width="50" height="50">
                                                </div>
                                                <h6 class="fw-bold mb-0 text-dark small">{{ node.ho_ten }}</h6>
                                                <span class="badge bg-secondary-soft text-secondary mt-1" style="font-size: 10px;">Đời {{ node.doi_thu }}</span>
                                                <span v-if="idx === 0" class="badge bg-primary text-white mt-1" style="font-size: 9px;">Bạn (A)</span>
                                                <span v-else-if="idx === result.path.length - 1" class="badge bg-warning text-dark mt-1" style="font-size: 9px;">Đối tượng (B)</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Connection Edge -->
                                        <div v-if="idx < result.path.length - 1" class="col-auto px-0 d-flex flex-column align-items-center justify-content-center connection-arrow-col">
                                            <div class="connection-line"></div>
                                            <span class="badge bg-dark text-white rounded-pill px-2 py-1 my-1 relation-step-label shadow-sm" style="font-size: 10px; z-index: 2;">
                                                {{ result.path[idx + 1].relation_label }}
                                            </span>
                                            <div class="connection-arrow"><i class="bx bx-chevron-right text-muted fs-5"></i></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue QR Scan Modal -->
        <div v-if="showQRModal" class="custom-modal-backdrop animate__animated animate__fadeIn" @click.self="closeQRModal">
            <div class="custom-modal-content animate__animated animate__zoomIn p-4 rounded-4 shadow-2xl bg-white position-relative">
                <button class="btn-close-custom position-absolute top-0 end-0 m-3 border-0 bg-transparent" @click="closeQRModal">
                    <i class="bx bx-x fs-2 text-muted"></i>
                </button>
                
                <div class="text-center mb-4">
                    <div class="qr-scan-icon-container bg-gold-soft text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: rgba(212, 175, 55, 0.1) !important;">
                        <i class="bx bx-qr-scan fs-1" style="color: #d4af37 !important;"></i>
                    </div>
                    <h3 class="fw-extrabold text-dark mb-1">Tải Ảnh Mã QR</h3>
                    <p class="text-secondary font-14">Chọn hoặc kéo thả ảnh QR của thành viên để quét nhanh thông tin</p>
                </div>

                <!-- Dropzone Area -->
                <div 
                    class="dropzone-area p-4 text-center rounded-3 border border-2 border-dashed d-flex flex-column align-items-center justify-content-center transition cursor-pointer"
                    :class="{'drag-active': dragActive, 'has-file': qrPreview}"
                    @dragover.prevent="dragActive = true"
                    @dragleave.prevent="dragActive = false"
                    @drop.prevent="handleDrop"
                    @click="triggerFileInput"
                    style="border-style: dashed !important;"
                >
                    <input type="file" ref="fileInput" class="d-none" accept="image/*" @change="handleFileSelect">
                    
                    <template v-if="!qrPreview">
                        <i class="bx bx-cloud-upload text-muted display-4 mb-2"></i>
                        <p class="mb-1 fw-bold text-dark font-15">Kéo thả ảnh vào đây hoặc nhấp để chọn</p>
                        <p class="font-12 text-muted mb-0">Hỗ trợ các định dạng PNG, JPG, JPEG</p>
                    </template>
                    <template v-else>
                        <div class="preview-container position-relative mb-2">
                            <img :src="qrPreview" class="img-fluid rounded border shadow-sm" style="max-height: 200px; object-fit: contain;">
                            <button class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-1 border-0" @click.stop="clearFile">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                        <p class="mb-0 fw-bold text-success font-14"><i class="bx bx-check-circle"></i> Đã chọn: {{ qrFile.name }}</p>
                    </template>
                </div>

                <!-- Scan Progress & Error Message -->
                <div v-if="isScanning" class="text-center mt-3 py-2">
                    <div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div>
                    <span class="fw-bold text-muted">Đang phân tích mã QR trực tuyến...</span>
                </div>
                
                <div v-if="scanError" class="alert alert-danger mt-3 mb-0 radius-10 d-flex align-items-center gap-2 font-13 shadow-xs">
                    <i class="bx bx-error-circle fs-5"></i>
                    <div>{{ scanError }}</div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-3 mt-4">
                    <button class="btn btn-light w-50 py-2.5 rounded-pill fw-bold border" @click="closeQRModal">Huỷ bỏ</button>
                    <button class="btn btn-heritage w-50 py-2.5 rounded-pill fw-bold text-white shadow-sm" :disabled="!qrFile || isScanning" @click="scanQRCode">
                        <i class="bx bx-analyse me-1"></i> Bắt đầu Quét
                    </button>
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
            result: null,
            showQRModal: false,
            dragActive: false,
            isScanning: false,
            scanError: null,
            qrFile: null,
            qrPreview: null
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

            axios.post('http://127.0.0.1:8000/api/thanh-vien/xac-dinh-quan-he', {
                id_a: this.idA,
                id_b: this.idB
            })
            .then(res => {
                if (res.data.status) {
                    this.result = {
                        term: res.data.term,
                        description: res.data.description,
                        path: res.data.path
                    };
                } else {
                    toastr.error(res.data.message || 'Lỗi xác định mối quan hệ!');
                }
            })
            .catch(err => {
                toastr.error(err.response?.data?.message || 'Có lỗi xảy ra khi kết nối máy chủ!');
            });
        },
        openQRModal() {
            this.showQRModal = true;
            this.scanError = null;
            this.clearFile();
        },
        closeQRModal() {
            this.showQRModal = false;
        },
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        handleDragOver() {
            this.dragActive = true;
        },
        handleDragLeave() {
            this.dragActive = false;
        },
        handleDrop(e) {
            this.dragActive = false;
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                this.setFile(e.dataTransfer.files[0]);
            }
        },
        handleFileSelect(e) {
            if (e.target.files && e.target.files[0]) {
                this.setFile(e.target.files[0]);
            }
        },
        setFile(file) {
            if (!file.type.startsWith('image/')) {
                this.scanError = 'Chỉ chấp nhận các tệp định dạng hình ảnh!';
                return;
            }
            this.qrFile = file;
            this.scanError = null;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                this.qrPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        clearFile() {
            this.qrFile = null;
            this.qrPreview = null;
            this.scanError = null;
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },
        scanQRCode() {
            if (!this.qrFile) return;
            this.isScanning = true;
            this.scanError = null;

            const formData = new FormData();
            formData.append('file', this.qrFile);

            // Dùng fetch() thay vì axios để tránh axios interceptors tự động
            // gắn header Authorization vào request third-party (gây lỗi CORS preflight)
            fetch('https://api.qrserver.com/v1/read-qr-code/', {
                method: 'POST',
                body: formData
                // KHÔNG set Content-Type header — browser tự set boundary đúng cho multipart/form-data
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP error: ' + res.status);
                return res.json();
            })
            .then(data => {
                const symbol = data[0]?.symbol[0];
                if (!symbol || symbol.error) {
                    this.scanError = symbol?.error || 'Không nhận diện được mã QR trong ảnh. Vui lòng thử lại với ảnh rõ nét hơn!';
                    return;
                }

                const qrText = symbol.data;
                if (!qrText) {
                    this.scanError = 'Không có dữ liệu trong mã QR này!';
                    return;
                }

                // Phân tích kết quả quét — tìm ID thành viên
                const detailMatch = qrText.match(/\/thanh-vien\/detail\/(\d+)/);
                if (detailMatch && detailMatch[1]) {
                    const id = detailMatch[1];
                    toastr.success('Quét mã QR thành công! Đang chuyển hướng...');
                    this.closeQRModal();
                    this.$router.push(`/thanh-vien/detail/${id}`);
                    return;
                }

                if (/^\d+$/.test(qrText.trim())) {
                    const id = qrText.trim();
                    toastr.success('Quét mã QR thành công! Đang chuyển hướng...');
                    this.closeQRModal();
                    this.$router.push(`/thanh-vien/detail/${id}`);
                    return;
                }

                this.scanError = 'Mã QR này không thuộc Hệ thống Gia Phả Số (Dữ liệu không khớp: ' + qrText + ')';
            })
            .catch(() => {
                this.scanError = 'Lỗi kết nối máy chủ quét QR! Vui lòng kiểm tra kết nối mạng và thử lại.';
            })
            .finally(() => {
                this.isScanning = false;
            });
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
.bg-primary-soft { background-color: #f0f7ff; }
.bg-secondary-soft { background-color: #f1f5f9; color: #475569; }
.avatar-mini-ring {
    padding: 3px;
    border: 2px solid #cbd5e1;
    border-radius: 50%;
    display: inline-block;
}
.border-primary .avatar-mini-ring { border-color: #0d6efd; }
.border-warning .avatar-mini-ring { border-color: #d4af37; }

.path-node {
    min-width: 120px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    transition: all 0.2s ease;
}
.path-node:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.connection-line {
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, #cbd5e1 0%, #94a3b8 100%);
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 1;
}

.connection-arrow-col {
    position: relative;
    width: 90px;
    height: 100px;
}

.relation-step-label {
    position: relative;
    z-index: 5;
    background-color: #0f172a !important;
}

.connection-arrow {
    position: relative;
    z-index: 2;
    margin-top: 2px;
}

@media (max-width: 768px) {
    .connection-arrow-col {
        width: 100%;
        height: auto;
        padding: 10px 0 !important;
    }
    .connection-line {
        display: none;
    }
    .bx-chevron-right {
        transform: rotate(90deg);
    }
}

/* Custom Vue Modal Styling */
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
}
.btn-close-custom {
    transition: all 0.2s ease;
}
.btn-close-custom:hover {
    transform: rotate(90deg);
}
.dropzone-area {
    min-height: 180px;
    border-color: #cbd5e1;
    background-color: #f8fafc;
    transition: all 0.3s ease;
}
.dropzone-area:hover, .dropzone-area.drag-active {
    border-color: #d4af37;
    background-color: #fdfefb;
}
.dropzone-area.has-file {
    border-color: #198754;
    background-color: #f6fff9;
}
.text-muted-soft {
    color: #94a3b8;
}
.btn-gold-glass {
    background: rgba(212, 175, 55, 0.1);
    border: 1px solid rgba(212, 175, 55, 0.25);
    color: #ffd891;
    transition: all 0.3s ease;
}
.btn-gold-glass:hover {
    background: rgba(212, 175, 55, 0.2);
    border-color: #d4af37;
    transform: translateY(-2px);
}
</style>
