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
            <!-- Bell Notification Icon & Dropdown -->
            <div class="notification-wrapper" v-click-outside="closeNotificationDropdown">
              <div class="bell-trigger" @click="toggleNotificationDropdown" :class="{ 'has-unread': unreadCount > 0 }">
                <i class="bx" :class="unreadCount > 0 ? 'bxs-bell animate-ring' : 'bx-bell'"></i>
                <span class="unread-badge" v-if="unreadCount > 0">{{ unreadCount }}</span>
              </div>

              <!-- Notifications Dropdown Panel -->
              <div class="notification-dropdown" :class="{ 'show': isNotificationOpen }">
                <div class="noti-header d-flex justify-content-between align-items-center">
                  <h6 class="mb-0 fw-bold"><i class="bx bx-bell me-1"></i>Thông báo dòng họ</h6>
                  <button v-if="unreadCount > 0" class="btn-mark-read" @click="markAllAsRead">
                    <i class="bx bx-check-double"></i> Đọc tất cả
                  </button>
                </div>
                
                <div class="noti-list scrollable-noti">
                  <!-- Case 1: Loading state -->
                  <div v-if="isNotiLoading" class="noti-empty text-center py-4">
                    <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                    <p class="mb-0 mt-1 small text-muted">Đang tải thông báo...</p>
                  </div>

                  <!-- Case 2: Empty state -->
                  <div v-else-if="notifications.length === 0" class="noti-empty text-center py-4">
                    <i class="bx bx-bell-off text-muted display-6 opacity-25"></i>
                    <p class="mb-0 mt-1 small text-muted">Không có thông báo nào mới.</p>
                  </div>

                  <!-- Case 3: List of notifications -->
                  <div v-else>
                    <div 
                      v-for="noti in notifications" 
                      :key="noti.uniqueId" 
                      class="noti-item" 
                      :class="{ 'unread': !noti.isRead }"
                      @click="readSingleNotification(noti)"
                    >
                      <div class="noti-icon-box" :class="noti.type">
                        <i class="bx" :class="noti.type === 'su-kien' ? 'bx-calendar' : 'bx-bell'"></i>
                      </div>
                      <div class="noti-content">
                        <span class="noti-badge-type">{{ noti.typeLabel }}</span>
                        <h6 class="noti-title mb-1">{{ noti.title }}</h6>
                        <p class="noti-text mb-1">{{ noti.message }}</p>
                        <span class="noti-time"><i class="bx bx-time-five me-1"></i>{{ noti.timeAgo }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

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
      comingSoonMenus: [],
      isNotificationOpen: false,
      isNotiLoading: false,
      notifications: [],
      readNotificationIds: [],
      unreadCount: 0
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
        this.loadReadNotificationIds();
        this.fetchNotifications();
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
    },
    loadReadNotificationIds() {
      try {
        const stored = localStorage.getItem('read_notification_ids');
        this.readNotificationIds = stored ? JSON.parse(stored) : [];
      } catch (e) {
        this.readNotificationIds = [];
      }
    },
    saveReadNotificationIds() {
      localStorage.setItem('read_notification_ids', JSON.stringify(this.readNotificationIds));
    },
    toggleNotificationDropdown() {
      this.isNotificationOpen = !this.isNotificationOpen;
      if (this.isNotificationOpen) {
        this.fetchNotifications();
      }
    },
    closeNotificationDropdown() {
      this.isNotificationOpen = false;
    },
    fetchNotifications() {
      const token = localStorage.getItem('access_token');
      if (!token) return;

      this.isNotiLoading = true;
      this.loadReadNotificationIds();

      const headers = { headers: { Authorization: `Bearer ${token}` } };

      Promise.all([
        axios.get('http://127.0.0.1:8000/api/su-kien/get-data', headers).catch(() => ({ data: { status: false } })),
        axios.get('http://127.0.0.1:8000/api/thong-bao/get-data', headers).catch(() => ({ data: { status: false } })),
        axios.get('http://127.0.0.1:8000/api/de-xuat/my-proposals', headers).catch(() => ({ data: { status: false } }))
      ])
      .then(([resEvents, resNotis, resProposals]) => {
        let list = [];

        if (resEvents.data && resEvents.data.status) {
          const events = resEvents.data.data.map(item => {
            const formattedDate = item.ngay_to_chuc ? new Date(item.ngay_to_chuc).toLocaleDateString('vi-VN') : '';
            return {
              uniqueId: `event_${item.id}`,
              id: item.id,
              type: 'su-kien',
              typeLabel: 'Sự kiện dòng tộc',
              title: `📅 ${item.loai || 'Sự kiện'}: ${item.tieu_de}`,
              message: `Diễn ra tại ${item.dia_diem || 'Gia đường'} vào ngày ${formattedDate}. ${item.noi_dung || ''}`,
              createdAt: new Date(item.created_at || item.ngay_to_chuc),
              timeAgo: this.getTimeAgo(item.created_at || item.ngay_to_chuc),
              isRead: this.readNotificationIds.includes(`event_${item.id}`)
            };
          });
          list.push(...events);
        }

        if (resNotis.data && resNotis.data.status) {
          const announcements = resNotis.data.data.map(item => {
            return {
              uniqueId: `announcement_${item.id}`,
              id: item.id,
              type: 'thong-bao',
              typeLabel: 'Thông báo chung',
              title: `📢 ${item.tieu_de}`,
              message: item.noi_dung || '',
              createdAt: new Date(item.created_at),
              timeAgo: this.getTimeAgo(item.created_at),
              isRead: this.readNotificationIds.includes(`announcement_${item.id}`)
            };
          });
          list.push(...announcements);
        }

        if (resProposals.data && resProposals.data.status) {
          const proposals = resProposals.data.data
            .filter(item => item.status === 'approved' || item.status === 'rejected')
            .map(item => {
              const isApp = item.status === 'approved';
              const typeLabel = item.type === 'edit' ? 'Chỉnh sửa' : (item.type === 'add_child' ? 'Thêm con' : (item.type === 'add_spouse' ? 'Thêm phối ngẫu' : 'Yêu cầu xóa'));
              const memberName = item.data?.ho_ten || '';
              
              return {
                uniqueId: `proposal_${item.id}_${item.status}`,
                id: item.id,
                type: isApp ? 'de-xuat-duyet' : 'de-xuat-tuchoi',
                typeLabel: isApp ? 'Đề xuất đã duyệt' : 'Đề xuất bị từ chối',
                title: isApp ? `✅ Đề xuất được phê duyệt` : `❌ Đề xuất bị từ chối`,
                message: isApp 
                  ? `Đề xuất "${typeLabel}: ${memberName}" của bạn đã được phê duyệt thành công!` 
                  : `Đề xuất "${typeLabel}: ${memberName}" của bạn bị từ chối. Lý do: ${item.note || 'Không có ghi chú.'}`,
                createdAt: new Date(item.updated_at || item.created_at),
                timeAgo: this.getTimeAgo(item.updated_at || item.created_at),
                isRead: this.readNotificationIds.includes(`proposal_${item.id}_${item.status}`)
              };
            });
          list.push(...proposals);
        }

        list.sort((a, b) => b.createdAt - a.createdAt);

        this.notifications = list;
        this.updateUnreadCount();
      })
      .finally(() => {
        this.isNotiLoading = false;
      });
    },
    updateUnreadCount() {
      this.unreadCount = this.notifications.filter(n => !n.isRead).length;
    },
    markAllAsRead() {
      this.notifications.forEach(n => {
        if (!this.readNotificationIds.includes(n.uniqueId)) {
          this.readNotificationIds.push(n.uniqueId);
        }
        n.isRead = true;
      });
      this.saveReadNotificationIds();
      this.updateUnreadCount();
    },
    readSingleNotification(noti) {
      if (!this.readNotificationIds.includes(noti.uniqueId)) {
        this.readNotificationIds.push(noti.uniqueId);
        noti.isRead = true;
        this.saveReadNotificationIds();
        this.updateUnreadCount();
      }
      this.isNotificationOpen = false;
      if (noti.type === 'su-kien') {
        this.$router.push('/su-kien');
      } else if (noti.type === 'thong-bao') {
        this.$router.push('/tuong-niem');
      } else {
        this.$router.push('/gia-pha');
      }
    },
    getTimeAgo(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      const now = new Date();
      const seconds = Math.floor((now - date) / 1000);

      if (seconds < 0) return 'Vừa xong';

      const intervals = {
        năm: 31536000,
        tháng: 2592000,
        tuần: 604800,
        ngày: 86400,
        giờ: 3600,
        phút: 60
      };

      for (const [unit, value] of Object.entries(intervals)) {
        const count = Math.floor(seconds / value);
        if (count >= 1) {
          return `${count} ${unit} trước`;
        }
      }
      return 'Vừa xong';
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
  flex-wrap: nowrap !important;
}

