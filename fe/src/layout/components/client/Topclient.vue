<template>
  <header class="top-client" :class="{ 'scrolled': isScrolled }">
    <div class="container">
      <nav class="navbar">
        <div class="logo">
          <router-link to="/">Heritage <span>Archivist</span></router-link>
        </div>
        <ul class="nav-links">
          <li><router-link to="/">Trang Chủ</router-link></li>
          <li><router-link to="/gia-pha">Gia Phả</router-link></li>
          <li v-if="isLoggedIn"><router-link to="/su-kien">Sự Kiện</router-link></li>
          <li v-if="isLoggedIn"><router-link to="/ban-do">Bản Đồ Số</router-link></li>
          <li v-if="isLoggedIn"><router-link to="/tuong-niem">Dòng Lịch Sử</router-link></li>
          <li><router-link to="/tra-cuu">Tra Cứu</router-link></li>
          <li><router-link to="/dich-vu-goi">Dịch Vụ Gói</router-link></li>
          <li v-for="menu in comingSoonMenus" :key="'cs'+menu.id">
            <router-link :to="'/coming-soon?name=' + encodeURIComponent(menu.ten_chuc_nang)" class="text-gold-client-menu">{{ menu.ten_chuc_nang }}</router-link>
          </li>
        </ul>
        <div class="nav-actions">
          <template v-if="!isLoggedIn">
            <router-link to="/login" class="btn-login">ĐĂNG NHẬP</router-link>
            <router-link to="/register" class="btn-start">KHỞI TẠO</router-link>
          </template>
          <template v-else>
            <div class="user-dropdown" v-click-outside="closeDropdown">
              <div class="user-info" @click="isDropdownOpen = !isDropdownOpen">
                <img :src="userAvatar" alt="User Avatar" class="user-avatar">
                <span class="user-name">{{ userName }}</span>
                <i class="bx bx-chevron-down"></i>
              </div>
              <div class="dropdown-menu" :class="{ 'show': isDropdownOpen }">
                <router-link v-if="isAdmin" to="/admin" @click="isDropdownOpen = false">
                  <i class="bx bx-shield-quarter"></i> Quản trị hệ thống
                </router-link>
                <router-link v-if="isDoiTac" to="/doi-tac/dashboard" @click="isDropdownOpen = false" class="partner-link">
                  <i class="bx bxs-briefcase text-gold"></i> Quản trị đối tác
                </router-link>
                <router-link to="/profile" @click="isDropdownOpen = false">
                  <i class="bx bx-user"></i> Hồ sơ cá nhân
                </router-link>
                <router-link to="/su-kien" @click="isDropdownOpen = false">
                  <i class="bx bx-calendar"></i> Sự kiện dòng họ
                </router-link>
                <router-link to="/ban-do" @click="isDropdownOpen = false">
                  <i class="bx bx-map-pin"></i> Bản đồ số mộ phần
                </router-link>
                <router-link to="/tuong-niem" @click="isDropdownOpen = false">
                  <i class="bx bx-history"></i> Dòng Lịch Sử Gia Tộc
                </router-link>
                <router-link to="/tra-cuu" @click="isDropdownOpen = false">
                  <i class="bx bx-search-alt"></i> Tra cứu xưng hô
                </router-link>
                <hr>
                <a href="javascript:;" @click="handleLogout">
                  <i class="bx bx-log-out"></i> Đăng xuất
                </a>
              </div>
            </div>
          </template>
        </div>
        <button class="mobile-toggle" @click="isMobileMenuOpen = !isMobileMenuOpen">
          <i class="bx" :class="isMobileMenuOpen ? 'bx-x' : 'bx-menu'"></i>
        </button>
      </nav>
    </div>
    <!-- Mobile Menu -->
    <div class="mobile-menu" :class="{ 'open': isMobileMenuOpen }">
      <ul>
        <li><router-link to="/" @click="isMobileMenuOpen = false">Trang Chủ</router-link></li>
        <li><router-link to="/gia-pha" @click="isMobileMenuOpen = false">Gia Phả</router-link></li>
        <li><router-link to="/dong-ho" @click="isMobileMenuOpen = false">Dòng Họ</router-link></li>
        <li><router-link to="/lien-he" @click="isMobileMenuOpen = false">Liên Hệ</router-link></li>
        <template v-if="!isLoggedIn">
          <li><router-link to="/login" class="mobile-btn" @click="isMobileMenuOpen = false">Đăng Nhập</router-link></li>
        </template>
        <template v-else>
          <li v-if="isDoiTac"><router-link to="/doi-tac/dashboard" @click="isMobileMenuOpen = false" style="color: #d4af37;">Quản trị đối tác</router-link></li>
          <li v-if="isAdmin"><router-link to="/admin" @click="isMobileMenuOpen = false" style="color: #3b82f6;">Quản trị hệ thống</router-link></li>
          <li><router-link to="/profile" @click="isMobileMenuOpen = false">Hồ sơ cá nhân</router-link></li>
          <li><router-link to="/su-kien" @click="isMobileMenuOpen = false">Sự kiện dòng họ</router-link></li>
          <li><router-link to="/ban-do" @click="isMobileMenuOpen = false">Bản đồ số</router-link></li>
          <li><router-link to="/tuong-niem" @click="isMobileMenuOpen = false">Dòng Lịch Sử</router-link></li>
          <li><router-link to="/tra-cuu" @click="isMobileMenuOpen = false">Tra cứu xưng hô</router-link></li>
          <li><a href="javascript:;" @click="handleLogout">Đăng xuất</a></li>
        </template>
      </ul>
    </div>
  </header>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Topclient',
  data() {
    return {
      isScrolled: false,
      isMobileMenuOpen: false,
      isLoggedIn: false,
      isAdmin: false,
      isDoiTac: false,
      isDropdownOpen: false,
      userName: '',
      defaultAvatar: "data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%231e293b'/%3E%3Ccircle cx='50' cy='35' r='18' fill='%23d4af37'/%3E%3Cpath d='M15 85 C15 67 30 55 50 55 C70 55 85 67 85 85 Z' fill='%23d4af37'/%3E%3C/svg%3E",
      userAvatar: "data:image/svg+xml;utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%231e293b'/%3E%3Ccircle cx='50' cy='35' r='18' fill='%23d4af37'/%3E%3Cpath d='M15 85 C15 67 30 55 50 55 C70 55 85 67 85 85 Z' fill='%23d4af37'/%3E%3C/svg%3E",
      comingSoonMenus: []
    }
  },
  directives: {
    'click-outside': {
      mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
          if (!(el === event.target || el.contains(event.target))) {
            binding.value(event);
          }
        };
        document.body.addEventListener('click', el.clickOutsideEvent);
      },
      unmounted(el) {
        document.body.removeEventListener('click', el.clickOutsideEvent);
      }
    }
  },
  mounted() {
    window.addEventListener('scroll', this.handleScroll);
    window.addEventListener('profile-updated', this.checkLogin);
    this.checkLogin();
    this.loadComingSoonMenus();
  },
  unmounted() {
    window.removeEventListener('scroll', this.handleScroll);
    window.removeEventListener('profile-updated', this.checkLogin);
  },
  methods: {
    loadComingSoonMenus() {
      const token = localStorage.getItem('access_token');
      if (!token) return;
      axios.get('http://127.0.0.1:8000/api/chuc-nang/coming-soon-menus', {
        headers: { Authorization: 'Bearer ' + token }
      }).then(res => {
        if (res.data && res.data.status) {
          this.comingSoonMenus = res.data.data || [];
        }
      }).catch(() => {});
    },
    handleScroll() {
      this.isScrolled = window.scrollY > 50;
    },
    checkLogin() {
      const token = localStorage.getItem('access_token');
      const userData = JSON.parse(localStorage.getItem('user'));
      if (token && userData) {
        this.isLoggedIn = true;
        // Robustness: handle both {user, permissions} and direct user object
        const user = userData.user || userData;
        this.userName = user.ho_ten;
        this.isAdmin = user.vai_tro === 'Admin';
        this.isDoiTac = user.is_doi_tac == 1 || user.is_doi_tac === true || user.vai_tro === 'Đối tác';
        const avatar = user.avatar;
        if (avatar && avatar !== 'https://dzfullstack.com/assets/images/logo-1.png') {
          this.userAvatar = avatar;
        } else {
          this.userAvatar = this.defaultAvatar;
        }
      } else {
        this.isLoggedIn = false;
        this.isAdmin = false;
        this.isDoiTac = false;
      }
    },
    closeDropdown() {
      this.isDropdownOpen = false;
    },
    handleLogout() {
      localStorage.removeItem('access_token');
      localStorage.removeItem('user');
      this.isLoggedIn = false;
      this.isAdmin = false;
      this.isDoiTac = false;
      this.isDropdownOpen = false;
      this.$router.push('/login');
    }
  },
  watch: {
    '$route'() {
      this.checkLogin();
    }
  }
}
</script>

