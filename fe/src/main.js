import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Default from './Layout/wrapper/index.vue'
import Client from './Layout/wrapper/client.vue'
import Blank from './Layout/wrapper/blank.vue'
import Partner from './Layout/wrapper/partner.vue'
import Toast, { POSITION } from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import 'toastr/build/toastr.min.css';
import './assets/css/custom-toastr.css';
import axios from 'axios';

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