.logo {
  flex-shrink: 0;
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
  gap: 20px;
  margin: 0;
  padding: 0;
  flex-shrink: 0;
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
  flex-shrink: 0;
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

/* Premium Notification Bell & Dropdown Styles */
.notification-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.bell-trigger {
  position: relative;
  cursor: pointer;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #fff;
  font-size: 20px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  margin-right: 15px;
}
.top-client.scrolled .bell-trigger {
  background: rgba(0, 0, 0, 0.03);
  border-color: rgba(0, 0, 0, 0.06);
  color: #1a1a1a;
}
.bell-trigger:hover {
  background: rgba(212, 175, 55, 0.1);
  border-color: rgba(212, 175, 55, 0.3);
  color: #d4af37;
  transform: scale(1.05);
}
.top-client.scrolled .bell-trigger:hover {
  background: rgba(212, 175, 55, 0.08);
  border-color: rgba(212, 175, 55, 0.25);
  color: #d4af37;
}
.unread-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #ef4444;
  color: #fff;
  font-size: 10px;
  font-weight: 800;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #000;
  box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4);
}
.top-client.scrolled .unread-badge {
  border-color: #fff;
}
.animate-ring {
  animation: ring 2.2s infinite ease-in-out;
}
@keyframes ring {
  0% { transform: rotate(0deg); }
  5% { transform: rotate(15deg); }
  10% { transform: rotate(-15deg); }
  15% { transform: rotate(10deg); }
  20% { transform: rotate(-10deg); }
  25% { transform: rotate(5deg); }
  30% { transform: rotate(-5deg); }
  35% { transform: rotate(0deg); }
  100% { transform: rotate(0deg); }
}

