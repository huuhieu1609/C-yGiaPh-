<template>
    <div class="row g-4">
        <div class="col-lg-4 col-md-12">
            <div class="card luxury-panel border-0 h-100 shadow-sm">
                <div class="card-header bg-transparent py-4 text-center">
                    <h5 class="mb-0 fw-bold panel-title text-uppercase">
                        <i class="bx bx-plus-circle me-1 text-rose"></i> {{ isEditing ? 'Cập Nhật Dòng Họ' : 'Khởi Tạo Dòng Họ' }}
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div v-if="!isEditing && listData.length >= 1" class="alert alert-premium-info text-center p-4">
                        <div class="info-glow-icon mb-3">
                            <i class="bx bx-info-circle"></i>
                        </div>
                        <p class="mb-2 fw-bold text-dark fs-6">Dòng họ đã được thiết lập</p>
                        <small class="text-secondary d-block lh-base">Tài khoản đối tác chỉ được phép quản lý duy nhất 01 cây gia phả (dòng họ) trên hệ thống toàn tộc.</small>
                    </div>

                    <form v-else @submit.prevent="saveData">
                        <div class="mb-4">
                            <label class="modern-field-label">Tên Dòng Họ (Chi Nhánh)</label>
                            <input type="text" class="form-control modern-input fw-semibold" placeholder="Nhập tên dòng họ (VD: Trần Gia)" v-model="formData.ten_chi" required>
                        </div>
                        <div class="mb-4">
                            <label class="modern-field-label">Mô Tả Tộc Phả</label>
                            <textarea class="form-control modern-input" rows="5" placeholder="Nhập mô tả chi tiết, nguồn gốc hoặc di huấn của dòng họ..." v-model="formData.mo_ta"></textarea>
                        </div>
                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <button type="button" class="btn btn-modern-cancel px-4" v-if="isEditing" @click="resetForm">Hủy</button>
                            <button type="submit" class="btn btn-luxury-primary px-4 fw-bold">
                                {{ isEditing ? 'Cập Nhật' : 'Khởi Tạo Ngay' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="card luxury-panel border-0 h-100 shadow-sm">
                <div class="card-header bg-transparent py-4">
                    <h5 class="mb-0 fw-bold panel-title text-uppercase">
                        <i class="bx bx-list-ul me-1 text-teal"></i> Thông Info Dòng Họ Của Tôi
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="table-responsive rounded-3 border border-light-subtle">
                        <table class="table modern-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="10%" class="text-center">STT</th>
                                    <th width="35%">Tên Dòng Họ</th>
                                    <th width="40%">Mô Tả Di Sản</th>
                                    <th width="15%" class="text-center">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="4" class="text-center py-5">
                                        <div class="spinner-border text-rose" role="status" style="width: 1.5rem; height: 1.5rem;">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="listData.length === 0">
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bx bx-folder-open fs-1 mb-2 d-block opacity-20 text-rose"></i>
                                        <span class="small text-secondary fw-medium">Bạn chưa khởi tạo dòng họ nào. Hãy thiết lập thông tin bên trái để bắt đầu tộc phả.</span>
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in listData" :key="item.id">
                                    <td class="text-center fw-bold text-secondary index-cell">{{ index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="clan-badge-avatar"><i class="bx bx-shield-quarter"></i></div>
                                            <span class="fw-bold text-dark fs-6">{{ item.ten_chi }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-secondary small text-truncate-2" :title="item.mo_ta">
                                            {{ item.mo_ta || 'Chưa có thông tin mô tả chi tiết dòng họ.' }}
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-action-edit" @click="editItem(item)" title="Chỉnh sửa dòng họ">
                                            <i class="bx bx-edit-alt"></i> Sửa
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
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'PartnerBranchManagement',
    data() {
        return {
            listData: [],
            formData: {
                id: null,
                ten_chi: '',
                mo_ta: ''
            },
            isEditing: false,
            isLoading: false
        }
    },
    mounted() {
        this.loadData();
    },
    methods: {
        getHeaders() {
            return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
        },
        loadData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        this.listData = res.data.data;
                    }
                })
                .finally(() => { this.isLoading = false; });
        },
        saveData() {
            const url = this.isEditing 
                ? 'http://127.0.0.1:8000/api/chi-nhanh/update'
                : 'http://127.0.0.1:8000/api/chi-nhanh/create';
            
            axios.post(url, this.formData, this.getHeaders())
                .then(res => {
                    if (res.data.status) {
                        toastr.success(res.data.message);
                        this.loadData();
                        this.resetForm();
<<<<<<< HEAD
                        if (!this.isEditing) {
                            this.$router.push('/doi-tac/gia-pha');
                        }
=======
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    toastr.error(err.response?.data?.message || 'Có lỗi xảy ra!');
                });
        },
        editItem(item) {
            this.isEditing = true;
            this.formData = { ...item };
        },
        resetForm() {
            this.isEditing = false;
            this.formData = { id: null, ten_chi: '', mo_ta: '' };
        }
    }
}
</script>

<style scoped>
/* ─── SYSTEM PREMIUM LIGHT PANEL ─── */
.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
}

