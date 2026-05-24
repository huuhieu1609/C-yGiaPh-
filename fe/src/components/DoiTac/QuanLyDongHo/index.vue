<template>
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
                        class="btn btn-sm btn-outline-info radius-8"
                        @click="openAccessModal(item)"
                        title="Cấp Quyền"
                      >
                        <i class="bx bx-share-alt m-0"></i>
                      </button>
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
    <!-- Access Modal -->
    <div class="modal fade" id="accessModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content radius-10 border-0">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold">Quản Lý Quyền Truy Cập - {{ selectedBranch?.ten_chi }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row">
              <div class="col-md-5 border-end">
                <h6 class="fw-bold mb-3">Cấp Quyền Mới</h6>
                <form @submit.prevent="grantAccess">
                  <div class="mb-3">
                    <label class="form-label">Email Người Dùng</label>
                    <input 
                      type="email" 
                      class="form-control" 
                      placeholder="nhap_email@example.com" 
                      v-model="accessEmail" 
                      required 
                    />
                    <small class="text-muted">Người dùng phải có tài khoản trên hệ thống.</small>
                  </div>
                  <button type="submit" class="btn btn-primary w-100" :disabled="isGranting">
                    <span v-if="isGranting" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Cấp Quyền & Gửi Email
                  </button>
                </form>
              </div>
              <div class="col-md-7 ps-4">
                <h6 class="fw-bold mb-3">Danh Sách Đã Cấp Quyền</h6>
                <div v-if="isLoadingAccessList" class="text-center py-3">
                  <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                </div>
                <div v-else-if="grantedUsers.length === 0" class="text-center py-3 text-muted">
                  <i class="bx bx-user-x fs-1 opacity-50 d-block mb-2"></i>
                  Chưa có ai được cấp quyền.
                </div>
                <ul v-else class="list-group list-group-flush">
                  <li v-for="user in grantedUsers" :key="user.id" class="list-group-item px-0">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-bold">{{ user.ho_ten }}</div>
                        <small class="text-muted">{{ user.email }}</small>
                      </div>
                      <button 
                        v-if="confirmingUserId !== user.id"
                        class="btn btn-sm btn-outline-danger" 
                        @click="confirmingUserId = user.id" 
                        title="Thu hồi quyền">
                        Thu hồi
                      </button>
                    </div>
                    <!-- Inline confirm row -->
                    <div v-if="confirmingUserId === user.id" class="mt-2 p-2 rounded" style="background:#fff5f5; border:1px solid #fecaca;">
                      <small class="text-danger fw-bold d-block mb-2">⚠️ Xác nhận thu hồi quyền của <span class="text-dark">{{ user.ho_ten }}</span>?</small>
                      <div class="d-flex gap-2">
                        <button class="btn btn-danger btn-sm flex-fill" @click="revokeAccess(user.id)">
                          <i class="bx bx-check me-1"></i>Xác nhận
                        </button>
                        <button class="btn btn-light btn-sm flex-fill" @click="confirmingUserId = null">
                          Hủy
                        </button>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
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
      selectedBranch: null,
      accessEmail: "",
      grantedUsers: [],
      isLoadingAccessList: false,
      isGranting: false,
      accessModal: null,
      confirmingUserId: null   // ID của user đang hiện confirm thu hồi
    };
  },
  mounted() {
    this.loadData();
    this.accessModal = new window.bootstrap.Modal(document.getElementById('accessModal'));
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
    openAccessModal(item) {
      this.selectedBranch = item;
      this.accessEmail = "";
      this.loadGrantedUsers();
      this.accessModal.show();
    },
    loadGrantedUsers() {
      this.isLoadingAccessList = true;
      axios.get(`http://127.0.0.1:8000/api/doi-tac/quyen-chi-nhanh/${this.selectedBranch.id}`, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            this.grantedUsers = res.data.data;
          }
        })
        .finally(() => {
          this.isLoadingAccessList = false;
        });
    },
    grantAccess() {
      if (!this.accessEmail) return;
      this.isGranting = true;
      
      const payload = {
        chi_nhanh_id: this.selectedBranch.id,
        email: this.accessEmail
      };

      axios.post('http://127.0.0.1:8000/api/doi-tac/cap-quyen-chi-nhanh', payload, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(res.data.message);
            this.accessEmail = "";
            this.loadGrantedUsers();
          } else {
            toastr.error(res.data.message);
          }
        })
        .catch(err => {
          toastr.error(err.response?.data?.message || "Có lỗi xảy ra!");
        })
        .finally(() => {
          this.isGranting = false;
        });
    },
    revokeAccess(userId) {
      const payload = {
        chi_nhanh_id: this.selectedBranch.id,
        nguoi_dung_id: userId
      };

      axios.post('http://127.0.0.1:8000/api/doi-tac/thu-hoi-quyen-chi-nhanh', payload, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(res.data.message);
            this.confirmingUserId = null;
            this.loadGrantedUsers();
          } else {
            toastr.error(res.data.message);
          }
        })
        .catch(err => {
          toastr.error(err.response?.data?.message || "Có lỗi xảy ra!");
        });
    }
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
