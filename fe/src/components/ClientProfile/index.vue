<template>
  <div class="profile-container container mt-5 pt-5 pb-5">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
            <h3 class="fw-bold mb-0" style="color: #d4af37;">Hồ sơ cá nhân</h3>
            <p class="text-muted mt-2">Quản lý thông tin tài khoản của bạn</p>
          </div>
          <div class="card-body p-4 p-md-5">
            <div class="row">
              <!-- Left Column: Avatar & Basic Info -->
              <div class="col-md-4 text-center mb-4 mb-md-0 border-end">
                <div class="position-relative d-inline-block">
                  <img :src="profile.avatar || 'https://dzfullstack.com/assets/images/logo-1.png'" class="rounded-circle border border-3 border-warning profile-avatar" alt="Avatar" width="150" height="150" style="object-fit: cover;">
                  <label for="avatar-upload" class="btn btn-sm btn-warning rounded-circle position-absolute bottom-0 end-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="Cập nhật ảnh đại diện">
                    <i class="bx bx-camera text-dark fs-5"></i>
                  </label>
                  <input type="file" id="avatar-upload" @change="onFileChange" class="d-none" accept="image/*">
                </div>
                <h4 class="mt-3 fw-bold">{{ profile.ho_ten }}</h4>
                <span class="badge bg-light text-dark border mb-2">{{ profile.vai_tro || 'Khách hàng' }}</span>
                <p class="text-muted small mb-0">{{ profile.email }}</p>
                
                <div class="mt-4">
                  <button type="button" class="btn btn-light w-100 mb-2" @click="handleLogout">
                    <i class="bx bx-log-out me-1"></i> Đăng Xuất
                  </button>
                </div>
              </div>

              <!-- Right Column: Forms -->
              <div class="col-md-8 ps-md-4">
                <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" style="color: #d4af37; font-weight: 600;">Thông Tin Chung</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" style="color: #6c757d; font-weight: 600;">Đổi Mật Khẩu</button>
                  </li>
                </ul>

                <div class="tab-content" id="profileTabsContent">
                  <!-- Tab Thông tin chung -->
                  <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <form @submit.prevent="updateProfile">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">Họ và tên</label>
                          <input type="text" class="form-control" v-model="profile.ho_ten" placeholder="Nhập họ và tên" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">Số điện thoại</label>
                          <input type="text" class="form-control" v-model="profile.so_dien_thoai" placeholder="Nhập số điện thoại">
                        </div>
                        <div class="col-md-12">
                          <label class="form-label fw-semibold">Email</label>
                          <input type="email" class="form-control" v-model="profile.email" disabled>
                          <small class="text-muted">Email không thể thay đổi sau khi đăng ký.</small>
                        </div>
                        <div class="col-md-12 mt-4 text-end">
                          <button type="submit" class="btn btn-primary px-4 py-2" style="background-color: #d4af37; border-color: #d4af37; color: #000; font-weight: 600;" :disabled="isUpdatingProfile">
                            <span v-if="!isUpdatingProfile"><i class="bx bx-save me-1"></i> Lưu Thay Đổi</span>
                            <span v-else><i class="bx bx-loader-alt bx-spin"></i> Đang xử lý...</span>
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>

                  <!-- Tab Đổi mật khẩu -->
                  <div class="tab-pane fade" id="password" role="tabpanel">
                    <form @submit.prevent="changePassword">
                      <div class="row g-3">
                        <div class="col-md-12">
                          <label class="form-label fw-semibold">Mật khẩu hiện tại</label>
                          <input type="password" class="form-control" v-model="passwordData.current_password" placeholder="Nhập mật khẩu hiện tại" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">Mật khẩu mới</label>
                          <input type="password" class="form-control" v-model="passwordData.new_password" placeholder="Nhập mật khẩu mới" required minlength="6">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label fw-semibold">Xác nhận mật khẩu mới</label>
                          <input type="password" class="form-control" v-model="passwordData.new_password_confirmation" placeholder="Nhập lại mật khẩu mới" required minlength="6">
                        </div>
                        <div class="col-md-12 mt-4 text-end">
                          <button type="submit" class="btn btn-dark px-4 py-2" :disabled="isChangingPassword">
                            <span v-if="!isChangingPassword"><i class="bx bx-lock-open-alt me-1"></i> Cập Nhật Mật Khẩu</span>
                            <span v-else><i class="bx bx-loader-alt bx-spin"></i> Đang xử lý...</span>
                          </button>
                        </div>
                      </div>
                    </form>
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
  name: 'ClientProfile',
  data() {
    return {
      profile: {
        ho_ten: '',
        email: '',
        so_dien_thoai: '',
        avatar: '',
        vai_tro: ''
      },
      avatarFile: null,
      passwordData: {
        current_password: '',
        new_password: '',
        new_password_confirmation: ''
      },
      isUpdatingProfile: false,
      isChangingPassword: false
    }
  },
  mounted() {
    this.loadUserProfile();
  },
  methods: {
    getHeaders() {
      const token = localStorage.getItem('access_token');
      return {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      };
    },
    loadUserProfile() {
      axios.get('http://127.0.0.1:8000/api/me', this.getHeaders())
        .then(res => {
          this.profile = res.data.user;
          // Update local storage so that Topclient avatar also updates if refreshed
          localStorage.setItem('user', JSON.stringify(res.data.user));
          window.dispatchEvent(new Event('profile-updated'));
        })
        .catch(err => {
          if(err.response && err.response.status === 401) {
            this.handleLogout();
          }
        });
    },
    onFileChange(e) {
      const file = e.target.files[0];
      if (file) {
        this.avatarFile = file;
        this.profile.avatar = URL.createObjectURL(file); // Preview locally
      }
    },
    updateProfile() {
      this.isUpdatingProfile = true;
      
      const formData = new FormData();
      formData.append('ho_ten', this.profile.ho_ten);
      if (this.profile.so_dien_thoai) {
        formData.append('so_dien_thoai', this.profile.so_dien_thoai);
      }
      if (this.avatarFile) {
        formData.append('avatar', this.avatarFile);
      }

      axios.post('http://127.0.0.1:8000/api/update-profile', formData, {
        headers: {
          ...this.getHeaders().headers,
          'Content-Type': 'multipart/form-data'
        }
      })
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.profile = res.data.user;
          localStorage.setItem('user', JSON.stringify(res.data.user));
          window.dispatchEvent(new Event('profile-updated'));
          // Reset file input
          this.avatarFile = null;
        }
      })
      .catch(err => {
        const msg = err.response?.data?.message || 'Có lỗi xảy ra khi cập nhật thông tin!';
        toastr.error(msg);
      })
      .finally(() => {
        this.isUpdatingProfile = false;
      });
    },
    changePassword() {
      if (this.passwordData.new_password !== this.passwordData.new_password_confirmation) {
        toastr.warning('Mật khẩu xác nhận không khớp!');
        return;
      }

      this.isChangingPassword = true;

      axios.post('http://127.0.0.1:8000/api/change-password', this.passwordData, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.passwordData = {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
          };
        }
      })
      .catch(err => {
        const msg = err.response?.data?.message || 'Có lỗi xảy ra khi đổi mật khẩu!';
        toastr.error(msg);
      })
      .finally(() => {
        this.isChangingPassword = false;
      });
    },
    handleLogout() {
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
      this.$router.push('/login');
    }
  }
}
</script>

<style scoped>
.profile-container {
  min-height: calc(100vh - 300px);
}
.form-control:focus {
  border-color: #d4af37;
  box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
}
.nav-tabs .nav-link {
  border: none;
  border-bottom: 2px solid transparent;
  padding: 10px 20px;
}
.nav-tabs .nav-link.active {
  border-bottom: 2px solid #d4af37;
  background-color: transparent;
}
.nav-tabs .nav-link:hover {
  border-bottom: 2px solid #e5c45e;
}
</style>
