// Phân tách phiên làm việc theo từng tab trình duyệt (Admin, Đối tác, Client mở đồng thời không bị đá nhau)
(function() {
    const keysToRedirect = ['access_token', 'user', 'permissions'];
    const originalGet = localStorage.getItem.bind(localStorage);
    const originalSet = localStorage.setItem.bind(localStorage);
    const originalRemove = localStorage.removeItem.bind(localStorage);

    localStorage.getItem = function(key) {
        if (keysToRedirect.includes(key)) {
            return sessionStorage.getItem(key);
        }
        return originalGet(key);
    };

    localStorage.setItem = function(key, value) {
        if (keysToRedirect.includes(key)) {
            return sessionStorage.setItem(key, value);
        }
        return originalSet(key, value);
    };

    localStorage.removeItem = function(key) {
        if (keysToRedirect.includes(key)) {
            return sessionStorage.removeItem(key);
        }
        return originalRemove(key);
    };
})();

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Default from './layout/wrapper/index.vue'
import Client from './layout/wrapper/client.vue'
import Blank from './layout/wrapper/blank.vue'
import Partner from './layout/wrapper/partner.vue'
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import 'toastr/build/toastr.min.css';
import './assets/css/custom-toastr.css';
import axios from 'axios';
import toastr from 'toastr';

// Cấu hình interceptor cho axios để tự động đính kèm token vào header
axios.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('access_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

axios.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response) {
      if (error.response.status === 401) {
        // Token không hợp lệ hoặc đã hết hạn (ví dụ khi refresh database)
        localStorage.removeItem('access_token');
        localStorage.removeItem('user');
        localStorage.removeItem('permissions');
        toastr.error("Phiên đăng nhập đã hết hạn hoặc cơ sở dữ liệu đã được làm mới. Vui lòng đăng nhập lại!");
        router.push('/login');
      } else if (error.response.status === 403) {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user?.is_doi_tac == 1) {
          toastr.error("Bạn không có quyền sử dụng chức năng này. Khi nào bên Admin bật lại thì bạn mới có thể dùng được!");
          router.push('/doi-tac/dashboard');
        }
      }
    }
    return Promise.reject(error);
  }
);

const app = createApp(App)

app.use(router)
app.use(Toast, {
  timeout: 3000,
  closeOnClick: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  position: POSITION.TOP_RIGHT
});

app.component("default-layout", Default);
app.component("client-layout", Client);
app.component("blank-layout", Blank);
app.component("partner-layout", Partner);
app.mount("#app")