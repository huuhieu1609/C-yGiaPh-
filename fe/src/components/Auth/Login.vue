<template>
    <div class="auth-page" :style="{ backgroundImage: 'url(' + heroBg + ')' }">
        <div class="auth-overlay">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <router-link to="/" class="auth-logo">Heritage <span>Archivist</span></router-link>
                        <h2>Đăng Nhập</h2>
                        <p>Chào mừng bạn quay trở lại hệ thống</p>
                    </div>
                    <form @submit.prevent="handleLogin" class="auth-form">
                        <div class="form-group">
                            <label>Địa chỉ Email</label>
                            <div class="input-wrapper">
                                <i class="bx bx-envelope"></i>
                                <input type="email" v-model="loginData.email" placeholder="example@email.com" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Mật khẩu</label>
                            <div class="input-wrapper">
                                <i class="bx bx-lock-alt"></i>
                                <input type="password" v-model="loginData.mat_khau" placeholder="Nhập mật khẩu"
                                    required>
                            </div>
                        </div>
                        <button type="submit" class="btn-auth" :disabled="isLoading">
                            <span v-if="!isLoading">ĐĂNG NHẬP</span>
                            <span v-else><i class="bx bx-loader-alt bx-spin"></i> ĐANG XỬ LÝ...</span>
                        </button>
                    </form>
                    <div class="auth-footer mt-4">
                        <p>Chưa có tài khoản? <router-link to="/register">Đăng ký ngay</router-link></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import heroBg from '@/assets/images/hero_bg.png';
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'Login',
    data() {
        return {
            heroBg,
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
                        toastr.success(res.data.message);

                        // 1. Lưu thông tin vào LocalStorage trước
                        localStorage.setItem('access_token', res.data.access_token);
                        localStorage.setItem('user', JSON.stringify(res.data.user));

                        // 2. Lấy thông tin user vừa đăng nhập để phân quyền chính xác
                        const user = res.data.user;

                        // 3. Chủ động điều hướng dựa trên vai trò thực tế của User
                        if (user && user.vai_tro?.toLowerCase() === 'admin') {
                            this.$router.push('/admin/dashboard');
                        } else if (user && user.is_doi_tac == 1) {
                            this.$router.push('/doi-tac/dashboard');
                        } else {
                            this.$router.push('/gia-pha');
                        }
                    } else {
                        toastr.error(res.data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    toastr.error(err.response?.data?.message || 'Email hoặc mật khẩu không đúng!');
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600;700&display=swap');

.auth-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    background-size: cover;
    background-position: center;
    position: relative;
    font-family: 'Inter', sans-serif;
}

.auth-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
}

.auth-container {
    width: 100%;
    max-width: 450px;
    margin: 0 auto;
    padding: 20px;
    position: relative;
    z-index: 1;
}

.auth-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 40px;
    color: #fff;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.auth-logo {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
    margin-bottom: 15px;
    display: block;
}

.auth-logo span {
    color: #d4af37;
    font-weight: 400;
    font-style: italic;
}

.auth-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    margin-bottom: 8px;
}

.auth-header p {
    color: #aaa;
    font-size: 13px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #d4af37;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.input-wrapper {
    position: relative;
}

.input-wrapper i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #d4af37;
    font-size: 18px;
}

.input-wrapper input {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 14px 15px 14px 45px;
    border-radius: 8px;
    color: #fff;
    font-size: 14px;
    transition: all 0.3s;
}

.input-wrapper input::placeholder {
    color: #fff;
    opacity: 0.6;
}

.input-wrapper input:focus {
    outline: none;
    border-color: #d4af37;
    background: rgba(255, 255, 255, 0.1);
}

.btn-auth {
    width: 100%;
    background: #d4af37;
    color: #000;
    border: none;
    padding: 15px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.btn-auth:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-auth:hover:not(:disabled) {
    background: #fff;
    transform: translateY(-2px);
}

.auth-footer {
    text-align: center;
    font-size: 13px;
    color: #aaa;
}

.auth-footer a {
    color: #d4af37;
    text-decoration: none;
    font-weight: 600;
}
</style>