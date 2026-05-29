<template>
    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card genealogy-main-card shadow-sm border-0 radius-10 h-100">
                <div class="card-header py-3 border-0 mt-2 text-center px-4">
                    <h5 class="mb-0 fw-bold text-uppercase theme-text-main">
                        <i class="bx bx-plus-circle me-1 text-warning"></i>
                        {{ isEditing ? 'Cập Nhật Dòng Họ' : 'Khởi Tạo Dòng Họ' }}
                    </h5>
                </div>

                <div class="card-body p-4 pt-2">
                  <!-- Phân Quyền UI -->
                  <div class="mb-3">
                    <PhanQuyen :all-members="allMembers" :chi-nhanhs="listData" :selected-chi-nhanh-id="null" />
                  </div>
                    <div
                        v-if="!isEditing && listData.length >= 1"
                        class="alert alert-info border-0 radius-10 p-4 text-center"
                    >
                        <i class="bx bx-info-circle fs-1 d-block mb-2 text-warning"></i>
                        <p class="mb-1 fw-bold">
                            Bạn đã khởi tạo dòng họ thành công.
                        </p>
                        <small>
                            Tài khoản đối tác chỉ được phép quản lý duy nhất 01 cây gia phả.
                        </small>
                    </div>

                    <form v-else @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Tên Dòng Họ (Chi Nhánh)
                            </label>

                            <input
                                type="text"
                                class="form-control radius-8"
                                placeholder="Nhập tên dòng họ"
                                v-model="formData.ten_chi"
                                required
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Mô Tả
                            </label>

                            <textarea
                                class="form-control radius-8"
                                rows="4"
                                placeholder="Nhập mô tả..."
                                v-model="formData.mo_ta"
                            ></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button
                                type="button"
                                class="btn btn-light radius-30 px-4"
                                v-if="isEditing"
                                @click="resetForm"
                            >
                                Hủy
                            </button>

                            <button
                                type="submit"
                                class="btn btn-warning text-white radius-30 px-4 fw-bold"
                            >
                                {{ isEditing ? 'Cập Nhật' : 'Khởi Tạo Ngay' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card genealogy-main-card shadow-sm border-0 radius-10 h-100">
                <div class="card-header py-3 border-0 mt-2 px-4">
                    <h5 class="mb-0 fw-bold text-uppercase theme-text-main">
                        <i class="bx bx-list-ul me-1 text-warning"></i>
                        Thông Tin Dòng Họ Của Tôi
                    </h5>
                </div>

                <div class="card-body p-4 pt-2">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>Tên Dòng Họ</th>
                                    <th>Mô Tả</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="4" class="text-center py-5">
                                        Loading...
                                    </td>
                                </tr>

                                <tr v-else-if="listData.length === 0">
                                    <td colspan="4" class="text-center py-5">
                                        Chưa có dữ liệu
                                    </td>
                                </tr>

                                <tr
                                    v-else
                                    v-for="(item, index) in listData"
                                    :key="item.id"
                                >
                                    <td class="text-center">
                                        {{ index + 1 }}
                                    </td>

                                    <td>{{ item.ten_chi }}</td>

                                    <td>{{ item.mo_ta || '---' }}</td>

                                    <td class="text-center">
                                        <button
                                            class="btn btn-sm btn-warning text-white"
                                            @click="editItem(item)"
                                        >
                                            <i class="bx bx-edit-alt"></i>
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
import PhanQuyen from '../PhanQuyen/index.vue';

export default {
  name: 'PartnerBranchManagement',
  components: { PhanQuyen },
  data() {
    return {
      listData: [],
      allMembers: [],
      formData: {
        id: null,
        ten_chi: '',
        mo_ta: ''
      },
      isEditing: false,
      isLoading: false
    };
  },
  mounted() {
    this.loadData();
    this.loadMembers();
  },
  methods: {
    getHeaders() {
      return {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('access_token')}`
        }
      };
    },
    loadData() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.listData = res.data.data;
          }
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    loadMembers() {
      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.allMembers = res.data.data;
          }
        }).catch(err => { console.error(err); });
    },
    saveData() {
      const wasEditing = this.isEditing;
      const url = this.isEditing
        ? 'http://127.0.0.1:8000/api/chi-nhanh/update'
        : 'http://127.0.0.1:8000/api/chi-nhanh/create';

      axios.post(url, this.formData, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(res.data.message);
            this.loadData();
            this.resetForm();
            if (!wasEditing) {
              this.$router.push('/doi-tac/thanh-vien');
            }
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
};
</script>

<style scoped>
/* ─── CẤU TRÚC CARD CHỦ ĐẠO THÍCH ỨNG THEME ─── */
.genealogy-main-card {
  background: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 16px !important;
}
.theme-text-main { color: var(--text-main) !important; }
.text-secondary { color: var(--text-sub) !important; }

/* Ô nhập liệu & Textarea viên thuốc đồng bộ */
.premium-input {
  border-radius: 12px !important;
  border: 1px solid var(--border-color) !important;
  padding: 10px 14px !important;
  font-size: 14px;
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  box-shadow: none !important;
  transition: all 0.2s ease;
}
.premium-input:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg) !important;
}

.premium-textarea {
  border-radius: 14px !important;
  border: 1px solid var(--border-color) !important;
  padding: 12px 14px !important;
  font-size: 14px;
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  box-shadow: none !important;
  resize: none;
}
.premium-textarea:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg) !important;
}

/* Nút bấm Cam Đào Gradient */
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
.radius-30 { border-radius: 30px !important; }
.radius-16 { border-radius: 16px !important; }

/* Khung thông báo mờ pastel */
.alert-premium-info {
  background-color: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 12px !important;
}

/* ─── HỆ THỐNG BẢNG BIỂU ĐỒNG BỘ MỜ ─── */
.table-container-premium {
  border: 1px solid var(--border-color);
  border-radius: 12px;
  overflow: hidden;
}
.table thead th {
  background-color: var(--input-bg) !important;
  color: var(--text-sub) !important;
  font-weight: 600 !important;
  font-size: 12.5px !important;
  text-transform: uppercase !important;
  border-bottom: 1px solid var(--border-color) !important;
  padding: 14px 16px !important;
}
.table-row-premium {
  border-bottom: 1px solid var(--border-color) !important;
  transition: background-color 0.2s ease;
}
.table-row-premium:hover { background-color: var(--input-bg) !important; }
.row-member-name { font-size: 14.5px; color: var(--text-main); }
.table-row-premium td { padding: 14px 16px !important; font-size: 13.5px; }

.line-clamp-desc-fix {
  max-width: 300px;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Nút sửa bút chì viền mờ */
.btn-action-edit {
  background: transparent !important;
  border-radius: 8px !important;
  font-size: 15px; padding: 5px 10px !important;
  transition: all 0.2s ease;
  border: 1px solid rgba(245, 158, 11, 0.3) !important; 
  color: #f59e0b !important;
}
.btn-action-edit:hover { background: #f59e0b !important; color: white !important; }
.font-medium { font-weight: 500; }
</style>