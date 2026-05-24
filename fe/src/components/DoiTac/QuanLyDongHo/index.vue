<template>
<<<<<<< HEAD
  <div class="row">
    <!-- Left Column: Add/Edit Form -->
    <div class="col-lg-4 col-md-12 mb-4">
      <div class="card shadow-sm border-0 radius-10 h-100">
        <div
          class="card-header bg-white py-3 border-0 border-bottom text-center"
        >
          <h5 class="mb-0 fw-bold text-uppercase" style="color: #6a11cb">
            <i class="bx bx-plus-circle me-1"></i>
            {{ isEditing ? "Cập Nhật Dòng Họ" : "Khởi Tạo Dòng Họ" }}
          </h5>
        </div>
        <div class="card-body p-4">
          <div
            v-if="!isEditing && listData.length >= 1"
            class="alert alert-info border-0 radius-10 p-4 text-center"
          >
            <i class="bx bx-info-circle fs-1 d-block mb-2"></i>
            <p class="mb-0 fw-bold">Bạn đã khởi tạo dòng họ.</p>
            <small
              >Tài khoản đối tác chỉ được phép quản lý duy nhất 01 cây gia phả
              (dòng họ).</small
            >
          </div>
          <form v-else @submit.prevent="saveData">
            <div class="mb-3">
              <label class="form-label fw-semibold"
                >Tên Dòng Họ (Chi Nhánh)</label
              >
              <input
                type="text"
                class="form-control radius-8 border-2 shadow-none"
                placeholder="Nhập tên dòng họ (VD: Trần Gia)"
                v-model="formData.ten_chi"
                required
              />
=======
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
>>>>>>> 79e905e13958ef115ad56dd6a10348b204274efb
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Mô Tả</label>
              <textarea
                class="form-control radius-8 border-2 shadow-none"
                rows="4"
                placeholder="Nhập mô tả chi tiết về dòng họ..."
                v-model="formData.mo_ta"
              ></textarea>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
              <button
                type="button"
                class="btn btn-light radius-8 px-4"
                v-if="isEditing"
                @click="resetForm"
              >
                Hủy
              </button>
              <button
                type="submit"
                class="btn text-white radius-8 px-4 fw-bold shadow-sm"
                style="
                  background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
                  border: none;
                "
              >
                {{ isEditing ? "Cập Nhật" : "Khởi Tạo Ngay" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Right Column: My Branch -->
    <div class="col-lg-8 col-md-12">
      <div class="card shadow-sm border-0 radius-10 h-100">
        <div class="card-header bg-white py-3 border-0 border-bottom">
          <h5 class="mb-0 fw-bold text-uppercase text-dark">
            <i class="bx bx-list-ul me-1"></i> Thông Tin Dòng Họ Của Tôi
          </h5>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
              <thead class="text-center text-white" style="background: #2c3e50">
                <tr>
                  <th width="10%" class="py-3">#</th>
                  <th width="35%" class="py-3">Tên Dòng Họ</th>
                  <th width="40%" class="py-3">Mô Tả</th>
                  <th width="15%" class="py-3">Hành Động</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="isLoading">
                  <td colspan="4" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                  </td>
                </tr>
                <tr v-else-if="listData.length === 0">
                  <td colspan="4" class="text-center py-5 text-muted">
                    <i
                      class="bx bx-folder-open fs-1 mb-2 d-block opacity-25"
                    ></i>
                    Bạn chưa khởi tạo dòng họ nào. Hãy điền thông tin bên trái
                    để bắt đầu.
                  </td>
                </tr>
                <tr v-for="(item, index) in listData" :key="item.id">
                  <td class="text-center fw-bold">{{ index + 1 }}</td>
                  <td class="fw-semibold">{{ item.ten_chi }}</td>
                  <td>{{ item.mo_ta || "---" }}</td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                      <button
                        class="btn btn-sm btn-outline-primary radius-8"
                        @click="editItem(item)"
                        title="Sửa"
                      >
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
<<<<<<< HEAD
import axios from "axios";
import toastr from "toastr";
=======
import axios from 'axios'; // Chỉ giữ lại dòng này
import toastr from 'toastr';
>>>>>>> 79e905e13958ef115ad56dd6a10348b204274efb

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
<<<<<<< HEAD
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
.radius-10 {
  border-radius: 10px;
}
.radius-8 {
  border-radius: 8px;
}
.table thead th {
  border-bottom: none;
  font-weight: 600;
}
</style>
=======
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
/* ─── CẤU TRÚC CARD VÀ CHỮ THÍCH ỨNG THEME ─── */
.genealogy-main-card {
  background: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 16px !important;
}
.theme-text-main { color: var(--text-main) !important; }
.font-medium { font-weight: 500; }

/* Các ô nhập liệu viên thuốc đồng bộ */
.premium-input {
  border-radius: 12px !important;
  border: 1px solid var(--border-color) !important;
  padding: 10px 14px !important;
  font-size: 14px;
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  box-sizing: border-box;
  box-shadow: none !important;
  transition: all 0.2s ease;
}
.premium-input:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg) !important;
}

/* Nút bấm Cam Đào Gradient cao cấp */
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

/* Khung thông báo tài khoản giới hạn */
.alert-premium-info {
  background-color: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 12px !important;
}

/* ─── HỆ THỐNG BẢNG BIỂU ĐỒNG BỘ ─── */
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
  letter-spacing: 0.5px;
  border-bottom: 1px solid var(--border-color) !important;
  padding: 14px 10px !important;
}
.table-row-premium {
  border-bottom: 1px solid var(--border-color) !important;
  transition: background-color 0.2s ease;
}
.table-row-premium:hover { background-color: var(--input-bg) !important; }
.row-member-name { font-size: 14.5px; color: var(--text-main); }
.table-row-premium td { padding: 14px 10px !important; font-size: 13.5px; }

/* Nút sửa bút chì viền mờ cao cấp */
.btn-action-edit {
  background: transparent !important;
  border-radius: 8px !important;
  font-size: 15px; padding: 5px 10px !important;
  transition: all 0.2s ease;
  border: 1px solid rgba(245, 158, 11, 0.3) !important; 
  color: #f59e0b !important;
}
.btn-action-edit:hover { background: #f59e0b !important; color: white !important; }
</style>
>>>>>>> 79e905e13958ef115ad56dd6a10348b204274efb
