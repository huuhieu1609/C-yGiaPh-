<template>
    <div class="auth-page" :style="{ backgroundImage: 'url(' + heroBg + ')' }">
        <div class="auth-overlay">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <router-link to="/" class="auth-logo">Heritage <span>Archivist</span></router-link>
                        <h2>Chào mừng trở lại</h2>
                        <p>Tiếp tục hành trình kết nối với cội nguồn của bạn</p>
                    </div>
                    <form @submit.prevent="handleLogin" class="auth-form">
                        <div class="form-group">
                            <label>Email</label>
                            <div class="input-wrapper">
                                <i class="bx bx-user"></i>
                                <input type="email" v-model="loginData.email" placeholder="Nhập email của bạn" required>
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
                        <div class="form-options">
                            <label class="remember-me">
                                <input type="checkbox"> <span>Ghi nhớ đăng nhập</span>
                            </label>
                            <router-link to="/forgot-password" class="forgot-password">Quên mật khẩu?</router-link>
                        </div>
                        <button type="submit" class="btn-auth" :disabled="isLoading">
                            <span v-if="!isLoading">ĐĂNG NHẬP</span>
                            <span v-else><i class="bx bx-loader-alt bx-spin"></i> ĐANG XỬ LÝ...</span>
                        </button>
                    </form>
                    <div class="auth-footer">
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
<<<<<<< HEAD

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
=======
                        let redirect = this.$route.query.redirect || '/';
                        if (res.data.user.is_doi_tac == 1) {
                            redirect = '/doi-tac/dashboard';
                        }
                        this.$router.push(redirect);
                    }
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Có lỗi xảy ra, vui lòng thử lại!';
                    toastr.error(msg);
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
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
<<<<<<< HEAD
    min-height: 100vh;
    display: flex;
    align-items: center;
=======
    height: 100vh;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
    background-size: cover;
    background-position: center;
    position: relative;
    font-family: 'Inter', sans-serif;
}

.auth-overlay {
    position: absolute;
<<<<<<< HEAD
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
=======
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
}

.auth-container {
    width: 100%;
    max-width: 450px;
<<<<<<< HEAD
    margin: 0 auto;
    padding: 20px;
    position: relative;
    z-index: 1;
=======
    padding: 20px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
}

.auth-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
<<<<<<< HEAD
    padding: 40px;
=======
    padding: 50px 40px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
    color: #fff;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
}

.auth-header {
    text-align: center;
<<<<<<< HEAD
    margin-bottom: 30px;
=======
    margin-bottom: 40px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
}

.auth-logo {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
<<<<<<< HEAD
    margin-bottom: 15px;
=======
    margin-bottom: 20px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
    display: block;
}

.auth-logo span {
    color: #d4af37;
    font-weight: 400;
    font-style: italic;
}

.auth-header h2 {
    font-family: 'Playfair Display', serif;
<<<<<<< HEAD
    font-size: 28px;
    margin-bottom: 8px;
=======
    font-size: 32px;
    margin-bottom: 10px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
}

.auth-header p {
    color: #aaa;
<<<<<<< HEAD
    font-size: 13px;
}

.form-group {
    margin-bottom: 20px;
=======
    font-size: 14px;
}

.form-group {
    margin-bottom: 25px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
}

.form-group label {
    display: block;
<<<<<<< HEAD
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 8px;
=======
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 10px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
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
<<<<<<< HEAD
    font-size: 18px;
=======
    font-size: 20px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
}

.input-wrapper input {
    width: 100%;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
<<<<<<< HEAD
    padding: 14px 15px 14px 45px;
    border-radius: 8px;
    color: #fff;
    font-size: 14px;
=======
    padding: 15px 15px 15px 50px;
    border-radius: 10px;
    color: #fff;
    font-size: 15px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
    transition: all 0.3s;
}

.input-wrapper input::placeholder {
    color: #fff;
<<<<<<< HEAD
    opacity: 0.6;
=======
    opacity: 0.8;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
}

.input-wrapper input:focus {
    outline: none;
    border-color: #d4af37;
    background: rgba(255, 255, 255, 0.1);
}

<<<<<<< HEAD
=======
.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    font-size: 13px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: #aaa;
}

.forgot-password {
    color: #d4af37;
    text-decoration: none;
}

>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
.btn-auth {
    width: 100%;
    background: #d4af37;
    color: #000;
    border: none;
<<<<<<< HEAD
    padding: 15px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
=======
    padding: 18px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 2px;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 25px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
}

.btn-auth:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-auth:hover:not(:disabled) {
    background: #fff;
    transform: translateY(-2px);
<<<<<<< HEAD
=======
    box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
}

.auth-footer {
    text-align: center;
<<<<<<< HEAD
    font-size: 13px;
=======
    font-size: 14px;
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
    color: #aaa;
}

.auth-footer a {
    color: #d4af37;
    text-decoration: none;
    font-weight: 600;
}
<<<<<<< HEAD
</style>
=======

.auth-footer a:hover {
    text-decoration: underline;
}
</style>
>>>>>>> 81ae88bc363c24c58beb23ab4fb36bdbc33721de
