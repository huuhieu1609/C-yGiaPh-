<template>
    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card genealogy-main-card shadow-sm border-0 radius-10 h-100">
                <div class="card-header py-3 border-0 mt-2 text-center px-4">
                    <h5 class="mb-0 fw-bold text-uppercase theme-text-main">
                        <i class="bx bx-plus-circle me-1 text-warning"></i> {{ isEditing ? 'Cập Nhật Dòng Họ' : 'Khởi Tạo Dòng Họ' }}
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div v-if="!isEditing && listData.length >= 1" class="alert alert-premium-info border-0 radius-10 p-4 text-center">
                        <i class="bx bx-info-circle fs-1 d-block mb-2 text-warning animate-pulse"></i>
                        <p class="mb-1 fw-bold text-dark theme-text-main">Bạn đã khởi tạo dòng họ thành công.</p>
                        <small class="text-secondary font-medium">Tài khoản đối tác chỉ được phép quản lý duy nhất 01 cây gia phả (dòng họ).</small>
                    </div>

                    <form v-else @submit.prevent="saveData">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Tên Dòng Họ (Chi Nhánh)</label>
                            <input type="text" class="form-control premium-input" placeholder="Nhập tên dòng họ (VD: Trần Gia)" v-model="formData.ten_chi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary">Mô Tả</label>
                            <textarea class="form-control premium-input" rows="4" placeholder="Nhập mô tả chi tiết về lịch sử, gốc gác dòng họ..." v-model="formData.mo_ta"></textarea>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="button" class="btn btn-light radius-30 px-4 fw-medium" v-if="isEditing" @click="resetForm">Hủy</button>
                            <button type="submit" class="btn btn-gradient-orange text-white radius-30 px-4 fw-bold shadow-sm">
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
                        <i class="bx bx-list-ul me-1 text-warning"></i> Thông Tin Dòng Họ Của Tôi
                    </h5>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="table-responsive table-container-premium">
                        <table class="table align-middle mb-0 text-nowrap">
                            <thead>
                                <tr class="text-center">
                                    <th width="10%">#</th>
                                    <th width="35%" class="text-start ps-3">Tên Dòng Họ</th>
                                    <th width="40%" class="text-start">Mô Tả</th>
                                    <th width="15%">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="isLoading">
                                    <td colspan="4" class="text-center py-5 bg-transparent">
                                        <div class="spinner-border text-warning" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else-if="listData.length === 0">
                                    <td colspan="4" class="text-center py-5 text-muted bg-transparent fw-medium">
                                        <i class="bx bx-folder-open display-4 mb-2 d-block text-warning opacity-50"></i>
                                        Bạn chưa khởi tạo dòng họ nào.<br>Hãy điền thông tin bảng bên trái để bắt đầu.
                                    </td>
                                </tr>
                                <tr v-else v-for="(item, index) in listData" :key="item.id" class="table-row-premium">
                                    <td class="text-center fw-bold theme-text-main bg-transparent">{{ index + 1 }}</td>
                                    <td class="fw-bold row-member-name text-start ps-3 bg-transparent">{{ item.ten_chi }}</td>
                                    <td class="text-secondary font-medium text-start text-wrap bg-transparent" style="max-width: 300px; lh-base">{{ item.mo_ta || '---' }}</td>
                                    <td class="text-center bg-transparent">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-action-edit" @click="editItem(item)" title="Sửa thông tin">
                                                <i class="bx bx-edit-alt m-0"></i>
                                            </button>
                                        </div>
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
import axios from "axios";
import toastr from "toastr";
export default {
  name: "PartnerBranchManagement",
  data() {
    return {
      listData: [],
      formData: {
        id: null,
        ten_chi: "",
        mo_ta: "",
      },
      isEditing: false,
      isLoading: false,
    };
  },
  mounted() {
    this.loadData();
  },
  methods: {
    getHeaders() {
      return {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      };
    },
    loadData() {
      this.isLoading = true;
      axios
        .get("http://127.0.0.1:8000/api/chi-nhanh/get-data", this.getHeaders())
        .then((res) => {
          if (res.data.status) {
            this.listData = res.data.data;
          }
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    saveData() {
      const wasEditing = this.isEditing;
      const url = this.isEditing
        ? "http://127.0.0.1:8000/api/chi-nhanh/update"
        : "http://127.0.0.1:8000/api/chi-nhanh/create";

      axios
        .post(url, this.formData, this.getHeaders())
        .then((res) => {
          if (res.data.status) {
            toastr.success(res.data.message);
            this.loadData();
            this.resetForm();
            if (!wasEditing) {
              this.$router.push("/doi-tac/thanh-vien");
            }
          } else {
            toastr.error(res.data.message);
          }
        })
        .catch((err) => {
          toastr.error(err.response?.data?.message || "Có lỗi xảy ra!");
        });
    },
    editItem(item) {
      this.isEditing = true;
      this.formData = { ...item };
    },
    resetForm() {
      this.isEditing = false;
      this.formData = { id: null, ten_chi: "", mo_ta: "" };
    },
  },
};
</script>

<style scoped>
/* ─── KHUNG CARD CHỦ ĐẠO THÍCH ỨNG THEME ─── */
.genealogy-main-card {
  background: var(--card-bg, #fff) !important;
  border: 1px solid var(--border-color, #eaeaea) !important;
  border-radius: 16px !important;
}
.theme-text-main { color: var(--text-main, #333) !important; }
.text-secondary { color: var(--text-sub, #6c757d) !important; }

/* Các ô nhập liệu viên thuốc đồng bộ */
.premium-input {
  border-radius: 12px !important;
  border: 1px solid var(--border-color, #eaeaea) !important;
  padding: 10px 16px !important;
  font-size: 14px;
  background-color: var(--input-bg, #f8f9fa) !important;
  color: var(--text-main, #333) !important;
  box-shadow: none !important;
  transition: all 0.2s ease;
}
.premium-input:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg, #fff) !important;
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
  color: #ffffff !important;
}
.radius-30 { border-radius: 30px !important; }
.radius-10 { border-radius: 10px !important; }

/* ─── HỆ THỐNG BẢNG BIỂU ĐỒNG BỘ MỜ ─── */
.table-container-premium {
  border: 1px solid var(--border-color, #eaeaea);
  border-radius: 12px;
  overflow: hidden;
}
.table thead th {
  background-color: var(--input-bg, #f8f9fa) !important;
  color: var(--text-sub, #6c757d) !important;
  font-weight: 600 !important;
  font-size: 12.5px !important;
  text-transform: uppercase !important;
  border-bottom: 1px solid var(--border-color, #eaeaea) !important;
  padding: 15px 16px !important;
}
.table-row-premium {
  border-bottom: 1px solid var(--border-color, #eaeaea) !important;
  transition: background-color 0.2s ease;
}
.table-row-premium:hover { background-color: var(--input-bg, #f8f9fa) !important; }
.row-member-name { font-size: 14.5px; color: var(--text-main, #333); }
.table-row-premium td { padding: 15px 16px !important; font-size: 13.5px; }

/* Nút sửa bút chì */
.btn-action-edit {
  background: transparent !important;
  border-radius: 8px !important;
  font-size: 15px; padding: 5px 10px !important;
  transition: all 0.2s ease;
  border: 1px solid rgba(245, 158, 11, 0.3) !important; 
  color: #f59e0b !important;
}
.btn-action-edit:hover { background: #f59e0b !important; color: white !important; }

/* Alert premium info */
.alert-premium-info { 
  background-color: rgba(245, 158, 11, 0.08) !important; 
  border: 1px solid rgba(245, 158, 11, 0.15) !important; 
}
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}
</style>
