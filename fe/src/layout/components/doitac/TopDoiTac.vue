<template>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand">
            <div class="topbar-logo-header">
                <div class="">
                    <img src="../../../assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
                </div>
                <div class="">
                    <h4 class="logo-text">Đối Tác</h4>
                </div>
            </div>
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i></div>
            
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative"
                            href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span
                                class="alert-count">7</span>
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="msg-header">
                                <p class="msg-header-title">Thông báo</p>
                                <p class="msg-header-clear ms-auto">Đánh dấu đã đọc</p>
                            </div>
                            <div class="header-notifications-list">
                                <!-- Placeholders -->
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="user-box dropdown">
                <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret"
                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img :src="userAvatar" class="user-img" alt="user avatar">
                    <div class="user-info ps-3">
                        <p class="user-name mb-0">{{ userName }}</p>
                        <p class="designattion mb-0">Đối Tác</p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><router-link class="dropdown-item" to="/profile"><i class="bx bx-user"></i><span>Hồ sơ cá nhân</span></router-link></li>
                    <li><div class="dropdown-divider mb-0"></div></li>
                    <li><a class="dropdown-item" href="javascript:;" @click="handleLogout"><i class='bx bx-log-out-circle'></i><span>Đăng xuất</span></a></li>
                </ul>
            </div>
        </nav>
    </div>
</template>

<script>
export default {
    name: 'TopDoiTac',
    data() {
        return {
            userName: 'Đối Tác',
            userAvatar: 'https://dzfullstack.com/assets/images/logo-1.png'
        }
    },
    mounted() {
        this.checkLogin();
    },
    methods: {
        checkLogin() {
            const userData = JSON.parse(localStorage.getItem('user'));
            if (userData) {
                const user = userData.user || userData;
                this.userName = user.ho_ten;
                if (user.avatar) {
                    this.userAvatar = user.avatar;
                }
            }
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
</style>
