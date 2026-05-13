<template>
    <div class="admin-login-page">
        <div class="login-box animate__animated animate__zoomIn">
            <div class="login-header text-center mb-4">
                <img src="../../../assets/images/logo-icon.png" width="70" alt="Logo">
                <h3 class="mt-3 fw-bold text-uppercase tracking-wider">Hệ Thống Quản Trị</h3>
                <p class="text-muted">Vui lòng đăng nhập để tiếp tục</p>
            </div>
            <form @submit.prevent="handleLogin">
                <div class="mb-3">
                    <label class="form-label fw-bold">Email quản trị</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bx bx-envelope"></i></span>
                        <input type="email" class="form-control border-start-0 ps-0" v-model="loginData.email" placeholder="admin@example.com" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bx bx-lock-alt"></i></span>
                        <input type="password" class="form-control border-start-0 ps-0" v-model="loginData.mat_khau" placeholder="••••••••" required>
                    </div>
                </div>
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Ghi nhớ</label>
                    </div>
                    <a href="javascript:;" class="text-primary small">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold radius-8" :disabled="isLoading">
                    <span v-if="!isLoading">ĐĂNG NHẬP HỆ THỐNG</span>
                    <span v-else><i class="bx bx-loader-alt bx-spin"></i> ĐANG XỬ LÝ...</span>
                </button>
            </form>
            <div class="login-footer mt-5 text-center">
                <p class="text-muted small">&copy; 2026 Heritage Archivist. All rights reserved.</p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'AdminLogin',
    data() {
        return {
            loginData: {
                email: '',
                mat_khau: ''
            },
            isLoading: false
        }
    },
    methods: {
        handleLogin() {
            this.isLoading = true;
            axios.post('http://127.0.0.1:8000/api/login', this.loginData)
                .then(res => {
                    if (res.data.status) {
                        if (res.data.user.vai_tro !== 'Admin') {
                            toastr.error('Tài khoản này không có quyền truy cập quản trị!');
                            return;
                        }
                        toastr.success(res.data.message);
                        localStorage.setItem('access_token', res.data.access_token);
                        localStorage.setItem('user', JSON.stringify(res.data.user));
                        this.$router.push('/admin/dashboard');
                    }
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Lỗi đăng nhập!';
                    toastr.error(msg);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
}
</script>

<style scoped>
.admin-login-page {
    height: 100vh;
    background: #f4f7f6;
    background: linear-gradient(135deg, #008bf8 0%, #00b4d8 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-box {
    width: 100%;
    max-width: 450px;
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.tracking-wider { letter-spacing: 1px; }
.radius-8 { border-radius: 8px; }

.input-group-text {
    border-color: #dee2e6;
    color: #008bf8;
}

.form-control:focus {
    box-shadow: none;
    border-color: #dee2e6;
}

.btn-primary {
    background: #008bf8;
    border-color: #008bf8;
    transition: 0.3s;
}

.btn-primary:hover {
    background: #0056b3;
    transform: translateY(-2px);
}
</style>