.notification-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  background: #ffffff;
  min-width: 360px;
  max-width: 400px;
  border-radius: 16px;
  box-shadow: 0 15px 45px rgba(0, 0, 0, 0.15);
  margin-top: 15px;
  display: none;
  border: 1px solid rgba(0, 0, 0, 0.07);
  z-index: 1010;
  overflow: hidden;
  animation: fadeInDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.notification-dropdown.show {
  display: block;
}
.noti-header {
  padding: 16px 20px;
  border-bottom: 1px solid #f3f4f6;
  background: #fdfdfd;
}
.noti-header h6 {
  color: #111827;
  font-size: 14px;
}
.btn-mark-read {
  background: transparent;
  border: none;
  color: #d4af37;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: opacity 0.2s;
  display: flex;
  align-items: center;
  gap: 3px;
}
.btn-mark-read:hover {
  opacity: 0.8;
}
.scrollable-noti {
  max-height: 380px;
  overflow-y: auto;
}
.noti-empty {
  color: #9ca3af;
  font-size: 13px;
}
.noti-item {
  display: flex;
  gap: 12px;
  padding: 16px 20px;
  border-bottom: 1px solid #f9fafb;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}
.noti-item:last-child {
  border-bottom: none;
}
.noti-item:hover {
  background: rgba(212, 175, 55, 0.02);
}
.noti-item.unread {
  background: rgba(212, 175, 55, 0.04);
}
.noti-item.unread:hover {
  background: rgba(212, 175, 55, 0.06);
}
.noti-icon-box {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
  margin-top: 2px;
}
.noti-icon-box.su-kien {
  background: rgba(59, 130, 246, 0.08);
  color: #3b82f6;
}
.noti-icon-box.thong-bao {
  background: rgba(249, 115, 22, 0.08);
  color: #f97316;
}
.noti-icon-box.de-xuat-duyet {
  background: rgba(16, 185, 129, 0.08);
  color: #10b981;
}
.noti-icon-box.de-xuat-tuchoi {
  background: rgba(239, 68, 68, 0.08);
  color: #ef4444;
}
.noti-content {
  flex-grow: 1;
}
.noti-badge-type {
  font-size: 9px;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.5px;
  padding: 2px 6px;
  border-radius: 4px;
  margin-bottom: 4px;
  display: inline-block;
}
.noti-item .su-kien + .noti-content .noti-badge-type {
  background: rgba(59, 130, 246, 0.08);
  color: #3b82f6;
}
.noti-item .thong-bao + .noti-content .noti-badge-type {
  background: rgba(249, 115, 22, 0.08);
  color: #f97316;
}
.noti-item .de-xuat-duyet + .noti-content .noti-badge-type {
  background: rgba(16, 185, 129, 0.08);
  color: #10b981;
}
.noti-item .de-xuat-tuchoi + .noti-content .noti-badge-type {
  background: rgba(239, 68, 68, 0.08);
  color: #ef4444;
}
.noti-title {
  color: #111827;
  font-size: 13px;
  font-weight: 700;
  line-height: 1.4;
}
.noti-text {
  color: #4b5563;
  font-size: 12px;
  line-height: 1.4;
  margin: 0;
}
.noti-time {
  color: #9ca3af;
  font-size: 10px;
  display: flex;
  align-items: center;
  margin-top: 4px;
}
</style>