.panel-title {
    font-size: 14.5px;
    letter-spacing: 0.5px;
    color: #111827;
}
.text-rose { color: #db2777; }
.text-teal { color: #0d9488; }

/* ─── LUXURY ALERT INFO FIELD ─── */
.alert-premium-info {
    background-color: #fafafa;
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 14px;
}
.info-glow-icon i {
    font-size: 38px;
    color: #db2777;
    filter: drop-shadow(0 4px 8px rgba(219, 39, 119, 0.2));
}

/* ─── MODERN FORM INPUTS ─── */
.modern-field-label {
    font-size: 11.5px;
    font-weight: 700;
    color: #6b7280;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 8px;
    display: block;
}
.modern-input {
    background: #f9fafb !important;
    border: 1px solid rgba(0, 0, 0, 0.06) !important;
    color: #111827 !important;
    border-radius: 12px;
    padding: 11px 14px;
    font-size: 14px;
    transition: all 0.2s ease;
}
.modern-input:focus {
    border-color: #db2777 !important;
    box-shadow: 0 4px 12px rgba(219, 39, 119, 0.06) !important;
    background: #ffffff !important;
}

/* ─── PREMIUM LUXURY BUTTONS ─── */
.btn-luxury-primary {
    background: linear-gradient(135deg, #db2777 0%, #f97316 50%, #f59e0b 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    font-size: 13.5px;
    border-radius: 30px;
    box-shadow: 0 4px 12px rgba(219, 39, 119, 0.2);
    transition: all 0.3s ease;
}
.btn-luxury-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(219, 39, 119, 0.35);
    filter: brightness(1.04);
}

.btn-modern-cancel {
    background: transparent;
    border: 1px solid rgba(0, 0, 0, 0.08);
    color: #6b7280;
    border-radius: 30px;
    font-size: 13.5px;
    transition: all 0.2s ease;
}
.btn-modern-cancel:hover {
    background: #f9fafb;
    color: #111827;
}

/* ─── MODERN LUXURY TABLE DESIGN ─── */
.modern-table thead {
    background: #f4f5f7;
}
.modern-table thead th {
    color: #4b5563;
    font-weight: 700;
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    padding: 14px 12px;
}
.modern-table tbody tr {
    transition: all 0.2s ease;
}
.modern-table tbody tr:hover {
    background-color: #fcfcfd;
}
.modern-table tbody td {
    padding: 14px 12px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    color: #374151;
    font-size: 14px;
}
.index-cell {
    font-size: 13px !important;
}

/* Avatar biểu tượng dòng tộc */
.clan-badge-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fff5f7;
    color: #db2777;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    border: 1px solid rgba(219, 39, 119, 0.15);
}

/* Trực quan cắt chữ dòng mô tả quá dài */
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.5;
}

/* Nút sửa dòng tộc mượt */
.btn-action-edit {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.08);
    color: #4f46e5; /* Tone màu Indigo đồng bộ mục trang chủ */
    font-size: 13px;
    font-weight: 600;
    border-radius: 8px;
    padding: 5px 12px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.btn-action-edit:hover {
    background: #4f46e5;
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
}
</style>