<style scoped>
.top-client {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1000;
  padding: 20px 0;
  transition: all 0.4s ease;
  background: transparent;
}

.top-client.scrolled {
  padding: 12px 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo a {
  font-family: 'Playfair Display', serif;
  font-size: 24px;
  font-weight: 700;
  color: #fff;
  text-decoration: none;
  letter-spacing: 1px;
}

.top-client.scrolled .logo a {
  color: #1a1a1a;
}

.logo span {
  color: #d4af37;
  font-weight: 400;
  font-style: italic;
}

.nav-links {
  display: flex;
  list-style: none;
  gap: 35px;
  margin: 0;
  padding: 0;
}

.nav-links a {
  text-decoration: none;
  color: #ffffff;
  font-size: 14px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  transition: all 0.3s ease;
  opacity: 0.9;
}

.top-client.scrolled .nav-links a {
  color: #1a1a1a;
}

.nav-links a:hover,
.nav-links a.router-link-active {
  color: #d4af37 !important;
  opacity: 1;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 20px;
}

.btn-login {
  color: #fff;
  text-decoration: none;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 1px;
}

.top-client.scrolled .btn-login {
  color: #1a1a1a;
}

.btn-start {
  background-color: #d4af37;
  color: #000;
  text-decoration: none;
  padding: 10px 25px;
  font-size: 12px;
  font-weight: 800;
  letter-spacing: 1px;
  border-radius: 4px;
  transition: transform 0.3s;
}

.btn-start:hover {
  transform: translateY(-2px);
  background-color: #c4a030;
}

/* User Dropdown */
.user-dropdown {
  position: relative;
  cursor: pointer;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  color: #fff;
}

.top-client.scrolled .user-info {
  color: #1a1a1a;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #d4af37;
}

.user-name {
  font-weight: 600;
  font-size: 14px;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: #fff;
  min-width: 200px;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  padding: 10px 0;
  margin-top: 15px;
  display: none;
  border: 1px solid #eee;
}

.dropdown-menu.show {
  display: block;
  animation: fadeInDown 0.3s ease;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-menu a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  color: #333;
  text-decoration: none;
  font-size: 14px;
  transition: background 0.3s;
}

.dropdown-menu a:hover {
  background: #fdf8ef;
  color: #d4af37;
}

.dropdown-menu hr {
  margin: 8px 0;
  border: none;
  border-top: 1px solid #eee;
}

.mobile-toggle {
  display: none;
  background: none;
  border: none;
  color: #fff;
  font-size: 30px;
  cursor: pointer;
}

.top-client.scrolled .mobile-toggle {
  color: #1a1a1a;
}

/* Mobile Menu */
.mobile-menu {
  position: fixed;
  top: 0;
  right: -100%;
  width: 80%;
  height: 100vh;
  background: #fff;
  z-index: 999;
  transition: right 0.4s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
}

.mobile-menu.open {
  right: 0;
}

.mobile-menu ul {
  list-style: none;
  padding: 0;
  text-align: center;
}

.mobile-menu li {
  margin-bottom: 30px;
}

.mobile-menu a {
  font-size: 24px;
  font-family: 'Playfair Display', serif;
  text-decoration: none;
  color: #1a1a1a;
  font-weight: 700;
}

.mobile-btn {
  display: inline-block;
  background: #d4af37;
  padding: 10px 30px;
  border-radius: 4px;
  font-size: 18px !important;
  font-family: 'Inter', sans-serif !important;
}

@media (max-width: 991px) {

  .nav-links,
  .nav-actions {
    display: none;
  }

  .mobile-toggle {
    display: block;
  }
}

.text-gold {
  color: #d4af37 !important;
}
.partner-link:hover {
  background: #fdf8ef !important;
}
.text-gold-client-menu {
  color: #ffd700 !important;
  font-weight: 700 !important;
  text-shadow: 0 0 4px rgba(255, 215, 0, 0.2);
}
.text-gold-client-menu:hover {
  color: #ffffff !important;
  text-shadow: 0 0 8px rgba(255, 255, 255, 0.4);
}
</style>
